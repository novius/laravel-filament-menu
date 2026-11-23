<?php

namespace Novius\LaravelFilamentMenu\Cast;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Novius\LaravelFilamentMenu\Contracts\MenuTemplate;
use Novius\LaravelFilamentMenu\Facades\MenuManager;

/**
 * @implements CastsAttributes<MenuTemplate|null, MenuTemplate|string|null>
 */
class AsTemplate implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?MenuTemplate
    {
        if (is_null($value)) {
            return null;
        }

        return MenuManager::template($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        if ($value instanceof MenuTemplate) {
            return $value->key();
        }

        return $value;
    }
}
