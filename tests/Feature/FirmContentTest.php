<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Support\FirmContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FirmContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_detects_bracketed_placeholder_copy(): void
    {
        $this->assertTrue(FirmContent::isPlaceholder('[Mission statement to be confirmed.]'));
        $this->assertTrue(FirmContent::isPlaceholder(FirmContent::placeholder('service_description')));
        $this->assertFalse(FirmContent::isPlaceholder('JW Pencraft Solutions Limited provides consultancy.'));
    }

    public function test_service_meta_description_prefers_seo_description(): void
    {
        $service = Service::factory()->create([
            'seo_description' => 'Dedicated SEO description.',
            'short_description' => 'Short body text.',
            'description' => 'Longer narrative.',
        ]);

        $this->assertSame('Dedicated SEO description.', FirmContent::serviceMetaDescription($service));
    }

    public function test_service_meta_description_uses_database_text_when_confirmed(): void
    {
        $service = Service::factory()->create([
            'short_description' => 'Structured corporate advisory support for growing organisations.',
            'description' => 'Longer narrative.',
        ]);

        $meta = FirmContent::serviceMetaDescription($service);

        $this->assertStringContainsString('Structured corporate advisory', $meta);
    }

    public function test_service_meta_description_falls_back_when_placeholder(): void
    {
        $service = Service::factory()->create([
            'title' => 'Corporate Consultancy',
            'short_description' => FirmContent::placeholder('service_description'),
            'description' => FirmContent::placeholder('service_description'),
        ]);

        $meta = FirmContent::serviceMetaDescription($service);

        $this->assertStringContainsString('Corporate Consultancy', $meta);
        $this->assertStringContainsString('documented service area', $meta);
        $this->assertStringNotContainsString('to be confirmed', $meta);
    }
}
