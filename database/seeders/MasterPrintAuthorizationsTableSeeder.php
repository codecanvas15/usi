<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MasterPrintAuthorizationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('master_print_authorizations')->delete();
        
        \DB::table('master_print_authorizations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'label' => 'Sale Order General',
                'type' => 'sale_order_general',
                'group' => 'Penjualan',
                'can_print' => 1,
                'created_at' => NULL,
                'updated_at' => '2024-03-23 19:43:12',
            ),
            1 => 
            array (
                'id' => 2,
                'label' => 'Sale Order Trading',
                'type' => 'sale_order_trading',
                'group' => 'Penjualan',
                'can_print' => 1,
                'created_at' => NULL,
                'updated_at' => '2024-03-23 19:43:12',
            ),
            2 => 
            array (
                'id' => 3,
                'label' => 'Delivery Order General',
                'type' => 'delivery_order_general',
                'group' => 'Penjualan',
                'can_print' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'label' => 'Delivery Order Trading',
                'type' => 'delivery_order_trading',
                'group' => 'Penjualan',
                'can_print' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'label' => 'Invoice General',
                'type' => 'invoice_general',
                'group' => 'Penjualan',
                'can_print' => 1,
                'created_at' => NULL,
                'updated_at' => '2024-03-23 19:43:13',
            ),
            5 => 
            array (
                'id' => 6,
                'label' => 'Invoice General Tax',
                'type' => 'invoice_general_tax',
                'group' => 'Penjualan',
                'can_print' => 1,
                'created_at' => NULL,
                'updated_at' => '2024-03-23 19:43:13',
            ),
            6 => 
            array (
                'id' => 7,
                'label' => 'Invoice General Kwitansi',
                'type' => 'invoice_general_receipt',
                'group' => 'Penjualan',
                'can_print' => 1,
                'created_at' => NULL,
                'updated_at' => '2024-03-23 19:43:13',
            ),
            7 => 
            array (
                'id' => 8,
                'label' => 'Invoice Trading',
                'type' => 'invoice_trading',
                'group' => 'Penjualan',
                'can_print' => 1,
                'created_at' => NULL,
                'updated_at' => '2024-03-23 19:43:14',
            ),
            8 => 
            array (
                'id' => 9,
                'label' => 'Invoice Trading Tax',
                'type' => 'invoice_trading_tax',
                'group' => 'Penjualan',
                'can_print' => 1,
                'created_at' => NULL,
                'updated_at' => '2024-03-23 19:43:14',
            ),
            9 => 
            array (
                'id' => 10,
                'label' => 'Invoice Trading Kwitansi',
                'type' => 'invoice_trading_receipt',
                'group' => 'Penjualan',
                'can_print' => 1,
                'created_at' => NULL,
                'updated_at' => '2024-03-23 19:43:14',
            ),
            10 => 
            array (
                'id' => 11,
                'label' => 'Purchase Request General',
                'type' => 'purchase_request_general',
                'group' => 'Pembelian',
                'can_print' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            11 => 
            array (
                'id' => 12,
                'label' => 'Purchase Request Service',
                'type' => 'purchase_request_jasa',
                'group' => 'Pembelian',
                'can_print' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            12 => 
            array (
                'id' => 13,
                'label' => 'Purchase Request Trading',
                'type' => 'purchase_request_trading',
                'group' => 'Pembelian',
                'can_print' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            13 => 
            array (
                'id' => 14,
                'label' => 'Purchase Order General',
                'type' => 'purchase_order_general',
                'group' => 'Pembelian',
                'can_print' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            14 => 
            array (
                'id' => 15,
                'label' => 'Purchase Order Service',
                'type' => 'purchase_order_service',
                'group' => 'Pembelian',
                'can_print' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            15 => 
            array (
                'id' => 16,
                'label' => 'Purchase Order Trading',
                'type' => 'purchase_order_trading',
                'group' => 'Pembelian',
                'can_print' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            16 => 
            array (
                'id' => 17,
                'label' => 'Purchase Order Transport',
                'type' => 'purchase_order_transport',
                'group' => 'Pembelian',
                'can_print' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            17 => 
            array (
                'id' => 18,
                'label' => 'LPB General',
                'type' => 'lpb_general',
                'group' => 'Pembelian',
                'can_print' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            18 => 
            array (
                'id' => 19,
                'label' => 'LPB Service',
                'type' => 'lpb_service',
                'group' => 'Pembelian',
                'can_print' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            19 => 
            array (
                'id' => 20,
                'label' => 'LPB Trading',
                'type' => 'lpb_trading',
                'group' => 'Pembelian',
                'can_print' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            20 => 
            array (
                'id' => 21,
                'label' => 'LPB Transport',
                'type' => 'lpb_transport',
                'group' => 'Pembelian',
                'can_print' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            21 => 
            array (
                'id' => 22,
                'label' => 'Penerimaan Deposit',
                'type' => 'receipt_of_deposit',
                'group' => 'Keuangan',
                'can_print' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            22 => 
            array (
                'id' => 23,
                'label' => 'Penerimaan Customer',
                'type' => 'customer_acceptance',
                'group' => 'Keuangan',
                'can_print' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            23 => 
            array (
                'id' => 24,
                'label' => 'Penerimaan Pengambilan Uang Muka',
                'type' => 'advance_payment_receipt',
                'group' => 'Keuangan',
                'can_print' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            24 => 
            array (
                'id' => 25,
                'label' => 'Penerimaan Giro Masuk',
                'type' => 'incoming_giro_receipt',
                'group' => 'Keuangan',
                'can_print' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            25 => 
            array (
                'id' => 26,
                'label' => 'Pengeluaran Pengajuan Dana',
                'type' => 'disbursement_of_funding_applications',
                'group' => 'Keuangan',
                'can_print' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            26 => 
            array (
                'id' => 27,
                'label' => 'Pengeluaran Kas Keluar',
                'type' => 'cash_out_disbursements',
                'group' => 'Keuangan',
                'can_print' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            27 => 
            array (
                'id' => 28,
                'label' => 'Pengeluaran Pengembalian Uang Muka',
                'type' => 'expenditure_withdrawal_advance',
                'group' => 'Keuangan',
                'can_print' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            28 => 
            array (
                'id' => 29,
                'label' => 'Kasbon',
                'type' => 'cash_receipt',
                'group' => 'Keuangan',
                'can_print' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}