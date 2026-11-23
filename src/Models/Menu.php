<?php

namespace Novius\LaravelFilamentMenu\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Kalnoy\Nestedset\Collection as NestedsetCollection;
use Novius\LaravelFilamentMenu\Cast\AsTemplate;
use Novius\LaravelFilamentMenu\Contracts\MenuTemplate;
use Novius\LaravelFilamentMenu\Database\Factories\MenuFactory;
use Novius\LaravelFilamentMenu\Facades\MenuManager;
use Novius\LaravelTranslatable\Traits\Translatable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * @property int $id
 * @property string $name
 * @property string|null $title
 * @property string $slug
 * @property string $locale
 * @property MenuTemplate $template
 * @property int|null $locale_parent_id
 * @property string|null $aria_label
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read NestedsetCollection<int, MenuItem> $items
 * @property-read Collection<int, Menu> $translations
 * @property-read Collection<int, Menu> $translationsWithDeleted
 *
 * @method static MenuFactory factory($count = null, $state = [])
 * @method static Builder<static>|Menu find($id, $columns = ['*'])
 * @method static Builder<static>|Menu newModelQuery()
 * @method static Builder<static>|Menu newQuery()
 * @method static Builder<static>|Menu query()
 * @method static Builder<static>|Menu withLocale(?string $locale)
 *
 * @mixin Model
 */
class Menu extends Model
{
    /** @use HasFactory<MenuFactory> */
    use HasFactory;

    use HasSlug;
    use Translatable;

    protected $table = 'menus';

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'template' => AsTemplate::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(static function (Menu $menu) {
            $locales = MenuManager::locales();
            if (empty($menu->locale) && $locales->count() === 1) {
                $menu->locale = $locales->firstOrFail()->code;
            }
        });
    }

    /**
     * @return HasMany<MenuItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(MenuManager::getMenuItemModel(), 'menu_id');
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->extraScope(fn (Builder|Menu $query) => $query->where('locale', $this->locale))
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->preventOverwrite()
            ->doNotGenerateSlugsOnUpdate();
    }

    public function getCacheName(): string
    {
        return 'laravel-filament-menu.menu.'.$this->id;
    }

    protected static function newFactory(): MenuFactory
    {
        return MenuFactory::new();
    }
}
