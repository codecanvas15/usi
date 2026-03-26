<?php

namespace App\Exports\SaleOrderTrading;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class SaleOrderTradingFakturPajakExport implements FromView, ShouldAutoSize, WithColumnFormatting, WithEvents
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
                // give border to data A6 to H6 + 7
                $event->sheet->getDelegate()->getStyle('A6:I' . (count($this->data['data']) + 7))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                $event->sheet->getDelegate()->getCell('A1')->setValue('PT UNITED SHIPPING INDONESIA')->getStyle()->getFont()->setBold(true);
                $event->sheet->getDelegate()->getCell('A3')->setValue('LAPORAN PENJUALAN TRADING DENGAN FAKTUR PAJAK')->getStyle()->getFont()->setBold(true);
                $event->sheet->getDelegate()->getCell('A4')->setValue('Tanggal : ' . request('from_date') . ' s/d ' . request('to_date'))->getStyle()->getFont()->setBold(true);

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

                $event->sheet->getDelegate()->getCell('A' . (count($this->data['data']) + 7))->setValue('Total')->getStyle()->getFont()->setBold(true);
                $event->sheet->getDelegate()->mergeCells('A' . (count($this->data['data']) + 7) . ':E' . (count($this->data['data']) + 7));

                // sub total to array
                $sub_total = array_column($this->data['data']->toArray(), 'subtotal');
                $event->sheet->getDelegate()->getCell('F' . (count($this->data['data']) + 7))->setValue(number_format(array_sum($sub_total), 2, ',', '.'))->getStyle()->getFont()->setBold(true);

                // ppn to array
                $ppn = array_column($this->data['data']->toArray(), 'additional_tax_total');
                $event->sheet->getDelegate()->getCell('G' . (count($this->data['data']) + 7))->setValue(number_format(array_sum($ppn), 2, ',', '.'))->getStyle()->getFont()->setBold(true);

                // grand total is from sub total + ppn
                $grand_total = array_sum($sub_total) + array_sum($ppn);
                $event->sheet->getDelegate()->getCell('H' . (count($this->data['data']) + 7))->setValue(number_format($grand_total, 2, ',', '.'))->getStyle()->getFont()->setBold(true);
            }
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}