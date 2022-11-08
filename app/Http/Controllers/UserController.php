<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
   public function create(Request $request){
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
            
            $user->roles()->attach(1);

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
                    'success' => false,
                    'message' => ' Error to create new User'
                ],
                404
            );
        }
    }

    public function login(Request $request){
        try {

            $input = $request->only('email', 'password');
            $jwt_token = null;

            if (!$jwt_token = JWTAuth::attempt($input)) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Invalid Email or Password',
                    ], Response::HTTP_UNAUTHORIZED
                );
        }

        return response()->json([
            'success' => true,
            'token' => $jwt_token,
        ]);

        } catch (\Exception $exception) {
            Log::error('Error to login' . $exception->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => ' Error to Login'
                ],
                404
            );
        }
    }

    public function get($id){

        $user = User::find($id);

        try {
            $me = auth()->user();

            if($user->id  == $me->id){
                return response()->json(
                    [
                        "success" => true,
                        "data" => $me
                    ],
                    200
                );
            } 

        } catch (\Exception $exception) {

            if($user->id == $user->id){
                return response()->json(
                    [
                        "success" => true,
                        "data" => (
                            [
                                "alias" => $user->alias,
                                "email" => $user->email
                            ]
                        )               
                    ],
                    200
                );
            }else{

                Log::error('Error to show this profile' . $exception->getMessage());
                return response()->json(
                    [
                        'success' => false,
                        'message' => ' Error to show this profile'
                    ],
                    404
                );
            }
        }
    }

    public function update(Request $request, $id){
        try {

            $validator = Validator::make(
                $request->all(),
                    [
                        'alias' => 'string|max:25',
                        'email' => 'string|email|max:255|unique:users',
                        'avatar' => 'integer',
                        'password' => 'string|min:6|max:25|regex:/[#$%^&*()+=!?¿.,:;]/i'                    
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

            $user = User::query()->find($id);               
            $me = auth()->user();

            if($user->id != $me->id){
                return response()->json(
                    [
                        "success" => false,
                        "message" => "you only can update yourself"
                    ],
                   400
                );
            }

            $fields = ['alias', 'email', 'avatar', 'password'];

            foreach($fields as $field){
                $value = $request->input($field);
                if(isset($value)){
                    if($field == 'password'){
                        $user->password = bcrypt($value);
                    }
                    else{
                        $user->$field = $value;
                    }
                }
            }   

            $user->save();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'you have modifcate your profile successfully'
                ],
            200
            );

        } catch (\Exception $exception) {
            Log::error('Error tu update your profile' . $exception->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => ' Error to update your profile'
                ],
                404
            );
        }        
    }
    
    public function delete($id){
        $user = User::query()->find($id);
        try {
            $me = auth()->user();
            $alias = $user->alias;

            if($user->id != $me->id){
                return response()->json(
                    [
                        "success" => false,
                        "message" => "you dont have permissions"
                    ],
                   400
                ); 
            }

            $user -> delete();

            return response()->json(
                [
                    'success' => true,
                    'message' => "user {$alias} deleted successfully"
                ],
                200
            );

        } catch (\Exception $exception) {
            Log::error('Error to delete your profile' . $exception->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => ' Error to delete your profile'
                ],
                404
            );
        }

    }
}
