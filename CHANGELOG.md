# Changelog

All notable changes to `novius/laravel-filament-menu` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased](https://github.com/novius/laravel-filament-menu/compare/1.0.6...HEAD)

## [1.0.6](https://github.com/novius/laravel-filament-menu/compare/1.0.5...1.0.6) - 2026-09-28

### Fixed

- Fix the path of the anonymous Blade components of the package.

### Removed

- Laravel 11 is no longer tested in the CI.

## [1.0.5](https://github.com/novius/laravel-filament-menu/compare/1.0.4...1.0.5) - 2026-06-02

### Fixed

- Fix the hydration of the menu items read from the cache: `extras` attribute and `menu` relation.

## [1.0.4](https://github.com/novius/laravel-filament-menu/compare/1.0.3...1.0.4) - 2026-06-02

### Fixed

- Fix the default values of the Filament navigation configs (label and icon) when they are set to `null`.

## [1.0.3](https://github.com/novius/laravel-filament-menu/compare/1.0.2...1.0.3) - 2026-06-02

### Added

- Add the `filament.menu` configs to customize the Filament navigation of the menu resource: `navigationLabel`,
  `navigationIcon`, `navigationSort`, `navigationGroup` and `shouldRegisterNavigation`.

## [1.0.2](https://github.com/novius/laravel-filament-menu/compare/1.0.1...1.0.2) - 2026-05-13

### Fixed

- Store arrays instead of objects in the menu cache, for Laravel 13.

## [1.0.1](https://github.com/novius/laravel-filament-menu/compare/1.0.0...1.0.1) - 2026-05-12

### Added

- Laravel 13 support.

## 1.0.0 - 2026-02-18

- First stable release, with Filament 4 and 5 support.
