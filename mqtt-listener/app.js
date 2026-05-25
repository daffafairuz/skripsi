const http = require('http');
const fs = require('fs');
const path = require('path');
const axios = require('axios');
const mqtt = require('mqtt');
const mysql = require('mysql2/promise');

require('dotenv').config({
    path:
    path.join(
        __dirname,
        '.env'
    )
});

function env(...names){

    for(
    const name
    of names
    ){

        const value =
        process.env[name];

        if(
        value!==undefined &&
        value!==''
        ){

            return value;

        }

    }

    return undefined;

}

function trimTrailingSlash(value){

    return String(
        value
    ).replace(
        /\/+$/,
        ''
    );

}

function sensorAlertEndpoint(){

    const explicitUrl =
    env('SENSOR_ALERT_URL');

    if(explicitUrl){

        return explicitUrl;

    }

    const apiUrl =
    env('API_URL');

    if(apiUrl){

        return trimTrailingSlash(
            apiUrl
        ).endsWith(
            '/sensor-alert'
        )
        ? apiUrl
        : `${trimTrailingSlash(apiUrl)}/sensor-alert`;

    }

    return `${trimTrailingSlash(env('APP_URL') || 'http://127.0.0.1:8000')}/api/sensor-alert`;

}

function normalizePhoneNumber(phoneNumber){

    if(!phoneNumber){

        return null;

    }

    let phone =
    String(
        phoneNumber
    ).replace(
        /\D/g,
        ''
    );

    if(
    phone.startsWith(
        '0'
    )
    ){

        phone =
        `62${phone.slice(1)}`;

    }
    else if(
    phone.startsWith(
        '8'
    )
    ){

        phone =
        `62${phone}`;

    }

    return phone || null;

}

function whatsappMessage(alert){

    return [
        'Smart Aquaponic Alert',
        `Site: ${alert.siteName || '-'}`,
        `Sensor: ${alert.sensorName || '-'}`,
        `Nilai: ${alert.value}${alert.unit || ''}`,
        `Status: ${alert.message}`
    ].join(
        '\n'
    );

}


// =====================
// LOGGER
// =====================

function log(text){

    const time =
    new Date().toISOString();

    const message =
    `[${time}] ${text}\n`;

    console.log(text);

    fs.appendFile(

        path.join(
            __dirname,
            'mqtt.log'
        ),

        message,

        (err)=>{

            if(err){

                console.error(
                    'Log Error:',
                    err
                );

            }

        }

    );

}


// =====================
// DATABASE
// =====================

const db =
mysql.createPool({

    host:
    env('DB_HOST', 'MYSQL_HOST'),

    port:
    Number(
        env('DB_PORT', 'MYSQL_PORT') || 3306
    ),

    user:
    env('DB_USERNAME', 'DB_USER', 'MYSQL_USER'),

    password:
    env('DB_PASSWORD', 'MYSQL_PASSWORD'),

    database:
    env('DB_DATABASE', 'DB_NAME', 'MYSQL_DATABASE'),

    waitForConnections:true,

    connectionLimit:10,

    queueLimit:0,

    enableKeepAlive:true,

    keepAliveInitialDelay:10000

});

const sensorAlertUrl =
sensorAlertEndpoint();

const sensorAlertSecret =
env('SENSOR_ALERT_SECRET');

const whatsappToken =
env('FONTE_TOKEN', 'FONNTE_TOKEN');

const whatsappUrl =
env('FONTE_URL', 'FONNTE_URL') ||
'https://api.fonnte.com/send';


// =====================
// SENSOR ALERT API
// =====================

async function sendSensorAlert(alert){

    if(!sensorAlertSecret){

        log(
            'Sensor alert secret missing'
        );

        return {
            created:
            false
        };

    }

    const response =
    await axios.post(

        sensorAlertUrl,

        {
            site_id:
            alert.siteId,

            message:
            alert.message,

            type:
            'warning'
        },

        {
            headers:{
                'X-Sensor-Alert-Secret':
                sensorAlertSecret
            },

            timeout:
            10000
        }

    );

    return response.data;

}

// =====================
// WHATSAPP API
// =====================

async function sendWhatsAppAlert(alert){

    if(!whatsappToken){

        log(
            'Fonnte token missing'
        );

        return;

    }

    const target =
    normalizePhoneNumber(
        alert.phoneNumber
    );

    if(!target){

        log(
            `WhatsApp target missing:
site_id=${alert.siteId}`
        );

        return;

    }

    await axios.post(

        whatsappUrl,

        new URLSearchParams({
            target:
            target,

            message:
            whatsappMessage(
                alert
            )
        }),

        {
            headers:{
                Authorization:
                whatsappToken
            },

            timeout:
            10000
        }

    );

}


// =====================
// TEST DB
// =====================

async function testDB(){

    try{

        const conn =
        await db.getConnection();

        log(
            'Database Connected'
        );

        conn.release();

    }
    catch(err){

        log(
            `DB Error: ${err.message}`
        );

    }

}


// =====================
// MQTT
// =====================

const client =
mqtt.connect(

process.env.MQTT_URL,

{

username:
process.env.MQTT_USERNAME,

password:
process.env.MQTT_PASSWORD,

reconnectPeriod:5000,

connectTimeout:30000,

keepalive:60

}

);


// =====================
// MQTT CONNECT
// =====================

client.on(

'connect',

()=>{

log(
'MQTT Connected'
);

client.subscribe(

process.env.MQTT_TOPIC,

(err)=>{

if(err){

log(
`Subscribe Error:
${err.message}`
);

return;

}

log(
`Subscribed:
${process.env.MQTT_TOPIC}`
);

}

);

}

);


