<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\SaleOrderTradingReportController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReportTradingTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $saleOrder = new SaleOrderTradingReportController();
        $response = $saleOrder->tradingSalesDetailAdditional([]);

        // show the response
        dd($response);
    }
}
