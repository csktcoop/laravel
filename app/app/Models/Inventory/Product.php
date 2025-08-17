<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    public const PRODUCT_WAREHOUSE_QUANTITY_NON_STOCK_MANAGED = null;

    /**
     * {@inheritDoc}
     *
     * @override
     */
    public function getFillable()
    {
        return ['name', 'sku', 'description', 'source'];
    }

    public function getPrice($companyId = null, $date = null)
    {
        $date = $date ?? now();

        return $this->prices()
            ->where(function ($query) use ($companyId) {
                $query->whereNull('company_id')
                    ->orWhere('company_id', $companyId);
            })
            ->where(function ($query) use ($date) {
                $query->whereNull('start_date')->orWhere('start_date', '<=', $date);
            })
            ->where(function ($query) use ($date) {
                $query->whereNull('end_date')->orWhere('end_date', '>=', $date);
            })
            ->orderByDesc('start_date')
            ->first();
    }

    // <relations>
    public function prices(): HasMany
    {
        return $this->hasMany(Price::class);
    }

    public function warehouses(): BelongsToMany
    {
        $pivot_table = 'product_warehouse';

        return $this->belongsToMany(Warehouse::class, $pivot_table)
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function categories(): BelongsToMany
    {
        $pivot_table = 'product_category';

        return $this->belongsToMany(Category::class, $pivot_table)
            ->withTimestamps();
    }
    // </relations>
}
