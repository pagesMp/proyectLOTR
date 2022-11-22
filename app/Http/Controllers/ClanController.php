<?php

namespace App\Http\Controllers;

use App\Models\Clan;
use Illuminate\Http\Request;

use function App\Http\Utils\error;
use function App\Http\Utils\isUserAuthenticated;
use function App\Http\Utils\validate;

class ClanController extends Controller
{
    public function create(Request $request){
        try {
            $me = isUserAuthenticated();
            $isValidated = validate(
                $request,
                [
                    'name' => 'required|string|max:15'
                ]
            );

            if($isValidated !== true){
                return $isValidated;
            };

            $newName = $request->input('name');
            
            $newClan = new Clan();
            $newClan->name = $newName;
                        
            if(!$me){
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'you need to be loged'
                    ],
                   400
                );
            };

            $newClan->save();  
            
            $newClan->user()->attach($me->id);

            return response()->json(
                [
                    'success' => true,
                    'data' => $newClan
                ],
               200
            );

        } catch (\Exception $exception) {
            return error("Error to create a new Clan", 400, $exception);
        }
    }
}
