<?php

namespace App\Filament\Resources\ContactInquiries\Tables;

use App\Enums\ContactInquiryStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContactInquiriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('organization')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('—'),
                TextColumn::make('subject')
                    ->searchable()
                    ->limit(40)
                    ->toggleable()
                    ->placeholder('—'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (ContactInquiryStatus $state): string => $state->label())
                    ->color(fn (ContactInquiryStatus $state): string => match ($state) {
                        ContactInquiryStatus::New => 'warning',
                        ContactInquiryStatus::Reviewed => 'info',
                        ContactInquiryStatus::Contacted => 'success',
                        ContactInquiryStatus::Closed => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('submitted_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('submitted_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(collect(ContactInquiryStatus::cases())->mapWithKeys(
                        fn (ContactInquiryStatus $status): array => [$status->value => $status->label()]
                    )->all()),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()->label('Update status'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
