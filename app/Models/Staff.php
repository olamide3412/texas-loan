<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Staff extends Model
{
    protected $appends = ['full_name','full_address','client_photo'];

     public function getFullNameAttribute(){
        $firstName = $this->first_name ? ucfirst($this->first_name) : '';
        $lastName = $this->last_name ? ucfirst($this->last_name) : '';
        $otherName = $this->other_name ? ucfirst($this->other_name) : '';


        $fullName = trim("{$firstName} {$lastName} {$otherName}");

        // Optionally, you can return a placeholder if the full name is empty
        return !empty($fullName) ? $fullName : 'No Name Provided';
    }

    public function getFullAddressAttribute(){

        $fullAddress = trim("{$this->residential_address}, {$this->local_government}, {$this->state} state");
        // Optionally, you can return a placeholder if the full name is empty
        return !empty($fullAddress) ? $fullAddress : 'No address Provided';
    }

    public function getClientPhotoAttribute(){
        $photo = $this->attributes['photo'] ?? '';
        if (!empty($photo) && Storage::exists($photo)) {
            return Storage::temporaryUrl($this->attributes['photo'], now()->addMinutes(5));
        }
        return null; // Vite::asset('resources/images/client_default.jpg'); // Provide a default image path if no photo is uploaded
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
