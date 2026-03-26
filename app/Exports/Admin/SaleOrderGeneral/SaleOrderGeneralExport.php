<?php

namespace App\Exports\Admin\SaleOrderGeneral;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class SaleOrderGeneralExport implements FromView, ShouldAutoSize, WithColumnFormatting, WithEvents
{
    public $data;
    public $view;

    public function __construct($view, $data)
    {
        $this->data = $data;
        $this->view = $view;
    }

    public function view(): View
    {
        return view($this->view, $this->data);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $no = 0;
                foreach ($this->data['data'] as $key => $item) {
                    foreach ($item as $key => $item_detail) {
                        if ($item_detail->payment_status == 'paid') {
                            $event->sheet->getStyle('A' . (8 + $no) . ':P' . (8 + $no))->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB(Color::COLOR_GREEN);
                        }
                        $no++;
                    }
                }

                $event->sheet->getDelegate()->getStyle('C')->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getStyle('C')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
                $event->sheet->getDelegate()->getStyle('C')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

                $event->sheet->getDelegate()->getRowDimension(1)->setRowHeight(-1);
                $event->sheet->getDelegate()->getColumnDimension('C')->setAutoSize(true);

                $event->sheet->getDelegate()->getStyle('H')->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getStyle('H')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
                $event->sheet->getDelegate()->getStyle('H')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

                $event->sheet->getDelegate()->getRowDimension(1)->setRowHeight(-1);
                $event->sheet->getDelegate()->getColumnDimension('H')->setAutoSize(true);
            }
        ];
    }

    public function columnFormats(): array
    {
        return [
            'M' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'N' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'O' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}
