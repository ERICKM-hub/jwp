<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Contracts\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        return view('services.index', [
            'services' => Service::query()->active()->ordered()->get(),
        ]);
    }

    public function show(string $slug): View
    {
        $service = Service::query()
            ->active()
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedServices = Service::query()
            ->active()
            ->where('id', '!=', $service->id)
            ->ordered()
            ->limit(3)
            ->get();

        return view('services.show', [
            'service' => $service,
            'relatedServices' => $relatedServices,
        ]);
    }
}
