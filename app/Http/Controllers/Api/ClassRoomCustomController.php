<?php

namespace App\Http\Controllers\Api;

use App\Events\UserJoined;
use App\Events\WebRtcSignal;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ClassRoomCustomController extends Controller
{
    /**
     * Join a presence channel room and notify others.
     * Body: { classroom_id: string, uid: string, name?: string }
     */
    public function join(Request $request)
    {
        $data = $request->validate([
            'classroom_id' => 'required|string',
            'uid' => 'required|string',
            'name' => 'nullable|string',
        ]);

        // Broadcast join event so the other peer can initiate offer/answer
        broadcast(new UserJoined($data['classroom_id'], [
            'uid' => $data['uid'],
            'name' => $data['name'] ?? 'Guest',
        ]));

        return response()->json(['success' => true]);
    }

    /**
     * Relay WebRTC signals via Laravel WebSockets.
     * Body: { classroom_id: string, from: string, to: string, signal: array }
     */
    public function signal(Request $request)
    {
        try {
            $data = $request->validate([
                'classroom_id' => 'required|integer',
                'from' => 'required|string',
                'to' => 'required|string',
                'signal' => 'required|array',
            ]);

            // Log::info('Broadcasting signal', $data);

            broadcast(new WebRtcSignal(
                $data['classroom_id'],
                $data['from'],
                $data['to'],
                $data['signal']
            ));

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('signal', ['message' => $e->getMessage()]);
        }
    }
}


