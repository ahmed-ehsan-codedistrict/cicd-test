<?php

use Illuminate\Database\Seeder;
use App\Models\Brand;
use App\Models\ProdPLM;

class BrandLogoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $Brands =  ProdPLM::getDistinctBrands();
        foreach ($Brands as $value) {

            Brand::Create(
                [
                    'Name' => $value->Brand,
                    'CompanyNo' => $value->CompanyNo
                ]
            );
        }
    }
}
