<?php

use Livewire\Component;


class Timings extends Component
{


}

?>

<x-layouts::app
    :title="__('Timings')"
    :breadcrumbs="[
        ['label' => __('Timings')],
    ]"
>
    <div class="p-4 relative">
        <flux:heading size="lg">{{ __("Booking Calendar") }}</flux:heading>

        <div class="flex gap-2 mb-4">
            <flux:button color="primary" href="{{ route('timings.create') }}">
                {{ __("Add Rule") }}
            </flux:button>
        </div>

        {{-- Loading overlay --}}
        <div
            id="calendar-loading"
            class="absolute inset-0 flex items-center justify-center bg-white/80 z-10"
        >
            <div
                class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"
            ></div>
        </div>

        <div id="calendar" class="mt-4"></div>
    </div>

    {{-- FullCalendar CSS & JS --}}
    <link
        href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.20/main.min.css"
        rel="stylesheet"
    />
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.20/index.global.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');
            const loadingEl = document.getElementById('calendar-loading');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                allDaySlot: false,
                slotMinTime: "08:00:00",
                slotMaxTime: "20:00:00",
                events: @json($events),
                loading: function(isLoading) {
                    // FullCalendar fires this callback when fetching/rendering events
                    if (isLoading) {
                        loadingEl.style.display = 'flex';
                    } else {
                        loadingEl.style.display = 'none';
                    }
                }
            });

            calendar.render();
        });
    </script>
</x-layouts::app>
