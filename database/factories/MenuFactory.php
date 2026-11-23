<?php

namespace Novius\LaravelFilamentMenu\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use LaravelLang\Locales\Facades\Locales;
use Novius\LaravelFilamentMenu\Models\Menu;

/**
 * @extends Factory<Menu>
 */
class MenuFactory extends Factory
{
    protected $model = Menu::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'locale' => Locales::installed()->random()->code,
        ];
    }
}
