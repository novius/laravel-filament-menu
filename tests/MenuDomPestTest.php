<?php

use Novius\LaravelFilamentMenu\Enums\LinkType;
use Novius\LaravelFilamentMenu\Models\MenuItem;
use Novius\LaravelFilamentMenu\Tests\Support\TestMenu as Menu;
use Novius\LaravelFilamentMenu\View\Components\Menu as MenuComponent;
use Symfony\Component\DomCrawler\Crawler;

it('rend une chaîne vide quand le menu est introuvable', function () {
    $component = new MenuComponent(menuSlug: 'unknown', locale: 'en');

    $html = $component->render();

    expect($html)->toBe('');
});

it('rend un nav avec les attributs par défaut, le titre et la structure de liste', function () {
    $menu = Menu::query()->create([
        'name' => 'main',
        'locale' => 'en',
        'template' => 'with-title',
        'title' => 'Main navigation',
    ]);

    // Un lien racine et un élément vide avec un enfant
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

    $child = MenuItem::query()->create([
        'title' => 'Child',
        'menu_id' => $menu->id,
        'parent_id' => $rootEmpty->id,
        'link_type' => LinkType::external_link,
        'external_link' => 'https://example.com/child',
    ]);

    $component = new MenuComponent(menuSlug: 'main', locale: 'en');
    $html = $component->render();

    $crawler = new Crawler($html);

    // nav
    $nav = $crawler->filter('nav#menu-main[role="navigation"]');
    expect($nav->count())->toBe(1);
    expect($nav->attr('aria-label'))->toBe('Main navigation');
    expect($nav->attr('class'))->toContain('lfm-container');

    // titre (template with-title)
    $title = $nav->filter('.lfm-title');
    expect($title->count())->toBe(1);
    expect(trim($title->text()))->toBe('Main navigation');

    // ul racine
    $rootUl = $nav->children('ul');
    expect($rootUl->count())->toBe(1);
    expect($rootUl->attr('data-depth'))->toBe('0');
    expect($rootUl->attr('class'))
        ->toContain('lfm-items-container')
        ->toContain('lfm--is-root');

    // li et a/span enfants immédiats
    $lis = $rootUl->children('li');
    expect($lis->count())->toBe(2);

    $homeLink = $lis->reduce(fn (Crawler $n) => $n->filter('a[href="https://example.com"]')->count() > 0)->first();
    expect($homeLink->count())->toBe(1);
    expect(trim($homeLink->filter('a')->text()))->toBe('Home');

    $moreSpan = $lis->reduce(fn (Crawler $n) => $n->filter('span')->count() > 0 && str_contains($n->text(), 'More'))->first();
    expect($moreSpan->count())->toBe(1);

    // Sous-liste de More
    $childUl = $moreSpan->children('ul');
    expect($childUl->count())->toBe(1);
    expect($childUl->attr('data-depth'))->toBe('1');

    $childLi = $childUl->children('li');
    expect($childLi->count())->toBe(1);
    $childA = $childLi->filter('a[href="https://example.com/child"]');
    expect($childA->count())->toBe(1);
    expect(trim($childA->text()))->toBe('Child');
});

it('marque l\'élément actif et positionne les attributs au bon endroit', function () {
    // Contexte de requête courant
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

    // Force current URL for isActiveItem
    $this->app['url']->forceRootUrl('https://example.com');
    $this->app['request']->initialize(
        [], [], [], [], [],
        ['HTTP_HOST' => 'example.com', 'REQUEST_URI' => '/child', 'HTTPS' => 'on']
    );
    $this->assertSame('https://example.com/child', url()->current());

    $component = new MenuComponent(
        menuSlug: 'main',
        locale: 'en',
        itemActiveClasses: 'is-active',
        itemContainsActiveClasses: 'has-active',
    );

    $html = $component->render();
    $crawler = new Crawler($html);

    // Lien actif
    $activeLink = $crawler->filter('a[href="https://example.com/child"][data-active="true"]');
    expect($activeLink->count())->toBe(1);
    expect($activeLink->attr('class'))->toContain('lfm-item');
    expect($activeLink->attr('class'))->toContain('is-active');

    // La liste enfant du parent doit porter le flag et la classe
    $childrenUl = $crawler->filter('li:contains("Parent") > ul');
    expect($childrenUl->count())->toBe(1);
    expect($childrenUl->attr('data-depth'))->toBe('1');
    expect($childrenUl->attr('class'))->toContain('lfm-items-container');
    expect($childrenUl->attr('class'))->toContain('has-active');
    expect($childrenUl->attr('data-active-items'))->toBe('true');
});
