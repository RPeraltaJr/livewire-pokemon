<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Type extends Model
{
    protected $fillable = [
        'name',
    ];

    public function pokemon()
    {
        return $this->belongsToMany(Pokemon::class, 'pokemon_type');
    }
}
