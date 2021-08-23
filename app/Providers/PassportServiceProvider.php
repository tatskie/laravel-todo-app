<?php

namespace App\Providers;

use League\OAuth2\Server\AuthorizationServer;
use Laravel\Passport\PassportServiceProvider;

class PassportServiceProvider extends PassportServiceProvider
{
    /**
     * make authorazation server
     *
     * @return AuthorizationServer
     */
    public function makeAuthorizationServer()
    {
        return new AuthorizationServer(
            $this->app->make(\Laravel\Passport\Bridge\ClientRepository::class),
            $this->app->make(\App\Repository\AccessTokenRepository::class),
            $this->app->make(\Laravel\Passport\Bridge\ScopeRepository::class),
            $this->makeCryptKey('private'),
            app('encrypter')->getKey()
        );
    }
}
