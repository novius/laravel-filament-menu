<?php

namespace Novius\LaravelFilamentMenu\Services;

use Filament\Resources\Resource as FilamentResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;
use LaravelLang\Locales\Data\LocaleData;
use LaravelLang\Locales\Facades\Locales;
use Novius\LaravelFilamentMenu\Contracts\MenuTemplate;
use Novius\LaravelFilamentMenu\Filament\Resources\MenuItems\MenuItemResource;
use Novius\LaravelFilamentMenu\Filament\Resources\Menus\MenuResource;
use Novius\LaravelFilamentMenu\Models\Menu;
use Novius\LaravelFilamentMenu\Models\MenuItem;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

class MenuManagerService
{
    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(protected array $config = []) {}

    /**
     * @return Collection<string, MenuTemplate>
     */
    public function templates(): Collection
    {
        $templates = [];
        $potentialTemplates = array_merge(
            Arr::get($this->config, 'templates', []),
            $this->autoloadIn('autoload_templates_in')
        );
        foreach ($potentialTemplates as $templateClass) {
            if (! class_exists($templateClass)) {
                continue;
            }

            if (! in_array(MenuTemplate::class, class_implements($templateClass), true) ||
                (new ReflectionClass($templateClass))->isAbstract()
            ) {
                continue;
            }
            /** @var MenuTemplate $template */
            $template = new $templateClass;
            $templates[$template->key()] = $template;
        }

        return collect($templates);
    }

    public function template(string $templateKey): ?MenuTemplate
    {
        $template = $this->templates()->get($templateKey);
        if (empty($template)) {
            return null;
        }

        return $template;
    }

    /**
     * @return Collection<string, LocaleData>
     */
    public function locales(): Collection
    {
        $locales = Arr::get($this->config, 'locales', []);

        return Locales::installed()
            ->when(! empty($locales), fn (Collection $collection) => $collection->filter(fn (LocaleData $localeData) => in_array($localeData->code, $locales, true)));
    }

    /** @return class-string<Menu> */
    public function getMenuModel(): string
    {
        return Arr::get($this->config, 'models.menu', Menu::class);
    }

    /**
     * @return class-string<FilamentResource>
     *
     * @throws InvalidArgumentException
     */
    public function getMenuResource(): string
    {
        return $this->getResource('menu', MenuResource::class, 'menu');
    }

    /** @return class-string<MenuItem> */
    public function getMenuItemModel(): string
    {
        return Arr::get($this->config, 'models.menu_item', MenuItem::class);
    }

    /**
     * @return class-string<FilamentResource>
     *
     * @throws InvalidArgumentException
     */
    public function getMenuItemResource(): string
    {
        return $this->getResource('menu_item', MenuItemResource::class, 'menu item');
    }

    /**
     * @param  class-string<FilamentResource>  $default
     * @return class-string<FilamentResource>
     *
     * @throws InvalidArgumentException
     */
    protected function getResource(string $key, string $default, string $label): string
    {
        $resource = Arr::get($this->config, 'resources.'.$key, $default);
        if (! is_string($resource) || ! is_subclass_of($resource, FilamentResource::class)) {
            throw new InvalidArgumentException('The '.$label.' resource must be a subclass of '.FilamentResource::class);
        }

        return $resource;
    }

    /**
     * @return array<int, string>
     */
    protected function autoloadIn(string $config_key): array
    {
        $namespace = app()->getNamespace();

        $resources = [];
        $autoload_templates_in = Arr::get($this->config, $config_key);
        if (empty($autoload_templates_in) || ! is_dir($autoload_templates_in)) {
            return $resources;
        }

        foreach ((new Finder)->in($this->config[$config_key])->files() as $resource) {
            $resource = $namespace.str_replace(
                ['/', '.php'],
                ['\\', ''],
                Str::after($resource->getPathname(), app_path().DIRECTORY_SEPARATOR)
            );

            $resources[] = $resource;
        }

        return $resources;
    }
}
