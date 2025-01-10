<section class="Pokemon-table mx-auto bg-gray-100 px-10 py-10">

    <div class="Pokemon-table__search mb-6 flex flex-wrap">
        <div class="w-full mb-3">
            <label for="keyword" class="block text-sm/6 font-medium text-gray-900 sr-only">Search</label>
            <div class="relative mt-2 rounded-md shadow-sm">
                <input type="search" wire:model.live="search" id="keyword"
                    class="block w-full rounded-md border-0 py-2.5 px-4 text-gray-900 bg-white ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:text-sm/6"
                    placeholder="Search by name or number">
            </div>
        </div>
        <div class="grid gap-x-0 md:gap-x-4 gap-y-3 md:gap-y-0 grid-cols-1 md:grid-cols-6 w-full">
            <div>
                <label class="text-sm font-medium text-gray-900 sr-only">Type</label>
                <select wire:model.live="type"
                    class="block w-full rounded-md border-0 py-2.5 px-4 text-gray-900 bg-white ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:text-sm/6">
                    <option value="">Select Type</option>
                    @foreach ($types as $type)
                    <option value="{{ $type->name }}">{{ Str::title($type->name) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-900 sr-only">Generation</label>
                <select wire:model.live="generation"
                    class="block w-full rounded-md border-0 py-2.5 px-4 text-gray-900 bg-white ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:text-sm/6">
                    <option value="">Select Region</option>
                    @foreach ($generations as $generation)
                    <option value="{{ $generation->id }}">{{ Str::title($generation->main_region) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-900 sr-only">Sort by</label>
                <select wire:model.live="sort"
                    class="block w-full rounded-md border-0 py-2.5 px-4 text-gray-900 bg-white ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:text-sm/6">
                    <option value="">Sort By</option>
                    <option value="hp">HP</option>
                    <option value="attack">Attack</option>
                    <option value="defense">Defense</option>
                    <option value="sp_attack">Special Attack</option>
                    <option value="sp_defense">Special Defense</option>
                    <option value="speed">Speed</option>
                </select>
            </div>
            @if ($sort)
            <div>
                <label class="w-30 text-sm font-medium text-gray-900 sr-only">Order</label>
                <select wire:model.live="order"
                    class="block w-full rounded-md border-0 py-2.5 px-4 text-gray-900 bg-white ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:text-sm/6">
                    <option value="desc">High-Low</option>
                    <option value="asc">Low-High</option>
                </select>
            </div>
            @endif
            <div>
                <label class="w-30 text-sm font-medium text-gray-900 sr-only">Artwork</label>
                <select wire:model.live="artwork"
                    class="block w-full rounded-md border-0 py-2.5 px-4 text-gray-900 bg-white ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 sm:text-sm/6">
                    <option value="">Select Artwork</option>
                    <option value="shiny">Shiny</option>
                </select>
            </div>
            <div class="flex items-center text-sm">
                <button wire:click="resetFilters" class="hover:underline">
                    Reset
                </button>
            </div>
        </div>
    </div>

    <div class="Pokemon-table__pagination mb-8">
        {{ $pokemons->links() }}
    </div>


    @if ($pokemons->isEmpty())
    <div class="text-center py-10">
        <p class="text-gray-600 text-lg">No Pokémon found. Try adjusting your search or filters.</p>
    </div>
    @endif

    <ul class="Pokemon-table__list grid gap-x-2 gap-y-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-4 md:gap-y-2"
        role="list">
        @foreach ($pokemons as $pokemon)
        <li class="Pokemon-table__list__item rounded flex flex-wrap">
            <a href="/pokemon/{{ $pokemon->pokedex_id }}" class="bg-white border p-6 transition hover:border-black">
                <div>
                    {{-- <img src="{{ $pokemon->artwork }}" alt="{{ $pokemon->name }}" class="w-full bg-gray-100"> --}}
                    {{-- <img src="{{ $pokemon->artwork_shiny }}" alt="{{ $pokemon->name }} shiny"
                        class="w-full bg-gray-100"> --}}
                    @if ($artwork == 'shiny')
                    <img src="{{ $pokemon->artwork_shiny }}" alt="{{ $pokemon->name }} shiny"
                        class="w-full bg-gray-100">
                    @else
                    <img src="{{ $pokemon->artwork }}" alt="{{ $pokemon->name }}" class="w-full bg-gray-100">
                    @endif
                </div>
                <div class="flex w-full mt-3">
                    <div class="my-auto">
                        <p class="text-gray-500 text-sm">#{{ str_pad($pokemon->pokedex_id, 3, '0', STR_PAD_LEFT) }}</p>
                        <h1 class="text-2xl mb-1">
                            {{ Str::title($pokemon->name) }}
                        </h1>
                        <ul class="Pokemon-table__list__types flex gap-x-1">
                            @foreach($pokemon->types as $type)
                            <li
                                class="Pokemon-table__list__types__item flex flex-1 bg-{{ $type->name }} rounded p-1 items-center w-16 max-w-16 text-center">
                                <small class="font-bold text-xs w-full cursor-pointer transition duration-500"
                                    type="button" wire:click.prevent="setType('{{ $type->name }}')">
                                    {{ Str::title($type->name) }}
                                </small>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </a>
        </li>
        @endforeach
    </ul>

    <div class="Pokemon-table__pagination mt-8">
        {{ $pokemons->links() }}
    </div>
</section>