<?php

namespace Tests\Feature;

use App\Models\TeamMember;
use App\Support\SiteSettings;
use Database\Seeders\ServiceCatalogueSeeder;
use Database\Seeders\SiteSettingsSeeder;
use Database\Seeders\TeamMemberSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BriefContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders_brief_sections(): void
    {
        $this->seed(ServiceCatalogueSeeder::class);
        $this->seed(SiteSettingsSeeder::class);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSeeText('Explore Our Services');
        $response->assertSeeText('Contact Us');
        $response->assertSeeText('JW Pencraft Solutions Limited');
        $response->assertSeeText('Seamless Solutions, Boundless Success.');
        $response->assertSeeText('Expertise for real progress');
        $response->assertSeeText('A clear path to your success');
        $response->assertSeeText('Trusted across Kenya');
        $response->assertSeeText('Hekima Group');
        $response->assertSeeText('Intelligent Home Living');
        $response->assertSeeText('Integrated Services LLC');
        $response->assertSeeText('JW Pencraft Solutions consistently delivers professional, practical, and timely solutions');
        $response->assertSeeText('jpencraftsolutions@gmail.com');
        $response->assertSeeText('MH House, P.O. Box 00100');
        $response->assertSee('Enquire now', false);
        $response->assertSee('wa.me/254757109861', false);
    }

    public function test_about_page_renders_mission_vision_and_story(): void
    {
        $this->seed(SiteSettingsSeeder::class);

        $this->get(route('about'))
            ->assertOk()
            ->assertSeeText('Our story')
            ->assertSeeText('Mission')
            ->assertSeeText('Vision')
            ->assertSeeText('Excellence')
            ->assertSeeText('Client-Centricity');
    }

    public function test_team_seeder_publishes_brief_profiles(): void
    {
        $this->seed(TeamMemberSeeder::class);

        $this->assertSame(3, TeamMember::query()->active()->count());

        $this->get(route('team'))
            ->assertOk()
            ->assertSeeText('Johnwesley Odhiambo')
            ->assertSeeText('Lead Consultant & Chief Executive Officer')
            ->assertSeeText('Emmanuel Thuku')
            ->assertSeeText('Sarah Gladys')
            ->assertDontSee('No published team profiles yet', false);
    }

    public function test_service_catalogue_publishes_brief_descriptions(): void
    {
        $this->seed(ServiceCatalogueSeeder::class);

        $this->get(route('services.show', 'corporate-consultancy'))
            ->assertOk()
            ->assertSeeText('Business strategy development')
            ->assertDontSee(config('firm.placeholders.service_description'), false);
    }

    public function test_whatsapp_url_uses_confirmed_phone(): void
    {
        $this->assertSame('https://wa.me/254757109861', SiteSettings::whatsappUrl());
    }
}
