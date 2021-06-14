<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\User;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;

class ProductExports implements FromArray, WithHeadings, ShouldAutoSize, WithStyles, WithColumnWidths, WithEvents, WithTitle
{
    protected $data;
    protected $title;

    public function __construct($data, $title = "")
    {
        $this->data = $data;
        $this->title = $title;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        return  $this->data['ProductInfo'];
    }

    public function headings(): array
    {
        return  $this->data['headings'];
    }
    public function styles(Worksheet $worksheet)
    {
        $kVal = 2;
        foreach ($this->data['imageURI'] as $key => $value) {
            $worksheet->getRowDimension($kVal)->setRowHeight(50);
            $kVal++;
        }
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [];
    }
    public function registerEvents(): array
    {
        $count = [
            count($this->data['headings']), //column count
            count($this->data['ProductInfo']) ? count($this->data['ProductInfo']) : 0 //row count, if there are images
        ];
        return [
            AfterSheet::class => function (AfterSheet $event) use ($count) {
                //Freeze frist row
                $event->sheet->freezePane('A2', 'A2');

                //Set auto width for the rest
                $columnindex = array(
                    'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
                    'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
                    'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ'
                );
                for ($i = 0; $i < $count[0]; $i++) //iterate based on column count
                {
                    if ($i > 76) break;

                    if ($count[1] && $i == 0) // Exception for image column, if there are images
                        $event->sheet->getColumnDimension('A')->setWidth(17);
                    else
                        $event->sheet->getColumnDimension($columnindex[$i])->setAutoSize(true);
                }

                //Set row height
                for ($i = 0; $i < $count[1]; $i++) //iterate based on row count
                {
                    $event->sheet->getRowDimension($i + 2)->setRowHeight(60);
                }

                if ($count[1]) {
                    foreach ($this->data['imageURI'] as $key => $value) {
                        $drawing = new Drawing();
                        $drawing->setName('image');
                        $drawing->setDescription('image');
                        $drawing->setPath(public_path($value));
                        $drawing->setHeight(70);
                        $drawing->setOffsetX(5);
                        $drawing->setOffsetY(5);
                        $drawing->setCoordinates($columnindex[$count[0]] . ($key + 2));
                        $drawing->setWorksheet($event->sheet->getDelegate());
                    }
                }
            },
        ];
    }

    public function title(): string
    {
        return $this->title;
    }

    // public function drawings()
    // {
    //     $image = [];
    //     $kVal = 2;
    //     $drawing = "drawing";
    //     foreach ($this->data['imageURI'] as $key => $value) {

    //         $drawing  = new Drawing();
    //         $drawing->setPath(public_path($value));

    //         $drawing->setHeight(10);
    //         $drawing->setWidth(10);
    //         $drawing->setCoordinates('K' . $kVal);
    //         array_push($image,  $drawing);

    //         $kVal++;
    //     }
    //     return $image;
    // }
}
