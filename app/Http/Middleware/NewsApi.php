<?php

namespace App\Http\Middleware;

use App\Auth;
use Closure;

class NewsApi
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->hasCookie('jwt')) {
            return response()->json(['U need to login first, before u can access this route']);
        }
        // Проверяем является ли этот запрос из тестов
        if ($request->has('testing')) {
            $check = (new Auth('token_test'))->cookieCheck($request->cookie('jwt'));
        } else {
            $check = (new Auth())->cookieCheck($request->cookie('jwt'));
        }

        if ($check) {
            return $next($request);
        }

        return response()->json(['Something went wrong']);
    }
}
