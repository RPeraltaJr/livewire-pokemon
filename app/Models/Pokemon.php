<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pokemon extends Model
{
    protected $fillable = [
        'name',
        'pokedex_id',
        'height',
        'weight',
        'sprite',
        'sprite_shiny',
        'artwork',
        'artwork_shiny',
        'hp',
        'attack',
        'defense',
        'sp_attack',
        'sp_defense',
        'speed',
        'description',
        'generation_id'
    ];

    public function types()
    {
        return $this->belongsToMany(Type::class, 'pokemon_type');
    }

    public function generation()
    {
        return $this->belongsTo(Generation::class);
    }

    /**
     * Get pokemons by name or pokedex id
     */
    public function scopeSearch($query, $value)
    {
        $query->where('name', 'like', "%{$value}%")->orWhere('pokedex_id', 'like', "%{$value}%");
    }
}
