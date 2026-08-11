<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FcmToken;
use Illuminate\Http\Request;

class FcmTokenController extends Controller
{
    /**
     * Register or update user FCM device token.
     * Endpoint: POST /api/fcm-token
     */
    public function store(Request $request)
    {
        $request->validate([
            'token'       => 'required|string',
            'device_info' => 'nullable|string|max:255',
        ]);

        $user = $request->user();
        $token = $request->token;
        $tokenHash = hash('sha256', $token);

        FcmToken::updateOrCreate(
            [
                'user_id'    => $user->id,
                'token_hash' => $tokenHash,
            ],
            [
                'token'       => $token,
                'device_info' => $request->device_info,
            ]
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Token FCM berhasil didaftarkan.',
        ]);
    }

    /**
     * Remove user FCM device token upon logout.
     * Endpoint: DELETE /api/fcm-token
     */
    public function destroy(Request $request)
    {
        $user = $request->user();
        $token = $request->input('token');

        if ($token) {
            $tokenHash = hash('sha256', $token);
            FcmToken::where('user_id', $user->id)
                ->where('token_hash', $tokenHash)
                ->delete();
        } else {
            FcmToken::where('user_id', $user->id)->delete();
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Token FCM berhasil dihapus.',
        ]);
    }
}
