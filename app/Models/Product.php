<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $appends = ['product_photo'];

    public function getProductPhotoAttribute(){
        $photo = $this->attributes['photo'] ?? '';
        if (!empty($photo) && Storage::exists($photo)) {
            return Storage::temporaryUrl($this->attributes['photo'], now()->addMinutes(30));
        }
        return null; // Vite::asset('resources/images/client_default.jpg'); // Provide a default image path if no photo is uploaded
    }
    // Product can be inside many order items
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
