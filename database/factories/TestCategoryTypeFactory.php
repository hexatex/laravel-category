<?php

namespace Hexatex\LaravelCategory\Factories;

use Hexatex\LaravelCategory\Tests\TestObjects\TestCategoryType;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestCategoryTypeFactory extends Factory
{
    protected $model = TestCategoryType::class;

    public function definition()
    {
        return [
            'test' => $this->faker->sentence,
        ];
    }
}
