<?php

use Illuminate\Database\Seeder;
use App\Models\Options;
use App\Models\COMPMS0;

class OptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $Companies =  COMPMS0::all();

    }
}
