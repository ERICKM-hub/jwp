<?php

namespace App\Filament\Resources\ContactInquiries\Schemas;

use App\Enums\ContactInquiryStatus;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactInquiryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Enquiry')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email')
                            ->copyable(),
                        TextEntry::make('phone')
                            ->placeholder('—'),
                        TextEntry::make('organization')
                            ->placeholder('—'),
                        TextEntry::make('subject')
                            ->placeholder('—')
                            ->columnSpanFull(),
                        TextEntry::make('message')
                            ->columnSpanFull()
                            ->wrap(),
                        TextEntry::make('status')
                            ->badge()
                            ->formatStateUsing(fn (ContactInquiryStatus|string|null $state): string => $state instanceof ContactInquiryStatus
                                ? $state->label()
                                : (string) $state),
                        TextEntry::make('submitted_at')
                            ->dateTime(),
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->placeholder('—'),
                        TextEntry::make('updated_at')
                            ->dateTime()
                            ->placeholder('—'),
                    ])
                    ->columns(2),
            ]);
    }
}
