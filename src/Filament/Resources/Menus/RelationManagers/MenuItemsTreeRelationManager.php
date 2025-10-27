<?php

namespace Novius\LaravelFilamentMenu\Filament\Resources\Menus\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;
use LogicException;
use Novius\FilamentRelationNested\Filament\Actions\FixTreeAction;
use Novius\FilamentRelationNested\Filament\Resources\RelationManagers\TreeRelationManager;
use Novius\LaravelFilamentMenu\Facades\MenuManager;
use Novius\LaravelFilamentMenu\Filament\Resources\MenuItems\MenuItemResource;
use Novius\LaravelFilamentMenu\Filament\Resources\Menus\MenuResource;
use Novius\LaravelFilamentMenu\Models\Menu;
use Novius\LaravelFilamentMenu\Models\MenuItem;

class MenuItemsTreeRelationManager extends TreeRelationManager
{
    protected static string $relationship = 'items';

    public function table(Table $table): Table
    {
        return MenuManager::getMenuItemResource()::table($table)
            ->columns([
                TextColumn::make('title'),
            ])
            ->pluralModelLabel(MenuItemResource::getPluralModelLabel())
            ->recordTitleAttribute('title')
            ->headerActions([
                CreateAction::make()
                    ->url(MenuItemResource::getUrl('create', ['menu' => $this->ownerRecord])),
                FixTreeAction::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->url(fn (MenuItem $record) => MenuItemResource::getUrl('edit', ['record' => $record])),
                DeleteAction::make()
                    ->successRedirectUrl(MenuResource::getUrl('edit', ['record' => $this->ownerRecord])),
            ]);
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return trans('laravel-filament-menu::menu.tree');
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    protected function moveTreeItemValidation(Model $node, ?int $parent, int $from, int $to): void
    {
        if ($parent === null) {
            return;
        }

        /** @var Menu $menu */
        $menu = $this->ownerRecord;
        $maxDepth = $menu->template->maxDepth();

        /** @phpstan-ignore-next-line */
        /** @var Model&NodeTrait $parentNode */
        $parentNode = $node->query()->findOrFail($parent);
        $newDepth = $parentNode->ancestors->count() + 1;

        if ($newDepth >= $maxDepth) {
            throw new LogicException(trans('laravel-filament-menu::menu.max_depth_reached', ['max_depth' => $maxDepth]));
        }
    }
}
