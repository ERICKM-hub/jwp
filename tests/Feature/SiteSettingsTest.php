<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageSiteSettings;
use App\Models\SiteSetting;
use App\Models\User;
use App\Support\FirmContent;
use App\Support\SiteSettings;
use Database\Seeders\SiteSettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SiteSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('super_admin', 'web');
        Role::findOrCreate('panel_user', 'web');
    }

    public function test_confirmed_settings_can_be_seeded_and_retrieved(): void
    {
        $this->seed(SiteSettingsSeeder::class);

        $this->assertSame('JW Pencraft Solutions Limited', SiteSetting::get('firm.legal_name'));
        $this->assertSame('JW Pencraft Solutions', SiteSettings::shortName());
        $this->assertSame('Kenya', SiteSettings::country());
        $this->assertSame('images/jw-pencraft-logo.png', SiteSettings::logoPath());
        $this->assertSame('images/hero.png', SiteSettings::heroImagePath());
    }

    public function test_brief_contact_and_narrative_settings_are_seeded(): void
    {
        $this->seed(SiteSettingsSeeder::class);

        $this->assertSame('jpencraftsolutions@gmail.com', SiteSettings::email());
        $this->assertSame('+254 757 109 861', SiteSettings::phone());
        $this->assertSame('Nairobi', SiteSettings::city());
        $this->assertNotNull(SiteSettings::mission());
        $this->assertNotNull(SiteSettings::vision());
        $this->assertNull(SiteSetting::get('firm.website'));
        $this->assertNull(SiteSettings::website());
        $this->assertSame([], SiteSettings::socialLinks());
    }

    public function test_settings_are_cached_and_flushed_on_update(): void
    {
        $this->seed(SiteSettingsSeeder::class);

        SiteSettings::legalName();
        $this->assertTrue(Cache::has(SiteSetting::CACHE_KEY));

        SiteSetting::set('firm.legal_name', 'JW Pencraft Solutions Limited');
        $this->assertFalse(Cache::has(SiteSetting::CACHE_KEY));
        $this->assertSame('JW Pencraft Solutions Limited', SiteSettings::legalName());
    }

    public function test_guest_cannot_access_site_settings(): void
    {
        $this->get('/staff/site-settings')
            ->assertRedirect('/staff/login');
    }

    public function test_unauthorized_user_cannot_access_site_settings(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/staff/site-settings')
            ->assertForbidden();
    }

    public function test_authorized_staff_can_access_and_update_settings(): void
    {
        $this->seed(SiteSettingsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $this->actingAs($user)
            ->get('/staff/site-settings')
            ->assertOk();

        Livewire::actingAs($user)
            ->test(ManageSiteSettings::class)
            ->fillForm([
                'firm' => [
                    'legal_name' => 'JW Pencraft Solutions Limited',
                    'short_name' => 'JW Pencraft Solutions',
                    'tagline' => 'Seamless Solutions, Boundless Success.',
                    'country' => 'Kenya',
                    'email' => 'info@example.test',
                    'phone' => null,
                    'address' => null,
                    'city' => null,
                    'business_hours' => null,
                    'website' => null,
                    'short_summary' => null,
                    'about' => null,
                    'mission' => null,
                    'vision' => null,
                    'professional_approach' => null,
                ],
                'social' => [
                    'facebook' => null,
                    'linkedin' => null,
                    'instagram' => null,
                    'x' => null,
                ],
                'branding' => [
                    'logo_path' => 'images/jw-pencraft-logo.png',
                    'hero_image_path' => 'images/hero.png',
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('info@example.test', SiteSetting::get('firm.email'));
        $this->assertSame('info@example.test', SiteSettings::email());
    }

    public function test_homepage_about_and_contact_still_load(): void
    {
        $this->seed(SiteSettingsSeeder::class);

        $this->get(route('home'))->assertOk()->assertSee(SiteSettings::tagline(), false);
        $this->get(route('about'))->assertOk()->assertSee('Mission', false);
        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('jpencraftsolutions@gmail.com', false)
            ->assertDontSee(config('firm.placeholders.email'), false)
            ->assertDontSee('Trusted by hundreds', false);
    }

    public function test_social_links_are_hidden_when_missing(): void
    {
        $this->seed(SiteSettingsSeeder::class);

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('facebook.com', false)
            ->assertDontSee('linkedin.com', false);
    }

    public function test_invalid_social_urls_are_rejected(): void
    {
        SiteSetting::set('social.linkedin', 'not-a-url');

        $this->assertSame([], SiteSettings::socialLinks());
    }
}
