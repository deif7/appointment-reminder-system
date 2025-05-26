<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Appointment\StoreAppointmentRequest;
use App\Http\Requests\AppointmentStatusUpdateRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Services\Appointment\AppointmentService;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Throwable;

class AppointmentController extends Controller
{
    public function __construct(public AppointmentService $service)
    {
    }

    /**
     * @throws Throwable
     */
    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->service
                ->store($request->validated())
                ->scheduleReminders();
            DB::commit();

            return response()->json([
                'message' => 'Appointment created successfully for your client.',
                'appointment' => $this->service->getAppointment()->load('recurrences'),
            ], 201);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create appointment.',
                'error' => $e->getMessage(),
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


    public function updateStatus(AppointmentStatusUpdateRequest $request, Appointment $appointment)
    {
        $this->service
            ->setAppointment($appointment)
            ->updateStatus($request->get('status'));;

        return response()->json([
            'message' => 'Appointment status updated successfully.',
            'appointment' => $this->service->getAppointment(),
        ]);
    }

}
