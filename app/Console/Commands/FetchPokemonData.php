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
    // protected $offset = 3;
    // protected $limit = 6;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // * Start time
        $startTime = microtime(true);

        $url = "https://pokeapi.co/api/v2/pokemon?limit={$this->limit}&offset={$this->offset}"; // https://pokeapi.co/
        $response = Http::get($url);

        if ($response->successful()) {
            $pokemonData = $response->json('results');
            $typeCache = Type::all()->keyBy('name');
            $generationCache = [];

            $this->info('Loading Pokémon data...');

            $bulkInsertData = [];
            $bulkTypeSyncData = [];
            $count = 0;
            $totalPokemon = count($pokemonData); // Total number of Pokémon to process

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

                $pokemonPokedexID = str_pad($pokemonStats['id'], 3, '0', STR_PAD_LEFT);

                // Prepare data for bulk insert
                $bulkInsertData[] = [
                    'pokedex_id'        => $pokemonPokedexID,
                    'pokedex_id_string' => $pokemonPokedexID,
                    'name'              => $pokemon['name'],
                    'height'            => $pokemonStats['height'],
                    'weight'            => $pokemonStats['weight'],
                    'sprite'            => $pokemonStats['sprites']['front_default'],
                    'sprite_shiny'      => $pokemonStats['sprites']['front_shiny'],
                    'artwork'           => $pokemonStats['sprites']['other']['official-artwork']['front_default'],
                    'artwork_shiny'     => $pokemonStats['sprites']['other']['official-artwork']['front_shiny'],
                    'hp'                => $stats['hp'],
                    'attack'            => $stats['attack'],
                    'defense'           => $stats['defense'],
                    'sp_attack'         => $stats['special_attack'],
                    'sp_defense'        => $stats['special_defense'],
                    'speed'             => $stats['speed'],
                    'description'       => $pokemonDescription,
                    'generation_id'     => $generation->id,
                ];

                // Prepare types for syncing
                foreach ($pokemonStats['types'] as $typeData) {
                    $typeName = $typeData['type']['name'];
                    if (!isset($typeCache[$typeName])) {
                        $typeCache[$typeName] = Type::create(['name' => $typeName]);
                    }
                    $bulkTypeSyncData[$pokemonStats['id']][] = $typeCache[$typeName]->id;
                }

                $count++;

                // * Calculate and display the progress
                $percentage = round(($count / $totalPokemon) * 100);
                echo "\r{$percentage}%\r";  // Print progress
            }

            // Bulk insert Pokémon data
            Pokemon::upsert($bulkInsertData, ['pokedex_id'], [
                'pokedex_id_string',
                'name',
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
            ]);

            // Sync types in bulk
            foreach ($bulkTypeSyncData as $pokedexId => $typeIds) {
                $pokemon = Pokemon::where('pokedex_id', $pokedexId)->first();
                if ($pokemon) {
                    $pokemon->types()->sync($typeIds);
                }
            }

            $this->info('Pokémon data inserted successfully!');
        } else {
            $this->error('Failed to fetch Pokémon data.');
        }

        // * End time
        $endTime = microtime(true);

        // * Calculate and display the execution time
        $executionTime = $endTime - $startTime;
        $this->info("Execution Time: " . round($executionTime, 2) . " seconds");
    }
}
