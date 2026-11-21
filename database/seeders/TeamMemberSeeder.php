<?php

namespace Database\Seeders;

use App\Models\TeamMember;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TeamMemberSeeder extends Seeder
{
    /**
     * Seed confirmed team profiles from docs/JW_Pencraft_Website_Brief.pdf.
     * Idempotent by slug. Does not invent photos or contact details.
     */
    public function run(): void
    {
        $members = [
            [
                'name' => 'Johnwesley Odhiambo',
                'slug' => 'johnwesley-odhiambo',
                'position' => 'Lead Consultant & Chief Executive Officer',
                'bio' => 'Johnwesley Odhiambo is the founder and Chief Executive Officer of JW Pencraft Solutions Limited. He provides strategic leadership to the firm while spearheading consultancy assignments in business strategy, legal research, governance, compliance, and organizational development. He is passionate about helping businesses and institutions develop practical solutions that drive sustainable growth and measurable impact.',
                'sort_order' => 0,
            ],
            [
                'name' => 'Emmanuel Thuku',
                'slug' => 'emmanuel-thuku',
                'position' => 'Business Development Manager',
                'bio' => 'Emmanuel Thuku leads business development initiatives and strategic partnerships at JW Pencraft Solutions. He is responsible for identifying growth opportunities, strengthening client relationships, and supporting the development of innovative solutions that address evolving market needs.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Sarah Gladys',
                'slug' => 'sarah-gladys',
                'position' => 'Client Relations Manager',
                'bio' => 'Sarah Gladys oversees client engagement and relationship management. She ensures clients receive exceptional service throughout their engagement journey while supporting communication, project coordination, and client satisfaction initiatives.',
                'sort_order' => 2,
            ],
        ];

        foreach ($members as $member) {
            TeamMember::query()->updateOrCreate(
                ['slug' => $member['slug']],
                [
                    'name' => $member['name'],
                    'position' => $member['position'],
                    'bio' => $member['bio'],
                    'qualification' => null,
                    'photo_path' => null,
                    'email' => null,
                    'phone' => null,
                    'linkedin_url' => null,
                    'sort_order' => $member['sort_order'],
                    'is_active' => true,
                    'slug' => $member['slug'] ?: Str::slug($member['name']),
                ],
            );
        }
    }
}
