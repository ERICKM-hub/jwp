<?php

namespace Tests\Feature;

use App\Models\Insight;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\TeamMember;
use App\Support\SiteSettings;
use Database\Seeders\ServiceCatalogueSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_layout_includes_canonical_and_page_titles(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<link rel="canonical"', false)
            ->assertSee('<title>', false)
            ->assertSee(SiteSettings::legalName(), false)
            ->assertSee(SiteSettings::tagline(), false);

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('About | '.SiteSettings::shortName(), false);
    }

    public function test_cta_band_uses_site_settings_name_not_law_firm_wording(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Ready to transform your business?', false);
        $response->assertSee('Contact Us', false);
        $response->assertDontSee('Ready to discuss your matter?', false);
        $response->assertDontSee('your matter', false);
    }

    public function test_privacy_page_avoids_fabricated_review_date_and_hardcoded_lede(): void
    {
        $response = $this->get(route('privacy'));

        $response->assertOk();
        $response->assertSee('visitors to the '.SiteSettings::shortName().' website', false);
        $response->assertSee('No last-reviewed date is asserted here.', false);
        $response->assertDontSee('Last updated:', false);
    }

    public function test_mobile_navigation_includes_enquire_cta(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('aria-label="Mobile"', false)
            ->assertSee('Enquire now', false);
    }

    public function test_inactive_and_unpublished_content_stays_hidden(): void
    {
        $this->seed(ServiceCatalogueSeeder::class);

        $inactive = Service::factory()->inactive()->create([
            'title' => 'Hidden Inactive Service',
            'slug' => 'hidden-inactive-service',
        ]);
        TeamMember::factory()->inactive()->create([
            'name' => 'Hidden Team Person',
            'slug' => 'hidden-team-person',
        ]);
        Insight::factory()->unpublished()->create([
            'title' => 'Hidden Draft Insight',
            'slug' => 'hidden-draft-insight',
        ]);
        Insight::factory()->scheduled()->create([
            'title' => 'Hidden Future Insight',
            'slug' => 'hidden-future-insight',
        ]);
        $published = Insight::factory()->published()->create([
            'title' => 'Visible Published Insight',
            'slug' => 'visible-published-insight',
        ]);

        $this->get(route('services.show', $inactive->slug))->assertNotFound();
        $this->get(route('services.index'))->assertDontSeeText('Hidden Inactive Service');
        $this->get(route('team'))->assertDontSeeText('Hidden Team Person');
        $this->get(route('insights'))
            ->assertSeeText('Visible Published Insight')
            ->assertDontSeeText('Hidden Draft Insight')
            ->assertDontSeeText('Hidden Future Insight');
        $this->get(route('insights.show', 'hidden-draft-insight'))->assertNotFound();
        $this->get(route('insights.show', 'hidden-future-insight'))->assertNotFound();
        $this->get(route('insights.show', $published->slug))->assertOk();
    }

    public function test_confirmed_identity_renders_and_missing_optional_settings_stay_honest(): void
    {
        SiteSetting::set('firm.website', null);
        SiteSetting::set('social.facebook', null);

        $response = $this->get(route('contact'));

        $response->assertOk();
        $response->assertSee(SiteSettings::legalName(), false);
        $response->assertSee('jpencraftsolutions@gmail.com', false);
        $response->assertSee('+254 757 109 861', false);
        $response->assertDontSee('123 Main Street', false);
        $response->assertDontSee('facebook.com', false);
    }

    public function test_application_name_fallback_is_firm_brand(): void
    {
        $this->assertSame('JW Pencraft Solutions', config('app.name'));
    }

    public function test_css_keeps_reduced_motion_and_dark_focus_contrast(): void
    {
        $css = file_get_contents(resource_path('css/app.css'));

        $this->assertNotFalse($css);
        $this->assertStringContainsString('prefers-reduced-motion: reduce', $css);
        $this->assertStringContainsString('.bg-brand-navy a:focus-visible', $css);
        $this->assertStringContainsString('outline-color: var(--color-brand-gold)', $css);
    }

    public function test_hero_and_logo_assets_exist_and_are_referenced(): void
    {
        $this->assertFileExists(public_path('images/jw-pencraft-logo.png'));
        $this->assertFileExists(public_path('images/hero.png'));

        $this->get(route('home'))
            ->assertOk()
            ->assertSee(asset('images/jw-pencraft-logo.png'), false)
            ->assertSee(asset('images/hero.png'), false)
            ->assertSee('alt="'.SiteSettings::legalName().' logo"', false);
    }
}
