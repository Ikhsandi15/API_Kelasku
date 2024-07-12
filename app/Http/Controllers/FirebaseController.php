<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Client;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class FirebaseController extends Controller
{

    public function sendMessageToAndroid($regId)
    {
        $client = new Client();

        // Mendapatkan nama pengguna
        $userName = User::where('id', Auth::id())->pluck('name')->first();
        if (!$userName) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Mempersiapkan payload untuk FCM
        $payload = [
            'to' => $regId,
            'notification' => [
                'title' => 'Hey kamu di colek',
                'body' => 'Kamu di colek sama ' . $userName,
            ]
        ];

        try {
            // Mengirim permintaan POST ke FCM
            $response = $client->post('https://fcm.googleapis.com/fcm/send', [
                'headers' => [
                    'Authorization' => 'Bearer ' . env('API_FCM_KEY'),
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);

            return Helper::APIResponse("Notification sent successfully", 200, null, json_decode($response->getbody(), true));
        } catch (\Exception $e) {
            Log::error('Error sending FCM notification: ' . $e->getMessage());

            return Helper::APIResponse("Failed to send notification", 500, "Internal Server Error", null);
        }
    }
}
