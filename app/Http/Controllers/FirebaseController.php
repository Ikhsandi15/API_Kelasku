<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FirebaseController extends Controller
{

    public function sendMessageToAndroid($regId)
    {
        $client = new Client();

        $response = $client->post('https://fcm.googleapis.com/fcm/send', [
            'headers' => [
                'Authorization' => 'key=' . env('API_FCM_KEY'),
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'to' => $regId,
                'notification' => [
                    'title' => 'Hey kamu di colek',
                    'body' => 'Kamu di colek sama ' . User::where('id', Auth::id())->pluck('name')->first(),
                ],
            ],
        ]);

        return $response->getBody();
    }
}
