<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<x-layouts::app :title="__('Create Timing Rules')">
    <div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="space-y-8">

            <!-- Opening Hours Rule Section -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-zinc-800 dark:to-zinc-800 px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                    <h2 class="text-xl font-semibold text-zinc-900 dark:text-white">Opening Hours Rule</h2>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">Define your business operating hours and slot configuration</p>
                </div>

                <form method="POST" action="{{ route('timings.store') }}" class="p-6 space-y-6">
                    @csrf
                    <flux:input type="hidden" name="type" value="opening_rule" />

                    <!-- Day and Time Selection -->
                    <div>
                        <h3 class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-4">Schedule</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <flux:select name="day_of_week" label="Day of Week" required>
                                @foreach(['MON' => 'Monday', 'TUE' => 'Tuesday', 'WED' => 'Wednesday', 'THU' => 'Thursday', 'FRI' => 'Friday', 'SAT' => 'Saturday', 'SUN' => 'Sunday'] as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </flux:select>
                            <flux:input
                                type="time"
                                name="opens_at"
                                label="Opens At"
                                placeholder="09:00"
                                required
                            />
                            <flux:input
                                type="time"
                                name="closes_at"
                                label="Closes At"
                                placeholder="17:00"
                                required
                            />
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-zinc-200 dark:border-zinc-700"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-3 bg-white dark:bg-zinc-900 text-zinc-500 dark:text-zinc-400 font-medium">Slot Configuration</span>
                        </div>
                    </div>

                    <!-- Slot Settings -->
                    <div>
                        <h3 class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-4">Appointment Settings</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <flux:input
                                type="number"
                                name="slot_duration_minutes"
                                label="Slot Duration (minutes)"
                                placeholder="30"
                                min="1"
                                required
                            />
                            <flux:input
                                type="number"
                                name="buffer_before"
                                label="Buffer Before (minutes)"
                                placeholder="0"
                                min="0"
                            />
                            <flux:input
                                type="number"
                                name="buffer_after"
                                label="Buffer After (minutes)"
                                placeholder="0"
                                min="0"
                            />
                        </div>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-2">Buffer time adds padding before and after each appointment slot</p>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end pt-4">
                        <flux:button type="submit" variant="primary">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                {{ __('Save Opening Rule') }}
                            </span>
                        </flux:button>
                    </div>
                </form>
            </div>

            <!-- Blocked Period Section -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 overflow-hidden">
                <div class="bg-gradient-to-r from-red-50 to-rose-50 dark:from-zinc-800 dark:to-zinc-800 px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                    <h2 class="text-xl font-semibold text-zinc-900 dark:text-white">Block Time Period</h2>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">Create unavailable time slots for holidays, breaks, or maintenance</p>
                </div>

                <form method="POST" action="{{ route('timings.store') }}" class="p-6 space-y-6">
                    @csrf
                    <flux:input type="hidden" name="type" value="blocked_period" />

                    <!-- Date and Time Range -->
                    <div>
                        <h3 class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-4">Time Period</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <flux:input
                                type="datetime-local"
                                name="starts_at"
                                label="Start Date & Time"
                                required
                            />
                            <flux:input
                                type="datetime-local"
                                name="ends_at"
                                label="End Date & Time"
                                required
                            />
                        </div>
                    </div>

                    <!-- Reason -->
                    <div>
                        <flux:input
                            type="text"
                            name="reason"
                            label="Reason (optional)"
                            placeholder="e.g., Holiday, Maintenance, Team Meeting"
                        />
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-2">Provide a reason to help identify this blocked period later</p>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end pt-4">
                        <flux:button type="submit" variant="danger">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                </svg>
                                {{ __('Save Blocked Period') }}
                            </span>
                        </flux:button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-layouts::app>
