const http = require("http");
const fs = require("fs");
const path = require("path");
const axios = require("axios");
const mqtt = require("mqtt");
const mysql = require("mysql2/promise");
const express = require("express");

require("dotenv").config({
    path: path.join(__dirname, ".env"),
});

function env(...names) {
    for (const name of names) {
        const value = process.env[name];

        if (value !== undefined && value !== "") {
            return value;
        }
    }

    return undefined;
}

function trimTrailingSlash(value) {
    return String(value).replace(/\/+$/, "");
}

function sensorAlertEndpoint() {
    const explicitUrl = env("SENSOR_ALERT_URL");

    if (explicitUrl) {
        return explicitUrl;
    }

    const apiUrl = env("API_URL");

    if (apiUrl) {
        return trimTrailingSlash(apiUrl).endsWith("/sensor-alert")
            ? apiUrl
            : `${trimTrailingSlash(apiUrl)}/sensor-alert`;
    }

    return `${trimTrailingSlash(env("APP_URL") || "http://127.0.0.1:8000")}/api/sensor-alert`;
}

function normalizePhoneNumber(phoneNumber) {
    if (!phoneNumber) {
        return null;
    }

    let phone = String(phoneNumber).replace(/\D/g, "");

    if (phone.startsWith("0")) {
        phone = `62${phone.slice(1)}`;
    } else if (phone.startsWith("8")) {
        phone = `62${phone}`;
    }

    return phone || null;
}

function whatsappMessage(alert) {
    return [
        "Smart Aquaponic Alert",
        `Site: ${alert.siteName || "-"}`,
        `Sensor: ${alert.sensorName || "-"}`,
        `Nilai: ${alert.value}${alert.unit || ""}`,
        `Status: ${alert.message}`,
    ].join("\n");
}

// =====================
// LOGGER
// =====================

function log(text) {
    const time = new Date().toISOString();

    const message = `[${time}] ${text}\n`;

    console.log(text);

    fs.appendFile(
        path.join(__dirname, "mqtt.log"),

        message,

        (err) => {
            if (err) {
                console.error("Log Error:", err);
            }
        },
    );
}

// =====================
// DATABASE
// =====================

const db = mysql.createPool({
    host: env("DB_HOST", "MYSQL_HOST"),

    port: Number(env("DB_PORT", "MYSQL_PORT") || 3306),

    user: env("DB_USERNAME", "DB_USER", "MYSQL_USER"),

    password: env("DB_PASSWORD", "MYSQL_PASSWORD"),

    database: env("DB_DATABASE", "DB_NAME", "MYSQL_DATABASE"),

    waitForConnections: true,

    connectionLimit: 10,

    queueLimit: 0,

    enableKeepAlive: true,

    keepAliveInitialDelay: 10000,
});

const sensorAlertUrl = sensorAlertEndpoint();

const sensorAlertSecret = env("SENSOR_ALERT_SECRET");

const whatsappToken = env("FONTE_TOKEN", "FONNTE_TOKEN");

const whatsappUrl =
    env("FONTE_URL", "FONNTE_URL") || "https://api.fonnte.com/send";

// =====================
// SENSOR ALERT API
// =====================

async function sendSensorAlert(alert) {
    if (!sensorAlertSecret) {
        log("Sensor alert secret missing");

        return {
            created: false,
        };
    }

    try {
        const payload = {
            site_id: alert.siteId,

            message: alert.message,

            type: "warning",
        };

        // =====================
        // LOG REQUEST
        // =====================

        log(
            `Sending Alert:
${JSON.stringify(payload)}`,
        );

        const response = await axios.post(
            sensorAlertUrl,

            payload,

            {
                headers: {
                    "X-Sensor-Alert-Secret": sensorAlertSecret,
                },

                timeout: 10000,
            },
        );

        // =====================
        // LOG RESPONSE
        // =====================

        log(
            `Sensor Alert Response:
${JSON.stringify(response.data)}`,
        );

        if (response.data?.created === true) {
            log(
                `Notification created:
${response.data.notification_id}`,
            );
        } else {
            log("Duplicate notification skipped");
        }

        return response.data;
    } catch (err) {
        log(
            `Sensor Alert Error:
${err.response?.data ? JSON.stringify(err.response.data) : err.message}`,
        );

        return {
            created: false,
        };
    }
}

// =====================
// WHATSAPP API
// =====================

