<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Appointment\StoreAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Services\Appointment\AppointmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Throwable;

class AppointmentController extends Controller
{
    public function __construct(public AppointmentService $service)
    {
    }

    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        try {
            $this->service
                ->store($request->validated())
                ->scheduleReminder();

            return response()->json([
                'message' => 'Appointment created successfully for your client.',
                'appointment' => $this->service->getAppointment()
            ], 201);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Failed to create appointment.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function upcoming(): AnonymousResourceCollection|JsonResponse
    {
        try {
            $user = auth()->user();
            $appointments = $this->service->getUpcomingAppointments($user);

            return AppointmentResource::collection($appointments);

        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Failed to fetch upcoming appointments.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function past(): AnonymousResourceCollection|JsonResponse
    {
        try {
            $user = auth()->user();
            $appointments = $this->service->getPastAppointments($user);

            return AppointmentResource::collection($appointments);

        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Failed to fetch past appointments.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
