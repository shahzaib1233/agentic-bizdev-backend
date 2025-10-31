<?php

namespace App\Models\BusinessDetails;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessDetailsModel extends Model
{
    //
    protected $table = 'clients';
      use SoftDeletes;

    protected $fillable = ['data'];

    protected $casts = [
        'data' => 'array', // returns associative array automatically
    ];

    // Convenience getters (optional)
    public function getBusinessNameAttribute(): ?string
    {
        return data_get($this->data, 'business_profile.business_name');
    }

    public function getContactEmailAttribute(): ?string
    {
        return data_get($this->data, 'contact.primary.email');
    }
}
