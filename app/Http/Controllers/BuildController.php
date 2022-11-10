<?php

namespace App\Http\Controllers;

use App\Models\Build;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use function App\Http\Utils\error;
use function App\Http\Utils\isUserAuthenticated;
use function App\Http\Utils\validate;

class BuildController extends Controller
{
    public function create(Request $request)
    {
        try {
            $me = isUserAuthenticated();
            validate(
                $request,
                [
                    'title' => 'required|string|max:25',
                    'tags' => 'array'
                ]
            );
            
            $newTitle = $request->input('title');
            $newData = $request->input('data');
            $newTags = $request->input('tags');           

            $newBuild = new Build();
            $newBuild->title = $newTitle;
            $newBuild->data = $newData;
            $newBuild->views = 0;
            $newBuild->tags = $newTags;

            if($me){
                $newBuild->user_id = auth()->user()->id;
            }else{
                $newBuild->user_id = 0;
            };         

            $newBuild->save();

            return response()->json(
                [
                    'success' => true,
                    'data' => $newBuild
                ],
               200
            );
            
        } catch (\Exception $exception) {
            return error("Error to create a new build", 400, $exception);
        }
    }

    public function get($id)
    {
        try {
            $build = Build::query()->where('id', $id)->firstOrFail();

            return response()->json(
                [
                    'success' => true,
                    'data' => $build
                ],
               200
            );
        } catch (\Exception $exception) {
            return error("Error to get build", 400, $exception);
        }
    }

    public function update(Request $request, $id){
        try {
            $build = Build::query()->where('id', $id)->firstOrFail();
            $me = isUserAuthenticated();

            validate(
                $request,
                [
                    'title' => 'required|string|max:25',
                    'tags' => 'array'
                ]
            );

            if($build && $me->id == $build->user_id){

                $newTitle = $request->input('title');
                $newData = $request->input('data');
                $newTags = $request->input('tags');           

                
                $build->title = $newTitle;
                $build->data = $newData;
                $build->tags = $newTags;

                $build->update(); 

                return response()->json(
                    [
                        'success' => true,
                        'message' => "build has been updated successfully"
                    ],
                   200
                );

            }else{
                return response()->json(
                    [
                        'success' => false,
                        'message' => "You dont have permissions to update this build"
                    ],
                   400
                );
            };            

        } catch (\Exception $exception) {
            return error("Error to update this build", 400, $exception);
        }
    }

    public function delete($id)
    {
        try {

            $build = Build::query()->where('id', $id)->firstOrFail();
            $me = isUserAuthenticated();

            if($build && $me->id == $build->user_id){
                $buildName = $build->title;

                $build->delete();

                return response()->json(
                    [
                        'success' => true,
                        'message' => "build $buildName has been deleted successfully"
                    ],
                   200
                );
            }else{
                return response()->json(
                    [
                        'success' => false,
                        'message' => "You dont have permissions to delete this build"
                    ],
                   400
                );
            };
            
        } catch (\Exception $exception) {
           return error("Error to delete build", 400, $exception); 
        }
    }
}   
