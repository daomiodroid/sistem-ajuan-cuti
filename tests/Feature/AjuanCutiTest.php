<?php

namespace Tests\Feature;

use App\Models\AjuanCuti;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\User;

class AjuanCutiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test pegawai can create leave request.
     */
    public function test_pegawai_can_create_leave_request(): void
    {
        $user = User::create([
            'nip' => '12345',
            'name' => 'Budi Santoso',
            'email' => 'budi@email.com',
            'password' => bcrypt('password'),
            'role' => 'pegawai',
        ]);

        $response = $this->actingAs($user)->post(route('ajuan-cuti.store'), [
            'jenis_cuti' => 'Cuti Tahunan',
            'tanggal_mulai' => '2026-08-01',
            'tanggal_selesai' => '2026-08-05',
            'keterangan' => 'Liburan keluarga',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('ajuan_cuti', [
            'nip' => '12345',
            'nama' => 'Budi Santoso',
            'status' => 'Menunggu',
        ]);
    }

    /**
     * Test validation date: tanggal_selesai must be after or equal to tanggal_mulai.
     */
    public function test_leave_request_dates_validation(): void
    {
        $user = User::create([
            'nip' => '12345',
            'name' => 'Budi Santoso',
            'email' => 'budi@email.com',
            'password' => bcrypt('password'),
            'role' => 'pegawai',
        ]);

        $response = $this->actingAs($user)->post(route('ajuan-cuti.store'), [
            'jenis_cuti' => 'Cuti Tahunan',
            'tanggal_mulai' => '2026-08-05',
            'tanggal_selesai' => '2026-08-01', // Invalid: selesai sebelum mulai
            'keterangan' => 'Liburan keluarga',
        ]);

        $response->assertSessionHasErrors(['tanggal_selesai']);
    }

    /**
     * Test admin can update leave status.
     */
    public function test_admin_can_update_leave_status(): void
    {
        $admin = User::create([
            'nip' => '99999',
            'name' => 'Admin Utama',
            'email' => 'admin@email.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $ajuan = AjuanCuti::create([
            'nip' => '12345',
            'nama' => 'Budi Santoso',
            'jenis_cuti' => 'Cuti Tahunan',
            'tanggal_mulai' => '2026-08-01',
            'tanggal_selesai' => '2026-08-05',
            'keterangan' => 'Liburan',
            'status' => 'Menunggu'
        ]);

        $response = $this->actingAs($admin)->patch(route('ajuan-cuti.status', $ajuan->id), [
            'status' => 'Disetujui',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('ajuan_cuti', [
            'id' => $ajuan->id,
            'status' => 'Disetujui',
        ]);
    }

    /**
     * Test pegawai can delete leave request.
     */
    public function test_pegawai_can_delete_leave_request(): void
    {
        $user = User::create([
            'nip' => '12345',
            'name' => 'Budi Santoso',
            'email' => 'budi@email.com',
            'password' => bcrypt('password'),
            'role' => 'pegawai',
        ]);

        $ajuan = AjuanCuti::create([
            'nip' => '12345',
            'nama' => 'Budi Santoso',
            'jenis_cuti' => 'Cuti Tahunan',
            'tanggal_mulai' => '2026-08-01',
            'tanggal_selesai' => '2026-08-05',
            'keterangan' => 'Liburan',
            'status' => 'Menunggu'
        ]);

        $response = $this->actingAs($user)->delete(route('ajuan-cuti.destroy', $ajuan->id));

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseMissing('ajuan_cuti', [
            'id' => $ajuan->id,
        ]);
    }
}
