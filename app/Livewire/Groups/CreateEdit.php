<?php

namespace App\Livewire\Groups;

use App\Models\ParticipantGroup;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Contracts\View\View;

class CreateEdit extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public ?ParticipantGroup $group = null;

    public function mount(Request $request): void
    {
        $groupId = $request->get('group');

        if ($groupId) {
            $this->group = ParticipantGroup::findOrFail($groupId);
        }

        $this->preFillForm();
    }

    private function preFillForm()
    {
        if ($this->group === null) {
            $this->form->fill();
            return;
        }

        $this->form->fill([
            'title' => $this->group->title,
            'food_factor' => $this->group->food_factor,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label(__('Title'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('food_factor')
                    ->label(__('Food Factor'))
                    ->required()
                    ->default(1)
                    ->numeric()
                    ->minValue(0.1),
            ])
            ->statePath('data')
            ->model(ParticipantGroup::class);
    }

    public function store()
    {
        $data = $this->form->getState();

        if ($this->group === null) {
            $this->group = new ParticipantGroup;
        }

        $this->group->title = $data['title'];
        $this->group->food_factor = $data['food_factor'];
        $this->group->team_id = Auth::user()->currentTeam->id;
        $this->group->save();

        $this->redirect(route('participant-groups.list'), true);
    }

    public function render(): View
    {
        return view('livewire.groups.create-edit')
            ->title($this->group === null
                ? __('Create Group')
                : __('Edit :group', ['group' => $this->group->title]));
    }
}
