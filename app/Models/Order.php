<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
  // Each order belongs to a client
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    // Each order has many order items
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // If order has employment details (for employed clients)
    public function employmentDetail(): HasOne
    {
        return $this->hasOne(EmploymentDetail::class);
    }

    // Order is created by a staff user
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Order approved by a staff user
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Order rejected by a staff user
    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    // Order given by a staff user
    public function givenBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'given_by');
    }
}
