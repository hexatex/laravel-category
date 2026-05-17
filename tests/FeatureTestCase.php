<?php

namespace Hexatex\LaravelCategory\Tests;

use Hexatex\LaravelMisc\Contracts\Authenticatable;
use Hexatex\LaravelUser\Factories\UserFactory;

abstract class FeatureTestCase extends TestCase
{
    protected ?Authenticatable $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = UserFactory::new()->create();

        \Laravel\Passport\Passport::loadKeysFrom(__DIR__ . '/../../../../storage/');
        $this->login();

        $this->withoutMiddleware(\Illuminate\Auth\Middleware\Authorize::class);

        // config(['auth.defaults.guard' => 'null']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // config(['auth.defaults.guard' => 'web']);
    }

    protected function resourceToArray($resource)
    {
        return json_decode(json_encode(($resource)), true);
    }

    protected function login(array $scopes = [], string $guard = 'api')
    {
        \Laravel\Passport\Passport::actingAs($this->user, $scopes, $guard);
    }

    protected function logout(string $guard = 'api')
    {
        app('auth')->guard($guard)->forgetUser();
    }
}
