<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatePartner
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if($user->user_type_id == 3){
            return $next($request);
        }
        
        return response()->json([
            'status' => 'Error',
            'message' => 'Unauthorized!!!',
        ], 200); 
    }
}
