<?php

use Livewire\Component;

new class extends Component
{
    public $client;
    public $bookings;

    public function mount($client)
    {
        $this->client = \App\Models\Client::findOrFail($client);
        $this->bookings = $this->client->bookings()->latest()->get();
    }

    public function deleteBooking($bookingId)
    {
        $booking = \App\Models\Booking::findOrFail($bookingId);
        $booking->delete();

        $this->bookings = $this->client->bookings()->latest()->get();
        session()->flash('message', 'Booking deleted successfully.');
    }
};
?>

<x-layouts::app
    :title="$client->name"
    :breadcrumbs="[
        ['label' => __('Clients'), 'url' => route('clients.index')],
        ['label' => $client->name],
    ]"
>
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <!-- Header with Actions -->
        <div class="mb-8 flex items-start justify-between">
            <div class="flex items-center gap-4">
                <div class="h-20 w-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center shadow-lg">
                    <span class="text-white font-bold text-2xl">
                        {{ strtoupper(substr($client->name, 0, 2)) }}
                    </span>
                </div>
                <div>
                    <flux:heading size="xl" class="text-zinc-900 dark:text-white">
                        {{ $client->name }}
                    </flux:heading>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                        Client since {{ $client->created_at->format('F j, Y') }}
                    </p>
                </div>
            </div>
            <div class="flex gap-3">
                <flux:button
                    variant="outline"
                    href="{{ route('clients.edit', $client) }}"
                >
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                        {{ __("Edit") }}
                    </span>
                </flux:button>
                <flux:button
                    variant="primary"
                    href="{{ route('bookings.create', ['client' => $client->id]) }}"
                >
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ __("New Booking") }}
                    </span>
                </flux:button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Client Information Card -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-800 overflow-hidden">
                    <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Client Information</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Email -->
                        <div>
                            <label class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-2">
                                Email Address
                            </label>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <a href="mailto:{{ $client->email }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                    {{ $client->email }}
                                </a>
                            </div>
                        </div>

                        <!-- Secret Code -->
                        <div>
                            <label class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-2">
                                Secret Code
                            </label>
                            <div class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                </svg>
                                <span class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                    {{ $client->secret_code }}
                                </span>
                            </div>
                        </div>

                        <!-- Notes -->
                        @if($client->notes)
                            <div>
                                <label class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-2">
                                    Notes
                                </label>
                                <div class="text-sm text-zinc-600 dark:text-zinc-400 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-3 border border-zinc-200 dark:border-zinc-700">
                                    {{ $client->notes }}
                                </div>
                            </div>
                        @endif

                        <!-- Stats -->
                        <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-zinc-900 dark:text-white">
                                        {{ $bookings->count() }}
                                    </div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                        Total Bookings
                                    </div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                        {{ $bookings->where('status', 'confirmed')->count() }}
                                    </div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                        Confirmed
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bookings List -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-800 overflow-hidden">
                    <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Booking History</h3>
                            <span class="text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $bookings->count() }} {{ Str::plural('booking', $bookings->count()) }}
                            </span>
                        </div>
                    </div>

                    @if($bookings->count() > 0)
                        <div class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @foreach($bookings as $booking)
                                <div class="p-6 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3 mb-2">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <span class="text-lg font-semibold text-zinc-900 dark:text-white">
                                                        {{ \Carbon\Carbon::parse($booking->date)->format('F j, Y') }}
                                                    </span>
                                                </div>

                                                @php
                                                    $statusColors = [
                                                        'pending' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400',
                                                        'confirmed' => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400',
                                                        'cancelled' => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400',
                                                        'completed' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400',
                                                    ];
                                                @endphp

                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$booking->status] ?? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-400' }}">
                                                    {{ ucfirst($booking->status) }}
                                                </span>
                                            </div>

                                            @if($booking->notes)
                                                <div class="mt-2 text-sm text-zinc-600 dark:text-zinc-400 flex items-start gap-2">
                                                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                                    </svg>
                                                    <span>{{ $booking->notes }}</span>
                                                </div>
                                            @endif

                                            <div class="mt-3 text-xs text-zinc-500 dark:text-zinc-400">
                                                Created {{ $booking->created_at->diffForHumans() }}
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2 ml-4">
                                            <flux:button
                                                variant="ghost"
                                                size="sm"
                                                href="{{ route('bookings.edit', $booking) }}"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                                </svg>
                                            </flux:button>
                                            <flux:button
                                                variant="ghost"
                                                size="sm"
                                                class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"
                                                wire:click="deleteBooking({{ $booking->id }})"
                                                wire:confirm="Are you sure you want to delete this booking?"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </flux:button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-12 px-6">
                            <svg class="mx-auto h-12 w-12 text-zinc-400 dark:text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-zinc-900 dark:text-white">No bookings yet</h3>
                            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Get started by creating a booking for this client.</p>
                            <div class="mt-6">
                                <flux:button
                                    variant="primary"
                                    href="{{ route('bookings.create', ['client' => $client->id]) }}"
                                >
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        {{ __("Create First Booking") }}
                                    </span>
                                </flux:button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>
