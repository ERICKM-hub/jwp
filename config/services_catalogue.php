<?php

/**
 * Confirmed service catalogue from docs/JW_Pencraft_Website_Brief.pdf
 *
 * Runtime source of truth: `services` database table.
 * This file is a seed/reference catalogue.
 */

return [

    [
        'title' => 'Corporate Consultancy',
        'slug' => 'corporate-consultancy',
        'short_description' => 'Strategic advisory services that support organizational growth, governance, operational efficiency, and long-term sustainability.',
        'description' => "Providing strategic advisory services that support organizational growth, governance, operational efficiency, and long-term sustainability.\n\nServices include:\n• Business strategy development\n• Corporate governance advisory\n• Organizational restructuring\n• Policy and procedure development\n• Market entry advisory",
        'icon' => 'corporate',
        'featured' => true,
    ],
    [
        'title' => 'Legal Research & Legal Writing',
        'slug' => 'legal-research-legal-writing',
        'short_description' => 'Professional legal research and drafting support for institutions, organizations, and professionals.',
        'description' => "Providing professional legal research and drafting support for institutions, organizations, and professionals.\n\nServices include:\n• Legal opinions\n• Legal memoranda\n• Research reports\n• Regulatory reviews\n• Policy analysis",
        'icon' => 'legal-research',
        'featured' => true,
    ],
    [
        'title' => 'Business Establishment & Compliance',
        'slug' => 'business-establishment-compliance',
        'short_description' => 'Supporting entrepreneurs and businesses through establishment, registration, and regulatory compliance.',
        'description' => "Supporting entrepreneurs and businesses through establishment, registration, and regulatory compliance.\n\nServices include:\n• Company registration\n• NGO and association registration\n• Business permits and licensing\n• Corporate compliance support\n• Annual returns and statutory filings",
        'icon' => 'compliance',
        'featured' => true,
    ],
    [
        'title' => 'Tendering & Procurement Support',
        'slug' => 'tendering-procurement-support',
        'short_description' => 'Helping businesses successfully participate in procurement opportunities.',
        'description' => "Helping businesses successfully participate in procurement opportunities.\n\nServices include:\n• Tender identification\n• Bid preparation\n• Tender documentation\n• Proposal development\n• Procurement advisory",
        'icon' => 'procurement',
        'featured' => false,
    ],
    [
        'title' => 'Business Documentation & Data Room Development',
        'slug' => 'business-documentation-data-room',
        'short_description' => 'Developing professional business documents that support investment readiness and organizational growth.',
        'description' => "Developing professional business documents that support investment readiness and organizational growth.\n\nServices include:\n• Business plans\n• Investment pitch decks\n• Company profiles\n• Concept notes\n• Grant proposals\n• Investor documentation",
        'icon' => 'documentation',
        'featured' => true,
    ],
    [
        'title' => 'Social Media & Brand Management',
        'slug' => 'social-media-brand-management',
        'short_description' => 'Helping businesses build strong brands and maintain meaningful digital engagement.',
        'description' => "Helping businesses build strong brands and maintain meaningful digital engagement.\n\nServices include:\n• Social media management\n• Content strategy\n• Brand development\n• Brand positioning\n• Digital marketing support",
        'icon' => 'brand',
        'featured' => false,
    ],
    [
        'title' => 'ESG & Sustainable Development Advisory',
        'slug' => 'esg-sustainable-development-advisory',
        'short_description' => 'Supporting organizations in integrating environmental, social, and governance principles into their operations.',
        'description' => "Supporting organizations in integrating environmental, social, and governance principles into their operations.\n\nServices include:\n• ESG strategy development\n• Sustainability reporting\n• Stakeholder engagement\n• Impact assessment\n• SDG alignment",
        'icon' => 'esg',
        'featured' => false,
    ],
    [
        'title' => 'Training & Capacity Building',
        'slug' => 'training-capacity-building',
        'short_description' => 'Practical training programs designed to strengthen organizational and professional capacity.',
        'description' => "Delivering practical training programs designed to strengthen organizational and professional capacity.\n\nAreas include:\n• Corporate governance\n• Compliance\n• Business development\n• Procurement\n• ESG and sustainability\n• Professional development",
        'icon' => 'training',
        'featured' => false,
    ],

];
