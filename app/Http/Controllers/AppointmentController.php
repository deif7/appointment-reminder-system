<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\Appointment\StoreAppointmentRequest;
use App\Models\Client;
use App\Services\Appointment\AppointmentService;

class AppointmentController extends Controller
{

    public function __construct(public AppointmentService $service)
    {
    }

    public function store(StoreAppointmentRequest $request)
    {
        $user = auth()->user();

        Client::where('id', $request->client_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $appointment = $this->service->store($request->validated(), $user->id);

        return response()->json([
            'message' => 'Appointment created successfully for your client.',
            'data' => $appointment,
        ], 201);
    }

    public function upcoming()
    {
        $user = auth()->user();

        $appointments = $this->service->getUpcomingAppointments($user);

        return response()->json($appointments);
    }

    public function past()
    {
        $user = auth()->user();

        $appointments = $this->service->getPastAppointments($user);

        return response()->json($appointments);
    }

}
