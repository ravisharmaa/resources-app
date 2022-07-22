<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'properties' => 'array'
    ];

    public function properties(): Attribute
    {
        return Attribute::make(
            set: function ($value) {
                return json_encode($value, JSON_PRETTY_PRINT);
            }
        );
    }
}
