<?php

namespace App\Helpers;

class Helper
{
    public static function APIResponse($message, $resCode, $error, ...$data)
    {
        return response()->json([
            'code' => $resCode,
            'msg' => $message,
            'error' => [$error],
            'data' => [...$data]
        ], $resCode);
    }
}
