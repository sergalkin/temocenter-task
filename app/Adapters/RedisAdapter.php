<?php


namespace App\Adapters;


use App\Auth;
use Illuminate\Support\Facades\Redis;

class RedisAdapter
{
    protected $redis;
    protected $key;

    public function __construct($key)
    {
        $this->redis = Redis::connection();
        $this->key = $key;
    }

    /**
     * Получаем массив токенов
     * @return array
     */
    public function getTokens(): array
    {
        $token = $this->redis->lrange($this->key, 0, -1);

        return $token;

    }

    /**
     * Добавляем в конец списка токен
     * @param $token
     * @return int
     */
    public function addToEnd($token): int
    {
        return $this->redis->rpush($this->key, $token);
    }

    /**
     * Обновляем в редис истекший токен на новый и сохраняем в куку
     * @param $old_token
     * @param $new_token
     * @return \Illuminate\Http\JsonResponse
     * @uses RedisAdapter->getToken для получения списка токенов
     * @uses Auth->generateCookie($new_token) для создания новой куки
     * @uses RedisAdapter->findTokenId для id у токена для обновления
     */
    public function updateToken($old_token, $new_token)
    {
        $tokens = $this->getTokens();

        $key = $this->findTokenId($tokens, $old_token);

        if (isset($key)) {
            $this->redis->lset($this->key, $key, $new_token);
            $auth = new Auth($this->key);
            return $auth->generateCookie($new_token);
        }
        return response()->json(['message' => 'fail in token update']);
    }

    /**
     * Возврат ключа токена, если он есть в списке
     * @param $tokens
     * @param $old_token
     * @return int|string|null
     */
    public function findTokenId($tokens, $old_token)
    {
        foreach ($tokens as $key => $value) {
            if ($value == $old_token) {
                return $key;
            }
        }
        return null;
    }

    /**
     * Возвращаем соединение с редисом
     * @return \Illuminate\Redis\Connections\Connection
     */
    public function returnConnection()
    {
        return $this->redis;
    }

    /**
     * Проверяем есть ли ключ token в редис
     * @return int
     */
    public function isTokenListExists(): int
    {
        return $this->redis->exists($this->key);
    }
}
