<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\Admin\CoaExportBeginningBalance;
use App\Models\Coa;
use App\Models\Journal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CoaImportBeginningBalanceController extends Controller
{
    /**
     * Display form for importing data
     */
    public function index()
    {
        return view('admin.coa-import-beginning-balance.index');
    }

    /**
     * Download import format
     */
    public function importFormat()
    {
        $data['model'] = Coa::all();

        return Excel::download(new CoaExportBeginningBalance($data), 'coa-import-format.xlsx');
    }

    /**
     * Upload and import the make preview the imported data
     */
    public function import(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file|mimes:xls,xlsx'
        ]);

        $the_file = $request->file('file');

        // * Load the file
        $spreadsheet = IOFactory::load($the_file->getRealPath());

        // * Get the active sheet
        $sheet        = $spreadsheet->getActiveSheet();

        // * Get the highest row and column
        $row_limit    = $sheet->getHighestDataRow();
        $row_range    = range(2, $row_limit);

        // * Prepare the data
        $data = array();

        // * Looping through the rows
        foreach ($row_range as $row) {
            $coa = Coa::find($sheet->getCell('A' . $row)->getValue());
            if ($coa && !$coa->is_parent) {
                // * Get the data
                $credit = $sheet->getCell('D' . $row)->getValue() ?? '0';
                $debit = $sheet->getCell('E' . $row)->getValue() ?? '0';

                // * Check if credit or debit is greater than 0
                if ($credit != 0 || $debit != 0) {
                    $data[] = [
                        'coa_id' => $sheet->getCell('A' . $row)->getValue(),
                        'account_code' => $sheet->getCell('B' . $row)->getValue(),
                        'account_name' => $sheet->getCell('C' . $row)->getValue(),
                        'credit' => $credit,
                        'debit' => $debit,
                    ];
                }
            }
        }

        return view('admin.coa-import-beginning-balance.preview', [
            'results' => $data
        ]);
    }

    /**
     * Store the imported data
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'coa_id.*' => 'required|exists:coas,id',
            'credit.*' => 'required',
            'debit.*' => 'required',
        ]);

        DB::beginTransaction();

        // * Create the journal
        $journal = new Journal();
        $journal->fill([
            'branch_id' => get_current_branch_id(),
            'date' => Carbon::parse($request->date),
            'reference_number' => "Saldo Awal COA",
            'remark' => "Saldo Awal COA",
            'status' => 'approve',
            'exchange_rate' => 1,
            'journal_type' => "Beginning Balance",
            'currency_id' => get_local_currency()->id,
            'created_by' => auth()->user()->id,
            'is_generated' => true,
        ]);

        try {
            $journal->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd('Terjadi kesalahan saat menyimpan data journal. Silahkan coba lagi atau hubungi administrator.' . $th->getMessage());
        }

        // * Create the journal details
        $journal_details = [];
        $credit = 0;
        $debit = 0;
        foreach ($request->coa_id as $key => $result) {
            $journal_details[] = [
                'journal_id' => $journal->id,
                'coa_id' => $result,
                'debit' => thousand_to_float($request['debit'][$key] ?? '0'),
                'credit' => thousand_to_float($request['credit'][$key] ?? '0'),
                'remark' => "Saldo Awal COA",
            ];

            $credit += thousand_to_float($request['credit'][$key] ?? '0');
            $debit += thousand_to_float($request['debit'][$key] ?? '0');
        }

        // * Save the journal details
        try {
            $journal->journal_details()->createMany($journal_details);
        } catch (\Throwable $th) {
            DB::rollBack();
            dd('Terjadi kesalahan saat menyimpan data journal detail. Silahkan coba lagi atau hubungi administrator.' . $th->getMessage());
        }

        // if ($credit != $debit) {
        //     DB::rollBack();

        // }

        // * Update the journal credit and debit
        $journal->fill([
            'credit' => $credit,
            'debit' => $debit,
        ]);

        // * Update the journal
        try {
            $journal->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd('Terjadi kesalahan saat menyimpan data journal. Silahkan coba lagi atau hubungi administrator.' . $th->getMessage());
        }

        DB::commit();

        return redirect()->route('admin.coa.index')->with($this->ResponseMessageCRUD(true, 'store', 'Data saldo awal COA berhasil disimpan.'));
    }
}
