<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GithubAuthorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authorization = request()->header('Authorization');
        if (empty($authorization)){
            return response()->json([
                'status' => 'error',
                'code' => 401,
                'message' => 'Authorization header is missing.'
            ], 401);
        }
        $github_api_token = config('services.techscode.api.github');
        if ($authorization !== $github_api_token){
            return response()->json([
                'status' => 'error',
                'code' => 401,
                'message' => 'Invalid authorization header.'
            ], 401);
        }

        return $next($request);
    }
}
