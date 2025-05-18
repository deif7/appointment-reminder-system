<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Appointment\StoreAppointmentRequest;
use App\Services\Appointment\AppointmentService;

class AppointmentController extends Controller
{

    public function __construct(public AppointmentService $service)
    {
    }

    public function store(StoreAppointmentRequest $request)
    {
        $this->service
            ->store($request->validated())
            ->scheduleReminder();

        return response()->json([
            'message' => 'Appointment created successfully for your client.',
            'appointment' => $this->service->getAppointment()
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
