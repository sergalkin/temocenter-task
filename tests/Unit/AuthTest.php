<?php

namespace Tests\Unit;

use App\Adapters\JwtAdapter;
use App\Adapters\RedisAdapter;
use App\Auth;
use App\Models\User;
use DemeterChain\A;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     * @covers \App\Auth::generateCookie()
     */
    public function auth_can_generate_cookie()
    {
        $auth = (new Auth('token_test'))->generateCookie()->getContent();
        $this->assertEquals('{"message":"Login successful"}', $auth);
    }

    /**
     * @test
     * @covers \App\Auth::cookieCheck()
     * @covers \App\Auth::iterateThroughTokens()
     */
    public function auth_can_check_cookie()
    {
        (new JwtAdapter ('token_test'))->generateToken();
        factory('App\Models\User')->create();

        $redis = new RedisAdapter('token_test');
        $cookie = $redis->getTokens()[0];

        $auth = (new Auth('token_test'))->cookieCheck($cookie);
        $this->assertTrue($auth instanceof User);
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
