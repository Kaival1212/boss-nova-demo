<?php

namespace App\Http\Controllers;

use App\Models\BlockedPeriod;
use App\Models\Booking;
use App\Models\Client;
use App\Models\OpeningRule;
use Illuminate\Http\Request;

class ApiBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function getBooking(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'secret_code' => 'required|digits:2',
        ]);

        $client = Client::where('name', $request->name)
            ->where('secret_code', $request->secret_code)
            ->first();
        if (! $client) {
            return response()->json(['message' => 'Client not found or invalid secret code.'], 404);
        }

        $bookings = Booking::where('client_id', $client->id)->get();

        return response()->json(['bookings' => $bookings], 200);

    }

    public function createBooking(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'notes' => 'nullable|string',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|string',
        ]);

        // 2 digits secret code generation
        $secret_code = rand(10, 99);

        $client = Client::create([
            'name' => $request->name,
            'email' => $request->email,
            'notes' => $request->notes,
            'secret_code' => $secret_code,
        ]);

        Booking::create([
            'client_id' => $client->id,
            'date' => $request->date.' '.$request->time,
        ]);

        return response()->json(['secret_code' => $secret_code], 201);

    }

    public function getLatestAvailableBookingTimes()
    {
        $openingRules = OpeningRule::all()->map(function ($rule) {
            return [
                'day_of_week' => $rule->day_of_week, // MON, TUE, etc.
                'opens_at' => $rule->opens_at,    // 09:00
                'closes_at' => $rule->closes_at,   // 17:00
                'slot_duration' => $rule->slot_duration_minutes,
                'buffer_before' => $rule->buffer_before ?? 0,
                'buffer_after' => $rule->buffer_after ?? 0,
            ];
        });

        $blockedPeriods = BlockedPeriod::all()->map(function ($block) {
            return [
                'starts_at' => $block->starts_at, // ISO string
                'ends_at' => $block->ends_at,   // ISO string
                'reason' => $block->reason ?? 'Blocked',
            ];
        });

        return response()->json([
            'opening_rules' => $openingRules,
            'blocked_periods' => $blockedPeriods,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        //
    }
}
