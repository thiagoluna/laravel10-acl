<?php

namespace App\Http\Middleware;

use App\Repositories\UserRepository;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class ACLMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!app(UserRepository::class)->hasPermissions($request->user(), Route::currentRouteName())){

            return response()->json([ "message" => "Not Authorized" ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
