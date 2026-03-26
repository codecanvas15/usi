<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\Item;
use Illuminate\Support\Collection;

class ItemExcelExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $type;

    public function __construct($type)
    {
        $this->type = $type;
    }

    public function collection()
    {
        $items = Item::with('item_category')
        ->where('type', $this->type)
            ->get();

        // Return only specific fields
        return $items->map(function ($item) {
            return [
                'kode' => $item->kode,
                'nama' => $item->nama,
                'item_category' => $item->item_category->nama,
                'type' => $item->type,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Kode',
            'Nama',
            'Item Category',
            'Type',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Apply borders to data cells only (A1 to D{highest row})
        $sheet->getStyle('A1:D' . $sheet->getHighestRow())
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        return [
            1 => ['font' => ['bold' => true]], // Bold style for first row (headers)
        ];
    }
}
