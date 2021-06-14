<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\COMPMS0;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        COMPMS0::firstOrCreate([
            'CompanyNo' => 1,
            'DomainPrefix' => "swatfame",
            'CONM2C' => "SWAT FAME INC"
        ]);
        
        COMPMS0::firstOrCreate([
            'CompanyNo' => 2,
            'DomainPrefix' => "codedistrict",
            'CONM2C' => "Code District"
        ]);
    }
}
