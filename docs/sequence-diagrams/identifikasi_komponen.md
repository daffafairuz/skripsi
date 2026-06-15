# Identifikasi Komponen Boundary, Controller, dan Entity (BCE) per Use Case

Dokumen ini memetakan seluruh elemen objek yang ada pada 17 berkas *Sequence Diagram* (`uc_smaq_01` s.d `uc_smaq_17`) berdasarkan arsitektur **Robustness Analysis** (BCE) yang diimplementasikan dalam struktur MVC Laravel SmartAquaponic.

---

### [UC-SMAQ-01] Login dan Logout
*   **Boundary:**
    *   `login.blade.php` (Halaman masuk)
    *   `admin/dashboard.blade.php` (Dasbor Admin)
    *   `user/dashboard.blade.php` (Dasbor User)
*   **Controller:** `AuthController`
*   **Entity:** `User`

### [UC-SMAQ-02] Mengatur Identitas Akun
*   **Boundary:**
    *   `account_settings/index.blade.php` (Form profil akun)
    *   `login.blade.php` (Halaman login setelah deaktivasi)
*   **Controller:** `AccountController`
*   **Entity:** `User`

### [UC-SMAQ-03] Mendapatkan Notifikasi dari Sistem
*   **Boundary:** `notification.blade.php` (Halaman notifikasi)
*   **Controller:** `NotificationController`
*   **Entity:** `User`, `Notification`

### [UC-SMAQ-04] Melihat halaman dashboard admin
*   **Boundary:**
    *   `login.blade.php` (Halaman login)
    *   `admin/dashboard.blade.php` (Halaman utama admin)
*   **Controller:** `AuthController`, `DashboardController`
*   **Entity:** `User`

### [UC-SMAQ-05] Melihat halaman dashboard User
*   **Boundary:**
    *   `login.blade.php` (Halaman login)
    *   `user/dashboard.blade.php` (Halaman utama user)
*   **Controller:** `AuthController`, `DashboardController`
*   **Entity:** `User`, `Site`

---

### [UC-SMAQ-06] Mengelola data user (Admin)
*   **Boundary:** `admin/users/index.blade.php` (Daftar Pengguna, Modal Tambah & Edit secara inline)
*   **Controller:** `UserController`
*   **Entity:** `User`

### [UC-SMAQ-07] Mengelola data device (Admin)
*   **Boundary:**
    *   `admin/devices/index.blade.php` (Daftar Device, Modal Tambah & Edit secara inline)
    *   `sensors/device.blade.php` (Halaman Monitoring)
*   **Controller:** `DeviceController`, `SensorController`
*   **Entity:** `Device`, `Sensor`

### [UC-SMAQ-08] Menghubungkan dan melepaskan site dengan device oleh admin
*   **Boundary:** `admin/devices/index.blade.php`
*   **Controller:** `SiteDeviceController`, `SiteDeviceService`
*   **Entity:** `Site`, `Device`

### [UC-SMAQ-09] Menghubungkan dan melepaskan site dengan device oleh user
*   **Boundary:** `user/devices/index.blade.php`
*   **Controller:** `SiteDeviceController`, `SiteDeviceService`
*   **Entity:** `Site`, `Device`

### [UC-SMAQ-10] Mengelola data aktuator (Admin)
*   **Boundary:** `actuators/index.blade.php` (Daftar Aktuator)
*   **Controller:** `ActuatorController`
*   **Entity:** `Actuator`, `Device`

### [UC-SMAQ-11] Mengelola data sensor (Admin)
*   **Boundary:** `sensors/index.blade.php` (Daftar Sensor)
*   **Controller:** `SensorController`
*   **Entity:** `Sensor`, `Device`

### [UC-SMAQ-12] Mengelola data site (Admin)
*   **Boundary:** `admin/sites/index.blade.php` (Daftar Site, Modal Tambah & Edit secara inline)
*   **Controller:** `SiteController`
*   **Entity:** `Site`, `User`

---

### [UC-SMAQ-13] Melihat data log aktuator
*   **Boundary:** `data_monitoring/log_aktuator.blade.php` (Tampilan log & CSV Trigger)
*   **Controller:** `ActuatorLogController`
*   **Entity:** `User`, `ActuatorLog`

### [UC-SMAQ-14] Melihat Riwayat Data Sensor
*   **Boundary:** `data_monitoring/riwayat_data_sensor.blade.php` (Tampilan data & CSV Trigger)
*   **Controller:** `DataSensorController`
*   **Entity:** `User`, `DataSensor`

### [UC-SMAQ-15] Mengelola Jadwal Grow Light
*   **Boundary:**
    *   `jadwal_grow_light/index.blade.php` (Daftar Jadwal)
    *   `jadwal_grow_light/create.blade.php` (Halaman Tambah)
    *   `jadwal_grow_light/edit.blade.php` (Halaman Edit)
*   **Controller:** `GrowLightScheduleController`
*   **Entity:** `GrowLightSchedule`, `Actuator`

### [UC-SMAQ-16] Mengelola Jadwal Feeder
*   **Boundary:**
    *   `jadwal_pakan/index.blade.php` (Daftar Jadwal)
    *   `jadwal_pakan/create.blade.php` (Halaman Tambah)
    *   `jadwal_pakan/edit.blade.php` (Halaman Edit)
*   **Controller:** `FeedScheduleController`
*   **Entity:** `FeedSchedule`, `Actuator`

### [UC-SMAQ-17] Mengaktifkan dan Menonaktifkan Aktuator
*   **Boundary:** `actuator_control.blade.php` (Panel kendali saklar)
*   **Controller:** `ActuatorControlController`, `MqttService`
*   **Entity:** `Actuator`, `ActuatorLog`
