<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    /**
     * Seed confirmed firm information from the website brief.
     * Does not invent social URLs, business hours, or website domain.
     */
    public function run(): void
    {
        $confirmed = [
            'firm.legal_name' => 'JW Pencraft Solutions Limited',
            'firm.short_name' => 'JW Pencraft Solutions',
            'firm.tagline' => 'Seamless Solutions, Boundless Success.',
            'firm.country' => 'Kenya',
            'firm.email' => 'jpencraftsolutions@gmail.com',
            'firm.phone' => '+254 757 109 861',
            'firm.address' => 'MH House, P.O. Box 00100',
            'firm.city' => 'Nairobi',
            'firm.short_summary' => 'We provide integrated business, legal, compliance, branding, and strategic advisory solutions that empower organizations to grow, remain compliant, and achieve sustainable success.',
            'firm.about' => 'JW Pencraft Solutions Limited is a multidisciplinary consultancy firm providing businesses and organizations with practical, strategic, and results-driven solutions. We combine expertise in business development, legal research, compliance, branding, governance, and sustainability to help our clients navigate challenges and unlock opportunities.',
            'firm.mission' => 'To provide integrated business, legal, compliance, branding, and sustainability solutions that enable our clients to grow, remain competitive, and achieve long-term success.',
            'firm.vision' => 'To be Africa\'s leading multidisciplinary consultancy firm delivering innovative, practical, and sustainable solutions that empower businesses and institutions to achieve excellence.',
            'firm.professional_approach' => 'At JW Pencraft Solutions, we combine multidisciplinary expertise in law, business strategy, compliance, governance, branding, and sustainability to deliver practical, actionable, and measurable solutions tailored to each client\'s unique needs.',
            'branding.logo_path' => 'images/jw-pencraft-logo.png',
            'branding.hero_image_path' => 'images/hero.png',
        ];

        foreach ($confirmed as $key => $value) {
            SiteSetting::query()->updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'type' => 'string',
                ],
            );
        }

        $nullableKeys = [
            'firm.business_hours',
            'firm.website',
            'social.facebook',
            'social.linkedin',
            'social.instagram',
            'social.x',
        ];

        foreach ($nullableKeys as $key) {
            SiteSetting::query()->firstOrCreate(
                ['key' => $key],
                [
                    'value' => null,
                    'type' => 'string',
                ],
            );
        }

        SiteSetting::flushCache();
    }
}
