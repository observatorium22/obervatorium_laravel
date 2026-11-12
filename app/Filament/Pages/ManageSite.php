<?php

namespace App\Filament\Pages;

use App\Models\Project;
use App\Models\SiteSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * @property-read Schema $form
 */
class ManageSite extends Page
{
    protected static ?string $navigationLabel = 'Homepage';

    protected static ?string $title = 'Homepage';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $settings = SiteSetting::current();

        $this->form->fill([
            'mission_statement' => $settings->mission_statement,
            'featured_project_ids_text' => implode(',', $settings->featured_project_ids ?? []),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('mission_statement')->maxLength(255)->columnSpanFull(),
                TextInput::make('featured_project_ids_text')
                    ->label('Homepage project order')
                    ->helperText('Comma-separated project ids. The first id is shown first on the gallery.')
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->submit('save'),
                        ]),
                    ]),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $ids = array_values(array_filter(array_map(
            fn (string $id): int => (int) trim($id),
            explode(',', (string) ($state['featured_project_ids_text'] ?? ''))
        )));

        $settings = SiteSetting::current();
        $settings->fill([
            'mission_statement' => $state['mission_statement'] ?? null,
            'featured_project_ids' => $ids,
        ])->save();

        Project::query()->update(['is_featured' => false]);

        if ($ids !== []) {
            Project::query()->whereIn('legacy_id', $ids)->update(['is_featured' => true]);
        }

        Notification::make()->title('Homepage saved')->success()->send();
    }
}
