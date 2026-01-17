<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<x-layouts::app
    :title="__('Create Timing Rule')"
>

    <form method="POST" action="{{ route('timings.store') }}">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <flux:select name="day_of_week" label="Day of Week">
                @foreach(['MON','TUE','WED','THU','FRI','SAT','SUN'] as $day)
                    <option value="{{ $day }}">{{ $day }}</option>
                @endforeach
            </flux:select>
            <flux:input type="hidden" name="type" class="hidden" value="opening_rule" />
            <flux:input type="time" name="opens_at" label="Opens At" required />
            <flux:input type="time" name="closes_at" label="Closes At" required />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
            <flux:input type="number" name="slot_duration_minutes" label="Slot Duration (min)" required />
            <flux:input type="number" name="buffer_before" label="Buffer Before (min)" />
            <flux:input type="number" name="buffer_after" label="Buffer After (min)" />
        </div>

        <flux:button type="submit" color="primary" class="mt-4">
            {{ __('Save Opening Rule') }}
        </flux:button>
    </form>


    <form method="POST" action="{{ route('timings.store') }}">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <flux:input type="hidden" class="hidden" name="type" value="blocked_period" />
            <flux:input type="datetime-local" name="starts_at" label="Start" required />
            <flux:input type="datetime-local" name="ends_at" label="End" required />
            <flux:input type="text" name="reason" label="Reason" />
        </div>

        <flux:button type="submit" color="danger" class="mt-4">
            {{ __('Save Blocked Period') }}
        </flux:button>
    </form>



</x-layouts::app>
