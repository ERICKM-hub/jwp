<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use App\Support\FirmContent;
use Database\Seeders\ServiceCatalogueSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ServiceCmsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('super_admin', 'web');
        Role::findOrCreate('panel_user', 'web');
    }

    public function test_active_scope_excludes_inactive_services(): void
    {
        Service::factory()->create(['slug' => 'active-one', 'is_active' => true]);
        Service::factory()->inactive()->create(['slug' => 'inactive-one']);

        $this->assertSame(1, Service::query()->active()->count());
        $this->assertTrue(Service::query()->active()->where('slug', 'active-one')->exists());
        $this->assertFalse(Service::query()->active()->where('slug', 'inactive-one')->exists());
    }

    public function test_featured_scope_only_includes_active_featured_services(): void
    {
        Service::factory()->featured()->create(['slug' => 'featured-active', 'is_active' => true]);
        Service::factory()->create([
            'slug' => 'featured-inactive',
            'featured' => true,
            'is_active' => false,
        ]);
        Service::factory()->create(['slug' => 'plain-active', 'featured' => false, 'is_active' => true]);

        $featured = Service::query()->featured()->pluck('slug');

        $this->assertTrue($featured->contains('featured-active'));
        $this->assertFalse($featured->contains('featured-inactive'));
        $this->assertFalse($featured->contains('plain-active'));
    }

    public function test_ordered_scope_uses_explicit_sort_order(): void
    {
        Service::factory()->create(['title' => 'Zebra', 'slug' => 'zebra', 'sort_order' => 2]);
        Service::factory()->create(['title' => 'Alpha', 'slug' => 'alpha', 'sort_order' => 0]);
        Service::factory()->create(['title' => 'Middle', 'slug' => 'middle', 'sort_order' => 1]);

        $this->assertSame(
            ['alpha', 'middle', 'zebra'],
            Service::query()->ordered()->pluck('slug')->all()
        );
    }

    public function test_services_index_lists_active_ordered_services_only(): void
    {
        Service::factory()->create([
            'title' => 'Second Service',
            'slug' => 'second-service',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        Service::factory()->create([
            'title' => 'First Service',
            'slug' => 'first-service',
            'sort_order' => 0,
            'is_active' => true,
        ]);
        Service::factory()->inactive()->create([
            'title' => 'Hidden Service',
            'slug' => 'hidden-service',
            'sort_order' => 0,
        ]);

        $response = $this->get(route('services.index'));

        $response->assertOk();
        $response->assertSeeText('First Service');
        $response->assertSeeText('Second Service');
        $response->assertDontSeeText('Hidden Service');
        $response->assertSeeInOrder(['First Service', 'Second Service']);
    }

    public function test_active_service_detail_and_seo_fallbacks(): void
    {
        $service = Service::factory()->create([
            'title' => 'Corporate Consultancy',
            'slug' => 'corporate-consultancy',
            'short_description' => FirmContent::placeholder('service_description'),
            'description' => FirmContent::placeholder('service_description'),
            'is_active' => true,
        ]);

        $this->get(route('services.show', $service->slug))
            ->assertOk()
            ->assertSeeText('Corporate Consultancy')
            ->assertSee(FirmContent::pendingLabel(), false);
    }

    public function test_service_seo_fields_are_used_when_present(): void
    {
        $service = Service::factory()->create([
            'title' => 'Corporate Consultancy',
            'slug' => 'corporate-consultancy',
            'seo_title' => 'Corporate Consultancy SEO Title',
            'seo_description' => 'Confirmed SEO description for consultancy.',
            'short_description' => 'Short body text.',
            'is_active' => true,
        ]);

        $this->assertSame('Corporate Consultancy SEO Title', $service->pageTitle());
        $this->assertSame('Confirmed SEO description for consultancy.', $service->pageDescription());

        $this->get(route('services.show', $service->slug))
            ->assertOk()
            ->assertSee('Corporate Consultancy SEO Title', false)
            ->assertSee('Confirmed SEO description for consultancy.', false);
    }

    public function test_inactive_and_unknown_service_slugs_return_404(): void
    {
        Service::factory()->inactive()->create(['slug' => 'inactive-service']);

        $this->get(route('services.show', 'inactive-service'))->assertNotFound();
        $this->get(route('services.show', 'not-a-real-service'))->assertNotFound();
    }

    public function test_guest_cannot_access_service_resource(): void
    {
        $this->get('/staff/services')->assertRedirect('/staff/login');
    }

    public function test_unauthorized_user_cannot_access_service_resource(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/staff/services')
            ->assertForbidden();
    }

    public function test_authorized_staff_can_access_service_resource(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        Service::factory()->create([
            'title' => 'Corporate Consultancy',
            'slug' => 'corporate-consultancy',
        ]);

        $this->actingAs($user)
            ->get('/staff/services')
            ->assertOk()
            ->assertSeeText('Corporate Consultancy');
    }

    public function test_catalogue_seeder_is_idempotent_and_preserves_admin_copy(): void
    {
        $this->seed(ServiceCatalogueSeeder::class);
        $this->assertSame(8, Service::query()->count());

        $service = Service::query()->where('slug', 'corporate-consultancy')->firstOrFail();
        $service->update([
            'short_description' => 'Administrator-entered short description.',
            'description' => 'Administrator-entered full description.',
            'seo_title' => 'Admin SEO Title',
            'seo_description' => 'Admin SEO description.',
            'featured' => false,
            'is_active' => false,
            'sort_order' => 99,
        ]);

        $this->seed(ServiceCatalogueSeeder::class);

        $this->assertSame(8, Service::query()->count());

        $service->refresh();

        $this->assertSame('Administrator-entered short description.', $service->short_description);
        $this->assertSame('Administrator-entered full description.', $service->description);
        $this->assertSame('Admin SEO Title', $service->seo_title);
        $this->assertSame('Admin SEO description.', $service->seo_description);
        $this->assertFalse($service->featured);
        $this->assertFalse($service->is_active);
        $this->assertSame(0, $service->sort_order);
        $this->assertSame('Corporate Consultancy', $service->title);
    }

    public function test_homepage_featured_services_exclude_inactive(): void
    {
        $this->seed(ServiceCatalogueSeeder::class);

        Service::factory()->featured()->create([
            'title' => 'Visible Featured',
            'slug' => 'visible-featured',
            'is_active' => true,
        ]);
        Service::factory()->create([
            'title' => 'Hidden Featured',
            'slug' => 'hidden-featured',
            'featured' => true,
            'is_active' => false,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeText('Strategic advisory')
            ->assertDontSeeText('Visible Featured')
            ->assertDontSeeText('Hidden Featured');
    }
}
