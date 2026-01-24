<?php

use Livewire\Component;
use App\Models\User;

new class extends Component {
    public User $user;

    public $availability;
    public $blocked;
    public $appointments;

    public $add_blocked = false;

    public function mount(User $user)
    {
        $this->user = $user;

        $this->availability = $user->availabilitySchedules()->get();
        $this->blocked = $user->blockedSchedules()->get();
        $this->appointments = $user->appointmentSchedules()->get();
    }

    public function render()
    {
        return $this->view()->title('User Details');
    }

    public function openAddAvailability()
    {
        $this->dispatch('open-modal', id: 'add-availability');
    }

    // acess this methiod to close the add availability modal via livewire from inside the component
    public function closeAddAvailability()
    {
        $this->add_availability = false;
    }
};
?>

<div class="flex flex-col gap-10">

    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="{{ route('users.index') }}">Users</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ $this->user->name }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:heading>
        {{ $this->user->name }}'s Schedule
    </flux:heading>

    <div class="flex gap-4">

        <flux:modal.trigger name="add-availability">
            <flux:button variant="primary">Add Availability</flux:button>
        </flux:modal.trigger>
        <flux:modal name="add-availability" class="md:w-96">
            @livewire('⚡add-availability', ['user' => $this->user])
        </flux:modal>

        <flux:modal.trigger name="add-blocked">
            <flux:button variant="danger">Add Blocked Time</flux:button>
            <flux:modal name="add-blocked" class="md:w-96">
                @livewire('⚡add-blocked', ['user' => $this->user])
            </flux:modal>
        </flux:modal.trigger>

    </div>

    @livewire('⚡fullcalender-lv', [
        'availability' => $this->availability,
        'blocked' => $this->blocked,
        'appointments' => $this->appointments,
    ])
</div>
