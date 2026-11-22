<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ContactInquiries\ContactInquiryResource;
use App\Filament\Resources\TeamMembers\TeamMemberResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\ContactInquiry;
use App\Models\Insight;
use App\Models\Service;
use App\Models\TeamMember;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class Stats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Contact Inquiries', ContactInquiry::count())
                ->description(ContactInquiry::whereMonth('created_at', now()->month)->count() . ' received in ' . now()->format('F'))
                ->descriptionIcon('heroicon-m-envelope')
                ->color('warning')
                ->url(ContactInquiryResource::getUrl('index')),

            Stat::make('Team Members', TeamMember::count())
                ->description('Active team profiles')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success')
                ->url(TeamMemberResource::getUrl('index')),

            Stat::make('Staff Accounts', User::count())
                ->description('Admin & staff users')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('gray')
                ->url(UserResource::getUrl('index')),
        ];
    }
}
