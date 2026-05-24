<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Site;
use App\Models\Device;
use App\Models\Actuator;
use App\Models\ActuatorLog;
use App\Models\Sensor;
use App\Models\DataSensor;
use App\Models\FeedSchedule;
use App\Models\GrowLightSchedule;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FullSystemTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $userA;
    private User $userB;
    private Site $siteA;
    private Site $siteB;
    private Device $deviceA;
    private Device $deviceB;
    private Actuator $pumpA;
    private Actuator $feederA;
    private Actuator $growLightA;
    private Actuator $pumpB;
    private Sensor $tempSensorA;
    private Sensor $phSensorA;
    private Sensor $tempSensorB;

    protected function setUp(): void
    {
        parent::setUp();

        // === Users ===
        $this->admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $this->userA = User::factory()->create(['role' => 'user', 'status' => 'active']);
        $this->userB = User::factory()->create(['role' => 'user', 'status' => 'active']);

        // === Sites ===
        $this->siteA = Site::create([
            'user_id' => $this->userA->id,
            'name' => 'Kebun Hidro Alpha',
            'location' => 'Jakarta',
            'mac_address' => 'AA:BB:CC:11:22:01',
        ]);
        $this->siteB = Site::create([
            'user_id' => $this->userB->id,
            'name' => 'Kebun Aqua Beta',
            'location' => 'Bandung',
            'mac_address' => 'AA:BB:CC:11:22:02',
        ]);

        // === Devices ===
        $this->deviceA = Device::create([
            'name' => 'ESP32-Slave-A',
            'mac_address' => '00:11:22:33:44:01',
            'status' => 'assigned',
        ]);
        $this->deviceB = Device::create([
            'name' => 'ESP32-Slave-B',
            'mac_address' => '00:11:22:33:44:02',
            'status' => 'assigned',
        ]);

        // Attach devices to sites (active)
        $this->siteA->devices()->attach($this->deviceA->id, [
            'started_at' => now(), 'created_at' => now(), 'updated_at' => now()
        ]);
        $this->siteB->devices()->attach($this->deviceB->id, [
            'started_at' => now(), 'created_at' => now(), 'updated_at' => now()
        ]);

        // === Actuators ===
        $this->pumpA = Actuator::create([
            'device_id' => $this->deviceA->id,
            'name' => 'Pompa Air A',
            'type' => 'waterpump',
            'default_state' => 'off',
        ]);
        $this->feederA = Actuator::create([
            'device_id' => $this->deviceA->id,
            'name' => 'Feeder A',
            'type' => 'feeder',
            'default_state' => 'off',
        ]);
        $this->growLightA = Actuator::create([
            'device_id' => $this->deviceA->id,
            'name' => 'Grow Light A',
            'type' => 'grow_light',
            'default_state' => 'off',
        ]);
        $this->pumpB = Actuator::create([
            'device_id' => $this->deviceB->id,
            'name' => 'Pompa Air B',
            'type' => 'waterpump',
            'default_state' => 'off',
        ]);

        // === Sensors ===
        $this->tempSensorA = Sensor::create([
            'device_id' => $this->deviceA->id,
            'name' => 'Suhu Udara A',
            'type' => 'temperature',
            'unit' => '°C',
            'min_threshold' => 20,
            'max_threshold' => 35,
        ]);
        $this->phSensorA = Sensor::create([
            'device_id' => $this->deviceA->id,
            'name' => 'pH Meter A',
            'type' => 'ph',
            'unit' => '',
        ]);
        $this->tempSensorB = Sensor::create([
            'device_id' => $this->deviceB->id,
            'name' => 'Suhu Udara B',
            'type' => 'temperature',
            'unit' => '°C',
        ]);
    }

    // =====================================================================
    // 1. AUTHENTICATION & AUTHORIZATION
    // =====================================================================

    public function test_guest_is_redirected_to_login()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_login_page_renders()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_user_can_login_with_valid_credentials()
    {
        $response = $this->post('/login', [
            'email' => $this->userA->email,
            'password' => 'password', // default factory password
        ]);
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->userA);
    }

    public function test_login_fails_with_invalid_credentials()
    {
        $response = $this->post('/login', [
            'email' => $this->userA->email,
            'password' => 'wrong-password',
        ]);
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_can_logout()
    {
        $response = $this->actingAs($this->userA)->post('/logout');
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_root_url_redirects_to_login()
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    // =====================================================================
    // 2. DASHBOARD (User & Admin)
    // =====================================================================

    public function test_user_dashboard_loads()
    {
        $response = $this->actingAs($this->userA)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewHas('hasSite', true);
        $response->assertViewHas('sites');
        $response->assertViewHas('userStats');
    }

    public function test_admin_dashboard_loads()
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewHas('adminStats');
        $response->assertViewHas('devices');
    }

    public function test_user_dashboard_shows_no_site_when_empty()
    {
        $noSiteUser = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($noSiteUser)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewHas('hasSite', false);
    }

    public function test_admin_dashboard_stats_are_correct()
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');
        $stats = $response->viewData('adminStats');

        $this->assertEquals(2, $stats['total_sites']);
        $this->assertEquals(2, $stats['total_users']); // userA + userB
        $this->assertEquals(2, $stats['total_devices']);
    }

    public function test_chart_data_endpoint_returns_json()
    {
        // Generate sensor data
        DataSensor::create(['sensor_id' => $this->tempSensorA->id, 'value' => 28.5]);

        $response = $this->actingAs($this->userA)->get('/chart-data');
        $response->assertStatus(200);
        $response->assertJsonStructure(['labels', 'datasets']);
    }

    // =====================================================================
    // 3. ADMIN RBAC - Admin-only routes
    // =====================================================================

    public function test_user_cannot_access_admin_user_management()
    {
        $response = $this->actingAs($this->userA)->get('/users');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_user_management()
    {
        $response = $this->actingAs($this->admin)->get('/users');
        $response->assertStatus(200);
    }

    public function test_user_cannot_access_admin_sensor_management()
    {
        $response = $this->actingAs($this->userA)->get('/sensors');
        $response->assertStatus(403);
    }

    public function test_user_cannot_access_admin_actuator_management()
    {
        $response = $this->actingAs($this->userA)->get('/actuators');
        $response->assertStatus(403);
    }

    public function test_admin_can_create_user()
    {
        $response = $this->actingAs($this->admin)->post('/users', [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'phone_number' => '081234567890',
            'role' => 'user',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', ['email' => 'testuser@example.com']);
    }

    public function test_admin_can_update_user()
    {
        $response = $this->actingAs($this->admin)->put("/users/{$this->userA->id}", [
            'name' => 'Updated Name',
            'email' => $this->userA->email,
            'role' => 'user',
            'status' => 'inactive',
        ]);

        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', ['id' => $this->userA->id, 'name' => 'Updated Name']);
    }

    public function test_admin_can_delete_user()
    {
        $victim = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($this->admin)->delete("/users/{$victim->id}");
        $response->assertRedirect('/users');
        $this->assertDatabaseMissing('users', ['id' => $victim->id]);
    }

    // =====================================================================
    // 4. SITES CRUD
    // =====================================================================

    public function test_admin_can_view_all_sites()
    {
        $response = $this->actingAs($this->admin)->get(route('sites.index'));
        $response->assertStatus(200);
        $response->assertSee('Kebun Hidro Alpha');
        $response->assertSee('Kebun Aqua Beta');
    }

    public function test_user_sees_only_own_site()
    {
        $response = $this->actingAs($this->userA)->get(route('sites.index'));
        $response->assertStatus(200);
        $response->assertSee('Kebun Hidro Alpha');
    }

    public function test_admin_can_create_site()
    {
        $response = $this->actingAs($this->admin)->post(route('sites.store'), [
            'user_id' => $this->userA->id,
            'name' => 'Site Baru',
            'location' => 'Surabaya',
            'mac_address' => 'AA:BB:CC:99:88:77',
        ]);

        $response->assertRedirect(route('sites.index'));
        $this->assertDatabaseHas('sites', ['name' => 'Site Baru']);
    }

    public function test_user_cannot_create_site_admin_only()
    {
        $response = $this->actingAs($this->userA)->get(route('sites.create'));
        $response->assertStatus(403);
    }

    // =====================================================================
    // 5. DEVICES
    // =====================================================================

    public function test_device_index_loads()
    {
        $response = $this->actingAs($this->admin)->get(route('devices.index'));
        $response->assertStatus(200);
    }

    // =====================================================================
    // 6. ACTUATOR LOG (Data Monitoring)
    // =====================================================================

    public function test_actuator_log_page_loads()
    {
        ActuatorLog::create([
            'actuator_id' => $this->pumpA->id,
            'action' => 'on',
            'triggered_by' => 'manual',
        ]);

        $response = $this->actingAs($this->userA)->get(route('actuator-log'));
        $response->assertStatus(200);
        $response->assertViewHas('logs');
        $response->assertViewHas('sites');
    }

    public function test_actuator_log_filters_by_site()
    {
        ActuatorLog::create([
            'actuator_id' => $this->pumpA->id,
            'action' => 'on',
            'triggered_by' => 'manual',
        ]);
        ActuatorLog::create([
            'actuator_id' => $this->pumpB->id,
            'action' => 'off',
            'triggered_by' => 'auto',
        ]);

        $response = $this->actingAs($this->admin)->get(route('actuator-log', [
            'site_id' => $this->siteA->id,
        ]));

        $response->assertStatus(200);
        $logs = $response->viewData('logs');
        foreach ($logs as $log) {
            $this->assertEquals($this->deviceA->id, $log->actuator->device_id);
        }
    }

    public function test_actuator_log_filters_by_device()
    {
        ActuatorLog::create([
            'actuator_id' => $this->pumpA->id,
            'action' => 'on',
            'triggered_by' => 'manual',
        ]);

        $response = $this->actingAs($this->userA)->get(route('actuator-log', [
            'device_id' => $this->deviceA->id,
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('selectedDeviceId', (string) $this->deviceA->id);
    }

    public function test_actuator_log_filters_by_tab()
    {
        ActuatorLog::create([
            'actuator_id' => $this->pumpA->id,
            'action' => 'on',
            'triggered_by' => 'manual',
        ]);

        $response = $this->actingAs($this->userA)->get(route('actuator-log', [
            'tab' => 'waterpump',
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('activeTab', 'waterpump');
    }

    public function test_actuator_log_empty_state()
    {
        $noSiteUser = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($noSiteUser)->get(route('actuator-log'));
        $response->assertStatus(200);
    }

    public function test_actuator_log_pagination()
    {
        // Create 15 logs
        for ($i = 0; $i < 15; $i++) {
            ActuatorLog::create([
                'actuator_id' => $this->pumpA->id,
                'action' => $i % 2 === 0 ? 'on' : 'off',
                'triggered_by' => 'manual',
            ]);
        }

        $response = $this->actingAs($this->userA)->get(route('actuator-log', ['per_page' => 10]));
        $response->assertStatus(200);
        $logs = $response->viewData('logs');
        $this->assertEquals(10, $logs->count());
        $this->assertEquals(15, $logs->total());
    }

    public function test_actuator_log_invalid_per_page_defaults_to_10()
    {
        ActuatorLog::create([
            'actuator_id' => $this->pumpA->id,
            'action' => 'on',
            'triggered_by' => 'manual',
        ]);

        $response = $this->actingAs($this->userA)->get(route('actuator-log', ['per_page' => 999]));
        $response->assertStatus(200);
        $response->assertViewHas('perPage', 10);
    }

    // =====================================================================
    // 7. DATA SENSOR (Data Monitoring)
    // =====================================================================

    public function test_data_sensor_page_loads()
    {
        DataSensor::create(['sensor_id' => $this->tempSensorA->id, 'value' => 28.5]);

        $response = $this->actingAs($this->userA)->get(route('data-sensor'));
        $response->assertStatus(200);
        $response->assertViewHas('pivotedData');
        $response->assertViewHas('activeSensorColumns');
    }

    public function test_data_sensor_filters_by_site()
    {
        DataSensor::create(['sensor_id' => $this->tempSensorA->id, 'value' => 28.5]);
        DataSensor::create(['sensor_id' => $this->tempSensorB->id, 'value' => 30.0]);

        $response = $this->actingAs($this->admin)->get(route('data-sensor', [
            'site_id' => $this->siteA->id,
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('selectedSiteId', (string) $this->siteA->id);
    }

    public function test_data_sensor_shows_active_sensor_columns_dynamically()
    {
        DataSensor::create(['sensor_id' => $this->tempSensorA->id, 'value' => 28.5]);
        DataSensor::create(['sensor_id' => $this->phSensorA->id, 'value' => 7.2]);

        $response = $this->actingAs($this->userA)->get(route('data-sensor', [
            'device_id' => $this->deviceA->id,
        ]));

        $cols = $response->viewData('activeSensorColumns');
        $this->assertArrayHasKey('temperature', $cols);
        $this->assertArrayHasKey('ph', $cols);
        $this->assertArrayNotHasKey('tds', $cols); // No TDS sensor on deviceA
    }

    public function test_data_sensor_empty_state()
    {
        $noSiteUser = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($noSiteUser)->get(route('data-sensor'));
        $response->assertStatus(200);
    }

    // =====================================================================
    // 8. CSV EXPORT - Actuator Log
    // =====================================================================

    public function test_actuator_log_csv_export_downloads()
    {
        ActuatorLog::create([
            'actuator_id' => $this->pumpA->id,
            'action' => 'on',
            'triggered_by' => 'manual',
        ]);

        $response = $this->actingAs($this->userA)->get(route('actuator-log.export-csv'));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('laporan_aktuator_log_', $response->headers->get('content-disposition'));
    }

    public function test_actuator_log_csv_contains_correct_headers()
    {
        ActuatorLog::create([
            'actuator_id' => $this->pumpA->id,
            'action' => 'on',
            'triggered_by' => 'manual',
        ]);

        $response = $this->actingAs($this->userA)->get(route('actuator-log.export-csv'));
        $content = $response->streamedContent();

        $this->assertStringContainsString('No', $content);
        $this->assertStringContainsString('Waktu', $content);
        $this->assertStringContainsString('Nama Aktuator', $content);
        $this->assertStringContainsString('Status', $content);
        $this->assertStringContainsString('Metode Kontrol', $content);
    }

    public function test_actuator_log_csv_contains_data()
    {
        ActuatorLog::create([
            'actuator_id' => $this->pumpA->id,
            'action' => 'on',
            'triggered_by' => 'manual',
        ]);

        $response = $this->actingAs($this->userA)->get(route('actuator-log.export-csv'));
        $content = $response->streamedContent();

        $this->assertStringContainsString('Pompa Air A', $content);
        $this->assertStringContainsString('ON', $content);
        $this->assertStringContainsString('Manual', $content);
    }

    public function test_actuator_log_csv_respects_tab_filter()
    {
        ActuatorLog::create([
            'actuator_id' => $this->pumpA->id,
            'action' => 'on',
            'triggered_by' => 'manual',
        ]);
        ActuatorLog::create([
            'actuator_id' => $this->feederA->id,
            'action' => 'on',
            'triggered_by' => 'auto',
        ]);

        $response = $this->actingAs($this->userA)->get(route('actuator-log.export-csv', [
            'tab' => 'waterpump',
        ]));
        $content = $response->streamedContent();

        $this->assertStringContainsString('Pompa Air A', $content);
        $this->assertStringNotContainsString('Feeder A', $content);
    }

    public function test_actuator_log_csv_respects_site_filter()
    {
        ActuatorLog::create([
            'actuator_id' => $this->pumpA->id,
            'action' => 'on',
            'triggered_by' => 'manual',
        ]);
        ActuatorLog::create([
            'actuator_id' => $this->pumpB->id,
            'action' => 'off',
            'triggered_by' => 'auto',
        ]);

        $response = $this->actingAs($this->admin)->get(route('actuator-log.export-csv', [
            'site_id' => $this->siteA->id,
        ]));
        $content = $response->streamedContent();

        $this->assertStringContainsString('Pompa Air A', $content);
        $this->assertStringNotContainsString('Pompa Air B', $content);
    }

    public function test_actuator_log_csv_empty_for_user_with_no_sites()
    {
        $noSiteUser = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($noSiteUser)->get(route('actuator-log.export-csv'));
        $response->assertStatus(200);
    }

    // =====================================================================
    // 9. CSV EXPORT - Data Sensor
    // =====================================================================

    public function test_data_sensor_csv_export_downloads()
    {
        DataSensor::create(['sensor_id' => $this->tempSensorA->id, 'value' => 28.5]);

        $response = $this->actingAs($this->userA)->get(route('data-sensor.export-csv'));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('laporan_data_sensor_', $response->headers->get('content-disposition'));
    }

    public function test_data_sensor_csv_contains_correct_headers()
    {
        DataSensor::create(['sensor_id' => $this->tempSensorA->id, 'value' => 28.5]);

        $response = $this->actingAs($this->userA)->get(route('data-sensor.export-csv'));
        $content = $response->streamedContent();

        $this->assertStringContainsString('No', $content);
        $this->assertStringContainsString('Waktu', $content);
        $this->assertStringContainsString('Device (Slave)', $content);
        $this->assertStringContainsString('Site (Master)', $content);
    }

    public function test_data_sensor_csv_contains_data()
    {
        DataSensor::create(['sensor_id' => $this->tempSensorA->id, 'value' => 28.5]);

        $response = $this->actingAs($this->userA)->get(route('data-sensor.export-csv'));
        $content = $response->streamedContent();

        $this->assertStringContainsString('ESP32-Slave-A', $content);
        $this->assertStringContainsString('28.5', $content);
    }

    public function test_data_sensor_csv_respects_site_filter()
    {
        DataSensor::create(['sensor_id' => $this->tempSensorA->id, 'value' => 28.5]);
        DataSensor::create(['sensor_id' => $this->tempSensorB->id, 'value' => 30.0]);

        $response = $this->actingAs($this->admin)->get(route('data-sensor.export-csv', [
            'site_id' => $this->siteA->id,
        ]));
        $content = $response->streamedContent();

        $this->assertStringContainsString('ESP32-Slave-A', $content);
        $this->assertStringNotContainsString('ESP32-Slave-B', $content);
    }

    public function test_data_sensor_csv_empty_for_user_with_no_sites()
    {
        $noSiteUser = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($noSiteUser)->get(route('data-sensor.export-csv'));
        $response->assertStatus(200);
    }

    // =====================================================================
    // 10. ACTUATOR CONTROL
    // =====================================================================

    public function test_actuator_control_page_loads()
    {
        $response = $this->actingAs($this->userA)->get(route('actuator-control'));
        $response->assertStatus(200);
        $response->assertViewHas('actuators');
    }

    public function test_user_can_toggle_own_actuator()
    {
        $response = $this->actingAs($this->userA)->post(route('actuator-control.toggle', $this->pumpA->id));
        $response->assertRedirect();

        $this->assertDatabaseHas('actuator_logs', [
            'actuator_id' => $this->pumpA->id,
            'action' => 'on', // default was off, so first toggle => on
            'triggered_by' => 'manual',
        ]);
    }

    public function test_user_cannot_toggle_other_users_actuator()
    {
        $response = $this->actingAs($this->userA)->post(route('actuator-control.toggle', $this->pumpB->id));
        $response->assertStatus(404); // findOrFail returns 404 for non-owned
    }

    public function test_admin_can_toggle_any_actuator()
    {
        $response = $this->actingAs($this->admin)->post(route('actuator-control.toggle', $this->pumpB->id));
        $response->assertRedirect();

        $this->assertDatabaseHas('actuator_logs', [
            'actuator_id' => $this->pumpB->id,
            'action' => 'on',
        ]);
    }

    public function test_double_toggle_returns_to_off()
    {
        // First toggle: off -> on
        $this->actingAs($this->userA)->post(route('actuator-control.toggle', $this->pumpA->id));

        // Verify first toggle was ON
        $firstLog = ActuatorLog::where('actuator_id', $this->pumpA->id)->latest('id')->first();
        $this->assertEquals('on', $firstLog->action);

        // Second toggle: on -> off
        $this->actingAs($this->userA)->post(route('actuator-control.toggle', $this->pumpA->id));

        $lastLog = ActuatorLog::where('actuator_id', $this->pumpA->id)->latest('id')->first();
        $this->assertEquals('off', $lastLog->action);
    }

    // =====================================================================
    // 11. FEED SCHEDULE
    // =====================================================================

    public function test_feed_schedule_index_loads()
    {
        $response = $this->actingAs($this->userA)->get(route('jadwal-pakan.index'));
        $response->assertStatus(200);
    }

    public function test_user_can_create_feed_schedule()
    {
        $response = $this->actingAs($this->userA)->post(route('jadwal-pakan.store'), [
            'actuator_id' => $this->feederA->id,
            'time' => '08:00',
            'duration' => 10,
        ]);

        $response->assertRedirect(route('jadwal-pakan.index'));
        $this->assertDatabaseHas('feed_schedules', [
            'actuator_id' => $this->feederA->id,
            'time' => '08:00:00',
            'duration' => 10,
        ]);
    }

    public function test_feed_schedule_rejects_overlap()
    {
        FeedSchedule::create([
            'actuator_id' => $this->feederA->id,
            'time' => '08:00:00',
            'duration' => 10,
        ]);

        $response = $this->actingAs($this->userA)->post(route('jadwal-pakan.store'), [
            'actuator_id' => $this->feederA->id,
            'time' => '08:05',
            'duration' => 10,
        ]);

        $response->assertSessionHasErrors('time');
    }

    public function test_feed_schedule_allows_non_overlap()
    {
        FeedSchedule::create([
            'actuator_id' => $this->feederA->id,
            'time' => '08:00:00',
            'duration' => 10,
        ]);

        $response = $this->actingAs($this->userA)->post(route('jadwal-pakan.store'), [
            'actuator_id' => $this->feederA->id,
            'time' => '08:10',
            'duration' => 5,
        ]);

        $response->assertSessionMissing('errors');
    }

    public function test_feed_schedule_update()
    {
        $schedule = FeedSchedule::create([
            'actuator_id' => $this->feederA->id,
            'time' => '08:00:00',
            'duration' => 10,
        ]);

        $response = $this->actingAs($this->userA)->put(route('jadwal-pakan.update', $schedule->id), [
            'actuator_id' => $this->feederA->id,
            'time' => '09:00',
            'duration' => 15,
        ]);

        $response->assertRedirect(route('jadwal-pakan.index'));
        $this->assertDatabaseHas('feed_schedules', [
            'id' => $schedule->id,
            'time' => '09:00:00',
            'duration' => 15,
        ]);
    }

    public function test_feed_schedule_delete()
    {
        $schedule = FeedSchedule::create([
            'actuator_id' => $this->feederA->id,
            'time' => '08:00:00',
            'duration' => 10,
        ]);

        $response = $this->actingAs($this->userA)->delete(route('jadwal-pakan.destroy', $schedule->id));
        $response->assertRedirect(route('jadwal-pakan.index'));
        $this->assertDatabaseMissing('feed_schedules', ['id' => $schedule->id]);
    }

    public function test_feed_schedule_validation_required_fields()
    {
        $response = $this->actingAs($this->userA)->post(route('jadwal-pakan.store'), []);

        $response->assertSessionHasErrors(['actuator_id', 'time', 'duration']);
    }

    public function test_feed_schedule_user_cannot_access_other_users_feeder()
    {
        // UserB's device doesn't have a feeder. Create one for testing.
        $feederB = Actuator::create([
            'device_id' => $this->deviceB->id,
            'name' => 'Feeder B',
            'type' => 'feeder',
            'default_state' => 'off',
        ]);

        $response = $this->actingAs($this->userA)->post(route('jadwal-pakan.store'), [
            'actuator_id' => $feederB->id,
            'time' => '10:00',
            'duration' => 5,
        ]);

        $response->assertSessionHasErrors('actuator_id');
    }

    // =====================================================================
    // 12. GROW LIGHT SCHEDULE
    // =====================================================================

    public function test_grow_light_schedule_index_loads()
    {
        $response = $this->actingAs($this->userA)->get(route('growlight.schedule'));
        $response->assertStatus(200);
    }

    public function test_user_can_create_grow_light_schedule()
    {
        $response = $this->actingAs($this->userA)->post(route('growlight.store'), [
            'actuator_id' => $this->growLightA->id,
            'start_time' => '06:00',
            'end_time' => '18:00',
        ]);

        $response->assertRedirect(route('growlight.schedule'));
        $this->assertDatabaseHas('grow_light_schedules', [
            'actuator_id' => $this->growLightA->id,
        ]);
    }

    public function test_grow_light_schedule_rejects_overlap()
    {
        GrowLightSchedule::create([
            'actuator_id' => $this->growLightA->id,
            'start_time' => '08:00:00',
            'end_time' => '12:00:00',
        ]);

        $response = $this->actingAs($this->userA)->post(route('growlight.store'), [
            'actuator_id' => $this->growLightA->id,
            'start_time' => '10:00',
            'end_time' => '14:00',
        ]);

        $response->assertSessionHasErrors('start_time');
    }

    public function test_grow_light_schedule_allows_non_overlap()
    {
        GrowLightSchedule::create([
            'actuator_id' => $this->growLightA->id,
            'start_time' => '08:00:00',
            'end_time' => '12:00:00',
        ]);

        $response = $this->actingAs($this->userA)->post(route('growlight.store'), [
            'actuator_id' => $this->growLightA->id,
            'start_time' => '13:00',
            'end_time' => '14:00',
        ]);

        $response->assertSessionMissing('errors');
    }

    public function test_grow_light_rejects_end_before_start()
    {
        $response = $this->actingAs($this->userA)->post(route('growlight.store'), [
            'actuator_id' => $this->growLightA->id,
            'start_time' => '18:00',
            'end_time' => '06:00',
        ]);

        $response->assertSessionHasErrors('end_time');
    }

    public function test_grow_light_schedule_delete()
    {
        $schedule = GrowLightSchedule::create([
            'actuator_id' => $this->growLightA->id,
            'start_time' => '08:00:00',
            'end_time' => '12:00:00',
        ]);

        $response = $this->actingAs($this->userA)->delete(route('growlight.destroy', $schedule->id));
        $response->assertRedirect(route('growlight.schedule'));
        $this->assertDatabaseMissing('grow_light_schedules', ['id' => $schedule->id]);
    }

    // =====================================================================
    // 13. DATA ISOLATION (Multi-user security)
    // =====================================================================

    public function test_user_a_cannot_see_user_b_actuator_logs()
    {
        // Create log for userB's device
        ActuatorLog::create([
            'actuator_id' => $this->pumpB->id,
            'action' => 'on',
            'triggered_by' => 'manual',
        ]);

        $response = $this->actingAs($this->userA)->get(route('actuator-log'));
        $response->assertStatus(200);

        $logs = $response->viewData('logs');
        foreach ($logs as $log) {
            $this->assertNotEquals($this->pumpB->id, $log->actuator_id);
        }
    }

    public function test_user_a_cannot_see_user_b_sensor_data()
    {
        DataSensor::create(['sensor_id' => $this->tempSensorB->id, 'value' => 30.0]);

        $response = $this->actingAs($this->userA)->get(route('data-sensor'));
        $response->assertStatus(200);

        $pivoted = $response->viewData('pivotedData');
        foreach ($pivoted as $row) {
            $this->assertNotEquals('ESP32-Slave-B', $row['device_name']);
        }
    }

    public function test_admin_can_see_all_actuator_logs()
    {
        ActuatorLog::create([
            'actuator_id' => $this->pumpA->id,
            'action' => 'on',
            'triggered_by' => 'manual',
        ]);
        ActuatorLog::create([
            'actuator_id' => $this->pumpB->id,
            'action' => 'off',
            'triggered_by' => 'auto',
        ]);

        $response = $this->actingAs($this->admin)->get(route('actuator-log'));
        $response->assertStatus(200);

        $logs = $response->viewData('logs');
        $this->assertGreaterThanOrEqual(2, $logs->total());
    }

    public function test_admin_csv_export_includes_all_data()
    {
        ActuatorLog::create([
            'actuator_id' => $this->pumpA->id,
            'action' => 'on',
            'triggered_by' => 'manual',
        ]);
        ActuatorLog::create([
            'actuator_id' => $this->pumpB->id,
            'action' => 'off',
            'triggered_by' => 'auto',
        ]);

        $response = $this->actingAs($this->admin)->get(route('actuator-log.export-csv'));
        $content = $response->streamedContent();

        $this->assertStringContainsString('Pompa Air A', $content);
        $this->assertStringContainsString('Pompa Air B', $content);
    }

    public function test_user_csv_export_only_includes_own_data()
    {
        ActuatorLog::create([
            'actuator_id' => $this->pumpA->id,
            'action' => 'on',
            'triggered_by' => 'manual',
        ]);
        ActuatorLog::create([
            'actuator_id' => $this->pumpB->id,
            'action' => 'off',
            'triggered_by' => 'auto',
        ]);

        $response = $this->actingAs($this->userA)->get(route('actuator-log.export-csv'));
        $content = $response->streamedContent();

        $this->assertStringContainsString('Pompa Air A', $content);
        $this->assertStringNotContainsString('Pompa Air B', $content);
    }

    // =====================================================================
    // 14. NOTIFICATIONS
    // =====================================================================

    public function test_notification_page_loads()
    {
        Notification::create([
            'site_id' => $this->siteA->id,
            'message' => 'Suhu melebihi batas',
            'type' => 'warning',
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->userA)->get(route('notifications'));
        $response->assertStatus(200);
    }

    // =====================================================================
    // 15. ACCOUNT SETTINGS
    // =====================================================================

    public function test_account_settings_page_loads()
    {
        $response = $this->actingAs($this->userA)->get(route('account-setting'));
        $response->assertStatus(200);
    }

    // =====================================================================
    // 16. EDGE CASES & ROBUSTNESS
    // =====================================================================

    public function test_invalid_site_id_filter_is_ignored()
    {
        $response = $this->actingAs($this->userA)->get(route('actuator-log', [
            'site_id' => 99999,
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('selectedSiteId', null);
    }

    public function test_invalid_device_id_filter_is_ignored()
    {
        $response = $this->actingAs($this->userA)->get(route('data-sensor', [
            'device_id' => 99999,
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('selectedDeviceId', null);
    }

    public function test_user_accessing_other_users_site_filter_is_ignored()
    {
        // UserA tries to filter by UserB's site
        $response = $this->actingAs($this->userA)->get(route('actuator-log', [
            'site_id' => $this->siteB->id,
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('selectedSiteId', null);
    }

    public function test_feed_schedule_duration_boundary_min()
    {
        $response = $this->actingAs($this->userA)->post(route('jadwal-pakan.store'), [
            'actuator_id' => $this->feederA->id,
            'time' => '10:00',
            'duration' => 0, // less than min
        ]);

        $response->assertSessionHasErrors('duration');
    }

    public function test_feed_schedule_duration_boundary_max()
    {
        $response = $this->actingAs($this->userA)->post(route('jadwal-pakan.store'), [
            'actuator_id' => $this->feederA->id,
            'time' => '10:00',
            'duration' => 61, // more than max
        ]);

        $response->assertSessionHasErrors('duration');
    }

    public function test_actuator_toggle_nonexistent_actuator()
    {
        $response = $this->actingAs($this->admin)->post(route('actuator-control.toggle', 99999));
        $response->assertStatus(404);
    }

    public function test_guest_cannot_access_csv_export()
    {
        $response = $this->get(route('actuator-log.export-csv'));
        $response->assertRedirect('/login');

        $response2 = $this->get(route('data-sensor.export-csv'));
        $response2->assertRedirect('/login');
    }

    public function test_guest_cannot_access_actuator_control()
    {
        $response = $this->get(route('actuator-control'));
        $response->assertRedirect('/login');
    }

    public function test_feed_schedule_wrong_actuator_type()
    {
        // Try to create feed schedule with a waterpump (should fail)
        $response = $this->actingAs($this->userA)->post(route('jadwal-pakan.store'), [
            'actuator_id' => $this->pumpA->id, // waterpump, not feeder
            'time' => '10:00',
            'duration' => 5,
        ]);

        $response->assertSessionHasErrors('actuator_id');
    }

    public function test_grow_light_wrong_actuator_type()
    {
        // Try to create grow light schedule with a waterpump (should fail)
        $response = $this->actingAs($this->userA)->post(route('growlight.store'), [
            'actuator_id' => $this->pumpA->id,
            'start_time' => '06:00',
            'end_time' => '18:00',
        ]);

        $response->assertSessionHasErrors('actuator_id');
    }
}
