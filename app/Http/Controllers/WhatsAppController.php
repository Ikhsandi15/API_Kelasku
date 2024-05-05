<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    public function sendMessage(Request $request, $phone)
    {
        $whatsappLink = "https://wa.me/{$phone}";

        // Mengirimkan respons redirect ke URL WhatsApp.
        return redirect()->away($whatsappLink);
    }
}
