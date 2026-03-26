<?php

namespace App\Jobs;

use App\Exports\BukuBesarExport;
use App\Exports\FinanceReport\PurchasingJournalReportExport;
use App\Exports\FinanceReport\SaleJournalReportExport;
use App\Exports\HarianKasBankDetailExport;
use App\Exports\InventoryReport\EndOfMonthStockReport;
use App\Exports\InventoryReport\StockCardReport;
use App\Exports\InventoryReport\StockMutationReport;
use App\Exports\TransaksiJournalExport;
use App\Http\Traits\NotificationTrait;
use App\Models\Download;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;

class DownloadQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, NotificationTrait;

    protected $request, $file_path, $paper_size, $orientation, $download_id, $data, $user;

    /**
     * Create a new job instance.
     */
    public function __construct($request, $file_path, $paper_size, $orientation, $download_id, $data = null)
    {
        $this->request = $request;
        $this->file_path = $file_path;
        $this->paper_size = $paper_size;
        $this->orientation = $orientation;
        $this->download_id = $download_id;
        $this->data = $data;
        $this->user = auth()->id();
        // Log::error($request_params);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        ini_set('memory_limit', -1);
        set_time_limit(0);
        ini_set('max_execution_time', 0);

        $download = Download::findOrFail($this->download_id);

        try {
            $data = [];
            $request = $this->request;
            $file_path = $this->file_path;
            $paper_size = $this->paper_size;
            $orientation = $this->orientation;
            $type = $request['type'] ?? null;
            $format = $request['format'];

            $inventory_controller = new \App\Http\Controllers\Admin\InventoryReportController();

            switch ($type) {
                case 'transaksi-jurnal':
                    $data = app('\App\Http\Controllers\Admin\FinanceReportController')->transaksi_jurnal($type, $request);
                    $excel_export = TransaksiJournalExport::class;
                    $paper_size = 'a3';
                    $orientation = 'landscape';
                    break;
                case "sale-journal-report":
                    $data = app('\App\Http\Controllers\Admin\FinanceReportController')->reportJournalSale($request);
                    $excel_export = SaleJournalReportExport::class;
                    $paper_size = 'a1';
                    $orientation = 'landscape';
                    break;
                case "buku-besar":
                    $data = app('\App\Http\Controllers\Admin\FinanceReportController')->buku_besar($type, $request);
                    $excel_export = BukuBesarExport::class;
                    $paper_size = 'a3';
                    $orientation = 'landscape';
                    break;
                case 'profit-loss-multiperiod':
                    $data = app('\App\Http\Controllers\Admin\FinanceReportProfitLossController')->get_data($type, $request, true, $request['year']);
                    $excel_export = HarianKasBankDetailExport::class;
                    $paper_size = 'a3';
                    $orientation = 'landscape';
                    break;
                case "purchasing-journal-report":
                    $data = app('\App\Http\Controllers\Admin\FinanceReportController')->reportJournalPurchasing($request);
                    $excel_export = PurchasingJournalReportExport::class;
                    $paper_size = 'a1';
                    $orientation = 'landscape';
                    break;

                // INVENTORY REPORT
                case "stock-card-report":
                    $data = $inventory_controller->reportStockCard($request);
                    $excel_export = StockCardReport::class;
                    break;
                case "stock-mutation-report":
                    $data = $inventory_controller->reportStockMutation($request);
                    $excel_export =  StockMutationReport::class;
                    break;
                case "end-of-monthly-stock-report":
                    $data = $inventory_controller->reportStockEndOfMonthlyStock($request);
                    $excel_export =  EndOfMonthStockReport::class;
                    break;
                case "stock-value":
                    $data = $inventory_controller->stockValueReport($request);

                    $excel_export =  \App\Exports\InventoryReport\StockValueExport::class;
                    break;
                default:
                    break;
            }

            if ($format == "pdf") {
                Log::error('it reached here');
                $pdf = Pdf::loadView($file_path, $data)
                    ->setPaper($paper_size ?? 'a4', $orientation ?? 'potrait');
                $file_name = 'download/' . $type . '-' . time() . '.pdf';
                Storage::disk('public')->put($file_name, $pdf->output());
            } elseif ($format == 'excel') {
                Excel::store(new $excel_export($file_path, $data), '1.xlsx', 'public', null, [
                    'visibility' => 'public',
                ]);
                $file_name = 'download/' . $type . '-' . time() . '.xlsx';
                try {
                    Storage::disk('public')->move('1.xlsx', $file_name);
                } catch (FileNotFoundException $e) {
                    Log::error($e->getMessage());
                    Log::error($e->getTraceAsString());
                }
            }

            $download->update([
                'path' => $file_name,
                'status' => 'done',
                'done_at' => now(),
            ]);

            $this->send_notification(
                branch_id: $request['branch_id'] ?? null,
                user_id: $this->user,
                roles: [],
                permissions: [],
                title: 'Download anda sudah selesai',
                body: 'Lihat di menu download untuk melihatnya',
                reference_model: Download::class,
                reference_id: $download->id,
                // link: route('dashboard.index'),
            );
        } catch (\Throwable $th) {
            Log::error($th);
            $download->update([
                'status' => 'failed',
                'done_at' => now(),
            ]);
        }
    }
}
