<?php

namespace App\Livewire;

use App\Models\Pokemon;
// use Livewire\Attributes\Title;
use Livewire\Component;

// #[Title("$pokemon->name")]
class PokemonDetailPage extends Component
{
    public $pokemon;

    public function mount(Pokemon $pokemon)
    {
        $this->pokemon = $pokemon;
    }

    public function render()
    {
        return view('livewire.pokemon-detail-page', [
            // 'pokemonCount' => Pokemon::count(), // Efficient count query
        ])->title(ucwords($this->pokemon->name) . '- Pokédex');
    }
}
