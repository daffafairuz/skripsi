const mqtt = require('mqtt');
const mysql = require('mysql2');
const axios = require('axios');


// ======================
// DATABASE
// ======================

const db = mysql.createConnection({

    host:'localhost',
    user:'root',
    password:'',
    database:'skripsi'

});


db.connect((err)=>{

    if(err){

        console.log(
            'DB Error:',
            err
        );

        return;
    }

    console.log(
        'Database Connected'
    );

});


// ======================
// MQTT
// ======================

const client = mqtt.connect(

'mqtts://d1d3bb1af2324fe8893ca5b4d8ff18c3.s1.eu.hivemq.cloud:8883',

{
    username:'aquaponic',
    password:'Aquaponic1'
}

);


client.on(
'connect',
()=>{

    console.log(
        'MQTT Connected'
    );

    client.subscribe(
        'aquaponic/device/data'
    );

});



// ======================
// RECEIVE MQTT
// ======================

client.on(
'message',
async(topic,message)=>{

try{

let data=
JSON.parse(
message.toString()
);

console.log(data);

let mac=
data.mac_address;


// cari device
db.query(

`
SELECT
id
FROM devices
WHERE mac_address=?
LIMIT 1
`,

[mac],

(err,deviceRows)=>{

if(err){

console.log(
err
);

return;

}


if(
deviceRows.length===0
){

console.log(
'Device not found'
);

return;

}


let deviceId=
deviceRows[0].id;


// ambil semua sensor device
db.query(

`
SELECT
id,
name,
type,
min_threshold,
max_threshold
FROM sensors
WHERE device_id=?
`,

[deviceId],

async(
err,
sensorRows
)=>{


if(err){

console.log(
err
);

return;

}


for(
let sensor
of sensorRows
){

let value=null;

let type=
sensor.type
.toLowerCase()
.trim();



// mapping sensor payload
switch(type){

case 'temperature':

value=
data.sensors.temperature;

break;


case 'humidity':

value=
data.sensors.humidity;

break;


case 'ph':

value=
data.sensors.ph;

break;


case 'tds':

value=
data.sensors.tds;

break;


case 'water_level':

value=
data.sensors.water_level;

break;

}


// skip jika payload tidak ada
if(
value===undefined ||
value===null
){

console.log(
`No value for ${type}`
);

continue;

}


// ==================
// INSERT SENSOR DATA
// ==================

db.query(

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
],

(err)=>{

if(err){

console.log(
'Insert Error:',
err
);

}
else{

console.log(
`${type}: ${value} saved`
);

}

}

);


// ==================
// THRESHOLD CHECK
// ==================

if(

value<
sensor.min_threshold ||

value>
sensor.max_threshold

){

try{

await axios.post(

'http://127.0.0.1:8000/api/sensor-alert',

{

site_id:1,

message:
`${sensor.name} abnormal (${value}${sensor.unit})`

}

);

console.log(
'Notification sent'
);

}
catch(err){

console.log(
'API Error:',
err.message
);

}

}

}

});

});

}
catch(error){

console.log(
'JSON Error:',
error
);

}

});