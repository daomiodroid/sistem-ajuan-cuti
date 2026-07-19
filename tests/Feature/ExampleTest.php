<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::create([
            'nip' => '12345',
            'name' => 'Budi Santoso',
            'email' => 'budi@email.com',
            'password' => bcrypt('password'),
            'role' => 'pegawai',
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
    }
}
