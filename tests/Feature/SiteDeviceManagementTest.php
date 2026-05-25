<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\Site;
use App\Models\SiteDevice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteDeviceManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private User $otherUser;

    private User $admin;

    private Site $site;

    private Site $otherSite;

    private Device $device;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => 'user',
        ]);

        $this->otherUser = User::factory()->create([
            'role' => 'user',
        ]);

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->site = Site::create([
            'user_id' => $this->user->id,
            'name' => 'User Site',
            'location' => 'Jakarta',
            'mac_address' => 'AA:BB:CC:DD:EE:01',
        ]);

        $this->otherSite = Site::create([
            'user_id' => $this->otherUser->id,
            'name' => 'Other Site',
            'location' => 'Bandung',
            'mac_address' => 'AA:BB:CC:DD:EE:02',
        ]);

        $this->device = Device::create([
            'name' => 'Device A',
            'mac_address' => '11:22:33:44:55:66',
            'description' => 'Test device',
            'status' => 'available',
        ]);
    }

    public function test_user_can_attach_available_device_to_owned_site(): void
    {
        $response = $this
            ->actingAs($this->user)
            ->post(route('sites.devices.store', $this->site), [
                'device_id' => $this->device->id,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('site_devices', [
            'site_id' => $this->site->id,
            'device_id' => $this->device->id,
            'ended_at' => null,
        ]);

        $this->assertEquals('assigned', $this->device->fresh()->status);
    }

    public function test_user_cannot_attach_device_to_unowned_site(): void
    {
        $response = $this
            ->actingAs($this->user)
            ->post(route('sites.devices.store', $this->otherSite), [
                'device_id' => $this->device->id,
            ]);

        $response->assertForbidden();
        $this->assertEquals('available', $this->device->fresh()->status);
    }

    public function test_user_can_detach_device_from_owned_site(): void
    {
        SiteDevice::create([
            'site_id' => $this->site->id,
            'device_id' => $this->device->id,
            'started_at' => now(),
        ]);

        $this->device->update([
            'status' => 'assigned',
        ]);

        $response = $this
            ->actingAs($this->user)
            ->delete(route('sites.devices.destroy', [$this->site, $this->device]));

        $response->assertRedirect();

        $this->assertNotNull(
            SiteDevice::where('site_id', $this->site->id)
                ->where('device_id', $this->device->id)
                ->first()
                ->ended_at
        );

        $this->assertEquals('available', $this->device->fresh()->status);
    }

    public function test_admin_can_attach_and_detach_device_for_any_site(): void
    {
        $attachResponse = $this
            ->actingAs($this->admin)
            ->post(route('site-devices.store'), [
                'site_id' => $this->otherSite->id,
                'device_id' => $this->device->id,
            ]);

        $attachResponse->assertRedirect();
        $this->assertEquals('assigned', $this->device->fresh()->status);

        $detachResponse = $this
            ->actingAs($this->admin)
            ->delete(route('sites.devices.destroy', [$this->otherSite, $this->device]));

        $detachResponse->assertRedirect();
        $this->assertEquals('available', $this->device->fresh()->status);
    }

    public function test_active_device_cannot_be_attached_twice(): void
    {
        SiteDevice::create([
            'site_id' => $this->otherSite->id,
            'device_id' => $this->device->id,
            'started_at' => now(),
        ]);

        $this->device->update([
            'status' => 'assigned',
        ]);

        $response = $this
            ->actingAs($this->user)
            ->post(route('sites.devices.store', $this->site), [
                'device_id' => $this->device->id,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseMissing('site_devices', [
            'site_id' => $this->site->id,
            'device_id' => $this->device->id,
            'ended_at' => null,
        ]);
    }
}
