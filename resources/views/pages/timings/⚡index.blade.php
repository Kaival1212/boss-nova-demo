<?php

use Livewire\Component;

new class extends Component
{
    public $events = [];

    public function mount()
    {
        // Fetch your events here
        // $this->events = ...
    }
};
?>

<x-layouts::app
    :title="__('Timings')"
    :breadcrumbs="[
        ['label' => __('Timings')],
    ]"
>
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="xl" class="text-zinc-900 dark:text-white">
                        {{ __("Booking Calendar") }}
                    </flux:heading>
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                        Manage your availability, opening hours, and blocked periods
                    </p>
                </div>
                <div class="flex gap-3">
                    <flux:button
                        variant="primary"
                        href="{{ route('timings.create') }}"
                        icon="plus"
                    >
                        {{ __("Add Rule") }}
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Calendar Card -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-800 overflow-hidden">
            <div class="relative">
                {{-- Loading overlay --}}
                <div
                    id="calendar-loading"
                    class="absolute inset-0 flex items-center justify-center bg-white/90 dark:bg-zinc-900/90 backdrop-blur-sm z-50 transition-opacity duration-200"
                >
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-4 border-zinc-200 border-t-blue-600 dark:border-zinc-700 dark:border-t-blue-500 mx-auto"></div>
                        <p class="mt-4 text-sm font-medium text-zinc-600 dark:text-zinc-400">Loading calendar...</p>
                    </div>
                </div>

                {{-- Calendar Container --}}
                <div id="calendar" class="p-6"></div>
            </div>
        </div>

        <!-- Legend -->
        <div class="mt-6 bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-800 p-4">
            <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">Legend</h3>
            <div class="flex flex-wrap gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded bg-blue-500"></div>
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Opening Hours</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded bg-red-500"></div>
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Blocked Period</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded bg-green-500"></div>
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Available Slots</span>
                </div>
            </div>
        </div>
    </div>

    {{-- FullCalendar CSS & JS --}}
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.20/main.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.20/index.global.min.js"></script>

    {{-- Custom Calendar Styles --}}
    <style>
        /* Modern Calendar Styling */
        #calendar {
            font-family: inherit;
        }

        /* Header styling */
        .fc .fc-toolbar-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: rgb(24 24 27);
        }

        @media (prefers-color-scheme: dark) {
            .fc .fc-toolbar-title {
                color: rgb(250 250 250);
            }
        }

        /* Button styling */
        .fc .fc-button-primary {
            background-color: rgb(59 130 246);
            border-color: rgb(59 130 246);
            color: white;
            font-weight: 500;
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            transition: all 0.2s;
        }

        .fc .fc-button-primary:hover {
            background-color: rgb(37 99 235);
            border-color: rgb(37 99 235);
        }

        .fc .fc-button-primary:not(:disabled):active,
        .fc .fc-button-primary:not(:disabled).fc-button-active {
            background-color: rgb(29 78 216);
            border-color: rgb(29 78 216);
        }

        /* Table styling */
        .fc-theme-standard th {
            background-color: rgb(244 244 245);
            border-color: rgb(228 228 231);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.025em;
            color: rgb(63 63 70);
        }

        @media (prefers-color-scheme: dark) {
            .fc-theme-standard th {
                background-color: rgb(39 39 42);
                border-color: rgb(63 63 70);
                color: rgb(228 228 231);
            }
        }

        .fc-theme-standard td {
            border-color: rgb(228 228 231);
        }

        @media (prefers-color-scheme: dark) {
            .fc-theme-standard td {
                border-color: rgb(63 63 70);
            }
        }

        /* Event styling */
        .fc-event {
            border-radius: 0.375rem;
            padding: 2px 4px;
            font-size: 0.875rem;
            font-weight: 500;
            border: none;
        }

        .fc-event-title {
            font-weight: 600;
        }

        /* Time grid styling */
        .fc .fc-timegrid-slot {
            height: 3rem;
        }

        .fc .fc-timegrid-slot-label {
            border-color: rgb(228 228 231);
        }

        @media (prefers-color-scheme: dark) {
            .fc .fc-timegrid-slot-label {
                border-color: rgb(63 63 70);
                color: rgb(161 161 170);
            }
        }

        /* Day header styling */
        .fc .fc-col-header-cell-cushion {
            padding: 0.75rem 0.5rem;
            color: rgb(24 24 27);
        }

        @media (prefers-color-scheme: dark) {
            .fc .fc-col-header-cell-cushion {
                color: rgb(250 250 250);
            }
        }

        /* Today highlight */
        .fc .fc-day-today {
            background-color: rgb(239 246 255) !important;
        }

        @media (prefers-color-scheme: dark) {
            .fc .fc-day-today {
                background-color: rgb(30 41 59) !important;
            }
        }

        /* Scrollbar styling */
        .fc-scroller::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .fc-scroller::-webkit-scrollbar-track {
            background: rgb(244 244 245);
            border-radius: 4px;
        }

        .fc-scroller::-webkit-scrollbar-thumb {
            background: rgb(161 161 170);
            border-radius: 4px;
        }

        .fc-scroller::-webkit-scrollbar-thumb:hover {
            background: rgb(113 113 122);
        }

        @media (prefers-color-scheme: dark) {
            .fc-scroller::-webkit-scrollbar-track {
                background: rgb(39 39 42);
            }

            .fc-scroller::-webkit-scrollbar-thumb {
                background: rgb(82 82 91);
            }

            .fc-scroller::-webkit-scrollbar-thumb:hover {
                background: rgb(113 113 122);
            }
        }

        /* Dark mode background */
        @media (prefers-color-scheme: dark) {
            .fc {
                background-color: rgb(24 24 27);
            }

            .fc .fc-scrollgrid {
                border-color: rgb(63 63 70);
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');
            const loadingEl = document.getElementById('calendar-loading');

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                buttonText: {
                    today: 'Today',
                    month: 'Month',
                    week: 'Week',
                    day: 'Day',
                    list: 'List'
                },
                allDaySlot: false,
                slotMinTime: "06:00:00",
                slotMaxTime: "22:00:00",
                slotDuration: "00:30:00",
                expandRows: true,
                stickyHeaderDates: true,
                nowIndicator: true,
                height: 'auto',
                contentHeight: 650,
                aspectRatio: 1.8,
                events: @json($events ?? []),
                eventDisplay: 'block',
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    meridiem: 'short'
                },
                slotLabelFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    meridiem: 'short'
                },
                eventClick: function(info) {
                    // Handle event click
                    console.log('Event clicked:', info.event);
                    // You can open a modal or navigate to edit page
                },
                loading: function(isLoading) {
                    if (isLoading) {
                        loadingEl.style.display = 'flex';
                    } else {
                        setTimeout(() => {
                            loadingEl.style.display = 'none';
                        }, 300);
                    }
                },
                eventClassNames: function(arg) {
                    // Add custom classes based on event type
                    return ['shadow-sm', 'hover:shadow-md', 'transition-shadow', 'cursor-pointer'];
                }
            });

            calendar.render();

            // Responsive behavior
            window.addEventListener('resize', function() {
                if (window.innerWidth < 768) {
                    calendar.changeView('timeGridDay');
                } else {
                    calendar.changeView('timeGridWeek');
                }
            });
        });
    </script>
</x-layouts::app>
