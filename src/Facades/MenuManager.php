<?php

namespace Novius\LaravelFilamentMenu\Facades;

use Filament\Resources\Resource as FilamentResource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use LaravelLang\Locales\Data\LocaleData;
use Novius\LaravelFilamentMenu\Contracts\MenuTemplate;
use Novius\LaravelFilamentMenu\Models\Menu;
use Novius\LaravelFilamentMenu\Models\MenuItem;
use Novius\LaravelFilamentMenu\Services\MenuManagerService;

/**
 * @method static Collection<string, MenuTemplate> templates()
 * @method static MenuTemplate|null template(string $templateKey)
 * @method static class-string<Menu> getMenuModel()
 * @method static class-string<FilamentResource> getMenuResource()
 * @method static class-string<MenuItem> getMenuItemModel()
 * @method static class-string<FilamentResource> getMenuItemResource()
 * @method static Collection<string, LocaleData> locales()
 *
 * @see MenuManagerService
 */
class MenuManager extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MenuManagerService::class;
    }
}
