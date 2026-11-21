<?php

namespace Tests\Feature;

use App\Models\Service;
use Database\Seeders\ServiceCatalogueSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_is_successful(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertViewIs('pages.home');
        $response->assertSee(config('firm.legal_name'), false);
        $response->assertSee(config('firm.tagline'), false);
    }

    public function test_about_page_is_successful(): void
    {
        $this->get(route('about'))
            ->assertOk()
            ->assertViewIs('pages.about')
            ->assertSee('Mission', false);
    }

    public function test_services_index_lists_seeded_catalogue(): void
    {
        $this->seed(ServiceCatalogueSeeder::class);

        $response = $this->get(route('services.index'));

        $response->assertOk();
        $response->assertViewIs('services.index');

        foreach (Service::query()->active()->ordered()->get() as $service) {
            $response->assertSeeText($service->title);
        }
    }

    public function test_service_detail_page_is_successful(): void
    {
        $this->seed(ServiceCatalogueSeeder::class);

        $service = Service::query()->active()->ordered()->first();

        $this->assertNotNull($service);

        $this->get(route('services.show', $service->slug))
            ->assertOk()
            ->assertViewIs('services.show')
            ->assertSeeText($service->title);
    }

    public function test_unknown_service_slug_returns_not_found(): void
    {
        $this->get(route('services.show', 'not-a-real-service'))
            ->assertNotFound();
    }

    public function test_team_page_shows_empty_state_without_invented_people(): void
    {
        $this->get(route('team'))
            ->assertOk()
            ->assertSee('No published team profiles yet', false)
            ->assertDontSee('Advocate Jane Doe', false);
    }

    public function test_insights_page_shows_empty_state(): void
    {
        $this->get(route('insights'))
            ->assertOk()
            ->assertSee('No published insights yet', false);
    }

    public function test_contact_page_is_successful(): void
    {
        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('Enquiry form', false)
            ->assertSee('jpencraftsolutions@gmail.com', false)
            ->assertSee('+254 757 109 861', false);
    }

    public function test_privacy_page_is_successful(): void
    {
        $this->get(route('privacy'))
            ->assertOk()
            ->assertSee('Privacy Policy', false);
    }

    public function test_navigation_links_are_present_on_home(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee(route('about'), false);
        $response->assertSee(route('services.index'), false);
        $response->assertSee(route('team'), false);
        $response->assertSee(route('insights'), false);
        $response->assertSee(route('contact'), false);
    }
}
