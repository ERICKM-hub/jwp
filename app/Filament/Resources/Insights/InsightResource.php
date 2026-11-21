<?php

namespace App\Filament\Resources\Insights;

use App\Filament\Resources\Insights\Pages\CreateInsight;
use App\Filament\Resources\Insights\Pages\EditInsight;
use App\Filament\Resources\Insights\Pages\ListInsights;
use App\Models\Insight;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class InsightResource extends Resource
{
    protected static ?string $model = Insight::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Content')
                    ->description('Enter only firm-confirmed article content. Do not invent articles or authors.')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, ?string $state, string $operation): void {
                                if ($operation === 'create') {
                                    $set('slug', Str::slug((string) $state));
                                }
                            }),
                        TextInput::make('slug')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->alphaDash()
                            ->required(fn (string $operation): bool => $operation === 'edit')
                            ->helperText('Generated from the title on create when left blank. Remains stable unless changed deliberately.'),
                        TextInput::make('author_name')
                            ->label('Author name')
                            ->maxLength(255)
                            ->helperText('Optional. Leave blank when no author should be shown publicly.'),
                        Textarea::make('excerpt')
                            ->rows(3)
                            ->maxLength(1000)
                            ->columnSpanFull(),
                        Textarea::make('body')
                            ->label('Body')
                            ->rows(14)
                            ->maxLength(50000)
                            ->required(fn (Get $get): bool => (bool) $get('is_published'))
                            ->helperText('Plain text. Required when publishing.')
                            ->columnSpanFull(),
                        FileUpload::make('featured_image_path')
                            ->label('Featured image')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(2048)
                            ->disk('public')
                            ->directory('insights')
                            ->visibility('public')
                            ->imagePreviewHeight('160')
                            ->helperText('Optional. JPEG, PNG, or WebP up to 2 MB. No AI-generated images.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Publishing')
                    ->description('New articles default to unpublished. Scheduled articles remain private until the publication time.')
                    ->schema([
                        Toggle::make('is_published')
                            ->label('Published')
                            ->default(false)
                            ->live()
                            ->helperText('Drafts and future-dated articles never appear publicly.'),
                        DateTimePicker::make('published_at')
                            ->label('Publication date')
                            ->native(false)
                            ->seconds(false)
                            ->required(fn (Get $get): bool => (bool) $get('is_published'))
                            ->helperText('Use a past/current time to publish now, or a future time to schedule.'),
                        Toggle::make('is_featured')
                            ->label('Featured')
                            ->default(false)
                            ->helperText('Featured only affects articles that are already publicly published.'),
                    ])
                    ->columns(3),

                Section::make('SEO')
                    ->description('Optional. Leave blank to fall back to the article title and excerpt.')
                    ->schema([
                        TextInput::make('seo_title')
                            ->label('SEO title')
                            ->maxLength(255),
                        Textarea::make('seo_description')
                            ->label('SEO description')
                            ->rows(3)
                            ->maxLength(320)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('author_name')
                    ->label('Author')
                    ->toggleable()
                    ->placeholder('—'),
                TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('—'),
                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean(),
                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->state(function (Insight $record): string {
                        if (! $record->is_published || $record->published_at === null) {
                            return 'Draft';
                        }

                        if ($record->published_at->isFuture()) {
                            return 'Scheduled';
                        }

                        return 'Published';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Published' => 'success',
                        'Scheduled' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                TernaryFilter::make('is_published')
                    ->label('Published'),
                TernaryFilter::make('is_featured')
                    ->label('Featured'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInsights::route('/'),
            'create' => CreateInsight::route('/create'),
            'edit' => EditInsight::route('/{record}/edit'),
        ];
    }
}
