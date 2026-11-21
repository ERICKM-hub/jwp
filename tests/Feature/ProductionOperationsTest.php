<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ProductionOperationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_endpoint_is_available(): void
    {
        $this->get('/up')->assertOk();
    }

    public function test_production_environment_forces_https_scheme(): void
    {
        $previous = $this->app['env'];
        $this->app['env'] = 'production';

        try {
            URL::forceScheme('https');
            $this->assertSame('https', parse_url(url('/contact'), PHP_URL_SCHEME));
        } finally {
            URL::forceScheme('http');
            $this->app['env'] = $previous;
        }
    }

    public function test_env_example_documents_production_and_omits_secrets(): void
    {
        $example = file_get_contents(base_path('.env.example'));

        $this->assertNotFalse($example);
        $this->assertStringContainsString('APP_NAME="JW Pencraft Solutions"', $example);
        $this->assertStringContainsString('APP_ENV=production', $example);
        $this->assertStringContainsString('APP_DEBUG=false', $example);
        $this->assertStringContainsString('DB_CONNECTION=mysql', $example);
        $this->assertStringContainsString('SESSION_SECURE_COOKIE', $example);
        $this->assertStringContainsString('APP_KEY=', $example);
        $this->assertDoesNotMatchRegularExpression('/^APP_KEY=base64:.+/m', $example);
        $this->assertStringNotContainsString('hello@example.com', $example);
    }

    public function test_gitignore_protects_env_files_but_keeps_example(): void
    {
        $gitignore = file_get_contents(base_path('.gitignore'));

        $this->assertNotFalse($gitignore);
        $this->assertStringContainsString('.env', $gitignore);
        $this->assertStringContainsString('.env.*', $gitignore);
        $this->assertStringContainsString('!.env.example', $gitignore);
    }

    public function test_deployment_documentation_exists(): void
    {
        $this->assertFileExists(base_path('docs/deployment.md'));
        $this->assertFileExists(base_path('docs/go-live-checklist.md'));
        $this->assertFileExists(base_path('docs/content-onboarding.md'));

        $runbook = file_get_contents(base_path('docs/deployment.md'));
        $this->assertNotFalse($runbook);
        $this->assertStringContainsString('php artisan migrate --force', $runbook);
        $this->assertStringContainsString('storage:link', $runbook);
        $this->assertStringContainsString('Do not deploy', $runbook);
        $this->assertStringContainsString('super_admin', $runbook);
    }
}
