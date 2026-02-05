<?php

use Livewire\Component;
use Livewire\Attributes\On;
use Zap\Models\Schedule;
use TallStackUi\Traits\Interactions;
use Zap\Enums\ScheduleTypes;
use App\Models\Client;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ScheduleTimeChangedNotification;

new class extends Component {
    use Interactions;

    public $availability;
    public $blocked;
    public $appointments;

    public $events = [];
    public $businessHours = [];
    public $pendingTimeProps;

    public $scheduleChangeReason = '';
    public $notifyUser = false;

    public function mount($availability, $blocked, $appointments)
    {
        $this->availability = $availability;
        $this->blocked = $blocked;
        $this->appointments = $appointments;
        $this->prepareEvents();
    }
    #[On('request-time-change')]
    public function requestTimeChange($props)
    {
        $this->pendingTimeProps = $props;
        Flux::modal('timechange-confirmation')->show();
    }

    public function convertFrequencyConfig($frequency_config)
    {
        $daysMap = [
            'Sunday' => 0,
            'Monday' => 1,
            'Tuesday' => 2,
            'Wednesday' => 3,
            'Thursday' => 4,
            'Friday' => 5,
            'Saturday' => 6,
        ];

        $daysOfWeek = [];

        foreach ($frequency_config->toArray()['days'] as $day) {
            $daysOfWeek[] = $daysMap[$day];
        }

        return $daysOfWeek;
    }

    public function prepareEvents()
    {
        $events = [];
        $businessHours = [];

        foreach ($this->availability as $item) {
            if ($item->is_active == 0) {
                continue;
            } else {
                $businessHours[] = [
                    'id' => $item->id,
                    'title' => $item->name,
                    'daysOfWeek' => $this->convertFrequencyConfig($item->frequency_config),
                    'startTime' => $item->periods()->get()->first()->start_time,
                    'endTime' => $item->periods()->get()->first()->end_time,
                    'color' => '#22c55e', // Green-500
                    'display' => 'background',
                ];
            }
        }

        foreach ($this->blocked as $item) {
            $period = $item->periods()->first();
            $start = $item->start_date->copy()->setTimeFromTimeString($period->start_time);
            $end = $item->end_date?->copy()->setTimeFromTimeString($period->end_time) ?? $item->start_date->copy()->setTimeFromTimeString($period->end_time);

            $events[] = [
                'id' => $item->id,
                'title' => $item->name,
                'start' => $start->toIso8601String(),
                'end' => $end->toIso8601String(),
                'color' => '#e35959', // red but transparent
                'durationEditable' => true,
                'startEditable' => true,
                'resourceEditable' => true,
                'overlap' => false,
                'editable' => true,
            ];
        }

        foreach ($this->appointments as $item) {
            $period = $item->periods()->first();
            $start = $item->start_date->copy()->setTimeFromTimeString($period->start_time);
            $end = $item->end_date?->copy()->setTimeFromTimeString($period->end_time) ?? $item->start_date->copy()->setTimeFromTimeString($period->end_time);

            $events[] = [
                'id' => $item->id,
                'title' => $item->name . ' notes -' . $item->metadata['notes'],
                'start' => $start->toIso8601String(),
                'end' => $end->toIso8601String(),
                'color' => '#3b82f6', // Blue-500
                'durationEditable' => true,
                'startEditable' => true,
                'resourceEditable' => true,
                'overlap' => false,
                'editable' => true,
            ];
        }

        $this->events = $events;
        $this->businessHours = $businessHours;
    }

    #[On('schedule-updated')]
    public function refreshCalendar()
    {
        $this->prepareEvents();
        $this->dispatch('calendar-refresh', events: $this->events);
    }

    public function confirmTimeChange()
    {
        $id = $this->pendingTimeProps['id'];
        $newStartTime = Carbon\Carbon::parse($this->pendingTimeProps['start'])->setTimezone(config('app.timezone'))->toTimeString();

        $newEndTime = Carbon\Carbon::parse($this->pendingTimeProps['end'])->setTimezone(config('app.timezone'))->toTimeString();

        $newStartDate = Carbon\Carbon::parse($this->pendingTimeProps['start'])->setTimezone(config('app.timezone'))->toDateString();
        $newEndDate = Carbon\Carbon::parse($this->pendingTimeProps['end'])->setTimezone(config('app.timezone'))->toDateString();

        $schedule = Schedule::find($id);

        if ($this->notifyUser && $schedule->schedule_type === ScheduleTypes::APPOINTMENT) {
            $client = Client::where('email', $schedule->metadata['client_email'])->first();
            $client->notify(new ScheduleTimeChangedNotification($schedule, $this->pendingTimeProps['old_start'], $this->pendingTimeProps['start'], $this->scheduleChangeReason));
        }

        $schedule->update([
            'start_date' => $newStartDate,
            'end_date' => $newEndDate,
        ]);

        $period = $schedule->periods()->first();
        $period->update([
            'start_time' => $newStartTime,
            'end_time' => $newEndTime,
        ]);

        Flux::modal('timechange-confirmation')->close();
        $this->toast()->success('Schedule updated successfully.')->send();
    }
};
?>

