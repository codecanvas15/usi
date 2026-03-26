<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterPrintAuthorizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            'Penjualan' => [
                [
                    'label' => 'Sale Order General',
                    'type' => 'sale_order_general',
                ],
                [
                    'label' => 'Sale Order Trading',
                    'type' => 'sale_order_trading',
                ],
                [
                    'label' => 'Delivery Order General',
                    'type' => 'delivery_order_general',
                ],
                [
                    'label' => 'Delivery Order Trading',
                    'type' => 'delivery_order_trading',
                ],
                [
                    'label' => 'Invoice General',
                    'type' => 'invoice_general',
                ],
                [
                    'label' => 'Invoice General Tax',
                    'type' => 'invoice_general_tax',
                ],
                [
                    'label' => 'Invoice General Kwitansi',
                    'type' => 'invoice_general_receipt',
                ],
                [
                    'label' => 'Invoice Trading',
                    'type' => 'invoice_trading',
                ],
                [
                    'label' => 'Invoice Trading Tax',
                    'type' => 'invoice_trading_tax',
                ],
                [
                    'label' => 'Invoice Trading Kwitansi',
                    'type' => 'invoice_trading_receipt',
                ],
                [
                    'label' => 'Invoice Trading Transport',
                    'type' => 'invoice_trading_transport',
                ],
                [
                    'label' => 'Invoice Trading Tax Transport',
                    'type' => 'invoice_trading_tax_transport',
                ]
            ],
            'Pembelian' => [
                [
                    'label' => 'Purchase Request General',
                    'type' => 'purchase_request_general',
                ],
                [
                    'label' => 'Purchase Request Service',
                    'type' => 'purchase_request_jasa',
                ],
                [
                    'label' => 'Purchase Request Trading',
                    'type' => 'purchase_request_trading',
                ],
                [
                    'label' => 'Purchase Order General',
                    'type' => 'purchase_order_general',
                ],
                [
                    'label' => 'Purchase Order Service',
                    'type' => 'purchase_order_service',
                ],
                [
                    'label' => 'Purchase Order Trading',
                    'type' => 'purchase_order_trading',
                ],
                [
                    'label' => 'Purchase Order Transport',
                    'type' => 'purchase_order_transport',
                ],
                [
                    'label' => 'LPB General',
                    'type' => 'lpb_general',
                ],
                [
                    'label' => 'LPB Service',
                    'type' => 'lpb_service',
                ],
                [
                    'label' => 'LPB Trading',
                    'type' => 'lpb_trading',
                ],
                [
                    'label' => 'LPB Transport',
                    'type' => 'lpb_transport',
                ],
            ],
            'Keuangan' => [
                [
                    'label' => 'Penerimaan Deposit',
                    'type' => 'receipt_of_deposit',
                ],
                [
                    'label' => 'Penerimaan Customer',
                    'type' => 'customer_acceptance',
                ],
                [
                    'label' => 'Penerimaan Pengambilan Uang Muka',
                    'type' => 'advance_payment_receipt',
                ],
                [
                    'label' => 'Penerimaan Giro Masuk',
                    'type' => 'incoming_giro_receipt',
                ],
                [
                    'label' => 'Pengeluaran Pengajuan Dana',
                    'type' => 'disbursement_of_funding_applications',
                ],
                [
                    'label' => 'Pengeluaran Kas Keluar',
                    'type' => 'cash_out_disbursements',
                ],
                [
                    'label' => 'Pengeluaran Pengembalian Uang Muka',
                    'type' => 'expenditure_withdrawal_advance',
                ],
                [
                    'label' => 'Kasbon',
                    'type' => 'cash_receipt',
                ]
            ]
        ];

        foreach ($array as $group => $values) {
            foreach ($values as $value) {
                DB::table('master_print_authorizations')->updateOrInsert(
                [
                    'type' => $value['type'],
                    'group' => $group,
                ],
                [
                    'label' => $value['label'],
                    'type' => $value['type'],
                    'group' => $group,
                ]);
            }
        }
    }
}
