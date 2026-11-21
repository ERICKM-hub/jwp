<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StaffPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('super_admin', 'web');
        Role::findOrCreate('panel_user', 'web');
    }

    public function test_staff_login_route_exists(): void
    {
        $response = $this->get('/staff/login');

        $response->assertOk();
    }

    public function test_guests_cannot_access_staff_dashboard(): void
    {
        $response = $this->get('/staff');

        $response->assertRedirect('/staff/login');
    }

    public function test_authorized_super_admin_can_access_staff_panel(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $response = $this->actingAs($user)->get('/staff');

        $response->assertOk();
    }

    public function test_authorized_panel_user_can_access_staff_panel(): void
    {
        $user = User::factory()->create();
        $user->assignRole('panel_user');

        $response = $this->actingAs($user)->get('/staff');

        $response->assertOk();
    }

    public function test_unauthorized_user_cannot_access_staff_panel(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/staff');

        $response->assertForbidden();
    }

    public function test_main_panel_path_is_no_longer_registered(): void
    {
        $response = $this->get('/main/login');

        $response->assertNotFound();
    }
}
