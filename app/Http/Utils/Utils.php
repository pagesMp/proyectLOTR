<?php

namespace App\Http\Utils;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

public function validate(Request $request, array $fields) {

    $validator = Validator::make($request->all(), $fields); 

    if ($validator->fails()) {
        return response()->json(
            [
                'success' => false,
                'message' => $validator->errors()
            ],
            400
        );
    }

   }

   public function isUserAuthenticated() {
    try {
        $me = auth()->user();
        return $me;
    }
    catch (\Exception $exception) {
        return false;
    }
   }

   public function error(string $message, int $code, Exception $exception) {
    Log::error($message . $exception->getMessage());
    return response()->json(
        [
            'success' => false,
            'message' => $message
        ],
        $code
    );
   }