async function sendWhatsAppAlert(alert) {
    if (!whatsappToken) {
        log("Fonnte token missing");

        return false;
    }

    const target = normalizePhoneNumber(alert.phoneNumber);

    if (!target) {
        log(
            `WhatsApp target missing:
            site_id=${alert.siteId}`,
        );

        return false;
    }

    try {
        const response = await axios.post(
            whatsappUrl,

            new URLSearchParams({
                target: target,

                message: whatsappMessage(alert),
            }),

            {
                headers: {
                    Authorization: whatsappToken,

                    "Content-Type": "application/x-www-form-urlencoded",
                },

                timeout: 10000,
            },
        );

        log(
            `Fonnte Response:
            ${JSON.stringify(response.data)}`,
        );

        if (response.data && response.data.status === true) {
            return true;
        }

        throw new Error(
            response.data?.reason ||
                response.data?.message ||
                "Unknown WhatsApp error",
        );
    } catch (err) {
        log(
            `WhatsApp API Error:
            ${err.message}`,
        );

        return false;
    }
}

// =====================
// TEST DB
// =====================

async function testDB() {
    try {
        const conn = await db.getConnection();

        log("Database Connected");

        conn.release();
    } catch (err) {
        log(`DB Error: ${err.message}`);
    }
}

// =====================
// MQTT
// =====================

const client = mqtt.connect(
    process.env.MQTT_URL,

    {
        username: process.env.MQTT_USERNAME,

        password: process.env.MQTT_PASSWORD,

        reconnectPeriod: 5000,

        connectTimeout: 30000,

        keepalive: 60,
    },
);

// =====================
// MQTT CONNECT
// =====================

client.on(
    "connect",

    () => {
        log("MQTT Connected");

        client.subscribe(
            [process.env.MQTT_TOPIC, "aquaponic/device/+/actuator"],

            (err) => {
                if (err) {
                    log(
                        `Subscribe Error:
${err.message}`,
                    );

                    return;
                }

                log(
                    `Subscribed to topics: ${process.env.MQTT_TOPIC} and aquaponic/device/+/actuator`,
                );
            },
        );
    },
);

// =====================
// MQTT STATUS
// =====================

client.on("error", (err) => {
    log(
        `MQTT Error:
${err.message}`,
    );
});

client.on("close", () => {
    log("MQTT Closed");
});

client.on("offline", () => {
    log("MQTT Offline");
});

client.on("reconnect", () => {
    log("MQTT Reconnecting...");
});

// =====================
// RECEIVE MQTT
// =====================

