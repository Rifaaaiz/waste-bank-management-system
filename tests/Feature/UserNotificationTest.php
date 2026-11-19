<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserNotificationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('notifications');
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');

        Schema::create('roles', function (Blueprint $table): void {
            $table->increments('role_id');
            $table->string('role_name', 50)->unique();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table): void {
            $table->increments('user_id');
            $table->string('username', 50)->unique();
            $table->string('password_hash');
            $table->unsignedInteger('role_id');
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table): void {
            $table->increments('notification_id');
            $table->unsignedInteger('user_id');
            $table->string('title', 100);
            $table->text('message');
            $table->string('notification_type', 50);
            $table->unsignedInteger('pickup_id')->nullable();
            $table->unsignedInteger('transaction_id')->nullable();
            $table->unsignedInteger('withdrawal_id')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        foreach (['Admin', 'Warga'] as $roleName) {
            Role::create(['role_name' => $roleName]);
        }
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_index_groups_real_notifications_by_date_and_scopes_them_to_the_signed_in_warga(): void
    {
        Carbon::setTestNow('2026-11-18 10:00:00');
        $warga = $this->createUser('warga-notification');
        $otherWarga = $this->createUser('other-warga-notification');

        $todayNotification = $this->createNotification($warga, 'Pencairan diproses', 'Permintaan pencairan sedang diperiksa.', now());
        $this->createNotification($warga, 'Notifikasi lama', 'Detail tanggal sebelumnya.', Carbon::parse('2026-10-29 09:00:00'));
        $this->createNotification($otherWarga, 'Notifikasi milik warga lain', 'Tidak boleh ditampilkan.', now());

        $this->actingAs($warga)
            ->get(route('user.notifications.index'))
            ->assertOk()
            ->assertSee('Hari ini')
            ->assertSee('29 Okt 2026')
            ->assertSee('18 Nov')
            ->assertSee('Pencairan diproses')
            ->assertSee('Permintaan pencairan sedang diperiksa.')
            ->assertSee(route('user.notifications.show', $todayNotification))
            ->assertDontSee('Notifikasi milik warga lain');
    }

    public function test_mark_all_read_updates_only_the_signed_in_wargas_notifications(): void
    {
        $warga = $this->createUser('warga-mark-read');
        $otherWarga = $this->createUser('other-warga-mark-read');
        $wargaNotification = $this->createNotification($warga, 'Notifikasi warga', 'Pesan', now());
        $otherNotification = $this->createNotification($otherWarga, 'Notifikasi lain', 'Pesan lain', now());

        $this->actingAs($warga)
            ->post(route('user.notifications.read-all'))
            ->assertRedirect(route('user.notifications.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('notifications', [
            'notification_id' => $wargaNotification,
            'is_read' => true,
        ]);
        $this->assertDatabaseHas('notifications', [
            'notification_id' => $otherNotification,
            'is_read' => false,
        ]);
    }

    public function test_show_marks_owned_notification_read_and_hides_other_users_notification(): void
    {
        $warga = $this->createUser('warga-show-notification');
        $otherWarga = $this->createUser('other-warga-show-notification');
        $notification = $this->createNotification($warga, 'Detail notifikasi', 'Pesan lengkap.', now());
        $otherNotification = $this->createNotification($otherWarga, 'Rahasia pengguna lain', 'Pesan privat.', now());

        $this->actingAs($warga)
            ->get(route('user.notifications.show', $notification))
            ->assertOk()
            ->assertSee('Detail notifikasi')
            ->assertSee('Pesan lengkap.')
            ->assertSee('Sudah dibaca');

        $this->assertDatabaseHas('notifications', [
            'notification_id' => $notification,
            'is_read' => true,
        ]);

        $this->get(route('user.notifications.show', $otherNotification))
            ->assertNotFound();
    }

    private function createUser(string $username): User
    {
        return User::create([
            'username' => $username,
            'password_hash' => Hash::make('Password-123'),
            'role_id' => Role::query()->where('role_name', 'Warga')->value('role_id'),
        ]);
    }

    private function createNotification(User $user, string $title, string $message, Carbon $createdAt): int
    {
        return DB::table('notifications')->insertGetId([
            'user_id' => $user->user_id,
            'title' => $title,
            'message' => $message,
            'notification_type' => 'withdrawal',
            'is_read' => false,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);
    }
}
