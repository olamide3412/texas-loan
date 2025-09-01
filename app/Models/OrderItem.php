<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
   // Belongs to an order
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Belongs to a product
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
