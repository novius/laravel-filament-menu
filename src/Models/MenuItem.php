<?php

namespace Novius\LaravelFilamentMenu\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Fluent;
use Kalnoy\Nestedset\Collection;
use Kalnoy\Nestedset\NodeTrait;
use Kalnoy\Nestedset\QueryBuilder;
use Novius\LaravelFilamentMenu\Enums\LinkType;
use Novius\LaravelFilamentMenu\Facades\MenuManager;
use Novius\LaravelJsonCasted\Casts\JsonWithCasts;
use Novius\LaravelLinkable\Facades\Linkable as LinkableFacade;
use Novius\LaravelLinkable\Traits\Linkable;

/**
 * @property int $id
 * @property string $title
 * @property int $menu_id
 * @property int $_lft
 * @property int $_rgt
 * @property int|null $parent_id
 * @property LinkType $link_type
 * @property string|null $external_link
 * @property string|null $internal_route
 * @property string|null $linkable_type
 * @property int|null $linkable_id
 * @property string|null $html
 * @property bool $target_blank
 * @property string|null $html_classes
 * @property Fluent $extras
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, MenuItem> $ancestors
 * @property-read Collection<int, MenuItem> $children
 * @property-read Collection<int, MenuItem> $descendants
 * @property-read Model&Linkable|null $linkable
 * @property-read Menu $menu
 * @property-read MenuItem|null $parent
 *
 * @method static Collection<int, static> all($columns = ['*'])
 * @method static Collection<int, static> hydrate(array $items, $connection = null)
 * @method static QueryBuilder<static>|MenuItem ancestorsAndSelf($id, array $columns = [])
 * @method static QueryBuilder<static>|MenuItem ancestorsOf($id, array $columns = [])
 * @method static QueryBuilder<static>|MenuItem applyNestedSetScope(?string $table = null)
 * @method static QueryBuilder<static>|MenuItem countErrors()
 * @method static QueryBuilder<static>|MenuItem d()
 * @method static QueryBuilder<static>|MenuItem defaultOrder(string $dir = 'asc')
 * @method static QueryBuilder<static>|MenuItem descendantsAndSelf($id, array $columns = [])
 * @method static QueryBuilder<static>|MenuItem descendantsOf($id, array $columns = [], $andSelf = false)
 * @method static QueryBuilder<static>|MenuItem fixSubtree($root)
 * @method static QueryBuilder<static>|MenuItem fixTree($root = null)
 * @method static Collection<int, static> get($columns = ['*'])
 * @method static QueryBuilder<static>|MenuItem getNodeData($id, $required = false)
 * @method static QueryBuilder<static>|MenuItem getPlainNodeData($id, $required = false)
 * @method static QueryBuilder<static>|MenuItem getTotalErrors()
 * @method static QueryBuilder<static>|MenuItem hasChildren()
 * @method static QueryBuilder<static>|MenuItem hasParent()
 * @method static QueryBuilder<static>|MenuItem isBroken()
 * @method static QueryBuilder<static>|MenuItem leaves(array $columns = [])
 * @method static QueryBuilder<static>|MenuItem makeGap(int $cut, int $height)
 * @method static QueryBuilder<static>|MenuItem moveNode($key, $position)
 * @method static QueryBuilder<static>|MenuItem newModelQuery()
 * @method static QueryBuilder<static>|MenuItem newQuery()
 * @method static QueryBuilder<static>|MenuItem orWhereAncestorOf(bool $id, bool $andSelf = false)
 * @method static QueryBuilder<static>|MenuItem orWhereDescendantOf($id)
 * @method static QueryBuilder<static>|MenuItem orWhereNodeBetween($values)
 * @method static QueryBuilder<static>|MenuItem orWhereNotDescendantOf($id)
 * @method static QueryBuilder<static>|MenuItem query()
 * @method static QueryBuilder<static>|MenuItem rebuildSubtree($root, array $data, $delete = false)
 * @method static QueryBuilder<static>|MenuItem rebuildTree(array $data, $delete = false, $root = null)
 * @method static QueryBuilder<static>|MenuItem reversed()
 * @method static QueryBuilder<static>|MenuItem root(array $columns = [])
 * @method static QueryBuilder<static>|MenuItem whereAncestorOf($id, $andSelf = false, $boolean = 'and')
 * @method static QueryBuilder<static>|MenuItem whereAncestorOrSelf($id)
 * @method static QueryBuilder<static>|MenuItem whereDescendantOf($id, $boolean = 'and', $not = false, $andSelf = false)
 * @method static QueryBuilder<static>|MenuItem whereDescendantOrSelf(string $id, string $boolean = 'and', string $not = false)
 * @method static QueryBuilder<static>|MenuItem whereIsAfter($id, $boolean = 'and')
 * @method static QueryBuilder<static>|MenuItem whereIsBefore($id, $boolean = 'and')
 * @method static QueryBuilder<static>|MenuItem whereIsLeaf()
 * @method static QueryBuilder<static>|MenuItem whereIsRoot()
 * @method static QueryBuilder<static>|MenuItem whereNodeBetween($values, $boolean = 'and', $not = false, $query = null)
 * @method static QueryBuilder<static>|MenuItem whereNotDescendantOf($id)
 * @method static QueryBuilder<static>|MenuItem withDepth(string $as = 'depth')
 * @method static QueryBuilder<static>|MenuItem withoutRoot()
 *
 * @mixin Model
 */
class MenuItem extends Model
{
    use NodeTrait;

    protected $table = 'menu_items';

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'link_type' => LinkType::class,
        'target_blank' => 'boolean',
        'extras' => JsonWithCasts::class.':getExtrasCasts',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saved(static function (MenuItem $item) {
            Cache::forget($item->menu->getCacheName());
        });
        static::deleted(static function (MenuItem $item) {
            Cache::forget($item->menu->getCacheName());
        });
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(MenuManager::getMenuModel(), 'menu_id');
    }

    public function linkable(): MorphTo
    {
        return $this->morphTo();
    }

    protected function getScopeAttributes(): array
    {
        return [
            'menu_id',
        ];
    }

    public function getExtrasCasts(): array
    {
        /** @phpstan-ignore nullsafe.neverNull, nullCoalesce.expr */
        return $this->menu?->template->casts() ?? [];
    }

    /**
     * Creates an href for the menu item according to its type.
     */
    public function href(): string
    {
        $href = '#';

        if (in_array($this->link_type, [LinkType::empty, LinkType::html], true)) {
            return $href;
        }

        if ($this->link_type === LinkType::external_link && ! empty($this->external_link)) {
            $href = $this->external_link;
        }

        if ($this->link_type === LinkType::internal_link) {
            /** @phpstan-ignore property.notFound */
            if ($this->linkable !== null) {
                $href = $this->linkable->url() ?? $href;
            } elseif (! empty($this->internal_link)) {
                $href = LinkableFacade::getLink($this->internal_link) ?? $href;
            }
        }

        return $href;
    }
}
