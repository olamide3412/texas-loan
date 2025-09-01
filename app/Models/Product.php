<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    // Product can be inside many order items
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
