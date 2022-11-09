<?php

namespace App\Http\Controllers;

use App\Models\Build;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BuildController extends Controller
{
    public function create(Request $request, $id){
        try {
            
            $validator = Validator::make($request->all(),
                [
                    'title' => 'required|string|max:25',
                    'respect' => 'required|integer|max:2',                   
                    'data' => 'required|array',
                    'tags' => 'array|string'
                ]
            );

            if($validator->fails()){
                return response()->json(
                    [
                        'success'=> false,
                        'message' => $validator->errors()
                    ],
                    400
                );
            }
            
            // $newTitle = $request->input('title');
            // $newRespect = $request->input('respect');
            // $newData = $request->input('data');

            // $user = User::query()->find($id);               
            // $me = auth()->user();           

            $newBuild = new Build();
            // $newBuild->title = $newTitle;
            // $newBuild->respect = $newRespect;
            // $newBuild->data = $newData;
            // $newBuild->user_id= auth()->user()->id;

            // $newBuild->save();

            $fields = ['title', 'respect', 'data', 'tags'];

            foreach($fields as $field){
                $value = $request->input($field);
                if(isset($value)){
                    if($field == 'data' && 'tags'){
                        
                    }
                    else{
                        $newBuild->$field = $value;
                    }
                }
            } 

            $newBuild->save();

        } catch (\Exception $exception) {
            Log::error('You can not create a new build');
            return response()->json(
                [
                    'success' => false,
                    'message' => 'error to create a new build'
                ]
            );
        }
    }

    public function get(){
        try {
            //code...
        } catch (\Exception $exception) {
            Log::error('You can not get this build');
            return response()->json(
                [
                    'success' => false,
                    'message' => 'error to getting this build'
                ]
            );
        }
    }

    public function put(){
        try {
            //code...
        } catch (\Exception $exception) {
            Log::error('You can not update this build');
            return response()->json(
                [
                    'success' => false,
                    'message' => 'error to update this build'
                ]
            );
        }
    }

    public function delete(){
        try {
            //code...
        } catch (\Exception $exception) {
            Log::error('You can not delete this build');
            return response()->json(
                [
                    'success' => false,
                    'message' => 'error to delete this build'
                ]
            );
        }
    }
}
