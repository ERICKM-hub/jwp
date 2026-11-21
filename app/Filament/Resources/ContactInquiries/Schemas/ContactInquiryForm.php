<?php

namespace App\Filament\Resources\ContactInquiries\Schemas;

use App\Enums\ContactInquiryStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactInquiryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Enquiry details')
                    ->description('Submitted content is read-only. Update status only.')
                    ->schema([
                        TextInput::make('name')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('email')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('phone')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('organization')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('subject')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                        Textarea::make('message')
                            ->disabled()
                            ->dehydrated(false)
                            ->rows(8)
                            ->columnSpanFull(),
                        Select::make('status')
                            ->required()
                            ->options(collect(ContactInquiryStatus::cases())->mapWithKeys(
                                fn (ContactInquiryStatus $status): array => [$status->value => $status->label()]
                            )->all())
                            ->native(false),
                    ])
                    ->columns(2),
            ]);
    }
}