<div>
    <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-800 overflow-hidden"
        wire:ignore>
        <div class="relative">
            {{-- Calendar Container --}}
            <div id="calendar" class="p-6"></div>
        </div>
    </div>

    <!-- Legend -->
    <div class="mt-6 bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-800 p-4">
        <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">
            Legend
        </h3>
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

    <flux:modal name="timechange-confirmation" class="md:w-96">
        <div class="flex flex-col gap-4">
            <h2 class="text-lg font-semibold">Confirm Time Change</h2>

            @if ($pendingTimeProps)
                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                    Are you sure you want to change
                    <strong>{{ $pendingTimeProps['title'] }}</strong>
                    from
                    <strong>{{ Carbon\Carbon::parse($pendingTimeProps['old_start'])->setTimezone(config('app.timezone'))->format('j M g:i A') }}</strong>
                    to
                    <strong>{{ Carbon\Carbon::parse($pendingTimeProps['end'])->setTimezone(config('app.timezone'))->format('j M g:i A') }}</strong>?
                </p>
            @endif

            <flux:field>
                <flux:label for="reason">Reason for Change (optional)</flux:label>
                <flux:input type="text" id="reason" wire:model="scheduleChangeReason"
                    placeholder="Enter reason for time change" />
            </flux:field>

            @if ($pendingTimeProps && strpos($pendingTimeProps['title'], 'Appointment') !== false)
                <flux:field>
                    <flux:label>Notify User about this Change</flux:label>
                    <flux:checkbox wire:model="notifyUser" />
                </flux:field>
            @endif

            <div class="flex justify-end gap-2 mt-4">
                <flux:button variant="outline"
                    wire:click="$dispatch('close-modal', { name: 'timechange-confirmation' })">
                    Cancel
                </flux:button>

                <flux:button variant="primary" wire:click="confirmTimeChange">
                    Confirm
                </flux:button>
            </div>
        </div>
    </flux:modal>

</div>
</div>

