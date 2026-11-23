---
name: laravel-filament-menu-development
description: >
  Install, configure and render menus managed with the Laravel Filament Menu package (novius/laravel-filament-menu):
  Filament plugin, Blade menu component, custom menu templates, internal links and menu seeders.
license: MIT
metadata:
  author: Novius
---

# Laravel Filament Menu

Use this skill when a Laravel application needs to manage menus in a Filament panel with
`novius/laravel-filament-menu`, render them in Blade, or customize their templates.

## Primary Goal

- apply the `novius/laravel-filament-menu` package's public API in the smallest correct way

## Workflow

### 1. Install and register the plugin

- `composer require novius/laravel-filament-menu`, then `php artisan filament:assets` and `php artisan migrate`.
- Add `Novius\LaravelFilamentMenu\Filament\MenuManagerPlugin::make()` to the `plugins()` of the Filament panel provider.
- Publish the config only when an option must change:
  `php artisan vendor:publish --provider="Novius\LaravelFilamentMenu\LaravelFilamentMenuServiceProvider" --tag="config"`.

### 2. Render a menu

Menus are rendered by slug and locale with the Blade component:

```blade
<x-laravel-filament-menu::menu menu-slug="header" locale="fr" />
```

- `locale` defaults to the current locale; `title-tag` and `item-empty-tag` default to `span`.
- Style with the default `lfm-*` CSS classes, or pass `container-classes`, `title-classes`, `list-classes`,
  `item-container-classes`, `item-classes` (string, array or Closure), `item-active-classes` and
  `item-contains-active-classes` (string).
- The menu items are cached forever; the cache is cleared when a menu item is saved or deleted, and when a model
  linked by an item changes.

### 3. Add a custom template

- Create a class implementing `Novius\LaravelFilamentMenu\Contracts\MenuTemplate` and using the
  `Novius\LaravelFilamentMenu\Concerns\IsMenuTemplate` trait, in `app/Menus/Templates` (autoloaded, see the
  `autoload_templates_in` config) or listed in the `templates` config.
- Define `key()`, `name()`, `hasTitle()`, `maxDepth()`, and optionally `fields()` (extra item fields, named
  `extras.*`), `casts()`, `view()` and `viewItem()`.
- Built-in templates: `with-title` (`MenuTemplateWithTitle`) and `without-title` (`MenuTemplateWithoutTitle`).

### 4. Internal links

- Internal links use `novius/laravel-linkable`: declare linkable routes in its `linkable_routes` config, and
  linkable models with its `Linkable` trait. In a custom item view, use `$item->href()` to get the URL.

### 5. Seed menus

- Extend `Novius\LaravelFilamentMenu\Database\Seeders\MenuSeeder`, return the menus from `menus()` (keyed by slug,
  with `name` and `template` class), and override `postCreate()` to add items.

## Rules, References, and Templates

Read before executing:

- `README.md` of the package for the full list of Blade component attributes and the template views to copy.
- `config/laravel-filament-menu.php` for the overridable models, resources and Filament navigation options.

## Examples

- Add a footer menu with a title: seed a `footer` menu with `MenuTemplateWithTitle`, then render
  `<x-laravel-filament-menu::menu menu-slug="footer" title-tag="h3" />` in the footer layout.
- Add a mega menu with an image per item: create a template whose `fields()` returns a file upload named
  `extras.image`, with its own `view()` and `viewItem()`.

## Anti-patterns

- do not query `MenuItem` models directly to render a menu; use the Blade component, which handles the tree, the
  active item and the cache
- do not hardcode item URLs for internal pages; use internal links so they follow the routes and slugs
- do not document package internals here; keep the skill focused on adoption in Laravel apps
