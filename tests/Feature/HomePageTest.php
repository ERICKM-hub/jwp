<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_homepage_responds_successfully(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
    }

    public function test_public_homepage_uses_jw_pencraft_foundation(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertViewIs('pages.home');
        $response->assertSee('JW Pencraft Solutions Limited', false);
        $response->assertSee('Seamless Solutions, Boundless Success.', false);
        $response->assertSee(asset('images/jw-pencraft-logo.png'), false);
        $response->assertSee(asset('images/hero.png'), false);
        $response->assertSee('bg-brand-white', false);
        $response->assertSee('text-brand-navy', false);
        $response->assertSee('Contact Us', false);
        $response->assertSee('Explore Our Services', false);
    }
}