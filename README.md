# Create a Laravel Project

# Install Livewire

# Set up Tailwind

# Create the Home page (rename Welcome.blade.php)

# Create PokemonTable livewire component

# Create a Migration for the Pokemon Table

`php artisan make:migration create_pokemon_table --create=pokemon`

1. Define the columns

**database/migrations/xxxx_xx_xx_create_pokemon_table.php**

```
public function up()
{
    Schema::create('pokemon', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('url');
        $table->timestamps();
    });
}
```

2. Run the migration to create the table
   `php artisan migrate`

# Make a Pokemon Model

`php artisan make:model Pokemon`

**Models/Pokemon.php**

```
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
        'hp',
        'attack',
        'defense',
        'sp_attack',
        'sp_defense',
        'speed',
    ];
}
```

# Create a Command to Fetch Data from the API

`php artisan make:command FetchPokemonData`

**Console/Commands/FetchPokemonData.php**

```
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Pokemon;

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
     * Execute the console command.
     */
    public function handle()
    {
        $url = "https://pokeapi.co/api/v2/pokemon?limit=151"; // adjust limit as needed
        $response = Http::get($url);

        if ($response->successful()) {
            $pokemonData = $response->json('results');

            foreach ($pokemonData as $pokemon) {
                $pokemonDetails = Http::get($pokemon['url'])->json(); // Get details for each Pokémon

                // Extract stats
                $stats = collect($pokemonDetails['stats'])->mapWithKeys(function ($stat) {
                    return [
                        strtolower(str_replace('-', '_', $stat['stat']['name'])) => $stat['base_stat']
                    ];
                });

                Pokemon::updateOrCreate(
                    [
                        'name'          => $pokemon['name'],
                        'pokedex_id'    => $pokemonDetails['id'],
                        'height'        => $pokemonDetails['height'],
                        'weight'        => $pokemonDetails['weight'],
                        'sprite'        => $pokemonDetails['sprites']['front_default'],
                        'sprite_shiny'  => $pokemonDetails['sprites']['front_shiny'],
                        'hp'            => $stats->get('hp'),
                        'attack'        => $stats->get('attack'),
                        'defense'       => $stats->get('defense'),
                        'sp_attack'     => $stats->get('special_attack'),
                        'sp_defense'    => $stats->get('special_defense'),
                        'speed'         => $stats->get('speed')
                    ]
                );
            }

            $this->info('Pokémon data inserted successfully!');
        } else {
            $this->error('Failed to fetch Pokémon data.');
        }
    }
}

```

# Run the Command

`php artisan fetch:pokemon`

# Create a Type Model

`php artisan make:model Type -m`

**Models/Type.php**

```
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    protected $fillable = [
        'name',
    ];
}
```

Update the migration file:

**database/migrations/xxxx_xx_xx_create_types_table.php**

```
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('types');
    }
};

```

# Create a Pivot Table for Pokémon and Types

`php artisan make:migration create_pokemon_type_table --create=pokemon_type`

Run the migration
`php artisan migrate`

# Define Relationships in the Models

**app/Models/Pokemon.php**

```
public function types()
{
    return $this->belongsToMany(Type::class, 'pokemon_type');
}
```

**app/Models/Type.php**

```
public function pokemon()
{
    return $this->belongsToMany(Pokemon::class, 'pokemon_type');
}
```

# Update the Fetch file

**app/Console/Commands/FetchPokemonData.php**

```
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
     * Execute the console command.
     */
    public function handle()
    {
        // $url = "https://pokeapi.co/api/v2/pokemon?limit=9"; // adjust limit as needed (https://pokeapi.co/)
        $url = "https://pokeapi.co/api/v2/pokemon?limit=905&offset=0";
        $response = Http::get($url);

        if ($response->successful()) {
            $pokemonData = $response->json('results');

            foreach ($pokemonData as $pokemon) {
                $pokemonDetails = Http::get($pokemon['url'])->json(); // Get details for each Pokémon

                // Extract stats
                $stats = collect($pokemonDetails['stats'])->mapWithKeys(function ($stat) {
                    return [
                        strtolower(str_replace('-', '_', $stat['stat']['name'])) => $stat['base_stat']
                    ];
                });

                // Get or create the Pokemon record
                $pokemonModel = Pokemon::updateOrCreate(
                    [
                        'name'          => $pokemon['name'],
                        'pokedex_id'    => str_pad($pokemonDetails['id'], 3, '0', STR_PAD_LEFT), // 009
                        'height'        => $pokemonDetails['height'],
                        'weight'        => $pokemonDetails['weight'],
                        'sprite'        => $pokemonDetails['sprites']['front_default'],
                        'sprite_shiny'  => $pokemonDetails['sprites']['front_shiny'],
                        'artwork'       => $pokemonDetails['sprites']['other']['official-artwork']['front_default'],
                        'artwork_shiny' => $pokemonDetails['sprites']['other']['official-artwork']['front_shiny'],
                        'hp'            => $stats->get('hp'),
                        'attack'        => $stats->get('attack'),
                        'defense'       => $stats->get('defense'),
                        'sp_attack'     => $stats->get('special_attack'),
                        'sp_defense'    => $stats->get('special_defense'),
                        'speed'         => $stats->get('speed')
                    ]
                );

                // Sync types
                $typeIds = [];
                foreach ($pokemonDetails['types'] as $typeData) {
                    $typeName = $typeData['type']['name'];
                    $type = Type::firstOrCreate(['name' => $typeName]);
                    $typeIds[] = $type->id;
                }
                $pokemonModel->types()->sync($typeIds); // Associate types with the Pokemon

            }

            $this->info('Pokémon data inserted successfully!');
        } else {
            $this->error('Failed to fetch Pokémon data.');
        }
    }
}
```

# Run the command

`php artisan fetch:pokemon`

# Create a Home Page and Detail Page

```
php artisan livewire:make PokemonHomePage
php artisan livewire:make PokemonDetailPage
```

# Create a Generation model

`php artisan make:model Generation -m`

# Define the Generation table structure

**database/migrations/create_generations_table.php**

```
Schema::create('generations', function (Blueprint $table) {
    $table->id();
    $table->string('name')->unique(); // e.g., "generation-i"
    $table->timestamps();
});

```

# Set Up Relationships in the Pokemon and Generation models

**app/Models/Pokemon.php**

```
public function generation()
{
    return $this->belongsTo(Generation::class);
}
```

**app/Models/Generation.php**

```
class Generation extends Model
{
    protected $fillable = [
        'name',
    ];

    public function pokemon()
    {
        return $this->hasMany(Pokemon::class);
    }
}
```

# Update FetchPokemonData.php

```
$generationName = $pokemonDetails['generation']['name'];

// Find or create the generation
$generation = Generation::firstOrCreate(['name' => $generationName]);

// Save or update the Pokemon model
$pokemonModel = Pokemon::updateOrCreate(
    ['pokedex_id' => $pokemonStats['id']],
    [
        // other fields
        'generation_id' => $generation->id, // Associate the generation
    ]
);
```

# Migration for generation_id in Pokemon table. Add a generation_id column to your pokemon table to store the foreign key.

`php artisan make:migration add_generation_id_to_pokemon_table --table=pokemon`

OR

Add to **migrations/create_pokemon_table.php** and do a refresh.

`$table->foreignId('generation_id')->nullable()->constrained('generations')->cascadeOnDelete();`

`php artisan migrate:refresh`
