<?php

namespace App\Http\Controllers;

use App\Enums\ReminderDispatch\ReminderStatusEnum;
use App\Http\Requests\ReminderChannelToggleRequest;
use App\Http\Resources\ReminderDispatchResource;
use App\Models\ReminderDispatch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Throwable;

class ReminderController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function scheduled(): AnonymousResourceCollection
    {
        return ReminderDispatchResource::collection(ReminderDispatch::whereStatus(ReminderStatusEnum::Scheduled->value)
            ->whereNull('sent_at')
            ->get());
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function sent(): AnonymousResourceCollection
    {
        return ReminderDispatchResource::collection(ReminderDispatch::whereStatus(ReminderStatusEnum::Sent->value)
            ->whereNotNull('sent_at')
            ->get());
    }

    /**
     * @param ReminderDispatch $reminder
     * @param ReminderChannelToggleRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function toggleChannel(ReminderDispatch $reminder, ReminderChannelToggleRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $oldChannel = $reminder->channel;

            $reminder->update([
                'channel' => $request->channel,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Channel updated successfully.',
                'old_channel' => $oldChannel,
                'updated_channel' => $request->channel,
            ]);

        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function analytics()
    {
        return response()->json([
            'upcoming' => ReminderDispatch::countUpcoming(),
            'sent' => ReminderDispatch::countSent(),
            'failed' => ReminderDispatch::countFailed(),
        ]);
    }
}
