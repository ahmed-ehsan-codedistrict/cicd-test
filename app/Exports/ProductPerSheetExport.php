<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\ProductExports;

class ProductPerSheetExport implements WithMultipleSheets
{
    use Exportable;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];
        //make tempData array to copy data from actaul array
        $tempData = [];
        $tempData['headings'] = $this->data['headings'];
        $totalRecords =  count($this->data['ProductInfo']);

        foreach ($this->data['groupName'] as $value) {
            //prepare data for individual sheet
            for ($idx = 0; $idx < $totalRecords; $idx++) {
                if ($value == $this->data['ProductInfo'][$idx]['Group']) {
                    $tempData['ProductInfo'][] = $this->data['ProductInfo'][$idx];
                    $tempData['imageURI'][] = $this->data['imageURI'][$idx];
                }
            }
            $sheets[] = new ProductExports($tempData, $value);
            $tempData['ProductInfo'] = [];
            $tempData['imageURI'] = [];
        }

        return $sheets;
    }
}
