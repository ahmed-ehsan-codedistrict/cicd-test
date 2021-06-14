<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            //'name' => "Hamza Bin Mansoor",
            'email' => "hamza.mansoor@codedistrict.com",
            'password' => "68a0099b3f45357798639a30c5fe3154",
            'CompanyNo' => 1,
            // 'tenant_id' => "1",
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
