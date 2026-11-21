<?php

namespace Tests\Feature;

use App\Models\Insight;
use App\Models\Service;
use App\Models\TeamMember;
use App\Models\User;
use Database\Seeders\ServiceCatalogueSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CmsContentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('super_admin', 'web');
        Role::findOrCreate('panel_user', 'web');
    }

    public function test_services_index_lists_active_services(): void
    {
        $active = Service::factory()->create([
            'title' => 'Corporate Consultancy',
            'slug' => 'corporate-consultancy',
            'is_active' => true,
        ]);
        Service::factory()->inactive()->create([
            'title' => 'Hidden Service',
            'slug' => 'hidden-service',
        ]);

        $response = $this->get(route('services.index'));

        $response->assertOk();
        $response->assertSeeText($active->title);
        $response->assertDontSeeText('Hidden Service');
    }

    public function test_active_service_detail_is_visible(): void
    {
        $service = Service::factory()->create([
            'title' => 'Legal Research & Legal Writing',
            'slug' => 'legal-research-legal-writing',
            'is_active' => true,
        ]);

        $this->get(route('services.show', $service->slug))
            ->assertOk()
            ->assertSeeText($service->title);
    }

    public function test_inactive_service_is_inaccessible(): void
    {
        $service = Service::factory()->inactive()->create([
            'slug' => 'inactive-service',
        ]);

        $this->get(route('services.show', $service->slug))
            ->assertNotFound();
    }

    public function test_unknown_service_returns_not_found(): void
    {
        $this->get(route('services.show', 'not-a-real-service'))
            ->assertNotFound();
    }

    public function test_team_page_shows_empty_state_when_no_members(): void
    {
        $this->get(route('team'))
            ->assertOk()
            ->assertSee('No published team profiles yet', false)
            ->assertDontSee('Advocate Jane Doe', false);
    }

    public function test_active_team_member_is_visible(): void
    {
        $member = TeamMember::factory()->create([
            'name' => 'Confirmed Practitioner',
            'slug' => 'confirmed-practitioner',
            'position' => 'Consultant',
            'is_active' => true,
        ]);
        TeamMember::factory()->inactive()->create([
            'name' => 'Inactive Person',
            'slug' => 'inactive-person',
        ]);

        $response = $this->get(route('team'));

        $response->assertOk();
        $response->assertSeeText($member->name);
        $response->assertSeeText('Consultant');
        $response->assertDontSeeText('Inactive Person');
        $response->assertDontSee('No published team profiles yet', false);
    }

    public function test_insights_page_shows_empty_state_when_none_published(): void
    {
        Insight::factory()->unpublished()->create([
            'title' => 'Draft Only',
            'slug' => 'draft-only',
        ]);

        $this->get(route('insights'))
            ->assertOk()
            ->assertSee('No published insights yet', false)
            ->assertDontSeeText('Draft Only');
    }

    public function test_published_insight_is_visible_on_index_and_detail(): void
    {
        $insight = Insight::factory()->published()->create([
            'title' => 'Confirmed Insight Title',
            'slug' => 'confirmed-insight-title',
        ]);

        $this->get(route('insights'))
            ->assertOk()
            ->assertSeeText($insight->title);

        $this->get(route('insights.show', $insight->slug))
            ->assertOk()
            ->assertSeeText($insight->title)
            ->assertSeeText('Confirmed body content for testing.');
    }

    public function test_unpublished_insight_is_inaccessible(): void
    {
        $insight = Insight::factory()->unpublished()->create([
            'slug' => 'unpublished-insight',
        ]);

        $this->get(route('insights.show', $insight->slug))
            ->assertNotFound();
    }

    public function test_unknown_insight_returns_not_found(): void
    {
        $this->get(route('insights.show', 'missing-insight'))
            ->assertNotFound();
    }

    public function test_staff_panel_login_still_accessible(): void
    {
        $this->get('/staff/login')->assertOk();
    }

    public function test_authorized_staff_can_open_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $this->actingAs($user)
            ->get('/staff')
            ->assertOk();
    }

    public function test_stage_three_routes_still_work(): void
    {
        $this->get(route('home'))->assertOk();
        $this->get(route('about'))->assertOk();
        $this->get(route('contact'))->assertOk();
        $this->get(route('privacy'))->assertOk();
        $this->get(route('team'))->assertOk();
        $this->get(route('insights'))->assertOk();
        $this->get(route('services.index'))->assertOk();
    }

    public function test_featured_active_services_appear_on_home(): void
    {
        $this->seed(ServiceCatalogueSeeder::class);

        Service::factory()->featured()->create([
            'title' => 'Featured Active Service',
            'slug' => 'featured-active-service',
            'is_active' => true,
        ]);
        Service::factory()->featured()->inactive()->create([
            'title' => 'Featured Inactive Service',
            'slug' => 'featured-inactive-service',
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSeeText('Strategic advisory');
        $response->assertSee(route('services.show', 'corporate-consultancy'), false);
        $response->assertDontSeeText('Featured Active Service');
        $response->assertDontSeeText('Featured Inactive Service');
    }
}
