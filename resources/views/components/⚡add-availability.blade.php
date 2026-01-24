<?php

use Livewire\Component;
use App\Models\User;
use App\Livewire\Forms\AddAvailability;
use TallStackUi\Traits\Interactions;

new class extends Component {
    use Interactions;

    public $user;
    public AddAvailability $form;

    public function mount(User $user)
    {
        $this->user = $user;
    }

    public function addSchedule()
    {
        $this->form->validate();
        try {
            Zap::for($this->user)->named('working_hours')->availability()->from(date('Y-m-d'))->addPeriod($this->form->startTime, $this->form->endTime)->weekly($this->form->selectedDays)->save();
            $this->closeModal();
            $this->toast()->success('Schedule added successfully.')->send();
        } catch (\Exception $e) {
            $this->closeModal();
            $this->toast()
                ->error('Failed to add schedule: ' . $e->getMessage())
                ->send();
        }
    }

    public function closeModal()
    {
        Flux::modal('add-availability')->close();
    }
};
?>

<form wire:submit.prevent="addSchedule" class="space-y-6">
    <!-- Schedule Name -->
    <flux:field>
        <flux:label>Schedule Name</flux:label>
        <flux:description>Give your schedule a descriptive name.</flux:description>
        <flux:input type="text" wire:model="form.scheduleName" placeholder="e.g., Morning Shift, Weekend Hours" />
        <flux:error name="form.scheduleName" />
    </flux:field>

    <!-- Select Days -->
    <flux:field>
        <flux:label>Active Days</flux:label>
        <flux:description>Choose which days this schedule applies to.</flux:description>
        <flux:error name="form.selectedDays" />
        <flux:checkbox.group wire:model="form.selectedDays">
            <div class="grid grid-cols-2 gap-3 mt-2">
                @foreach ($form->days as $day)
                    <flux:checkbox value="{{ $day }}" label="{{ $day }}" />
                @endforeach
            </div>
        </flux:checkbox.group>
    </flux:field>

    <!-- Time Range -->
    <flux:field>
        <flux:label>Time Range</flux:label>
        <flux:description>Set the start and end time for this schedule.</flux:description>
        <div class="grid grid-cols-2 gap-4 mt-2">
            <div>
                <flux:label class="text-sm text-zinc-600 dark:text-zinc-400 mb-1.5">Start Time</flux:label>
                <flux:input type="time" wire:model="form.startTime" />
                <flux:error name="form.startTime" />
            </div>
            <div>
                <flux:label class="text-sm text-zinc-600 dark:text-zinc-400 mb-1.5">End Time</flux:label>
                <flux:input type="time" wire:model="form.endTime" />
                <flux:error name="form.endTime" />
            </div>
        </div>
    </flux:field>

    <!-- Modal Actions -->
    <div class="flex gap-2 justify-end pt-4">
        <flux:button type="button" variant="ghost" wire:click="closeModal">
            Cancel
        </flux:button>

        <flux:button type="submit" variant="primary">
            Add Schedule
        </flux:button>
    </div>
</form>
