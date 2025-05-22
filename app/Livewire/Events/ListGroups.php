<?php

namespace App\Livewire\Events;

use App\Models\Event;
use App\Models\ParticipantGroup;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ListGroups extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public Event $event;

    public function mount(Event $event)
    {
        $this->event = $event;
    }

    public function table(Table $table): Table
    {
        return $table
            ->relationship(fn (): BelongsToMany => $this->event->participantGroups())
            ->recordTitleAttribute('title')
            ->inverseRelationship('events')
            ->emptyStateHeading(__('No groups yet'))
            ->emptyStateDescription(__('Add a group on the top right.'))
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('Title')),
                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('Quantity'))
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->label(__('Total'))),
            ])
            ->headerActions([
                Tables\Actions\Action::make('campflow_import')
                    ->modalHeading(__('Import from Campflow'))
                    ->modalContent(view('partials.participant-groups-campflow-import', [
                        'availableGroups' => ParticipantGroup::get()->jsonSerialize(),
                    ]))
                    ->modalSubmitAction(false),
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(fn (Builder $query) => $query->where('team_id', Auth::user()->currentTeam->id))
                    ->modalHeading(__('Attach group'))
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\TextInput::make('quantity')
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->required(),
                    ]),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }

    #[On('submit-unique-group-counts')]
    public function handleCampflowImport(array $mappings, bool $clearStoredMappings)
    {
        if ($clearStoredMappings) {
            $this->event->participantGroups()->detach();
        }

        $existingGroups = $this->event->participantGroups->keyBy('id');

        $allGroups = ParticipantGroup::get();
        foreach ($allGroups as $group) {
            if (! isset($mappings[$group->id])) {
                continue;
            }

            $existingQuantity = $existingGroups[$group->id]->pivot->quantity ?? 0;

            $this->event->participantGroups()->syncWithoutDetaching([
                $group->id => ['quantity' => $existingQuantity + (int) $mappings[$group->id]],
            ]);
        }
    }

    public function render(): View
    {
        return view('livewire.events.list-groups');
    }
}
