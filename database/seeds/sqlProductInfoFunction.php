<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class sqlProductInfoFunction extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()

    {


        $path = public_path('Sql/ProductInfo.sql');

        $sql = file_get_contents($path);

        DB::unprepared($sql);

    }
}
