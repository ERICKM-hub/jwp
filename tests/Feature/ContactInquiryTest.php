<?php

namespace Tests\Feature;

use App\Enums\ContactInquiryStatus;
use App\Filament\Resources\ContactInquiries\Pages\EditContactInquiry;
use App\Models\ContactInquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ContactInquiryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('super_admin', 'web');
        Role::findOrCreate('panel_user', 'web');
    }

    public function test_contact_page_loads(): void
    {
        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('Enquiry form', false)
            ->assertSee('Submit enquiry', false);
    }

    public function test_guest_can_submit_valid_enquiry_and_it_is_stored(): void
    {
        $payload = [
            'name' => 'Amina Wanjiku',
            'email' => 'amina@example.com',
            'phone' => '+254700000000',
            'organization' => 'Example Co',
            'subject' => 'Service enquiry',
            'message' => 'I would like information about corporate consultancy.',
        ];

        $response = $this->from(route('contact'))
            ->post(route('contact.submit'), $payload);

        $response->assertRedirect(route('contact'));
        $response->assertSessionHas('enquiry_success');

        $this->assertDatabaseHas('contact_inquiries', [
            'name' => 'Amina Wanjiku',
            'email' => 'amina@example.com',
            'organization' => 'Example Co',
            'subject' => 'Service enquiry',
            'status' => ContactInquiryStatus::New->value,
        ]);

        $this->followRedirects($response)
            ->assertOk()
            ->assertSee('Your enquiry has been received', false);
    }

    public function test_required_fields_are_enforced(): void
    {
        $response = $this->from(route('contact'))
            ->post(route('contact.submit'), [
                'name' => '',
                'email' => '',
                'message' => '',
            ]);

        $response->assertRedirect(route('contact'));
        $response->assertSessionHasErrors(['name', 'email', 'message']);
        $this->assertDatabaseCount('contact_inquiries', 0);
    }

    public function test_invalid_email_is_rejected(): void
    {
        $response = $this->from(route('contact'))
            ->post(route('contact.submit'), [
                'name' => 'Test User',
                'email' => 'not-an-email',
                'message' => 'Hello there.',
            ]);

        $response->assertRedirect(route('contact'));
        $response->assertSessionHasErrors(['email']);
        $this->assertDatabaseCount('contact_inquiries', 0);
    }

    public function test_validation_errors_preserve_old_input(): void
    {
        $response = $this->from(route('contact'))
            ->post(route('contact.submit'), [
                'name' => 'Test User',
                'email' => 'not-an-email',
                'organization' => 'Acme',
                'message' => 'Hello there.',
            ]);

        $response->assertRedirect(route('contact'));
        $response->assertSessionHasInput('name', 'Test User');
        $response->assertSessionHasInput('organization', 'Acme');
    }

    public function test_contact_form_includes_csrf_token_field(): void
    {
        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('name="_token"', false);
    }

    public function test_contact_submit_route_uses_web_middleware_stack(): void
    {
        $route = app('router')->getRoutes()->getByName('contact.submit');

        $this->assertNotNull($route);
        $this->assertContains('web', $route->gatherMiddleware());
        $this->assertTrue(
            collect($route->gatherMiddleware())->contains(
                fn (string $middleware): bool => str_contains($middleware, 'throttle')
            )
        );
    }

    public function test_submitted_message_is_not_rendered_as_raw_html_on_contact_page(): void
    {
        $script = '<script>alert("xss")</script>';

        $this->from(route('contact'))
            ->post(route('contact.submit'), [
                'name' => 'Safe User',
                'email' => 'safe@example.com',
                'message' => $script,
            ])
            ->assertRedirect(route('contact'));

        $this->get(route('contact'))
            ->assertOk()
            ->assertDontSee($script, false)
            ->assertDontSee('<script>alert("xss")</script>', false);
    }

    public function test_guests_cannot_access_staff_contact_inquiries(): void
    {
        $this->get('/staff/contact-inquiries')
            ->assertRedirect('/staff/login');
    }

    public function test_authorized_staff_can_access_contact_inquiries(): void
    {
        $inquiry = ContactInquiry::factory()->create([
            'name' => 'Visible Enquiry',
            'email' => 'visible@example.com',
            'message' => 'Please review this enquiry.',
        ]);

        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $this->actingAs($user)
            ->get('/staff/contact-inquiries')
            ->assertOk()
            ->assertSeeText('Visible Enquiry')
            ->assertSeeText('visible@example.com');

        $this->actingAs($user)
            ->get('/staff/contact-inquiries/'.$inquiry->id)
            ->assertOk()
            ->assertSeeText('Please review this enquiry.');
    }

    public function test_staff_can_change_enquiry_status(): void
    {
        $inquiry = ContactInquiry::factory()->create([
            'status' => ContactInquiryStatus::New,
        ]);

        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $this->actingAs($user);

        Livewire::test(
            EditContactInquiry::class,
            ['record' => $inquiry->getRouteKey()]
        )
            ->fillForm([
                'status' => ContactInquiryStatus::Reviewed->value,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(
            ContactInquiryStatus::Reviewed,
            $inquiry->fresh()->status
        );
    }

    public function test_staff_view_escapes_html_in_message(): void
    {
        $inquiry = ContactInquiry::factory()->create([
            'message' => '<img src=x onerror=alert(1)>Payload',
        ]);

        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $response = $this->actingAs($user)
            ->get('/staff/contact-inquiries/'.$inquiry->id);

        $response->assertOk();
        $response->assertDontSee('<img src=x onerror=alert(1)>', false);
        $response->assertSeeText('Payload');
    }
}
