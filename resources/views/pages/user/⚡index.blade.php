<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;


new class extends Component
{

    use WithPagination;

    public $users = [];
    public int $quantity = 10;
    public ?string $search = null;

    public function mount()
    {

        $this->users = \App\Models\User::all();
    }

    public function render()
    {
        return $this->view()
            ->title(__('Users'));

    }

        public function with(): array
        {
        return [
            'headers' => [
                ['index' => 'id', 'label' => 'Id'],
                ['index' => 'name', 'label' => 'Name'],
                ['index' => 'email', 'label' => 'Email'],
                ['index' => 'type', 'label' => 'Role'],
                ['index' => 'action' , 'label' => 'Actions' ],
            ],
            'rows' => User::query()
                ->when($this->search, function (Builder $query) {
                    return $query->where('name', 'like', "%{$this->search}%");
                })
            ->paginate($this->quantity)->withQueryString(),
            'type' => 'data',
        ];
        }

};
?>

<div class="flex flex-col gap-10">

<flux:heading size="xl" class="flex justify-between">
    {{ __("Users") }}

        <flux:button href="{{ route('users.create') }}" icon="plus" variant="primary" >
        Add User
    </flux:button>


</flux:heading>


<x-ts-table
    :$headers
    :$rows
    paginate
    simple-pagination
    :filter="['quantity' => 'quantity' , 'search' => 'search']"
    :quantity="[10,20,50]"
    loading
    >
        @interact('column_action', $row, $type)
           <flux:button href="{{ route('users.show', $row->id) }}" icon="eye" variant="filled" size="sm">
            View
           </flux:button>
        @endinteract
    </x-ts-table>

</div>
