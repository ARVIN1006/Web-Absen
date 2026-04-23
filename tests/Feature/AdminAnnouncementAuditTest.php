<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAnnouncementAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_announcement_and_log_activity(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.announcements.store'), [
            'title' => 'Informasi Internal',
            'content' => 'Pengumuman untuk seluruh tim.',
            'type' => 'info',
        ]);

        $response->assertRedirect(route('admin.announcements.index'));

        $announcement = Announcement::first();

        $this->assertNotNull($announcement);
        $this->assertTrue((bool) $announcement->is_active);

        $this->assertDatabaseHas('activity_logs', [
            'actor_id' => $admin->id,
            'subject_type' => Announcement::class,
            'subject_id' => $announcement->id,
            'event' => 'announcement.created',
        ]);
    }
}
