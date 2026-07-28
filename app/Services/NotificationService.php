<?php

namespace App\Services;

use App\Models\FcmToken;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class NotificationService
{
    protected static function getMessaging()
    {
        $credentialsPath = storage_path('app/firebase-service-account.json');
        if (!file_exists($credentialsPath)) {
            Log::warning("FCM Service Account JSON not found at {$credentialsPath}");
            return null;
        }

        try {
            return (new Factory())
                ->withServiceAccount($credentialsPath)
                ->createMessaging();
        } catch (\Throwable $e) {
            Log::error("Failed to initialize Firebase Messaging: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Send Push Notification to all FCM tokens of a specific User.
     *
     * @param User|int $user
     * @param string $title
     * @param string $body
     * @param array $dataPayload Payload containing type & id e.g. ['type' => 'cuti', 'id' => '12']
     */
    public static function sendToUser($user, string $title, string $body, array $dataPayload = []): void
    {
        $userId = $user instanceof User ? $user->id : $user;
        $tokens = FcmToken::where('user_id', $userId)->get();

        if ($tokens->isEmpty()) {
            return;
        }

        $messaging = static::getMessaging();
        if (!$messaging) {
            return;
        }

        $notification = Notification::create($title, $body);

        // Convert all payload values to string as required by FCM Data payload
        $formattedData = [];
        foreach ($dataPayload as $k => $v) {
            $formattedData[(string)$k] = (string)$v;
        }

        foreach ($tokens as $fcmRecord) {
            try {
                $message = CloudMessage::new()
                    ->toToken($fcmRecord->token)
                    ->withNotification($notification)
                    ->withData($formattedData);

                $messaging->send($message);
            } catch (\Throwable $e) {
                Log::warning("FCM send failed for user {$userId}, token ID {$fcmRecord->id}: " . $e->getMessage());
                
                // If error indicates invalid/expired token, delete from database
                $errStr = strtolower($e->getMessage());
                if (
                    str_contains($errStr, 'unregistered') ||
                    str_contains($errStr, 'invalid') ||
                    str_contains($errStr, 'not_found') ||
                    str_contains($errStr, 'not found') ||
                    str_contains($errStr, 'expired')
                ) {
                    $fcmRecord->delete();
                }
            }
        }
    }
}
