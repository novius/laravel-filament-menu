<?php

namespace Novius\LaravelFilamentMenu\Filament\Resources\MenuItems\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Novius\LaravelFilamentMenu\Facades\MenuManager;
use Novius\LaravelFilamentMenu\Filament\Resources\MenuItems\MenuItemResource;
use Novius\LaravelFilamentMenu\Models\MenuItem;

class MenuItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'children';

    /** @var MenuItem */
    public Model $ownerRecord;

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return trans('laravel-filament-menu::menu.sub_menus');
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        /** @var MenuItem $ownerRecord */
        $canView = parent::canViewForRecord($ownerRecord, $pageClass);
        if (! $canView) {
            return false;
        }

        return $ownerRecord->ancestors->count() < ($ownerRecord->menu->template->maxDepth() - 1);
    }

    public function table(Table $table): Table
    {
        return MenuManager::getMenuItemResource()::table($table)
            ->modifyQueryUsing(function (Builder|MenuItem $query) {
                $query->whereHas('parent', fn (Builder|MenuItem $query) => $query->where('id', $this->ownerRecord->id));
            })
            ->pluralModelLabel(MenuItemResource::getPluralModelLabel())
            ->recordTitleAttribute('title')
            ->headerActions([
                CreateAction::make()
                    ->url(MenuItemResource::getUrl('create', ['menu' => $this->ownerRecord->menu, 'parent' => $this->ownerRecord])),
            ])
            ->recordActions([
                EditAction::make()
                    ->url(fn (MenuItem $record) => MenuItemResource::getUrl('edit', ['record' => $record])),
                DeleteAction::make()
                    ->successRedirectUrl(MenuItemResource::getUrl('edit', ['record' => $this->ownerRecord])),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
