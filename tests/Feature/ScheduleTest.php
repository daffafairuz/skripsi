<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Site;
use App\Models\Device;
use App\Models\Actuator;
use App\Models\FeedSchedule;
use App\Models\GrowLightSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $admin;
    private $site;
    private $device;
    private $feederActuator;
    private $growLightActuator;

    protected function setUp(): void
    {
        parent::setUp();

        // Create user
        $this->user = User::factory()->create([
            'role' => 'user'
        ]);

        // Create admin
        $this->admin = User::factory()->create([
            'role' => 'admin'
        ]);

        // Create site
        $this->site = Site::create([
            'user_id' => $this->user->id,
            'name' => 'Kebun Budi',
            'location' => 'Depok',
            'mac_address' => 'AA:BB:CC:11:22:01',
        ]);

        // Create device
        $this->device = Device::create([
            'name' => 'Device 1',
            'mac_address' => '00:11:22:33:44:55',
            'status' => 'assigned'
        ]);

        // Connect device to site
        $this->site->devices()->attach($this->device->id, [
            'started_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Create actuators
        $this->feederActuator = Actuator::create([
            'device_id' => $this->device->id,
            'name' => 'My Feeder',
            'type' => 'feeder',
            'default_state' => 'off',
            'state' => 'off',
        ]);

        $this->growLightActuator = Actuator::create([
            'device_id' => $this->device->id,
            'name' => 'My Grow Light',
            'type' => 'grow_light',
            'default_state' => 'off',
            'state' => 'off',
        ]);
    }

    public function test_user_can_access_own_feed_schedules()
    {
        FeedSchedule::create([
            'actuator_id' => $this->feederActuator->id,
            'time' => '08:00:00',
            'duration' => 10
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('jadwal-pakan.index'));

        $response->assertStatus(200);
        $response->assertSee('My Feeder');
    }

    public function test_user_can_create_feed_schedule_for_owned_feeder()
    {
        $response = $this->actingAs($this->user)
            ->post(route('jadwal-pakan.store'), [
                'actuator_id' => $this->feederActuator->id,
                'time' => '10:00',
                'duration' => 5
            ]);

        $response->assertRedirect(route('jadwal-pakan.index'));
        $this->assertDatabaseHas('feed_schedules', [
            'actuator_id' => $this->feederActuator->id,
            'time' => '10:00:00',
            'duration' => 5
        ]);
    }

    public function test_user_cannot_create_feed_schedule_for_unowned_feeder()
    {
        $otherUser = User::factory()->create();
        $otherSite = Site::create([
            'user_id' => $otherUser->id,
            'name' => 'Other Kebun',
            'location' => 'Bogor',
            'mac_address' => 'AA:BB:CC:11:22:99',
        ]);
        $otherDevice = Device::create([
            'name' => 'Other Device',
            'mac_address' => '00:11:22:33:44:99',
            'status' => 'assigned'
        ]);
        $otherSite->devices()->attach($otherDevice->id, [
            'started_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $otherFeeder = Actuator::create([
            'device_id' => $otherDevice->id,
            'name' => 'Other Feeder',
            'type' => 'feeder',
            'default_state' => 'off',
            'state' => 'off',
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('jadwal-pakan.store'), [
                'actuator_id' => $otherFeeder->id,
                'time' => '10:00',
                'duration' => 5
            ]);

        $response->assertSessionHasErrors(['actuator_id']);
        $this->assertDatabaseMissing('feed_schedules', [
            'actuator_id' => $otherFeeder->id
        ]);
    }

    public function test_user_cannot_duplicate_feed_schedule_time_for_same_feeder()
    {
        FeedSchedule::create([
            'actuator_id' => $this->feederActuator->id,
            'time' => '10:00:00',
            'duration' => 5
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('jadwal-pakan.store'), [
                'actuator_id' => $this->feederActuator->id,
                'time' => '10:00',
                'duration' => 15
            ]);

        $response->assertSessionHasErrors(['time']);
    }

    public function test_user_can_access_own_grow_light_schedules()
    {
        GrowLightSchedule::create([
            'actuator_id' => $this->growLightActuator->id,
            'start_time' => '06:00:00',
            'end_time' => '18:00:00'
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('growlight.schedule'));

        $response->assertStatus(200);
        $response->assertSee('My Grow Light');
    }

    public function test_user_cannot_create_overlapping_grow_light_schedules()
    {
        GrowLightSchedule::create([
            'actuator_id' => $this->growLightActuator->id,
            'start_time' => '08:00:00',
            'end_time' => '12:00:00'
        ]);

        // Attempt 1: Fully overlaps (09:00 - 10:00)
        $response1 = $this->actingAs($this->user)
            ->post(route('growlight.store'), [
                'actuator_id' => $this->growLightActuator->id,
                'start_time' => '09:00',
                'end_time' => '10:00'
            ]);
        $response1->assertSessionHasErrors(['start_time']);

        // Attempt 2: Overlaps start (07:00 - 09:00)
        $response2 = $this->actingAs($this->user)
            ->post(route('growlight.store'), [
                'actuator_id' => $this->growLightActuator->id,
                'start_time' => '07:00',
                'end_time' => '09:00'
            ]);
        $response2->assertSessionHasErrors(['start_time']);

        // Attempt 3: Overlaps end (11:00 - 13:00)
        $response3 = $this->actingAs($this->user)
            ->post(route('growlight.store'), [
                'actuator_id' => $this->growLightActuator->id,
                'start_time' => '11:00',
                'end_time' => '13:00'
            ]);
        $response3->assertSessionHasErrors(['start_time']);

        // Attempt 4: No overlap (13:00 - 14:00)
        $response4 = $this->actingAs($this->user)
            ->post(route('growlight.store'), [
                'actuator_id' => $this->growLightActuator->id,
                'start_time' => '13:00',
                'end_time' => '14:00'
            ]);
        $response4->assertSessionMissing('errors');
    }

    public function test_user_cannot_create_overlapping_feed_schedules()
    {
        FeedSchedule::create([
            'actuator_id' => $this->feederActuator->id,
            'time' => '10:00:00',
            'duration' => 10
        ]);

        // Attempt 1: Overlaps start (09:55 for 10 minutes -> 09:55 - 10:05)
        $response1 = $this->actingAs($this->user)
            ->post(route('jadwal-pakan.store'), [
                'actuator_id' => $this->feederActuator->id,
                'time' => '09:55',
                'duration' => 10
            ]);
        $response1->assertSessionHasErrors(['time']);

        // Attempt 2: Overlaps end (10:05 for 10 minutes -> 10:05 - 10:15)
        $response2 = $this->actingAs($this->user)
            ->post(route('jadwal-pakan.store'), [
                'actuator_id' => $this->feederActuator->id,
                'time' => '10:05',
                'duration' => 10
            ]);
        $response2->assertSessionHasErrors(['time']);

        // Attempt 3: No overlap (10:10 for 5 minutes -> 10:10 - 10:15)
        $response3 = $this->actingAs($this->user)
            ->post(route('jadwal-pakan.store'), [
                'actuator_id' => $this->feederActuator->id,
                'time' => '10:10',
                'duration' => 5
            ]);
        $response3->assertSessionMissing('errors');
    }

    public function test_user_filtering_by_site()
    {
        // Create second site
        $siteB = Site::create([
            'user_id' => $this->user->id,
            'name' => 'Site B',
            'location' => 'Bogor',
            'mac_address' => 'AA:BB:CC:11:22:02',
            'latitude' => '1.234',
            'longitude' => '5.678',
        ]);

        // Create second device
        $deviceB = Device::create([
            'name' => 'Device B',
            'mac_address' => '00:11:22:33:44:56',
            'description' => 'Device B desc',
            'status' => 'assigned'
        ]);
        $siteB->devices()->attach($deviceB->id, [
            'started_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Create second feeder actuator
        $feederB = Actuator::create([
            'device_id' => $deviceB->id,
            'name' => 'Feeder B',
            'type' => 'feeder',
            'status' => 'off'
        ]);

        // Create second growlight actuator
        $growLightB = Actuator::create([
            'device_id' => $deviceB->id,
            'name' => 'Growlight B',
            'type' => 'grow_light',
            'status' => 'off'
        ]);

        // Create schedules
        $feedA = FeedSchedule::create([
            'actuator_id' => $this->feederActuator->id,
            'time' => '07:00:00',
            'duration' => 5
        ]);

        $feedB = FeedSchedule::create([
            'actuator_id' => $feederB->id,
            'time' => '08:00:00',
            'duration' => 5
        ]);

        $glA = GrowLightSchedule::create([
            'actuator_id' => $this->growLightActuator->id,
            'start_time' => '06:00:00',
            'end_time' => '07:00:00'
        ]);

        $glB = GrowLightSchedule::create([
            'actuator_id' => $growLightB->id,
            'start_time' => '08:00:00',
            'end_time' => '09:00:00'
        ]);

        // 1. Check Device page filtering
        $responseDev = $this->actingAs($this->user)
            ->get(route('devices.index', ['site_id' => $this->site->id]));
        $responseDev->assertSee('Device 1');
        $responseDev->assertDontSee('Device B');

        // 2. Check Feed Schedules page filtering
        $responseFeed = $this->actingAs($this->user)
            ->get(route('jadwal-pakan.index', ['site_id' => $this->site->id]));
        $responseFeed->assertSee('07:00');
        $responseFeed->assertDontSee('08:00');

        // 3. Check Grow Light Schedules page filtering
        $responseGL = $this->actingAs($this->user)
            ->get(route('growlight.schedule', ['site_id' => $this->site->id]));
        $responseGL->assertSee('06:00');
        $responseGL->assertDontSee('08:00');
    }
}
