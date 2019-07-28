<?php

namespace App\Http\Middleware;

use App\Auth;
use App\Models\User;
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

        if ($check instanceof User) {
            return $next($request);
        }
        // Получаем данные ошибки из куки
        $message = json_decode($check->getContent());
        // Создаем json ответ с сообщением об ошибке и удаляем куку
        return response()->json([$message->message])->withCookie(\Cookie::forget('jwt'));
    }
}
