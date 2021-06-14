<?php

use Illuminate\Database\Seeder;
use App\Models\COMPMS0;
use App\Models\Options;

class LinesheetStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $Visibility = $this->visibilityArr();
        $Status = $this->statusArr();
        $Archived = $this->archivedArr();
        $Companies = COMPMS0::all();
        foreach ($Companies as $value) {
            foreach ($Visibility as $key => $item) {
                Options::updateOrCreate(
                    [
                        'DisplayID' => $key,
                        'DisplayValue' => $item,
                        'CompanyNo' => $value->CompanyNo,
                        'TableName' => 'LineSheets',
                        'TableColumn' => 'visibility'
                    ],
                    [
                        'DisplayID' => $key,
                        'DisplayValue' => $item,
                        'CompanyNo' => $value->CompanyNo,
                        'TableName' => 'LineSheets',
                        'TableColumn' => 'visibility'
                    ]
                );
            }
            foreach ($Status as $key => $item) {
                Options::updateOrCreate(
                    [
                        'DisplayID' => $key,
                        'DisplayValue' => $item,
                        'CompanyNo' => $value->CompanyNo,
                        'TableName' => 'LineSheets',
                        'TableColumn' => 'status'
                    ],
                    [
                        'DisplayID' => $key,
                        'DisplayValue' => $item,
                        'CompanyNo' => $value->CompanyNo,
                        'TableName' => 'LineSheets',
                        'TableColumn' => 'status'
                    ]
                );
            }
            foreach ($Archived as $key => $item) {
                Options::updateOrCreate(
                    [
                        'DisplayID' => $key,
                        'DisplayValue' => $item,
                        'CompanyNo' => $value->CompanyNo,
                        'TableName' => 'LineSheets',
                        'TableColumn' => 'isArchived'
                    ],
                    [
                        'DisplayID' => $key,
                        'DisplayValue' => $item,
                        'CompanyNo' => $value->CompanyNo,
                        'TableName' => 'LineSheets',
                        'TableColumn' => 'isArchived'
                    ]
                );
            }
        }
    }

    //LineSheet Status Array

    public function visibilityArr()
    {
        return array(
            '0' => 'Public',
            '1' => 'Private',
            '2' => 'Shared',
        );
    }

    public function statusArr()
    {
        return array(
            '0' => 'Inactive',
            '1' => 'Active',
        );
    }

    public function archivedArr()
    {
        return array(
            '0' => 'Archived',
            '1' => 'NotArchived',
        );
    }
}