{{-- Custom Calendar Styles --}}
<style>
    /* ================================
   Flux-Aligned FullCalendar Theme
   ================================ */

    #calendar {
        font-family: var(--font-sans);
        color: var(--color-zinc-100);
    }

    /* ---------- Header ---------- */

    .fc .fc-toolbar {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--color-zinc-800);
        background: transparent;
    }

    .fc .fc-toolbar-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--color-zinc-100);
    }

    /* ---------- Buttons ---------- */

    .fc .fc-button {
        background: transparent;
        border: 1px solid var(--color-zinc-700);
        color: var(--color-zinc-200);
        border-radius: 0.5rem;
        padding: 0.4rem 0.75rem;
        font-weight: 500;
        transition: all 0.15s ease;
    }

    .fc .fc-button:hover {
        background: var(--color-zinc-800);
        border-color: var(--color-zinc-600);
    }

    .fc .fc-button-primary,
    .fc .fc-button-active {
        background: var(--color-accent);
        border-color: var(--color-accent);
        color: var(--color-accent-foreground);
    }

    /* ---------- Column Headers ---------- */

    .fc-theme-standard th {
        background: transparent;
        border-bottom: 1px solid var(--color-zinc-800);
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.05em;
        font-weight: 600;
        color: var(--color-zinc-400);
    }

    /* ---------- Grid ---------- */

    .fc-theme-standard td {
        border-color: var(--color-zinc-800);
    }

    /* ---------- Time Column ---------- */

    .fc .fc-timegrid-slot-label {
        font-size: 0.75rem;
        color: var(--color-zinc-400);
    }

    /* ---------- Today ---------- */

    .fc .fc-day-today {
        background: color-mix(in srgb,
                var(--color-accent-foreground) 12%,
                transparent) !important;
    }

    /* ---------- Events ---------- */

    .fc-event {
        background: var(--color-accent);
        color: var(--color-accent-foreground);
        border: none;
        border-radius: 0.5rem;
        padding: 0.25rem 0.4rem;
        font-size: 0.75rem;
        font-weight: 600;
        box-shadow: 0 2px 6px rgb(0 0 0 / 0.2);
        transition: transform 0.12s ease, box-shadow 0.12s ease;
    }

    .fc-event:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgb(0 0 0 / 0.3);
    }

    .fc-event-title {
        font-weight: 600;
    }

    .fc-non-business {
        background: color-mix(in srgb,
                var(--color-accent) 20%,
                transparent);
    }

    /* ---------- List View ---------- */

    .fc-list {
        border: none;
    }

    .fc-list-day-cushion {
        padding: 0.75rem 1rem;
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        background: transparent;
        color: var(--color-zinc-400);
    }

    .fc-list-event {
        margin: 0.5rem 1rem;
        padding: 0.5rem 0.75rem;
        border-radius: 0.75rem;
        border: 1px solid var(--color-zinc-800);
        background: var(--color-zinc-900);
    }

    .fc-list-event:hover {
        background: color-mix(in srgb,
                var(--color-accent) 10%,
                transparent);
    }

    /* ---------- Mobile ---------- */

    @media (max-width: 768px) {
        .fc .fc-toolbar {
            flex-direction: column;
            gap: 0.5rem;
        }
    }
</style>



@script
    <script>
        document.addEventListener('livewire:navigated', () => {
            initCalendar();
        });

        // Also run on first load
        document.addEventListener('livewire:load', () => {
            initCalendar();
        });

        function initCalendar() {
            const calendarEl = document.getElementById('calendar');
            const loadingEl = document.getElementById('calendar-loading');

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                buttonText: {
                    today: 'Today',
                    month: 'Month',
                    week: 'Week',
                    day: 'Day',
                },
                allDaySlot: false,
                slotDuration: "00:20:00",
                businessHours: @json($businessHours ?? []),
                expandRows: true,
                stickyHeaderDates: true,
                nowIndicator: true,
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
                    console.log('Event clicked:', info.event);
                },
                eventDrop: function(info) {
                    console.log('Event dropped:', info.event);

                    Livewire.dispatch('request-time-change', {
                        props: {
                            id: info.event.id,
                            title: info.event.title,
                            old_start: info.oldEvent.start.toISOString(),
                            old_end: info.oldEvent.end.toISOString(),
                            start: info.event.start.toISOString(),
                            end: info.event.end.toISOString()
                        }
                    });
                },
                eventResize: function(info) {

                    Livewire.dispatch('request-time-change', {
                        props: {
                            id: info.event.id,
                            title: info.event.title,
                            old_start: info.oldEvent.start.toISOString(),
                            old_end: info.oldEvent.end.toISOString(),
                            start: info.event.start.toISOString(),
                            end: info.event.end.toISOString()
                        }
                    });
                },

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
        }
    </script>
@endscript
