<?php

use Livewire\Component;
use Livewire\Attributes\Validate;
use TallStackUi\Traits\Interactions;

new class extends Component {
    use Interactions;

    public $user;

    #[Validate('required|date')]
    public $blockStartDate;

    #[Validate('nullable|date')]
    public $blockEndDate;

    #[Validate('nullable|date_format:H:i')]
    public $blockStartTime;

    #[Validate('nullable|date_format:H:i|after:blockStartTime')]
    public $blockEndTime;

    #[Validate('required|string|max:255')]
    public $blockScheduleReason;

    #[Validate('required|boolean')]
    public $isSameDay = false;

    #[Validate('required|boolean')]
    public $isWholeDay = false;

    public function mount($user)
    {
        $this->user = $user;
    }

    public function addBlock()
    {
        $this->validate();
        try {
            $command = Zap::for($this->user)->named($this->blockScheduleReason)->blocked();

            if ($this->isWholeDay) {
                $this->blockStartTime = '00:00';
                $this->blockEndTime = '23:59';
            }
            if ($this->isSameDay) {
                $command = $command->on($this->blockStartDate)->addPeriod($this->blockStartTime, $this->blockEndTime);
            } else {
                $command = $command->between($this->blockStartDate, $this->blockEndDate)->addPeriod($this->blockStartTime, $this->blockEndTime);
            }

            $command->save();
            $this->closeModal();
            $this->toast()->success('Blocked time added successfully.')->send();
        } catch (\Exception $e) {
            $this->closeModal();
            $this->toast()
                ->error('Failed to add blocked time: ' . $e->getMessage())
                ->send();
            return;
        }
    }

    public function closeModal()
    {
        Flux::modal('add-blocked')->close();
    }
};
?>

<form wire:submit.prevent="addBlock" class="space-y-5">
    <!-- Date Selection -->
    <div class="space-y-3">
        <div>
            <flux:label class="text-sm font-medium mb-1.5">{{ $isSameDay ? 'Date' : 'Start Date' }}</flux:label>
            <flux:input type="date" wire:model.live="blockStartDate" />
            <flux:error name="blockStartDate" />
        </div>

        @if (!$isSameDay)
            <div>
                <flux:label class="text-sm font-medium mb-1.5">End Date</flux:label>
                <flux:input type="date" wire:model="blockEndDate" />
                <flux:error name="blockEndDate" />
            </div>
        @endif

        <flux:checkbox wire:model.live="isSameDay" label="Single day only" />
    </div>

    <!-- Time Selection -->
    <div class="space-y-3">
        <flux:checkbox wire:model.live="isWholeDay" label="Block entire day" />

        @if (!$isWholeDay)
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <flux:label class="text-sm font-medium mb-1.5">Start Time</flux:label>
                    <flux:input type="time" wire:model="blockStartTime" />
                    <flux:error name="blockStartTime" />
                </div>
                <div>
                    <flux:label class="text-sm font-medium mb-1.5">End Time</flux:label>
                    <flux:input type="time" wire:model="blockEndTime" />
                    <flux:error name="blockEndTime" />
                </div>
            </div>
        @endif
    </div>

    <div>
        <flux:label class="text-sm font-medium mb-1.5">Reason</flux:label>
        <flux:input type="text" wire:model="blockScheduleReason"
            placeholder="e.g., Vacation, Meeting, Personal time" />
        <flux:error name="blockScheduleReason" />
    </div>

    <!-- Action Buttons -->
    <div class="flex gap-2 justify-end pt-3">
        <flux:button type="button" variant="ghost" wire:click="closeModal">
            Cancel
        </flux:button>

        <flux:button type="submit" variant="primary">
            Block Time
        </flux:button>
    </div>
</form>
