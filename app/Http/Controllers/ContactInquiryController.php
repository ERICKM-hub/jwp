<?php

namespace App\Http\Controllers;

use App\Enums\ContactInquiryStatus;
use App\Http\Requests\StoreContactInquiryRequest;
use App\Models\ContactInquiry;
use Illuminate\Http\RedirectResponse;

class ContactInquiryController extends Controller
{
    public function store(StoreContactInquiryRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $duplicate = ContactInquiry::query()
            ->where('email', $data['email'])
            ->where('message', $data['message'])
            ->where('submitted_at', '>=', now()->subMinutes(2))
            ->exists();

        if ($duplicate) {
            return redirect()
                ->route('contact')
                ->with('enquiry_success', 'Your enquiry has already been received. Our team will review it shortly.');
        }

        ContactInquiry::query()->create([
            ...$data,
            'status' => ContactInquiryStatus::New,
            'submitted_at' => now(),
        ]);

        return redirect()
            ->route('contact')
            ->with('enquiry_success', 'Your enquiry has been received. Our team will review it shortly.');
    }
}
