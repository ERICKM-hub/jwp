<?php

namespace Tests\Feature;

use App\Filament\Resources\TeamMembers\Pages\CreateTeamMember;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TeamCmsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('super_admin', 'web');
        Role::findOrCreate('panel_user', 'web');
    }

    public function test_active_scope_and_ordering(): void
    {
        TeamMember::factory()->create([
            'name' => 'Second Person',
            'slug' => 'second-person',
            'sort_order' => 2,
            'is_active' => true,
        ]);
        TeamMember::factory()->create([
            'name' => 'First Person',
            'slug' => 'first-person',
            'sort_order' => 0,
            'is_active' => true,
        ]);
        TeamMember::factory()->inactive()->create([
            'name' => 'Hidden Person',
            'slug' => 'hidden-person',
            'sort_order' => 1,
        ]);

        $active = TeamMember::query()->active()->ordered()->pluck('slug')->all();

        $this->assertSame(['first-person', 'second-person'], $active);
    }

    public function test_team_page_empty_state(): void
    {
        $this->get(route('team'))
            ->assertOk()
            ->assertSee('No published team profiles yet', false)
            ->assertDontSee('Meet our experts', false)
            ->assertDontSee('Industry-leading professionals', false);
    }

    public function test_team_page_shows_active_members_only_in_order(): void
    {
        TeamMember::factory()->create([
            'name' => 'Ada Confirmed',
            'slug' => 'ada-confirmed',
            'position' => 'Consultant',
            'bio' => 'Confirmed short biography.',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        TeamMember::factory()->create([
            'name' => 'Ben Confirmed',
            'slug' => 'ben-confirmed',
            'position' => 'Advisor',
            'sort_order' => 0,
            'is_active' => true,
        ]);
        TeamMember::factory()->inactive()->create([
            'name' => 'Inactive Person',
            'slug' => 'inactive-person',
            'position' => 'Hidden Role',
        ]);

        $response = $this->get(route('team'));

        $response->assertOk();
        $response->assertSeeText('Ada Confirmed');
        $response->assertSeeText('Ben Confirmed');
        $response->assertSeeText('Consultant');
        $response->assertSeeText('Confirmed short biography.');
        $response->assertDontSeeText('Inactive Person');
        $response->assertDontSeeText('Hidden Role');
        $response->assertSeeInOrder(['Ben Confirmed', 'Ada Confirmed']);
        $response->assertDontSee('No published team profiles yet', false);
    }

    public function test_optional_contact_links_render_only_when_supplied(): void
    {
        TeamMember::factory()->create([
            'name' => 'Public Contact',
            'slug' => 'public-contact',
            'position' => 'Consultant',
            'email' => 'public@example.test',
            'phone' => '+254700000000',
            'linkedin_url' => 'https://www.linkedin.com/in/example',
            'is_active' => true,
        ]);
        TeamMember::factory()->create([
            'name' => 'No Contacts',
            'slug' => 'no-contacts',
            'position' => 'Advisor',
            'email' => null,
            'phone' => null,
            'linkedin_url' => 'http://insecure.example',
            'is_active' => true,
        ]);

        $response = $this->get(route('team'));

        $response->assertOk();
        $response->assertSee('mailto:public@example.test', false);
        $response->assertSee('+254700000000', false);
        $response->assertSee('https://www.linkedin.com/in/example', false);
        $response->assertSee('rel="noopener noreferrer"', false);
        $response->assertDontSee('http://insecure.example', false);
        $response->assertDontSee('mailto:no-contacts', false);
    }

    public function test_photo_placeholder_and_alt_text(): void
    {
        TeamMember::factory()->create([
            'name' => 'Jane Confirmed',
            'slug' => 'jane-confirmed',
            'position' => 'Consultant',
            'photo_path' => null,
            'is_active' => true,
        ]);

        $this->get(route('team'))
            ->assertOk()
            ->assertSeeText('JC')
            ->assertDontSee('storage/team/', false);
    }

    public function test_guest_cannot_access_team_management(): void
    {
        $this->get('/staff/team-members')->assertRedirect('/staff/login');
    }

    public function test_unauthorized_user_cannot_access_team_management(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/staff/team-members')
            ->assertForbidden();
    }

    public function test_authorized_staff_can_access_team_management(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        TeamMember::factory()->create([
            'name' => 'Staff Visible',
            'slug' => 'staff-visible',
            'position' => 'Consultant',
        ]);

        $this->actingAs($user)
            ->get('/staff/team-members')
            ->assertOk()
            ->assertSeeText('Staff Visible');
    }

    public function test_create_validation_requires_name_and_role(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        Livewire::actingAs($user)
            ->test(CreateTeamMember::class)
            ->fillForm([
                'name' => '',
                'position' => '',
                'slug' => 'missing-fields',
                'sort_order' => 0,
                'is_active' => false,
            ])
            ->call('create')
            ->assertHasFormErrors(['name', 'position']);
    }

    public function test_invalid_email_and_linkedin_are_rejected(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        Livewire::actingAs($user)
            ->test(CreateTeamMember::class)
            ->fillForm([
                'name' => 'Valid Name',
                'slug' => 'valid-name',
                'position' => 'Consultant',
                'email' => 'not-an-email',
                'linkedin_url' => 'ftp://example.com/profile',
                'sort_order' => 0,
                'is_active' => false,
            ])
            ->call('create')
            ->assertHasFormErrors(['email', 'linkedin_url']);
    }

    public function test_valid_optional_fields_are_accepted(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        Livewire::actingAs($user)
            ->test(CreateTeamMember::class)
            ->fillForm([
                'name' => 'Accepted Member',
                'slug' => 'accepted-member',
                'position' => 'Consultant',
                'bio' => 'Confirmed biography text.',
                'email' => 'accepted@example.test',
                'phone' => '+254711111111',
                'linkedin_url' => 'https://www.linkedin.com/in/accepted',
                'sort_order' => 3,
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('team_members', [
            'slug' => 'accepted-member',
            'email' => 'accepted@example.test',
            'linkedin_url' => 'https://www.linkedin.com/in/accepted',
            'is_active' => true,
        ]);
    }

    public function test_non_image_upload_is_rejected(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $user->assignRole('super_admin');

        Livewire::actingAs($user)
            ->test(CreateTeamMember::class)
            ->fillForm([
                'name' => 'Upload Test',
                'slug' => 'upload-test',
                'position' => 'Consultant',
                'photo_path' => UploadedFile::fake()->create('notes.exe', 100, 'application/x-msdownload'),
                'sort_order' => 0,
                'is_active' => false,
            ])
            ->call('create')
            ->assertHasFormErrors(['photo_path']);
    }

    public function test_database_seeder_creates_confirmed_team_members_only(): void
    {
        $this->seed();

        $this->assertSame(3, TeamMember::query()->count());
        $this->assertTrue(TeamMember::query()->where('slug', 'johnwesley-odhiambo')->exists());
        $this->assertTrue(TeamMember::query()->where('slug', 'emmanuel-thuku')->exists());
        $this->assertTrue(TeamMember::query()->where('slug', 'sarah-gladys')->exists());
    }
}
