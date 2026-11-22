<?php

namespace App\Filament\Widgets;

use App\Models\ContactInquiry;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class ContactInquiries extends TableWidget
{
    protected static ?int $sort = 2;

    // Takes 3 columns out of a 4-column grid (3/4 width) on large screens
    protected int | string | array $columnSpan = [
        'md' => 3,
        'xl' => 3,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => ContactInquiry::query())
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                // TextColumn::make('email')
                //     ->label('Email address')
                //     ->searchable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('organization')
                    ->searchable(),
                // TextColumn::make('subject')
                //     ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->searchable(),
                TextColumn::make('submitted_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
