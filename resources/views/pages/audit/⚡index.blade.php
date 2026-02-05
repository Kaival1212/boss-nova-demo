<?php

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

new class extends Component {
    use WithPagination;

    // Search and filters
    public string $search = '';

    // Sorting
    public array $sort = [
        'column' => 'created_at',
        'direction' => 'desc',
    ];

    // Reset pagination when filters change
    public function updatedSearch()
    {
        $this->resetPage();
    }

    // Clear all filters
    public function clearFilters()
    {
        $this->search = '';

        $this->resetPage();
    }

    public function render()
    {
        // Start building the query
        $query = Activity::query()
            ->with(['causer', 'subject'])
            ->orderBy($this->sort['column'], $this->sort['direction']);

        // Apply search if provided
        if ($this->search) {
            $query
                ->where('description', 'like', '%' . $this->search . '%')
                ->orWhere('log_name', 'like', '%' . $this->search . '%')
                ->orWhere('subject_type', 'like', '%' . $this->search . '%');
        }

        // Get the activities
        $activities = $query->paginate(20);

        return $this->view()->with([
            'activities' => $activities,
        ]);
    }
};
?>

<div class="space-y-6">
    {{-- Page Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Audit Logs</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Track who did what, to whom, and when in your system
        </p>
    </div>


    {{-- Filters --}}
    <x-ts-card>
        <div class="space-y-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Filters</h3>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                {{-- Search --}}
                <x-ts-input wire:model.live.debounce.300ms="search" placeholder="Search..." label="Search" />
            </div>
        </div>
    </x-ts-card>

    {{-- Activities List --}}
    <x-ts-card>
        @if ($activities->count() > 0)
            <div class="space-y-4">
                @foreach ($activities as $activity)
                    <div class="border-b border-gray-200 pb-4 last:border-0 dark:border-gray-700">
                        {{-- WHO DID WHAT TO WHOM AND WHEN --}}
                        <div class="flex items-start gap-4">
                            {{-- User Avatar/Icon --}}
                            <div class="flex-shrink-0">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                                    <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                                        @if ($activity->causer)
                                            {{ strtoupper(substr($activity->causer->name ?? 'U', 0, 2)) }}
                                        @else
                                            SY
                                        @endif
                                    </span>
                                </div>
                            </div>

                            {{-- Activity Description --}}
                            <div class="flex-1 min-w-0">
                                {{-- Main sentence: WHO did WHAT to WHOM --}}
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{-- WHO --}}
                                    <span class="font-semibold">
                                        @if ($activity->causer)
                                            {{ $activity->causer->name ?? 'Unknown User' }}
                                        @else
                                            System
                                        @endif
                                    </span>

                                    {{-- WHAT (action) --}}
                                    @if ($activity->description == 'created')
                                        <span class="text-green-600 dark:text-green-400 font-medium"> created </span>
                                    @elseif($activity->description == 'updated')
                                        <span class="text-blue-600 dark:text-blue-400 font-medium"> updated </span>
                                    @elseif($activity->description == 'deleted')
                                        <span class="text-red-600 dark:text-red-400 font-medium"> deleted </span>
                                    @else
                                        <span class="font-medium"> {{ $activity->description }} </span>
                                    @endif

                                    {{-- WHOM (subject) --}}
                                    <span class="font-semibold">
                                        {{ class_basename($activity->subject_type) }}
                                        @if ($activity->subject_id)
                                            <span class="text-gray-500 dark:text-gray-400">#{{ $activity->subject_id }}</span>
                                        @endif
                                    </span>
                                </div>

                                {{-- WHEN --}}
                                <div class="mt-1 flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                    <span>{{ $activity->created_at->format('M d, Y') }} at {{ $activity->created_at->format('h:i A') }}</span>
                                    <span>•</span>
                                    <span>{{ $activity->created_at->diffForHumans() }}</span>
                                </div>

                                {{-- CHANGES (expandable) --}}
                                @if ($activity->properties && count($activity->properties) > 0)
                                    <div class="mt-2">
                                        <details class="group">
                                            <summary class="cursor-pointer text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400 flex items-center gap-1">
                                                <svg class="h-4 w-4 transition-transform group-open:rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                                View what changed
                                            </summary>
                                            <div class="mt-2 ml-5 rounded-lg bg-gray-50 p-3 text-xs dark:bg-gray-800">
                                                {{-- Show old and new values if available --}}
                                                @if (isset($activity->properties['old']) && isset($activity->properties['attributes']))
                                                    <div class="space-y-3">
                                                        @foreach ($activity->properties['attributes'] as $key => $newValue)
                                                            @php
                                                                $oldValue = $activity->properties['old'][$key] ?? null;
                                                            @endphp
                                                            @if ($oldValue != $newValue)
                                                                <div class="border-l-2 border-gray-300 pl-3 dark:border-gray-600">
                                                                    <div class="font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                                                        {{ ucfirst(str_replace('_', ' ', $key)) }}
                                                                    </div>
                                                                    <div class="space-y-1">
                                                                        <div class="flex items-start gap-2">
                                                                            <span class="text-red-600 dark:text-red-400 font-medium">From:</span>
                                                                            <span class="text-gray-600 dark:text-gray-400">{{ $oldValue ?? 'Empty' }}</span>
                                                                        </div>
                                                                        <div class="flex items-start gap-2">
                                                                            <span class="text-green-600 dark:text-green-400 font-medium">To:</span>
                                                                            <span class="text-gray-900 dark:text-white font-medium">{{ $newValue ?? 'Empty' }}</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @else
                                                    {{-- Fallback: show raw JSON --}}
                                                    <pre class="overflow-x-auto text-gray-700 dark:text-gray-300">{{ json_encode($activity->properties, JSON_PRETTY_PRINT) }}</pre>
                                                @endif
                                            </div>
                                        </details>
                                    </div>
                                @endif
                            </div>

                            {{-- Action Badge --}}
                            <div class="flex-shrink-0">
                                @if ($activity->description == 'created')
                                    <x-ts-badge color="green" text="Created" />
                                @elseif($activity->description == 'updated')
                                    <x-ts-badge color="blue" text="Updated" />
                                @elseif($activity->description == 'deleted')
                                    <x-ts-badge color="red" text="Deleted" />
                                @else
                                    <x-ts-badge color="gray" text="{{ ucfirst($activity->description) }}" />
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-4 border-t border-gray-200 pt-4 dark:border-gray-700">
                {{ $activities->links() }}
            </div>
        @else
            {{-- Empty State --}}
            <div class="py-12 text-center">
                <p class="text-gray-500 dark:text-gray-400">No activities found</p>
                @if ($search)
                    <x-ts-button wire:click="clearFilters" color="primary" class="mt-4">
                        Clear Filters
                    </x-ts-button>
                @endif
            </div>
        @endif
    </x-ts-card>
</div>
