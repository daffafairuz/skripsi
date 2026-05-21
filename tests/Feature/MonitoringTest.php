<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Site;
use App\Models\Device;
use App\Models\Actuator;
use App\Models\ActuatorLog;
use App\Models\Sensor;
use App\Models\DataSensor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonitoringTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $site1;
    private $site2;
    private $device1;
    private $device2;
    private $actuator1;
    private $sensor1;

    protected function setUp(): void
    {
        parent::setUp();

        // Create user
        $this->user = User::factory()->create([
            'role' => 'user'
        ]);

        // Create site 1
        $this->site1 = Site::create([
            'user_id' => $this->user->id,
            'name' => 'Site Satu',
            'location' => 'Jakarta',
            'mac_address' => 'AA:BB:CC:11:22:11',
        ]);

        // Create site 2
        $this->site2 = Site::create([
            'user_id' => $this->user->id,
            'name' => 'Site Dua',
            'location' => 'Bandung',
            'mac_address' => 'AA:BB:CC:11:22:22',
        ]);

        // Create device 1
        $this->device1 = Device::create([
            'name' => 'Slave Alpha',
            'mac_address' => '00:11:22:33:44:01',
            'status' => 'assigned'
        ]);

        // Create device 2
        $this->device2 = Device::create([
            'name' => 'Slave Beta',
            'mac_address' => '00:11:22:33:44:02',
            'status' => 'assigned'
        ]);

        // Attach devices to sites
        $this->site1->devices()->attach($this->device1->id, [
            'started_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $this->site2->devices()->attach($this->device2->id, [
            'started_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Create actuators
        $this->actuator1 = Actuator::create([
            'device_id' => $this->device1->id,
            'name' => 'Pompa Air Alpha',
            'type' => 'waterpump',
            'default_state' => 'off',
        ]);

        // Create sensors
        $this->sensor1 = Sensor::create([
            'device_id' => $this->device1->id,
            'name' => 'Suhu Udara',
            'type' => 'temperature',
            'unit' => '°C',
        ]);
    }

    public function test_actuator_log_page_loads_with_filters_and_pagination()
    {
        // Generate logs
        ActuatorLog::create([
            'actuator_id' => $this->actuator1->id,
            'action' => 'on',
            'triggered_by' => 'manual',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('actuator-log', [
                'site_id' => $this->site1->id,
                'device_id' => $this->device1->id,
                'per_page' => 10,
            ]));

        $response->assertStatus(200);
        $response->assertViewHas('sites');
        $response->assertViewHas('devices');
        $response->assertViewHas('selectedSiteId', $this->site1->id);
        $response->assertViewHas('selectedDeviceId', $this->device1->id);
        $response->assertViewHas('availableTabs');
        
        // Assert only 'waterpump' is present in availableTabs (not growlight or others)
        $tabs = $response->viewData('availableTabs');
        $this->assertArrayHasKey('waterpump', $tabs);
        $this->assertArrayNotHasKey('growlight', $tabs);
    }

    public function test_sensor_history_page_loads_with_filters_and_pagination()
    {
        // Generate sensor data
        DataSensor::create([
            'sensor_id' => $this->sensor1->id,
            'value' => 28.5,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('data-sensor', [
                'site_id' => $this->site1->id,
                'device_id' => $this->device1->id,
                'per_page' => 10,
            ]));

        $response->assertStatus(200);
        $response->assertViewHas('sites');
        $response->assertViewHas('devices');
        $response->assertViewHas('selectedSiteId', $this->site1->id);
        $response->assertViewHas('selectedDeviceId', $this->device1->id);
        $response->assertViewHas('activeSensorColumns');

        // Assert only 'temperature' column is active
        $cols = $response->viewData('activeSensorColumns');
        $this->assertArrayHasKey('temperature', $cols);
        $this->assertArrayNotHasKey('humidity', $cols);
    }
}
