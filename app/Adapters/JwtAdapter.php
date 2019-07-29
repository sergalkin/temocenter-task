<?php


namespace App\Adapters;

use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;

class JwtAdapter
{
    protected $redis;

    /**
     * JwtAdapter constructor.
     *
     * @param $key
     */
    public function __construct($key)
    {
        $this->redis = new RedisAdapter($key);
    }

    /**
     * Проверяем полученный токен на валидность
     *
     * @param $token
     * @return \Illuminate\Http\JsonResponse|string|User
     */
    public function tokenCheck($token)
    {
        try {
            return JWTAuth::setToken($token)->authenticate();
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            $token = $this->refreshToken($token);
            return $token;
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            abort(401, 'Token is invalid');
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            abort(401, 'Token is not present');
        }
        return response()->json(['message' => 'Something went wrong']);
    }

    /**
     * Создаем новый токен для пользователя под номером 1
     *
     * @uses JWTFactory::make()
     * @uses JWTAuth::encode()
     * @return Object
     */
    public function generateToken()
    {
        $payload = JWTFactory::sub('1')->name('dummy user')->make();
        $token = JWTAuth::encode($payload);

        $this->redis->addToEnd($token);

        return $token;
    }

    /**
     * Обновляем токен в редисе
     *
     * @uses JWTAuth::refresh()
     * @uses JWTAuth::setToken()
     * @param $old_token
     * @return string
     */
    public function refreshToken($old_token): string
    {
        $new_token = JWTAuth::refresh(JWTAuth::setToken($old_token));
        $this->redis->updateToken($old_token, $new_token);

        return $new_token;
    }
}
