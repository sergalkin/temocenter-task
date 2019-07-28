<?php

namespace Tests;

use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @param array|string $cookies
     * @return $this
     */
    protected function disableCookiesEncryption($cookies)
    {
        $this->app->resolving(EncryptCookies::class,
            function ($object) use ($cookies) {
                $object->disableFor($cookies);
            });

        return $this;
    }
}