// =====================
// MQTT STATUS
// =====================

client.on(
'error',
(err)=>{
log(
`MQTT Error:
${err.message}`
);
}
);

client.on(
'close',
()=>{
log(
'MQTT Closed'
);
}
);

client.on(
'offline',
()=>{
log(
'MQTT Offline'
);
}
);

client.on(
'reconnect',
()=>{
log(
'MQTT Reconnecting...'
);
}
);


// =====================
// RECEIVE MQTT
// =====================

client.on(

'message',

async(
topic,
mqttMessage
)=>{

let conn;
let pendingAlerts = [];

try{

log(
`Topic:
${topic}`
);


// =====================
// JSON
// =====================

const data=
JSON.parse(
mqttMessage.toString()
);


log(
JSON.stringify(
data
)
);


// =====================
// VALIDATION
// =====================

if(
!data.mac_address
){

log(
'MAC missing'
);

return;

}


if(
!data.sensors
){

log(
'Sensors missing'
);

return;

}


const mac=
data.mac_address;


// =====================
// DB CONNECTION
// =====================

conn=
await db.getConnection();

await conn.beginTransaction();


// =====================
// DEVICE
// =====================

const
[deviceRows]=

await conn.execute(

`
SELECT
d.id,
sd.site_id,
s.name AS site_name,
u.name AS user_name,
u.phone_number

FROM devices d

JOIN site_devices sd
ON sd.device_id=d.id
AND sd.ended_at IS NULL

JOIN sites s
ON s.id=sd.site_id

JOIN users u
ON u.id=s.user_id

WHERE
d.mac_address=?

LIMIT 1
`,

[
mac
]

);


if(
deviceRows.length===0
){

log(
`Active device site not found:
${mac}`
);

await conn.rollback();

return;

}


const device=
deviceRows[0];


// =====================
// SENSOR LIST
// =====================

const
[sensorRows]=

await conn.execute(

`
SELECT

id,
name,
json_key,
unit,
min_threshold,
max_threshold

FROM sensors

WHERE
device_id=?

`,

[
device.id
]

);


// =====================
// LOOP SENSOR
// =====================

for(
let sensor
of sensorRows
){

const key=
sensor.json_key;


if(
!key
){

continue;

}


// =====================
// GET VALUE
// =====================

let value=
data.sensors[key];


if(
value===undefined ||
value===null
){

continue;

}


value=
Number(
value
);


if(
isNaN(
value
)
){

continue;

}


log(
`${key}
=> ${value}`
);


// =====================
// INSERT SENSOR
// =====================

await conn.execute(

`
INSERT INTO
data_sensors
(

sensor_id,
value,
created_at,
updated_at

)

VALUES
(
?,
?,
NOW(),
NOW()
)

`,

[
sensor.id,
value
]

);


log(
`${sensor.name}
saved`
);


// =====================
// THRESHOLD
// =====================

const min=
sensor.min_threshold;

const max=
sensor.max_threshold;

const minValue=
min===null
? null
: Number(
min
);

const maxValue=
max===null
? null
: Number(
max
);

const isBelowMin=
minValue!==null &&
!Number.isNaN(
minValue
) &&
value<minValue;

const isAboveMax=
maxValue!==null &&
!Number.isNaN(
maxValue
) &&
value>maxValue;


if(

isBelowMin ||
isAboveMax

){

const unit=
sensor.unit || '';

const notifMessage=

`${sensor.name}
abnormal
(${value}${unit})`;


log(
`Threshold alert queued:
${notifMessage}`
);

pendingAlerts.push({
    siteId:
    device.site_id,

    siteName:
    device.site_name,

    userName:
    device.user_name,

    phoneNumber:
    device.phone_number,

    sensorName:
    sensor.name,

    value:
    value,

    unit:
    unit,

    message:
    notifMessage
});

}

}

await conn.commit();

for(
const alert
of pendingAlerts
){

try{

const result =
await sendSensorAlert(
    alert
);

log(
`Notification sent:
${alert.message}`
);

if(
!result ||
result.created!==false
){

try{

await sendWhatsAppAlert(
alert
);

log(
`WhatsApp sent:
${alert.phoneNumber || '-'}`
);

}
catch(waErr){

log(
`WhatsApp API Error:
${waErr.message}`
);

}

}
else{

log(
`WhatsApp skipped for duplicate:
${alert.message}`
);

}

}
catch(alertErr){

log(
`Notification API Error:
${alertErr.message}`
);

}

}

}

catch(err){

if(conn){

try{

await conn.rollback();

}
catch(e){}

}

log(
`Runtime Error:
${err.message}`
);

}

finally{

if(conn){

conn.release();

}

}

}

);


// =====================
// START APP
// =====================

(async()=>{

log(
'=== APP STARTED ==='
);

await testDB();

})();


// =====================
// HEARTBEAT
// =====================

setInterval(

()=>{

log(
'Heartbeat alive'
);

},

300000

);


// =====================
// HTTP
// =====================

http.createServer(

(req,res)=>{

res.writeHead(

200,

{
'Content-Type':
'text/plain'
}

);

res.end(
'OK'
);

}

)

.listen(

process.env.PORT,

()=>{

log(

`HTTP Running:
${process.env.PORT}`

);

}

);


// =====================
// GLOBAL ERROR
// =====================

process.on(

'uncaughtException',

(err)=>{

log(

`Uncaught Error:
${err.message}`

);

}

);

process.on(

'unhandledRejection',

(err)=>{

log(

`Unhandled Promise:
${err}`

);

}

);
