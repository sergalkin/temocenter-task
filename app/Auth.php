<?php

namespace App;


use App\Adapters\JwtAdapter;
use App\Adapters\RedisAdapter;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Auth
{
    protected $redis;
    protected $jwt;
    public $key;

    public function __construct($key = 'token')
    {
        $this->key = $key;
        $this->redis = new RedisAdapter($key);
        $this->jwt = new JwtAdapter($key);
    }

    public function login(Request $request)
    {
        /**
         * Если нет списка токенов в редисе и упользователя есть кука
         * Создаем новую куку вместе с созданием нового списка в редисе
         */
        if (!$this->redis->isTokenListExists() && $request->hasCookie('jwt')) {
            return $this->generateCookie();
        }

        if ($request->hasCookie('jwt')) {
            $result = $this->cookieCheck($request->cookie('jwt'));
            return $this->checkInstance($result);
        }
        return $this->generateCookie();
    }

    /**
     * Проверка на наличие куки в списке редиса
     * @param string $cookie
     * @return User|JsonResponse|string
     */
    public function cookieCheck(string $cookie)
    {
        $token = $this->redis->getTokens();
        return $this->iterateThroughTokens($cookie, $token);
    }

    /**
     * Проход по списку токенов в редисе и проверка наличия токена из куки в нем
     * @param string $cookie
     * @param array $token
     * @return User|JsonResponse|string
     */
    public function iterateThroughTokens(string $cookie, array $token)
    {
        foreach ($token as $item) {
            if ($item == $cookie) {
                return $this->jwt->tokenCheck($item);
            }
        }

        return $response = response()->json([
            'message' => 'Cookie data mismatch',
        ]);
    }

    /**
     * @param null $token
     * @return JsonResponse
     */
    public function generateCookie($token = null)
    {
        if (!isset($token)) {
            $token = $this->jwt->generateToken();
        }

        $response = response()->json([
            'message' => 'Login successful',
        ]);
        $response->withCookie(cookie('jwt', $token, 1440));
        return $response;
    }

    /**
     * Проверяем возврат JwtAuth, если это объект пользователя то выводим сообщение об успешной авторизации
     * Если не пользователь, значит токе обновился. Возвращаем его и сообщение об этом
     * @param $result
     * @return JsonResponse
     */
    protected function checkInstance($result)
    {
        if ($result instanceof User) {
            $response = response()->json([
                'message' => 'U have been authorized',
            ]);
            return $response;
        } else {
            $response = response()->json([
                'message' => 'Cookie update successful',
            ]);;
            $response->withCookie(cookie('jwt', $result, 1440));

            return $response;
        }
    }

}
