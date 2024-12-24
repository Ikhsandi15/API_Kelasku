<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class ColekController extends Controller
{
    public function colek(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'token' => 'required|string', // FCM Token penerima
            'message' => 'required|string',
        ]);

        $firebaseCredentialPath = config('services.firebase.credentials');

        // Inisialisasi Firebase Messaging
        $firebase = (new Factory())->withServiceAccount($firebaseCredentialPath);
        $messaging = $firebase->createMessaging();

        $message = CloudMessage::withTarget('token', $validated['token'])
            ->withNotification([
                'title' => 'Anda Dicolek Oleh ' . Auth::user()->name,
                'body' => $validated['message'],
            ]);

        try {
            $messaging->send($message);
            return Helper::APIResponse('success', 200, null, "Pesan Terkirim!!");
        } catch (\Exception $e) {
            return Helper::APIResponse('failed', 500, $e->getMessage(), null);
        }
    }
}
