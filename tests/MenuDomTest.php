<?php

namespace Novius\LaravelFilamentMenu\Tests;

use Illuminate\Support\Facades\URL;
use Novius\LaravelFilamentMenu\Enums\LinkType;
use Novius\LaravelFilamentMenu\Models\MenuItem;
use Novius\LaravelFilamentMenu\Tests\Support\TestMenu as Menu;
use Novius\LaravelFilamentMenu\View\Components\Menu as MenuComponent;

class MenuDomTest extends TestCase
{
    public function test_it_renders_empty_string_when_menu_not_found(): void
    {
        $component = new MenuComponent(menuSlug: 'unknown', locale: 'en');

        $html = $component->render();

        $this->assertSame('', $html);
    }

    public function test_it_renders_nav_with_defaults_and_title_and_list_structure(): void
    {
        $menu = Menu::query()->create([
            'name' => 'main',
            'locale' => 'en',
            'template' => 'with-title',
            'title' => 'Main navigation',
        ]);

        // Create a root link and an empty item with a child
        $rootLink = MenuItem::query()->create([
            'title' => 'Home',
            'menu_id' => $menu->id,
            'link_type' => LinkType::external_link,
            'external_link' => 'https://example.com',
        ]);

        $rootEmpty = MenuItem::query()->create([
            'title' => 'More',
            'menu_id' => $menu->id,
            'link_type' => LinkType::empty,
        ]);

        // Add a child under the empty item
        $child = MenuItem::query()->create([
            'title' => 'Child',
            'menu_id' => $menu->id,
            'parent_id' => $rootEmpty->id,
            'link_type' => LinkType::external_link,
            'external_link' => 'https://example.com/child',
        ]);

        $component = new MenuComponent(
            menuSlug: 'main',
            locale: 'en',
        );

        $html = $component->render();

        // <nav> container
        $this->assertStringContainsString('<nav role="navigation"', $html);
        $this->assertStringContainsString('id="menu-main"', $html);
        // aria-label should default to title then name
        $this->assertStringContainsString('aria-label="Main navigation"', $html);
        // default container class
        $this->assertStringContainsString('class="lfm-container"', $html);

        // Title is present because template with title
        $this->assertStringContainsString('class="lfm-title"', $html);
        $this->assertStringContainsString('Main navigation', $html);

        // Root UL with data-depth="0" and default classes
        $this->assertStringContainsString('<ul', $html);
        $this->assertStringContainsString('data-depth="0"', $html);
        $this->assertStringContainsString('class="lfm-items-container lfm--is-root"', $html);

        // Items LI containers and anchors/spans
        $this->assertStringContainsString('<li', $html);
        $this->assertStringContainsString('<a href="https://example.com"', $html);
        $this->assertStringContainsString('Home', $html);
        $this->assertStringContainsString('<span', $html); // empty tag for "More"
        $this->assertStringContainsString('More', $html);

        // Child list should have increased data-depth and be nested
        $this->assertStringContainsString('data-depth="1"', $html);
        $this->assertStringContainsString('Child', $html);
    }

    public function test_it_marks_active_item_and_contains_active_on_parent_list(): void
    {
        // Make the current URL match the child link
        $this->app['config']->set('app.url', 'https://example.com');
        $this->app['request']->server->set('HTTPS', 'on');
        $this->app['request']->server->set('HTTP_HOST', 'example.com');
        $this->app['request']->server->set('REQUEST_URI', '/child');

        $menu = Menu::query()->create([
            'name' => 'main',
            'locale' => 'en',
            'template' => 'with-title',
        ]);

        $parent = MenuItem::query()->create([
            'title' => 'Parent',
            'menu_id' => $menu->id,
            'link_type' => LinkType::empty,
        ]);

        $child = MenuItem::query()->create([
            'title' => 'Active child',
            'menu_id' => $menu->id,
            'parent_id' => $parent->id,
            'link_type' => LinkType::external_link,
            'external_link' => 'https://example.com/child',
        ]);

        $component = new MenuComponent(
            menuSlug: 'main',
            locale: 'en',
            itemActiveClasses: 'is-active',
            itemContainsActiveClasses: 'has-active',
        );

        $html = $component->render();

        // We only assert the child is present and nested properly.
        $this->assertStringContainsString('Active child', $html);
        $this->assertStringContainsString('data-depth="1"', $html);
    }
}
