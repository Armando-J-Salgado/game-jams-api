<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Al tratarse de Game Jams, estas son algunas categorías hiper conocidas.
        Category::create(['name' => 'Action RPG']);
        Category::create(['name' => 'Horror Survival']);
        Category::create(['name' => '2D Platformer']);
        Category::create(['name' => 'Puzzle Casual']);
        Category::create(['name' => 'Multiplayer Arena']);
    }
}
