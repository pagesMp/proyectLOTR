<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

use function App\Http\Utils\error;
use function App\Http\Utils\isUserAuthenticated;
use function App\Http\Utils\validate;

class UserController extends Controller
{
    public function create(Request $request)
    {
        try {
            $isValidated = validate(
                $request,
                [
                    'alias' => 'required|string|max:25',
                    'email' => 'required|string|email|max:255|unique:users',
                    'password' => 'required|string|min:6|max:25|regex:/[#$%^&*()+=!?¿.,:;]/i'
                ]
            );

            if($isValidated !== true){
                return $isValidated;
            };

            $user = User::create(
                [
                    'alias' => $request->get('alias'),
                    'email' => $request->get('email'),
                    'password' => bcrypt($request->password),
                ]
            );
            
            $user->roles()->attach("f97c0620-12bc-42e5-b9c3-4e7f5618285e");

            return response()->json(
                [
                    'success' => true,
                    'message' => 'User Created',
                    'data' => $user
                ],
                200
            );
        } catch (\Exception $exception) {
            return error("Error to create user", 400, $exception);
        }
    }

    public function login(Request $request)
    {
        try {

            $input = $request->only('email', 'password');
            $jwt_token = null;

            if (!$jwt_token = JWTAuth::attempt($input)) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Invalid Email or Password',
                    ],
                    Response::HTTP_UNAUTHORIZED
                );
            }

            return response()->json([
                'success' => true,
                'token' => $jwt_token,
            ]);
        } catch (\Exception $exception) {
            return error("Error to login", 400, $exception);
        }
    }

    public function get($id)
    {
        $user = User::firstOrFail($id);

        try {
            $me = isUserAuthenticated();
            if ($user->id  == $me->id) {
                return response()->json(
                    [
                        "success" => true,
                        "data" => $me
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        "success" => true,
                        "data" => ([
                            "alias" => $user->alias,
                            "email" => $user->email
                        ]
                        )
                    ],
                    200
                );
            }
        } catch (\Exception $exception) {
            return error('Error to show this profiel', 400, $exception);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $isValidated = validate(
                $request,
                [
                    'alias' => 'string|max:25',
                    'email' => 'string|email|max:255|unique:users',
                    'avatar' => 'integer',
                    'password' => 'string|min:6|max:25|regex:/[#$%^&*()+=!?¿.,:;]/i'
                ]
            );

            if($isValidated !== true){
                return $isValidated;
            };

            $user = User::query()->firstOrFail($id); 
            $me = isUserAuthenticated();
            
            if ($user->id != $me->id) {
                return response()->json(
                    [
                        "success" => false,
                        "message" => "you only can update yourself"
                    ],
                    400
                );
            }
            else{
                $fields = ['alias', 'email', 'avatar', 'password'];

                foreach ($fields as $field) {
                    $value = $request->input($field);
                    if (isset($value)) {
                        if ($field == 'password') {
                            $user->password = bcrypt($value);
                        } else {
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
            }

        } catch (\Exception $exception) {
            return error("Error to update your profile", 400, $exception);            
        }
    }

    public function delete($id)
    {
        $user = User::query()->firstOrFail($id);
        try {
            $me = isUserAuthenticated();
            $alias = $user->alias;

            if ($user->id != $me->id) {
                return response()->json(
                    [
                        "success" => false,
                        "message" => "you dont have permissions"
                    ],
                    400
                );
            }
            else{

                $user->delete();

                return response()->json(
                    [
                        'success' => true,
                        'message' => "user {$alias} deleted successfully"
                    ],
                    200
                );   
            };

        } catch (\Exception $exception) {
            return error("Error to delere your profile", 400, $exception);           
        }
    }
}