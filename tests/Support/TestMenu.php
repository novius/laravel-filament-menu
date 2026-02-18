<?php

namespace Novius\LaravelFilamentMenu\Tests\Support;

use Novius\LaravelFilamentMenu\Models\Menu as BaseMenu;
use Novius\LaravelTranslatable\Support\TranslatableModelConfig;

class TestMenu extends BaseMenu
{
    public function translatableConfig(): TranslatableModelConfig
    {
        // Limit available locales to a fixed small set for tests to avoid
        // depending on LaravelLang\Locales package configuration.
        return new TranslatableModelConfig(
            available_locales: ['en']
        );
    }
}
