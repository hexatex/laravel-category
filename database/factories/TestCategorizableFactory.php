<?php

namespace Hexatex\LaravelCategory\Factories;

use Hexatex\LaravelCategory\Tests\TestObjects\TestCategorizable;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestCategorizableFactory extends Factory
{
    protected $model = TestCategorizable::class;

    public function definition()
    {
        return [];
    }
}
