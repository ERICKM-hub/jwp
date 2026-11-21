<?php

namespace Tests\Feature;

use App\Models\Insight;
use App\Models\Service;
use App\Models\TeamMember;
use Database\Seeders\ServiceCatalogueSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisualPresentationTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_uses_rounded_service_cards_and_motion_classes(): void
    {
        $this->seed(ServiceCatalogueSeeder::class);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('home-pillar-card', false);
        $response->assertSee('motion-reveal', false);
        $response->assertSee('rounded-xl', false);
    }

    public function test_services_index_renders_service_card_component(): void
    {
        Service::factory()->create([
            'title' => 'Corporate Consultancy',
            'slug' => 'corporate-consultancy',
            'is_active' => true,
        ]);

        $this->get(route('services.index'))
            ->assertOk()
            ->assertSee('motion-card', false)
            ->assertSeeText('Corporate Consultancy')
            ->assertSeeText('Explore service');
    }

    public function test_team_cards_use_rounded_language_when_members_exist(): void
    {
        TeamMember::factory()->create([
            'name' => 'Confirmed Practitioner',
            'slug' => 'confirmed-practitioner',
            'is_active' => true,
        ]);

        $this->get(route('team'))
            ->assertOk()
            ->assertSee('motion-card', false)
            ->assertSeeText('Confirmed Practitioner');
    }

    public function test_insight_cards_use_rounded_language_when_published(): void
    {
        Insight::factory()->published()->create([
            'title' => 'Confirmed Insight Title',
            'slug' => 'confirmed-insight-title',
        ]);

        $this->get(route('insights'))
            ->assertOk()
            ->assertSee('motion-card', false)
            ->assertSeeText('Confirmed Insight Title')
            ->assertSeeText('Read more');
    }

    public function test_css_defines_reduced_motion_support(): void
    {
        $css = file_get_contents(resource_path('css/app.css'));

        $this->assertStringContainsString('prefers-reduced-motion: reduce', $css);
        $this->assertStringContainsString('.motion-card', $css);
        $this->assertStringContainsString('.motion-reveal', $css);
    }
}
