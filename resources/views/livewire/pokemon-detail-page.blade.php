<div class="bg-white px-12 py-10">
    <script>
        const goBack = () => {
            if (document.referrer.includes('/pokemon')) {
                window.location.href = '/';
            } else {
                history.back();
            }
        }
    </script>
    <p class="text-sm text-black mb-6 cursor-pointer hover:underline inline-block" onclick="goBack();" type="button">
        <span class="fa fa-long-arrow-left text-xs mr-1"></span> Back to Pokédex
    </p>

    <div class="flex flex-wrap gap-x-16">
        <div class="md: ">
            <img src="{{ $pokemon->artwork }}" alt="{{ $pokemon->name }}" class="w-full max-w-sm bg-gray-100">
            <h1 class="text-3xl mt-4 mb-2">
                {{ Str::title($pokemon->name) }} <small class="text-gray-500">[#{{ str_pad($pokemon->pokedex_id, 3, '0',
                    STR_PAD_LEFT) }}]</small>
            </h1>
            <ul class="Pokemon-table__list__types flex gap-x-1">
                @foreach($pokemon->types as $type)
                <li class="Pokemon-table__list__types__item flex">
                    <p class="bg-{{ $type->name }} rounded py-1 px-3 items-center w-20 max-w-20 text-center">{{
                        Str::title($type->name) }}</p>
                </li>
                @endforeach
            </ul>
        </div>
        <div class="flex-1">
            <ul class="grid gap-y-8">
                <li>
                    <p class="font-bold mb-4">Description</p>
                    <p>{{ $pokemon->description }}</p>
                    <p class="border inline-block py-2 px-3 rounded mt-4">
                        {{ Str::title($pokemon->generation->main_region) }} Region
                    </p>
                    <p class="border inline-block py-2 px-3 rounded mt-4">
                        Height: {{ round($pokemon->height * 0.1 * 3.28084) }}'{{ round(($pokemon->height * 0.1 *
                        3.28084 - ($pokemon->height * 0.1
                        * 3.28084)) * 1) }}" ft
                    </p>
                    <p class="border inline-block py-2 px-3 rounded mt-4">
                        Weight: {{ round($pokemon->weight * 0.1 * 2.20462, 2) }} lbs
                    </p>
                </li>
                <li>
                    <p class="font-bold text-center mb-4">Base Stats</p>
                    <table
                        class="charts-css bar show-labels show-heading charts-css-type-{{ $pokemon->types[0]->name }}">
                        <tbody>
                            <tr>
                                <th scope="row" title="HP">HP</th>
                                <td style="--size: {{ $pokemon->hp / 255 }};">
                                    {{ $pokemon->hp }}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" title="Attack">Atk</th>
                                <td style="--size: {{ $pokemon->attack / 255 }};">
                                    {{ $pokemon->attack }}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" title="Defense">Def</th>
                                <td style="--size: {{ $pokemon->defense / 255 }};">
                                    {{ $pokemon->defense }}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" title="Special Attack">Sp Atk</th>
                                <td style="--size: {{ $pokemon->sp_attack / 255 }};">
                                    {{ $pokemon->sp_attack }}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" title="Special Defense">Sp Def</th>
                                <td style="--size: {{ $pokemon->sp_defense / 255 }};">
                                    {{ $pokemon->sp_defense }}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" title="Speed">Speed</th>
                                <td style="--size: {{ $pokemon->speed / 255 }};">
                                    {{ $pokemon->speed }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </li>
            </ul>
        </div>
    </div>

    {{-- <ul class="nav flex justify-between mt-12">
        <li class="nav__item">
            @if($pokemon->id > 1)
            <a href="/pokemon/{{ $pokemon->id - 1 }}" class="border py-2 px-3 inline-block rounded" title="[#{{ str_pad($pokemon->pokedex_id - 1, 3, '0',
                    STR_PAD_LEFT) }}]">
                <span class="fa fa-chevron-left mr-1"></span> Previous
            </a>
            @endif
        </li>
        <li class="nav__item">
            @if($pokemon->id < $pokemonCount) <a href="/pokemon/{{ $pokemon->id + 1 }}"
                class="border py-2 px-3 inline-block rounded" title="[#{{ str_pad($pokemon->pokedex_id + 1, 3, '0',
                    STR_PAD_LEFT) }}]">
                Next <span class="fa fa-chevron-right ml-1"></span>
                </a>
                @endif
        </li>
    </ul> --}}

</div>