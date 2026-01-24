<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Booking;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Zap;

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
            'email' => 'required|email',
            'notes' => 'nullable|string',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|string',
            'doctor_id' => 'required|integer|exists:users,id',
            'end_time' => 'required|string',
        ]);

        // 2 digits secret code generation
        $secret_code = rand(10, 99);

        $client = Client::where('email', $request->email)->first();
        if ($client) {
            $secret_code = $client->secret_code;
        } else {
            $client = new Client;
            $client->name = $request->name;
            $client->email = $request->email;
            $client->secret_code = $secret_code;
            $client->save();
        }

        $doctor = User::find($request->doctor_id);

        try {

            $zap = Zap::for($doctor)->named('Appointment by - '.$client->name)->appointment()->from($request->date)->addPeriod($request->time, $request->end_time)->withMetaData([
                'client_name' => $client->name,
                'client_email' => $client->email,
                'notes' => $request->notes,
            ])->save();

            Booking::create([
                'client_id' => $client->id,
                'notes' => $request->notes,
                'date' => $request->date.' '.$request->time,
                'doctor_id' => $request->doctor_id,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create booking: '.$e->getMessage()], 500);
        }

        return response()->json(['secret_code' => $secret_code, 'booking' => $zap], 201);

    }

    public function getLatestAvailableBookingTimes(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:users,id',
        ]);

        $id = $request->id;
        $user = User::find($id);

        $data = $user->availabilitySchedules()->get();
        $data = $data->map(function ($schedule) {
            return [
                'name' => $schedule->name,
                'description' => $schedule->description,
                'is_recurring' => $schedule->is_recurring,
                'frequency' => $schedule->frequency,
                'frequency_config' => $schedule->frequency_config,
                'start_time' => $schedule->periods->first()->start_time,
                'end_time' => $schedule->periods->first()->end_time,
            ];
        });

        return response()->json(['data' => $data], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function getdoctors()
    {
        $doctors = User::where('type', 'staff')->get();
        $data = UserResource::collection($doctors);

        return response()->json(['doctors' => $data], 200);
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