client.on(
    "message",

    async (topic, mqttMessage) => {
        let conn = null;
        let pendingAlerts = [];

        try {
            log(`Topic: ${topic}`);

            const data = JSON.parse(mqttMessage.toString());

            log(JSON.stringify(data));

            if (topic === process.env.MQTT_TOPIC) {
                // =====================
                // VALIDATION
                // =====================

                if (!data.mac_address) {
                    log("MAC missing");
                    return;
                }

                if (!data.sensors) {
                    log("Sensors missing");
                    return;
                }

                const mac = data.mac_address;

                // =====================
                // DB CONNECTION
                // =====================

                conn = await db.getConnection();

                await conn.beginTransaction();

                // =====================
                // DEVICE
                // =====================

                const [deviceRows] = await conn.execute(
                    `
SELECT

d.id,
sd.site_id,
s.name AS site_name,
u.name AS user_name,
u.phone_number,
u.whatsapp_notification,
u.email_notification

FROM devices d

JOIN site_devices sd
ON sd.device_id=d.id
AND sd.ended_at IS NULL

JOIN sites s
ON s.id=sd.site_id

JOIN users u
ON u.id=s.user_id

WHERE d.mac_address=?

LIMIT 1
`,

                    [mac],
                );

                if (deviceRows.length === 0) {
                    log(
                        `Device not found:
${mac}`,
                    );

                    await conn.rollback();

                    return;
                }

                const device = deviceRows[0];

                // =====================
                // GET SENSORS
                // =====================

                const [sensorRows] = await conn.execute(
                    `
SELECT

id,
name,
type,
unit,
min_threshold,
max_threshold

FROM sensors

WHERE device_id=?
`,
                    [device.id],
                );

                // =====================
                // NORMALIZE MQTT DATA
                // =====================

                const sensorPayload = {};

                for (const [key, value] of Object.entries(data.sensors)) {
                    sensorPayload[key.toLowerCase()] = Number(value);
                }

                // =====================
                // LOOP SENSOR
                // =====================

                for (const sensor of sensorRows) {
                    const sensorType = sensor.type.toLowerCase().trim();

                    let value = sensorPayload[sensorType];

                    if (value === undefined || value === null || isNaN(value)) {
                        continue;
                    }

                    log(
                        `${sensorType}
=> ${value}`,
                    );

                    // =====================
                    // SAVE SENSOR
                    // =====================

                    const createdAt = data.timestamp || new Date();
                    const updatedAt = data.timestamp || new Date();

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
?,
?
)
`,

                        [sensor.id, value, createdAt, updatedAt],
                    );

                    log(
                        `${sensor.name}
saved`,
                    );

                    // =====================
                    // THRESHOLD
                    // =====================

                    const min = sensor.min_threshold;

                    const max = sensor.max_threshold;

                    const isBelow = min !== null && value < Number(min);

                    const isAbove = max !== null && value > Number(max);

                    if (isBelow || isAbove) {
                        const unit = sensor.unit || "";

                        const message = `${sensor.name}
abnormal
(${value}${unit})`;

                        pendingAlerts.push({
                            siteId: device.site_id,

                            siteName: device.site_name,

                            userName: device.user_name,

                            phoneNumber: device.phone_number,

                            whatsappNotification: device.whatsapp_notification,

                            emailNotification: device.email_notification,

                            sensorName: sensor.name,

                            value: value,

                            unit: unit,

                            message: message,
                        });

                        log(
                            `Alert queued:
${message}`,
                        );
                    }
                }

                await conn.commit();

                // =====================
                // SEND NOTIFICATION
                // =====================

                for (const alert of pendingAlerts) {
                    try {
                        const result = await sendSensorAlert(alert);

                        if (result?.created === true) {
                            if (alert.whatsappNotification === 1 || alert.whatsappNotification === true) {
                                await sendWhatsAppAlert(alert);

                                log(
                                    `WhatsApp sent:
${alert.phoneNumber}`,
                                );
                            } else {
                                log(`WhatsApp notification disabled for user: ${alert.userName}`);
                            }
                        }
                    } catch (err) {
                        log(
                            `Alert Error:
${err.message}`,
                        );
                    }
                }
            } // close if (topic === process.env.MQTT_TOPIC)
            else if (
                topic.startsWith("aquaponic/device/") &&
                topic.endsWith("/actuator")
            ) {
                const parts = topic.split("/");
                const macAddress = parts[2];

                const type = data.type || data.actuator_type;
                const state = data.state || data.action;
                
                // =====================
                // SYSTEM MESSAGE
                // =====================
                
                const systemTypes = [
                    "PAIR",
                    "UNPAIR",
                    "RECONFIG",
                    "config_ack",
                    "CONFIG_ACK",
                ];
                
                if (
                    type &&
                    systemTypes.includes(type)
                ) {
                
                    log(
                        `[SYSTEM] ${type} | ${macAddress}`
                    );
                
                    return;
                
                }
                
                // =====================
                // CONFIG SNAPSHOT
                // =====================
                
                if (data.actuators) {
                
                    log(
                        `[CONFIG] ${macAddress}`
                    );
                
                    return;
                
                }


                if (!type || !state) {
                    log(
                        `Error: Payload aktuator tidak lengkap untuk device ${macAddress} (type=${type}, state=${state})`,
                    );
                } else {
                    let actConn = null;
                    try {
                        actConn = await db.getConnection();
                        await actConn.beginTransaction();

                        const [actuatorRows] = await actConn.execute(
                            `SELECT a.id, a.name 
                 FROM actuators a
                 JOIN devices d ON d.id = a.device_id
                 WHERE d.mac_address = ? AND a.type = ?
                 LIMIT 1`,
                            [macAddress, type],
                        );

                        if (actuatorRows.length === 0) {
                            log(
                                `Error: Aktuator dengan tipe ${type} tidak ditemukan untuk device ${macAddress}`,
                            );
                            await actConn.rollback();
                        } else {
                            const actuator = actuatorRows[0];

                            await actConn.execute(
                                `INSERT INTO actuator_logs (actuator_id, action, triggered_by, created_at, updated_at)
                     VALUES (?, ?, ?, NOW(), NOW())`,
                                [actuator.id, state.toLowerCase(), "auto"],
                            );

                            await actConn.commit();
                            log(
                                `[DB SUCCESS] Perubahan aktuator ${actuator.name} (${type}) menjadi ${state} berhasil disimpan (triggered_by: auto)`,
                            );
                        }
                    } catch (err) {
                        log(
                            `Error menyimpan log aktuator dari ESP32: ${err.message}`,
                        );
                        if (actConn) {
                            try {
                                await actConn.rollback();
                            } catch (e) {
                                log(`Rollback Error: ${e.message}`);
                            }
                        }
                    } finally {
                        if (actConn) {
                            try {
                                actConn.release();
                            } catch (e) {
                                log(`Release Error: ${e.message}`);
                            }
                        }
                    }
                }
            }
        } catch (err) {
            log(
                `Runtime Error:
${err.message}`,
            );

            if (conn) {
                try {
                    await conn.rollback();
                } catch (e) {
                    log(
                        `Rollback Error:
        ${e.message}`,
                    );
                }
            }
        } finally {
            if (conn) {
                try {
                    conn.release();
                } catch (e) {
                    log(
                        `Release Error:
        ${e.message}`,
                    );
                }

                conn = null;
            }
        }
    },
);

// =====================
// START APP
// =====================

(async () => {
    log("=== APP STARTED ===");

    await testDB();
})();

// =====================
// HEARTBEAT
// =====================

setInterval(
    () => {
        log("Heartbeat alive");
    },

    300000,
);

// =====================
// HTTP
// =====================

const app = express();
app.use(express.json());

const BASE_PATH =
process.env.BASE_PATH || "";

app.use((req, res, next) => {

    log(
        `HTTP ${req.method} ${req.originalUrl}`
    );

    next();

});

// Endpoint untuk mempublikasikan konfigurasi ke MQTT

app.get(`${BASE_PATH}/health`,(req, res) => {
        res.send("OK");
    }
);

app.post(`${BASE_PATH}/publish-config`, (req, res) => {
    const payload = req.body;
    const macAddress = payload.mac_address;

    log(
            "CONFIG ENDPOINT HIT"
        );

    if (!macAddress) {
        log("Error: Request /publish-config tidak memiliki mac_address");
        return res.status(400).json({ error: "mac_address is required" });
    }

    const topic = `aquaponic/device/${macAddress}/config`;
    const message = JSON.stringify(payload);

    client.publish(topic, message, { qos: 1, retain: true }, (err) => {
        if (err) {
            log(`Publish Error ke topik ${topic}: ${err.message}`);
            return res.status(500).json({ error: err.message });
        }

        log(`Berhasil publish konfigurasi ke MQTT topik ${topic}`);
        res.json({ success: true });
    });
});

// Endpoint untuk sinkronisasi daftar slave ke master
app.post(`${BASE_PATH}/publish-master-sync`, (req, res) => {
    const payload = req.body;
    const masterMac = payload.master_mac;
    const siteId = payload.site_id;
    const slaves = payload.slaves;
    
    log(
        "SYNC ENDPOINT HIT"
    );

    log(
        JSON.stringify(req.body)
    );

    if (!masterMac) {
        log("Error: Request /publish-master-sync tidak memiliki master_mac");
        return res.status(400).json({ error: "master_mac is required" });
    }

    const topic = `aquaponic/master/${masterMac}/sync`;
    const message = JSON.stringify({
        site_id: siteId,
        slaves: slaves,
    });

    client.publish(topic, message, { qos: 1, retain: true }, (err) => {
        if (err) {
            log(`Publish Error ke topik ${topic}: ${err.message}`);
            return res.status(500).json({ error: err.message });
        }

        log(`Berhasil publish sinkronisasi slave ke MQTT topik ${topic}`);
        res.json({ success: true });
    });
});



// Fallback untuk sembarang request (kompatibilitas lama)
app.use((req, res) => {
    res.send("OK");
});

const PORT = process.env.PORT || 5000;
app.listen(PORT, () => {
    log(`HTTP Running on port ${PORT}`);
});

// =====================
// GLOBAL ERROR
// =====================

process.on(
    "uncaughtException",

    (err) => {
        log(
            `Uncaught Error:
${err.message}`,
        );
    },
);

process.on(
    "unhandledRejection",

    (err) => {
        log(
            `Unhandled Promise:
${err}`,
        );
    },
);
