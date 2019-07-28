<?php

namespace Tests\Unit;

use App\Adapters\JwtAdapter;
use App\Adapters\RedisAdapter;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Facades\JWTAuth;

class RedisAdapterTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @covers \App\Adapters\RedisAdapter::getTokens()
     * @uses JwtAdapter::generateToken()
     * @test
     */
    public function redis_adapter_can_return_array_of_tokens()
    {
        (new JwtAdapter('token_test'))->generateToken();
        (new JwtAdapter('token_test'))->generateToken();

        $redis = new RedisAdapter('token_test');
        $tokens = $redis->getTokens();

        $this->assertIsArray($tokens);
        $this->assertNotEmpty($tokens);
    }

    /**
     * @covers \App\Adapters\RedisAdapter::addToEnd()
     * @uses JwtAdapter::generateToken()
     * @uses RedisAdapter::returnConnection()
     * @test
     */
    public function redis_can_add_token_to_an_end_of_list()
    {
        $token = (new JwtAdapter('token_test'))->generateToken();
        $redis = (new RedisAdapter('token_test'))->returnConnection();

        $countBeforePush = $redis->llen('token_test');
        (new RedisAdapter('token_test'))->addToEnd($token);
        $countAfterPush = $redis->llen('token_test');

        $this->assertEquals(1, $countAfterPush - $countBeforePush);
    }

    /**
     * @covers \App\Adapters\RedisAdapter::findTokenId()
     * @uses JwtAdapter::generateToken()
     * @uses RedisAdapter::returnConnection()
     * @uses RedisAdapter::getTokens()
     * @test
     */
    public function redis_adapter_can_find_token_id()
    {
        $redis = new RedisAdapter('token_test');

        $expectedCount = (new RedisAdapter('token_test'))->returnConnection()->llen('token_test');
        $old_token = (new JwtAdapter('token_test'))->generateToken();

        $tokens = $redis->getTokens();
        $result = $redis->findTokenId($tokens, $old_token);

        $this->assertEquals($expectedCount, $result);
    }

    /**
     * @covers \App\Adapters\RedisAdapter::returnConnection()
     * @test
     */
    public function redis_adapter_can_return_connection()
    {
        $redis = new RedisAdapter('token_test');
        $this->assertInstanceOf('Illuminate\Redis\Connections\PredisConnection', $redis->returnConnection());
    }

    /**
     * @covers \App\Adapters\RedisAdapter::updateToken()
     * @uses JwtAdapter::generateToken()
     * @uses JwtAdapter::refreshToken()
     * @uses RedisAdapter::returnConnection()
     * @test
     */
    public function redis_adapter_can_update_token()
    {
        $redis = new RedisAdapter('token_test');
        $redis_connection = (new RedisAdapter('token_test'))->returnConnection();
        (new JwtAdapter('token_test'))->generateToken();

        // Получаем список токенов
        $tokens = $redis->getTokens();
        // Получаем id последнего токена с учетом сдвига для array
        $last_token = $redis_connection->llen('token_test')-1;
        // Получаем старый токен
        $old_token = $tokens[$last_token];
        // Обновляем старый токен
        $new_token = (new JwtAdapter ('token_test'))->refreshToken($old_token);
        // Обновляем токены
        $updated_token = $redis->updateToken($old_token,$new_token);
        // Проверяем, чтобы последний токен в редисе был обновлен
        $this->assertNotEquals($old_token, $updated_token);
    }

    /**
     * @covers \App\Adapters\RedisAdapter::isTokenListExists()
     * @uses JwtAdapter::generateToken()
     * @test
     */
    public function redis_can_check_if_key_token_exists()
    {
        (new JwtAdapter('token_test'))->generateToken();
        $redis = (new RedisAdapter('token_test'));

        $result = $redis->isTokenListExists();
        $this->assertEquals(1, $result);
    }

    /**
     * Удаляем лишний мусор из редиса
     * @afterClass
     */
    public static function delete_garbage()
    {
        (new RedisAdapter('token_test'))->returnConnection()->del('token_test');
    }
}
