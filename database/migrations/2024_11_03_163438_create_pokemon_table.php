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
        Schema::create('pokemon', function (Blueprint $table) {
            $table->id();
            $table->integer('pokedex_id')->unique();
            $table->string('pokedex_id_string')->nullable();
            $table->string('name');
            // $table->string('type');
            $table->integer('height')->nullable();
            $table->integer('weight')->nullable();
            $table->string('sprite')->nullable();
            $table->string('sprite_shiny')->nullable();
            $table->string('artwork')->nullable();
            $table->string('artwork_shiny')->nullable();
            $table->integer('hp')->nullable();
            $table->integer('attack')->nullable();
            $table->integer('defense')->nullable();
            $table->integer('sp_attack')->nullable();
            $table->integer('sp_defense')->nullable();
            $table->integer('speed')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('generation_id')->nullable()->constrained('generations')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pokemon');
    }
};
