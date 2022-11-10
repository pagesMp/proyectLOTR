<?php

namespace App\Http\Utils;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

function validate(Request $request, array $fields)
{

    $validator = Validator::make($request->all(), $fields);
    if ($validator->passes()) {
        return true;
    }
    else {
        return response()->json(
            [
                'success' => false,
                'message' => $validator->errors()
            ],
            400
        );
    }
    
}

function isUserAuthenticated()
{
    try {
        return auth()->user();
    } catch (\Exception $exception) {
        return false;
    }
}

function error(string $message, int $code, Exception $exception)
{
    Log::error($message . $exception->getMessage());
    return response()->json(
        [
            'success' => false,
            'message' => $message
        ],
        $code
    );
}
