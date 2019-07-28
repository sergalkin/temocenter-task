<?php

namespace Tests\Unit;

use App\Adapters\JwtAdapter;
use App\Adapters\RedisAdapter;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JwtAdapterTest extends TestCase
{
    use DatabaseMigrations;

    protected $jwt;

    public function setUp(): void
    {
        parent::setUp();
        $this->jwt = new JwtAdapter('token_test');
    }

    /**
     * @covers \App\Adapters\JwtAdapter::generateToken
     * @test
     */
    public function jwt_adapter_can_generate_token()
    {
        $token = $this->jwt->generateToken();
        $this->assertNotEmpty($token);
    }

    /**
     * @covers \App\Adapters\JwtAdapter::refreshToken()
     * @uses   \App\Adapters\JwtAdapter::generateToken()
     * @test
     */
    public function jwt_adapter_can_refresh_token()
    {
        $old_token = $this->jwt->generateToken();
        $result = $this->jwt->refreshToken($old_token);
        $this->assertTrue(is_string($result));
    }

    /**
     * @covers \App\Adapters\JwtAdapter::tokenCheck
     * @uses   \App\Adapters\JwtAdapter::generateToken()
     * @test
     */
    public function jwt_adapter_can_check_token()
    {
        factory('App\Models\User')->create();

        $token = $this->jwt->generateToken();
        $result = $this->jwt->tokenCheck($token);
        $this->assertTrue($result instanceof User);
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
