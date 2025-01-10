<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pokemon extends Model
{
    protected $fillable = [
        'name',
        'pokedex_id',
        'pokedex_id_string',
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

    public function getRouteKeyName()
    {
        return 'pokedex_id'; // Or whatever field you use for the route; this allows passing pokedex_id as the slug (ex. /pokemon/{POKEDEX_ID})
    }

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
        $query->where('name', 'like', "%{$value}%")->orWhere('pokedex_id_string', 'like', "%{$value}%");
    }
}
