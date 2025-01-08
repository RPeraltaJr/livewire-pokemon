<?php

namespace App\Livewire;

use Livewire\Attributes\Title;
use App\Models\Pokemon;
use App\Models\Type;
use App\Models\Generation;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Pokédex')]
class PokemonHomePage extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $generation = ''; // Empty by default for 'all'

    #[Url(history: true)]
    public $type = ''; // Empty by default for 'all'

    #[Url(history: true)]
    public $sort = ''; // Empty by default

    #[Url(history: true)]
    public $order = 'desc';

    #[Url(history: true)]
    public $artwork = '';

    #[Url()]
    public $perPage = 16;

    /**
     * Handles changes to filter variables and resets pagination.
     */
    public function updated($propertyName)
    {
        $this->resetPage(); // Resets pagination to page 1 for any update
    }

    /**
     * Set the selected type and reset pagination.
     */
    public function setType($type)
    {
        $this->type = $type;
        $this->resetPage(); // sets page to page #1 when searching
    }

    /**
     * Reset all filters and return to page #1
     */
    public function resetFilters()
    {
        $this->reset(['search', 'generation', 'type', 'sort', 'order', 'artwork']); // Reset specific properties
        $this->perPage = 16; // Reset to default perPage
        $this->resetPage();
    }


    public function render()
    {
        $pokemons = Pokemon::search($this->search)
            ->when($this->type !== '', function ($query) {
                // many-to-many relationship
                $query->whereHas('types', function ($query) {
                    $query->where('name', $this->type);
                });
            })
            ->when($this->generation !== '', function ($query) {
                $query->where('generation_id', $this->generation);
            })
            ->when($this->sort, function ($query) {
                $order = in_array($this->order, ['asc', 'desc']) ? $this->order : 'desc';
                $query->orderBy($this->sort, $order);
            })
            ->paginate($this->perPage);

        return view('livewire.pokemon-home-page', [
            'generations'   => Generation::all(),
            'types'         => Type::all(), // Cached or sorted as needed
            'pokemons'      => $pokemons,
        ]);
    }
}
