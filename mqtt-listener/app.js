require('dotenv').config();

const http=require('http');
const fs=require('fs');
const mqtt=require('mqtt');
const mysql=require('mysql2/promise');


// =====================
// LOGGER
// =====================

function log(text){

    const time=
    new Date().toISOString();

    const message=
    `[${time}] ${text}\n`;

    console.log(text);

    fs.appendFile(
        'mqtt.log',
        message,
        ()=>{}
    );

}


// =====================
// DATABASE
// =====================

const db=
mysql.createPool({

    host:
    process.env.DB_HOST,

    user:
    process.env.DB_USER,

    password:
    process.env.DB_PASSWORD,

    database:
    process.env.DB_NAME,

    waitForConnections:true,
    connectionLimit:10,
    queueLimit:0,

    enableKeepAlive:true,
    keepAliveInitialDelay:10000

});


// =====================
// DB TEST
// =====================

async function testDB(){

    try{

        const conn=
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

const client=
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
`Subscribe Error: ${err.message}`
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
message
)=>{

let conn;

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
message.toString()
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
id,
site_id

FROM devices

WHERE
mac_address=?

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
`Device not found:
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
// INSERT DATA
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


if(

min!==null &&
max!==null

){

if(

value<min ||
value>max

){

const unit=
sensor.unit || '';

const message=

`${sensor.name}
abnormal
(${value}${unit})`;


// =====================
// PREVENT DUPLICATE
// =====================

const
[exist]=

await conn.execute(

`
SELECT id
FROM notifications

WHERE

site_id=?

AND
message=?

AND

created_at >
DATE_SUB(
NOW(),
INTERVAL 5 MINUTE
)

LIMIT 1
`,

[
device.site_id,
message
]

);


if(
exist.length===0
){

await conn.execute(

`
INSERT INTO
notifications
(

site_id,
message,
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
device.site_id,
message
]

);


log(
'Notification saved'
);

}

}

}

}


await conn.commit();

}

catch(err){

if(
conn
)
{

await conn.rollback();

}

log(
`Runtime Error:
${err.message}`
);

}

finally{

if(
conn
)
{

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