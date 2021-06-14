<?php

use Illuminate\Database\Seeder;
use App\Models\Options;
use App\Models\COMPMS0;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $OrderStatus = $this->orderStatusArr();
        $worksheetType = $this->worksheetType();
        $Companies = COMPMS0::all();
        foreach ($Companies as $value) {
            foreach ($OrderStatus as $key => $item) {
                Options::updateOrCreate(
                    [
                        'DisplayID' => $key,
                        'DisplayValue' => $item,
                        'CompanyNo' => $value->CompanyNo,
                        'TableName' => 'PreOrderHdr',
                        'TableColumn' => 'PreOrderStatus'
                    ],
                    [
                        'DisplayID' => $key,
                        'DisplayValue' => $item,
                        'CompanyNo' => $value->CompanyNo,
                        'TableName' => 'PreOrderHdr',
                        'TableColumn' => 'PreOrderStatus'
                    ]
                );
            }
            foreach ($worksheetType as $key => $wt) {
                Options::updateOrCreate(
                    [
                        'DisplayID' => $key,
                        'DisplayValue' => $wt,
                        'CompanyNo' => $value->CompanyNo,
                        'TableName' => 'PreOrderHdr',
                        'TableColumn' => 'WorkSheetType'
                    ],
                    [
                        'DisplayID' => $key,
                        'DisplayValue' => $wt,
                        'CompanyNo' => $value->CompanyNo,
                        'TableName' => 'PreOrderHdr',
                        'TableColumn' => 'WorkSheetType'
                    ]
                );
            }
        }
    }



    //Order Status Array

    public function orderStatusArr()
    {
        return array(
            'P' => 'Production',
            'S' => 'Sourcing',
            'D' => 'Draft',
            'F' => 'Finalized',
            'M' => 'Marchendise',
        );
    }

    public function worksheetType()
    {
        return array(
            'O' => 'Order',
            'S' => 'Sourcing',

        );
    }
}
