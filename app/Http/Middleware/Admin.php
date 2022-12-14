<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $userId = auth()->user()->id;
        $user = User::find($userId);

        $hasRole = $user->roles->contains("3d6ab219-8f63-4a4a-bb2f-0d3c9336601a");
        
        if(!$hasRole){
            return response()->json(
                [
                    "success" => true,
                    "message" => "Dont have permisions"
                ],
               400
            );
        }

        return $next($request);
    }
}