<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Notifications\Models\EnterpriseNotification;
use Tests\TestCase;

class NotificationCenterTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_view_and_mark_notification_read(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::query()->where('email', 'owner@holding.test')->firstOrFail();
        $notification = EnterpriseNotification::query()->create([
            'user_id' => $owner->id,
            'type' => 'approval.pending',
            'severity' => 'warning',
            'title' => 'Approval menunggu',
            'message' => 'Ada purchase order yang perlu direview.',
        ]);
        EnterpriseNotification::query()->create([
            'user_id' => null,
            'type' => 'system.broadcast',
            'severity' => 'info',
            'title' => 'Global broadcast hidden for now',
            'message' => 'Not displayed because this slice is user-specific.',
        ]);

        $this->actingAs($owner)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Approval menunggu')
            ->assertDontSee('Global broadcast hidden for now')
            ->assertSee('1 unread');

        $this->actingAs($owner)
            ->post(route('notifications.mark-read', $notification))
            ->assertRedirect();

        $this->assertNotNull($notification->refresh()->read_at);
        $this->assertDatabaseHas('activity_logs', [
            'event' => 'notifications.marked_read',
            'subject_type' => EnterpriseNotification::class,
            'subject_id' => $notification->id,
        ]);
    }

    public function test_owner_can_mark_all_notifications_read(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::query()->where('email', 'owner@holding.test')->firstOrFail();

        EnterpriseNotification::query()->create([
            'user_id' => $owner->id,
            'type' => 'inventory.low_stock',
            'severity' => 'warning',
            'title' => 'Low stock alert 1',
            'message' => 'Stock rendah 1.',
        ]);
        EnterpriseNotification::query()->create([
            'user_id' => $owner->id,
            'type' => 'inventory.low_stock',
            'severity' => 'warning',
            'title' => 'Low stock alert 2',
            'message' => 'Stock rendah 2.',
        ]);

        $this->actingAs($owner)
            ->post(route('notifications.mark-all-read'))
            ->assertRedirect();

        $this->assertSame(0, EnterpriseNotification::query()->where('user_id', $owner->id)->whereNull('read_at')->count());
        $this->assertDatabaseHas('activity_logs', [
            'event' => 'notifications.marked_all_read',
            'user_id' => $owner->id,
        ]);
    }
}
