<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Generation extends Model
{
    protected $fillable = [
        'name',
        'main_region',
    ];

    public function pokemon()
    {
        return $this->hasMany(Pokemon::class);
    }
}
