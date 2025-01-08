<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Pokemon;
use App\Models\Type;
use App\Models\Generation;

class FetchPokemonData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:pokemon';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Pokémon data from the API and insert into the database';

    /**
     * Offset and Limit for API (https://pokeapi.co/)
     *
     * @var integer
     */
    protected $offset = 0;
    protected $limit = 1025;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $url = "https://pokeapi.co/api/v2/pokemon?limit={$this->limit}&offset={$this->offset}"; // https://pokeapi.co/
        $response = Http::get($url);

        if ($response->successful()) {
            $pokemonData = $response->json('results');
            $typeCache = Type::all()->keyBy('name');
            $generationCache = [];

            foreach ($pokemonData as $pokemon) {
                $pokemonStatsResponse = Http::retry(3, 100)->get($pokemon['url']);
                if (!$pokemonStatsResponse->successful()) {
                    $this->error("Failed to fetch stats for Pokémon: {$pokemon['name']}");
                    continue;
                }
                $pokemonStats = $pokemonStatsResponse->json();

                // * Extract stats
                $stats = collect($pokemonStats['stats'])->mapWithKeys(function ($stat) {
                    return [
                        strtolower(str_replace('-', '_', $stat['stat']['name'])) => $stat['base_stat']
                    ];
                });

                // * Extract Details with Description in English
                $pokemonDetails = Http::get("https://pokeapi.co/api/v2/pokemon-species/{$pokemonStats['id']}")->json();
                $pokemonDescription = collect($pokemonDetails['flavor_text_entries'])
                    ->firstWhere('language.name', 'en')['flavor_text'] ?? null;

                if ($pokemonDescription) {
                    // Convert to UTF-8 to handle any misencoded characters
                    $pokemonDescription = mb_convert_encoding($pokemonDescription, 'UTF-8', 'auto');
                }

                // * Find or create the generation with main_region
                $generationName = $pokemonDetails['generation']['name'];

                if (!isset($generationCache[$generationName])) {
                    $generationDetails = Http::retry(3, 100)->get("{$pokemonDetails['generation']['url']}")->json();
                    $mainRegion = $generationDetails['main_region']['name'];

                    $generation = Generation::updateOrCreate(
                        ['name' => $generationName],
                        ['main_region' => $mainRegion]
                    );

                    $generationCache[$generationName] = $generation;
                } else {
                    $generation = $generationCache[$generationName];
                }

                // * Get or create the Pokemon record
                $pokemonModel = Pokemon::updateOrCreate(
                    [
                        'pokedex_id'    => str_pad($pokemonStats['id'], 3, '0', STR_PAD_LEFT), // Ensure uniqueness by Pokedex ID
                    ],
                    [
                        'name'          => $pokemon['name'],
                        'height'        => $pokemonStats['height'],
                        'weight'        => $pokemonStats['weight'],
                        'sprite'        => $pokemonStats['sprites']['front_default'],
                        'sprite_shiny'  => $pokemonStats['sprites']['front_shiny'],
                        'artwork'       => $pokemonStats['sprites']['other']['official-artwork']['front_default'],
                        'artwork_shiny' => $pokemonStats['sprites']['other']['official-artwork']['front_shiny'],
                        'hp'            => $stats->get('hp'),
                        'attack'        => $stats->get('attack'),
                        'defense'       => $stats->get('defense'),
                        'sp_attack'     => $stats->get('special_attack'),
                        'sp_defense'    => $stats->get('special_defense'),
                        'speed'         => $stats->get('speed'),
                        'description'   => $pokemonDescription,
                        'generation_id' => $generation->id, // Associate the generation
                    ]
                );

                // * Sync types
                $typeIds = [];
                foreach ($pokemonStats['types'] as $typeData) {
                    $typeName = $typeData['type']['name'];
                    if (!isset($typeCache[$typeName])) {
                        $typeCache[$typeName] = Type::create(['name' => $typeName]);
                    }
                    $typeIds[] = $typeCache[$typeName]->id;
                }
                $pokemonModel->types()->sync($typeIds);
            }

            $this->info('Pokémon data inserted successfully!');
        } else {
            $this->error('Failed to fetch Pokémon data.');
        }
    }
}
