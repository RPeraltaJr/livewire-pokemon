<?php

use App\Livewire\PokemonDetailPage;
use App\Livewire\PokemonHomePage;
use Illuminate\Support\Facades\Route;

Route::get('/', PokemonHomePage::class);

Route::get('/pokemon/{pokemon}', PokemonDetailPage::class);
