<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * {@inheritDoc}
     *
     * @override
     */
    public function getFillable()
    {
        return ['name', 'location', 'source'];
    }

    public function products(): BelongsToMany
    {
        $pivot_table = 'product_warehouse';

        return $this->belongsToMany(Product::class, $pivot_table)
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
