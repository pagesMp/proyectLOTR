<?php

namespace App\Http\Controllers;

use App\Models\Build;
use Illuminate\Http\Request;

use function App\Http\Utils\error;
use function App\Http\Utils\isUserAuthenticated;
use function App\Http\Utils\validate;

class BuildController extends Controller
{
    public function create(Request $request)
    {
        try {
            $me = isUserAuthenticated();
            $isValidated = validate(
                $request,
                [
                    'title' => 'required|string|max:15',
                    'data' => 'required|array',
                    'data.commander' => 'required|array',
                    'data.commander.id' => 'required|integer',
                    'data.levels.experience' => 'required|numeric|min:1|max:50',
                    'data.levels.respect' => 'required|numeric|min:0|max:25',
                    'data.levels.influence' => 'required|numeric|min:0',
                    'tags' => 'array'
                ]
            );

            if($isValidated !== true){
                return $isValidated;
            };
            
            $newTitle = $request->input('title');
            $newData = $request->input('data');
            $newTags = $request->input('tags');           

            $newBuild = new Build();
            $newBuild->title = $newTitle;
            $newBuild->data = $newData;
            $newBuild->views = 0;
            $newBuild->tags = $newTags;

            if($me){
                $newBuild->user_id = $me->id;
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

            $isValidated = validate(
                $request,
                [
                    'title' => 'required|string|max:25',
                    'tags' => 'array'
                ]
            );

            if($isValidated !== true){
                return $isValidated;
            };

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

    public function addView(string $id){
        try {
            $build = Build::query()->where('id', $id)->firstOrFail();
            $build->views += 1;
            $build->update();

            return response()->json(
                ['success' => true], 200
            );

        } catch (\Exception $exception) {
            return error("Error to delete build", 400, $exception); 
        }

    }
}   
