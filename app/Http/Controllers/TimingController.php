<?php

namespace App\Http\Controllers;

use App\Models\BlockedPeriod;
use App\Models\Booking;
use App\Models\OpeningRule;
use Illuminate\Http\Request;

class TimingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = [];

        $openingRules = OpeningRule::all();
        $blockedPeriods = BlockedPeriod::all();
        $bookings = Booking::all();

        // Map weekdays to FullCalendar numbers
        $weekMap = [
            'SUN' => 0,
            'MON' => 1,
            'TUE' => 2,
            'WED' => 3,
            'THU' => 4,
            'FRI' => 5,
            'SAT' => 6,
        ];

        // Opening rules → recurring background events
        foreach ($openingRules as $rule) {
            $events[] = [
                'title' => 'Open',
                'daysOfWeek' => [$weekMap[$rule->day_of_week]],
                'startTime' => $rule->opens_at,
                'endTime' => $rule->closes_at,
                'display' => 'background',
                'color' => '#2B7FFF', // green
            ];
        }

        // Blocked periods → regular events
        foreach ($blockedPeriods as $block) {
            $events[] = [
                'title' => $block->reason ?? 'Blocked',
                'start' => $block->starts_at,
                'end' => $block->ends_at,
                'color' => '#FB2B37', // red
            ];
        }

        // Bookings → regular events
        foreach ($bookings as $booking) {
            $events[] = [
                'title' => $booking->invitee_name,
                'start' => $booking->starts_at,
                'end' => $booking->ends_at,
                'color' => '#00C951', // blue
            ];
        }

        return view('pages.timings.⚡index', data: compact('events'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return view('pages.timings.⚡create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        if ($request['type'] == 'opening_rule') {

            $request->validate([
                'day_of_week' => 'required|string|in:MON,TUE,WED,THU,FRI,SAT,SUN',
                'opens_at' => 'required|date_format:H:i',
                'closes_at' => 'required|date_format:H:i|after:opens_at',
                'slot_duration_minutes' => 'required|integer|min:1',
                'buffer_before' => 'nullable|integer|min:0',
                'buffer_after' => 'nullable|integer|min:0',
            ]);

            OpeningRule::create([
                'day_of_week' => $request->day_of_week,
                'opens_at' => $request->opens_at,
                'closes_at' => $request->closes_at,
                'slot_duration_minutes' => $request->slot_duration_minutes,
                'buffer_before' => $request->buffer_before ?? 0,
                'buffer_after' => $request->buffer_after ?? 0,
            ]);

            return redirect()->route('timings.index')->with('success', 'Opening rule created successfully.');
        } elseif ($request['type'] == 'blocked_period') {

            $request->validate([
                'starts_at' => 'required|date',
                'ends_at' => 'required|date|after:starts_at',
                'reason' => 'nullable|string|max:255',
            ]);
            BlockedPeriod::create([
                'starts_at' => $request->starts_at,
                'ends_at' => $request->ends_at,
                'reason' => $request->reason,
            ]);

            return redirect()->route('timings.index')->with('success', 'Blocked period created successfully.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
