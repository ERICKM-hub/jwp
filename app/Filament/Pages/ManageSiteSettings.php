<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use App\Support\SiteSettings;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * @property-read Schema $form
 */
class ManageSiteSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Site Settings';

    protected static ?string $title = 'Site Settings';

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'site-settings';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return 'Site Settings';
    }

    public function mount(): void
    {
        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $nested = [];

        foreach (SiteSettings::formState() as $key => $value) {
            data_set($nested, $key, $value);
        }

        $this->form->fill($nested);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Firm Identity')
                    ->schema([
                        TextInput::make('firm.legal_name')
                            ->label('Legal name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('firm.short_name')
                            ->label('Short name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('firm.tagline')
                            ->label('Tagline')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('firm.country')
                            ->label('Country')
                            ->required()
                            ->maxLength(120),
                    ])
                    ->columns(2),

                Section::make('Contact')
                    ->description('Leave blank until confirmed by the firm. Empty fields show pending-confirmation placeholders publicly.')
                    ->schema([
                        TextInput::make('firm.email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('firm.phone')
                            ->label('Phone')
                            ->tel()
                            ->maxLength(40),
                        TextInput::make('firm.address')
                            ->label('Address')
                            ->maxLength(255),
                        TextInput::make('firm.city')
                            ->label('City')
                            ->maxLength(120),
                        TextInput::make('firm.business_hours')
                            ->label('Business hours')
                            ->maxLength(255),
                        TextInput::make('firm.website')
                            ->label('Website')
                            ->url()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make('Social')
                    ->description('Only add verified profile URLs. Invalid or empty URLs are not shown publicly.')
                    ->schema([
                        TextInput::make('social.facebook')
                            ->label('Facebook')
                            ->url()
                            ->maxLength(255),
                        TextInput::make('social.linkedin')
                            ->label('LinkedIn')
                            ->url()
                            ->maxLength(255),
                        TextInput::make('social.instagram')
                            ->label('Instagram')
                            ->url()
                            ->maxLength(255),
                        TextInput::make('social.x')
                            ->label('X / Twitter')
                            ->url()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make('Content')
                    ->description('Do not invent mission, vision, or approach copy. Leave blank until firm-approved text is available.')
                    ->schema([
                        Textarea::make('firm.short_summary')
                            ->label('Short summary')
                            ->rows(3)
                            ->maxLength(1000)
                            ->columnSpanFull(),
                        Textarea::make('firm.about')
                            ->label('About text')
                            ->rows(5)
                            ->maxLength(5000)
                            ->columnSpanFull(),
                        Textarea::make('firm.mission')
                            ->label('Mission')
                            ->rows(4)
                            ->maxLength(2000)
                            ->columnSpanFull(),
                        Textarea::make('firm.vision')
                            ->label('Vision')
                            ->rows(4)
                            ->maxLength(2000)
                            ->columnSpanFull(),
                        Textarea::make('firm.professional_approach')
                            ->label('Professional approach')
                            ->rows(4)
                            ->maxLength(3000)
                            ->columnSpanFull(),
                    ]),

                Section::make('Branding')
                    ->description('Paths are relative to the public directory. Do not replace the verified logo or hero assets without firm approval.')
                    ->schema([
                        TextInput::make('branding.logo_path')
                            ->label('Logo path')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('branding.hero_image_path')
                            ->label('Hero image path')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(2),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach (SiteSettings::keys() as $key) {
            $value = data_get($data, $key);

            if (is_string($value)) {
                $value = strip_tags($value);
            }

            SiteSetting::set($key, $value);
        }

        Notification::make()
            ->title('Site settings saved')
            ->success()
            ->send();

        $this->fillForm();
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getFormContentComponent(),
            ]);
    }

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('save')
            ->footer([
                Actions::make([
                    Action::make('save')
                        ->label('Save settings')
                        ->submit('save')
                        ->keyBindings(['mod+s']),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
