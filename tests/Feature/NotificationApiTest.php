<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\WelcomeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NotificationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_list_and_mark_notifications_as_read(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $user->notify(new WelcomeNotification());
        $notification = $user->fresh()->unreadNotifications->first();

        $this->getJson('/api/notifications')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data');

        $this->getJson('/api/notifications/unread')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data');

        $this->patchJson('/api/notifications/' . $notification->id . '/read')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->getJson('/api/notifications/unread')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(0, 'data');
    }

    public function test_authenticated_user_can_mark_all_notifications_as_read(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $user->notify(new WelcomeNotification());
        $user->notify(new WelcomeNotification());

        $this->patchJson('/api/notifications/read-all')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->getJson('/api/notifications/unread')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(0, 'data');
    }
}
