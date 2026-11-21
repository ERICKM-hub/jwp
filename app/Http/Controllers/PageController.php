<?php

namespace App\Http\Controllers;

use App\Models\Insight;
use App\Models\Service;
use App\Models\TeamMember;
use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        return view('pages.home', [
            'featuredServices' => Service::query()->active()->featured()->ordered()->take(4)->get(),
            'latestInsights' => Insight::query()->published()->ordered()->take(4)->get(),
            'whyChooseUs' => config('firm.why_choose_us', []),
            'focusAreas' => config('firm.focus_areas', []),
            'process' => config('firm.process', []),
            'clients' => config('firm.clients', []),
            'testimonials' => config('firm.testimonials', []),
            'homepage' => config('firm.homepage', []),
        ]);
    }

    public function about(): View
    {
        return view('pages.about', [
            'values' => config('firm.values', []),
            'ourStory' => config('firm.our_story'),
        ]);
    }

    public function team(): View
    {
        return view('pages.team', [
            'teamMembers' => TeamMember::query()->active()->ordered()->get(),
        ]);
    }

    public function insights(): View
    {
        return view('pages.insights', [
            'insights' => Insight::query()->published()->ordered()->get(),
        ]);
    }

    public function insightShow(string $slug): View
    {
        $insight = Insight::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('pages.insight-show', [
            'insight' => $insight,
        ]);
    }

    public function contact(): View
    {
        return view('pages.contact');
    }

    public function privacy(): View
    {
        return view('pages.privacy');
    }
}
