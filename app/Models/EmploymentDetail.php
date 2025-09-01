<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmploymentDetail extends Model
{
   protected $casts = [
        'guarantors' => 'array',
    ];

    // Employment detail belongs to a client
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    // Employment detail belongs to an order
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
