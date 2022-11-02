<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
   public function register(Request $request){
        try {
            $validator = Validator::make(
                $request->all(),
                    [
                        'alias' => 'required|string|max:25',
                        'email' => 'required|string|email|max:255|unique:users',
                        'password' => 'required|string|min:6|max:25|regex:/[#$%^&*()+=!?¿.,:;]/i'                    
                    ]
            ); 
            
            if ($validator->fails()) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => $validator->errors()
                    ],
                    400 
                );
            }

            $user = User::create(
                [
                    'alias' => $request->get('alias'),
                    'email' => $request->get('email'),
                    'password' => bcrypt($request->password),
                ]

            );

            return response()->json(
                [
                    'success' => true,
                    'message' => 'User Created',
                    'data' => $user
                ],
                200
            );

        } catch (\Exception $exception) {
            Log::error('Error to create user' . $exception->getMessage());
            return response()->json(
                [
                    'seccess' => false,
                    'message' => ' Error to create new User'
                ],
                404
            );
        }
    }

   
}
