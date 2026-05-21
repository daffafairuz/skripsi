<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Site;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationBadgeTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $admin;
    private $site1;
    private $site2;

    protected function setUp(): void
    {
        parent::setUp();

        // Create standard user
        $this->user = User::factory()->create([
            'role' => 'user'
        ]);

        // Create admin user
        $this->admin = User::factory()->create([
            'role' => 'admin'
        ]);

        // Create site belonging to $this->user
        $this->site1 = Site::create([
            'user_id' => $this->user->id,
            'name' => 'Site User',
            'location' => 'Jakarta',
            'mac_address' => '11:22:33:44:55:66',
        ]);

        // Create site belonging to another user / different owner
        $otherUser = User::factory()->create(['role' => 'user']);
        $this->site2 = Site::create([
            'user_id' => $otherUser->id,
            'name' => 'Site Other',
            'location' => 'Bandung',
            'mac_address' => '22:33:44:55:66:77',
        ]);
    }

    public function test_user_card_shows_correct_unread_notifications_badge_for_regular_user()
    {
        // 1. Initial check: zero unread notifications on account settings page
        $response = $this->actingAs($this->user)->get(route('account-setting'));
        $response->assertStatus(200);
        // Should not display the red badge element or the count since unreadCount is 0
        $response->assertDontSee('animate-pulse');

        // 2. Add 1 unread notification for site1 (user's site)
        Notification::create([
            'site_id' => $this->site1->id,
            'message' => 'Notification 1',
            'is_read' => false,
            'type' => 'info',
        ]);

        // Add 1 read notification for site1 (should not count)
        Notification::create([
            'site_id' => $this->site1->id,
            'message' => 'Notification Read',
            'is_read' => true,
            'type' => 'info',
        ]);

        // Add 1 unread notification for site2 (other user's site, should not count for this user)
        Notification::create([
            'site_id' => $this->site2->id,
            'message' => 'Notification Other Site',
            'is_read' => false,
            'type' => 'info',
        ]);

        // Check badge on account settings page
        $response = $this->actingAs($this->user)->get(route('account-setting'));
        $response->assertStatus(200);
        // Should see the red badge
        $response->assertSee('animate-pulse');
        $response->assertSee('1');

        // 3. Add 9 more unread notifications for user's site (total 10 unread in scope)
        for ($i = 0; $i < 9; $i++) {
            Notification::create([
                'site_id' => $this->site1->id,
                'message' => 'Notification ' . ($i + 2),
                'is_read' => false,
                'type' => 'info',
            ]);
        }

        // Check badge on account settings page shows '9+'
        $response = $this->actingAs($this->user)->get(route('account-setting'));
        $response->assertStatus(200);
        $response->assertSee('animate-pulse');
        $response->assertSee('9+');

        // 4. Visit notifications page -> should mark all user's notifications as read
        $response = $this->actingAs($this->user)->get(route('notifications'));
        $response->assertStatus(200);

        // Verify that user's notifications in database are now marked as read
        $this->assertEquals(0, Notification::whereIn('site_id', [$this->site1->id])->where('is_read', false)->count());

        // Verify that other user's unread notification remains unread
        $this->assertEquals(1, Notification::where('site_id', $this->site2->id)->where('is_read', false)->count());
    }

    public function test_user_card_shows_correct_unread_notifications_badge_for_admin()
    {
        // Add unread notification on site1
        Notification::create([
            'site_id' => $this->site1->id,
            'message' => 'Notification 1',
            'is_read' => false,
            'type' => 'info',
        ]);

        // Add unread notification on site2 (owned by other user)
        Notification::create([
            'site_id' => $this->site2->id,
            'message' => 'Notification 2',
            'is_read' => false,
            'type' => 'info',
        ]);

        // Admin should see both unread notifications (total = 2) on account settings page
        $response = $this->actingAs($this->admin)->get(route('account-setting'));
        $response->assertStatus(200);
        $response->assertSee('animate-pulse');
        $response->assertSee('2');

        // Visit notifications page -> should mark all unread notifications as read
        $response = $this->actingAs($this->admin)->get(route('notifications'));
        $response->assertStatus(200);

        // Verify all notifications in DB are now marked as read
        $this->assertEquals(0, Notification::where('is_read', false)->count());
    }
}
