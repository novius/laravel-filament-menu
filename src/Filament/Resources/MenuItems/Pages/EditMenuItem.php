<?php

namespace Novius\LaravelFilamentMenu\Filament\Resources\MenuItems\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Novius\LaravelFilamentMenu\Facades\MenuManager;
use Novius\LaravelFilamentMenu\Models\MenuItem;

class EditMenuItem extends EditRecord
{
    public static function getResource(): string
    {
        return MenuManager::getMenuItemResource();
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function configureDeleteAction(DeleteAction $action): void
    {
        /** @var MenuItem $record */
        $record = $this->getRecord();

        $action
            ->authorize(MenuManager::getMenuItemResource()::canDelete($record))
            ->successRedirectUrl(MenuManager::getMenuResource()::getUrl('edit', ['record' => $record->menu]));
    }

    public function getBreadcrumbs(): array
    {
        /** @var MenuItem $record */
        $record = $this->getRecord();

        $breadcrumbs = [
            MenuManager::getMenuResource()::getUrl() => MenuManager::getMenuResource()::getBreadcrumb(),
            MenuManager::getMenuResource()::getUrl('view', ['record' => $record->menu]) => $record->menu->name,
            MenuManager::getMenuResource()::getUrl('edit', ['record' => $record->menu]) => MenuManager::getMenuItemResource()::getBreadcrumb(),
        ];

        if ($record->exists) {
            $title = $this->getRecordTitle();
            $breadcrumbs[MenuManager::getMenuItemResource()::getUrl('edit', ['record' => $record])] = $title instanceof Htmlable ? $title->toHtml() : $title;
        }

        if (filled($cluster = static::getCluster())) {
            $breadcrumbs = $cluster::unshiftClusterBreadcrumbs($breadcrumbs);
        }

        $breadcrumbs[] = $this->getBreadcrumb();

        return $breadcrumbs;
    }
}
