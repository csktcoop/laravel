<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Price extends Model
{
    use HasFactory, SoftDeletes;

    public const STORE_ID_ALL_STORES = 0;

    /**
     * {@inheritDoc}
     *
     * @override
     */
    public function getFillable()
    {
        return [
            'product_id',
            'store_id',
            // 'customer_group_id',
            // 'promotion_id',
            'amount',
            'currency',
            'start_date', // at 00:00 AM
            'end_date', // at 00:00 AM
            'source',
            'note',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
