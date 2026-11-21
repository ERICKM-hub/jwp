<?php

namespace Tests\Feature;

use App\Filament\Resources\Insights\Pages\CreateInsight;
use App\Filament\Resources\Insights\Pages\EditInsight;
use App\Models\Insight;
use App\Models\User;
use App\Support\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InsightCmsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('super_admin', 'web');
        Role::findOrCreate('panel_user', 'web');
    }

    public function test_published_scope_hides_drafts_and_future_articles(): void
    {
        Insight::factory()->published()->create(['slug' => 'past-published']);
        Insight::factory()->published()->create([
            'slug' => 'current-published',
            'published_at' => now(),
        ]);
        Insight::factory()->unpublished()->create(['slug' => 'draft-hidden']);
        Insight::factory()->scheduled()->create(['slug' => 'future-hidden']);
        Insight::factory()->featured()->unpublished()->create(['slug' => 'featured-draft']);
        Insight::factory()->featured()->scheduled()->create(['slug' => 'featured-future']);

        $slugs = Insight::query()->published()->ordered()->pluck('slug')->all();

        $this->assertSame(['current-published', 'past-published'], $slugs);
        $this->assertFalse(Insight::query()->published()->where('slug', 'draft-hidden')->exists());
        $this->assertFalse(Insight::query()->published()->where('slug', 'future-hidden')->exists());
        $this->assertFalse(Insight::query()->featured()->where('slug', 'featured-draft')->exists());
        $this->assertFalse(Insight::query()->featured()->where('slug', 'featured-future')->exists());
    }

    public function test_ordered_scope_prioritises_featured_then_newest(): void
    {
        Insight::factory()->published()->create([
            'slug' => 'older-featured',
            'is_featured' => true,
            'published_at' => now()->subDays(5),
        ]);
        Insight::factory()->published()->create([
            'slug' => 'newest-plain',
            'is_featured' => false,
            'published_at' => now()->subDay(),
        ]);
        Insight::factory()->published()->create([
            'slug' => 'newer-featured',
            'is_featured' => true,
            'published_at' => now()->subDays(2),
        ]);

        $this->assertSame(
            ['newer-featured', 'older-featured', 'newest-plain'],
            Insight::query()->published()->ordered()->pluck('slug')->all()
        );
    }

    public function test_insights_index_shows_published_only_and_hides_drafts_and_future(): void
    {
        Insight::factory()->published()->create([
            'title' => 'Visible Past Insight',
            'slug' => 'visible-past',
        ]);
        Insight::factory()->unpublished()->create([
            'title' => 'Hidden Draft Insight',
            'slug' => 'hidden-draft',
        ]);
        Insight::factory()->scheduled()->create([
            'title' => 'Hidden Future Insight',
            'slug' => 'hidden-future',
        ]);
        Insight::factory()->featured()->unpublished()->create([
            'title' => 'Featured Draft Insight',
            'slug' => 'featured-draft-insight',
        ]);

        $response = $this->get(route('insights'));

        $response->assertOk();
        $response->assertSeeText('Visible Past Insight');
        $response->assertDontSeeText('Hidden Draft Insight');
        $response->assertDontSeeText('Hidden Future Insight');
        $response->assertDontSeeText('Featured Draft Insight');
        $response->assertDontSee('No published insights yet', false);
    }

    public function test_insight_show_visibility_rules(): void
    {
        $published = Insight::factory()->published()->create([
            'title' => 'Public Article',
            'slug' => 'public-article',
            'body' => 'Confirmed public body.',
            'author_name' => 'Confirmed Author',
        ]);
        $draft = Insight::factory()->unpublished()->create(['slug' => 'draft-article']);
        $future = Insight::factory()->scheduled()->create(['slug' => 'future-article']);

        $this->get(route('insights.show', $published->slug))
            ->assertOk()
            ->assertSeeText('Public Article')
            ->assertSeeText('Confirmed public body.')
            ->assertSeeText('Confirmed Author');

        $this->get(route('insights.show', $draft->slug))->assertNotFound();
        $this->get(route('insights.show', $future->slug))->assertNotFound();
        $this->get(route('insights.show', 'unknown-article'))->assertNotFound();
    }

    public function test_seo_title_and_description_fallbacks(): void
    {
        $withSeo = Insight::factory()->published()->create([
            'title' => 'Article Title',
            'slug' => 'article-with-seo',
            'seo_title' => 'Custom SEO Title',
            'seo_description' => 'Custom SEO description for the article.',
            'excerpt' => 'Plain excerpt.',
        ]);
        $withoutSeo = Insight::factory()->published()->create([
            'title' => 'Fallback Title',
            'slug' => 'article-fallback-seo',
            'seo_title' => null,
            'seo_description' => null,
            'excerpt' => 'Fallback excerpt text.',
        ]);
        $generic = Insight::factory()->published()->create([
            'title' => 'Generic Title',
            'slug' => 'article-generic-seo',
            'seo_title' => null,
            'seo_description' => null,
            'excerpt' => null,
        ]);

        $this->assertSame('Custom SEO Title', $withSeo->pageTitle());
        $this->assertSame('Custom SEO description for the article.', $withSeo->pageDescription());

        $this->assertSame('Fallback Title | '.SiteSettings::shortName(), $withoutSeo->pageTitle());
        $this->assertSame('Fallback excerpt text.', $withoutSeo->pageDescription());

        $this->assertStringContainsString('Generic Title', $generic->pageDescription());
        $this->assertStringContainsString(SiteSettings::legalName(), $generic->pageDescription());

        $this->get(route('insights.show', $withSeo->slug))
            ->assertOk()
            ->assertSee('Custom SEO Title', false)
            ->assertSee('Custom SEO description for the article.', false);
    }

    public function test_author_omitted_when_empty(): void
    {
        Insight::factory()->published()->create([
            'title' => 'No Author Article',
            'slug' => 'no-author-article',
            'author_name' => null,
        ]);

        $this->get(route('insights.show', 'no-author-article'))
            ->assertOk()
            ->assertSeeText('No Author Article')
            ->assertDontSee('aria-hidden="true">·</span>', false);
    }

    public function test_guest_cannot_access_insight_management(): void
    {
        $this->get('/staff/insights')->assertRedirect('/staff/login');
    }

    public function test_unauthorized_user_cannot_access_insight_management(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/staff/insights')
            ->assertForbidden();
    }

    public function test_authorized_staff_can_access_insight_management(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        Insight::factory()->unpublished()->create([
            'title' => 'Staff Visible Draft',
            'slug' => 'staff-visible-draft',
        ]);

        $this->actingAs($user)
            ->get('/staff/insights')
            ->assertOk()
            ->assertSeeText('Staff Visible Draft');
    }

    public function test_new_insights_default_to_unpublished(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        Livewire::actingAs($user)
            ->test(CreateInsight::class)
            ->fillForm([
                'title' => 'Draft By Default',
                'slug' => 'draft-by-default',
                'excerpt' => 'Optional excerpt.',
                'body' => 'Draft body content.',
                'is_published' => false,
                'is_featured' => false,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('insights', [
            'slug' => 'draft-by-default',
            'is_published' => false,
        ]);

        $this->get(route('insights.show', 'draft-by-default'))->assertNotFound();
    }

    public function test_slug_auto_generates_on_create_and_stays_stable_on_edit(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        Livewire::actingAs($user)
            ->test(CreateInsight::class)
            ->fillForm([
                'title' => 'Stable Slug Title',
                'slug' => '',
                'body' => 'Body for slug test.',
                'is_published' => false,
                'is_featured' => false,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $insight = Insight::query()->where('title', 'Stable Slug Title')->firstOrFail();
        $this->assertSame('stable-slug-title', $insight->slug);

        Livewire::actingAs($user)
            ->test(EditInsight::class, ['record' => $insight->getRouteKey()])
            ->fillForm([
                'title' => 'Changed Title Without Slug Rewrite',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('stable-slug-title', $insight->fresh()->slug);
        $this->assertSame('Changed Title Without Slug Rewrite', $insight->fresh()->title);
    }

    public function test_duplicate_slug_is_rejected(): void
    {
        Insight::factory()->unpublished()->create(['slug' => 'taken-slug']);

        $user = User::factory()->create();
        $user->assignRole('super_admin');

        Livewire::actingAs($user)
            ->test(CreateInsight::class)
            ->fillForm([
                'title' => 'Another Title',
                'slug' => 'taken-slug',
                'body' => 'Body text.',
                'is_published' => false,
                'is_featured' => false,
            ])
            ->call('create')
            ->assertHasFormErrors(['slug']);
    }

    public function test_published_articles_require_body_and_published_at(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        Livewire::actingAs($user)
            ->test(CreateInsight::class)
            ->fillForm([
                'title' => 'Incomplete Publish',
                'slug' => 'incomplete-publish',
                'body' => '',
                'is_published' => true,
                'published_at' => null,
                'is_featured' => false,
            ])
            ->call('create')
            ->assertHasFormErrors(['body', 'published_at']);
    }

    public function test_non_image_featured_upload_is_rejected(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $user->assignRole('super_admin');

        Livewire::actingAs($user)
            ->test(CreateInsight::class)
            ->fillForm([
                'title' => 'Upload Reject',
                'slug' => 'upload-reject',
                'body' => 'Body text.',
                'featured_image_path' => UploadedFile::fake()->create('notes.exe', 100, 'application/x-msdownload'),
                'is_published' => false,
                'is_featured' => false,
            ])
            ->call('create')
            ->assertHasFormErrors(['featured_image_path']);
    }

    public function test_database_seeder_does_not_create_insights(): void
    {
        $this->seed();

        $this->assertSame(0, Insight::query()->count());
    }

    public function test_empty_state_remains_honest(): void
    {
        Insight::factory()->unpublished()->create([
            'title' => 'Draft Only',
            'slug' => 'draft-only-empty',
        ]);

        $this->get(route('insights'))
            ->assertOk()
            ->assertSee('No published insights yet', false)
            ->assertDontSee('Coming soon', false)
            ->assertDontSee('Check back soon', false)
            ->assertDontSeeText('Draft Only');
    }
}
