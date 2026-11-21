<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserCmsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('super_admin', 'web');
        Role::findOrCreate('panel_user', 'web');
    }

    public function test_guest_cannot_access_user_management(): void
    {
        $this->get('/staff/users')->assertRedirect('/staff/login');
    }

    public function test_unauthorized_user_cannot_access_user_management(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/staff/users')
            ->assertForbidden();
    }

    public function test_super_admin_can_access_user_management(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $this->actingAs($user)
            ->get('/staff/users')
            ->assertOk();
    }

    public function test_super_admin_can_open_create_user_page(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $this->actingAs($user)
            ->get('/staff/users/create')
            ->assertOk()
            ->assertSeeText('Create User');
    }
}
