<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    public const PARENT_ID_NONE = 0;

    /**
     * {@inheritDoc}
     *
     * @override
     */
    public function getFillable()
    {
        return ['name', 'slug', 'parent_id', 'path', 'source'];
    }

    protected static function booted()
    {
        // </Events>
        static::created(function (Category $category) {
            // It requires ID to build 'path', do it on created.
            $category->path = $category->parent_id
                ? $category->parent->path.'/'.$category->id
                : (string) $category->id;

            // Do it quietly to avoid further event triggered.
            $category->saveQuietly();
        });

        static::updating(function (Category $category) {
            if ($category->isDirty('parent_id')) {
                // Only recalc if the parent_id actually changed.
                $category->path = $category->parent_id
                    ? $category->parent->path.'/'.$category->id
                    : (string) $category->id;
            }
        });
        // </Events>
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): BelongsToMany
    {
        $pivot_table = 'product_category';

        return $this->belongsToMany(Product::class, $pivot_table);
    }
}
