<?php

namespace App\Http\Controllers;

use App\Enums\ReminderDispatch\ReminderChannelEnum;
use App\Enums\ReminderDispatch\ReminderStatusEnum;
use App\Models\ReminderDispatch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

class ReminderController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function scheduled()
    {
        return response()->json(
            ReminderDispatch::whereStatus(ReminderStatusEnum::Scheduled->value)
                ->whereNull('sent_at')
                ->get()
        );
    }

    /**
     * @return JsonResponse
     */
    public function sent()
    {
        return response()->json(
            ReminderDispatch::whereStatus(ReminderStatusEnum::Sent->value)
                ->whereNotNull('sent_at')
                ->get()
        );
    }

    /**
     * @param ReminderDispatch $reminder
     * @param Request $request
     * @return void
     * @throws Throwable
     */
    public function toggleChannel(ReminderDispatch $reminder, Request $request): JsonResponse
    {
        $request->validate([
            'channel' => [
                'required',
                Rule::in(array_column(ReminderChannelEnum::cases(), 'value')),
            ],
        ]);

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
}
