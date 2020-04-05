<?php

namespace Nksoft\Articles\database\seeds;

use Illuminate\Database\Seeder;

class NksoftArticlesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(NavigationsTableSeeder::class);
    }
}
