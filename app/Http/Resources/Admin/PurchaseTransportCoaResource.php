<?php

namespace App\Http\Resources\Admin;

use App\Models\PurchaseTransport;
use App\Models\PurchaseTransportTax;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseTransportCoaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $purchase_detail = $this->purchase_transport_details($request);
        $vendor = $this->vendor;
        $taxes = $this->purchase_transport_taxes;
        $item = $this->item;

        // * item coa
        $item_coa_data = [];
        $item_type_coa = $item->item_category->item_type->item_type_coas;

        $item_coa = [];

        /**
         *
         * NOTE: This function is for purchase item, asset, service, and manufacture item
         *
         * ! ==============================================================================================
         * ! purchase item
         * ! ==============================================================================================
         * ? get item type coa inventory
         * * CREDIT
         *
         * ! ==============================================================================================
         * ! asset
         * ! ==============================================================================================
         * ? get item type coa asset
         * * CREDIT
         *
         * ! ==============================================================================================
         * ! service
         * ! ==============================================================================================
         * ? get item type coa expense
         * * CREDIT
         *
         * ! ==============================================================================================
         * ! manufacture item
         * ! ==============================================================================================
         * ? get item type coa inventory
         * * CREDIT
         *
         */
        foreach ($item_type_coa as $key => $coa) {
            if ($item->item_category->item_type->nama == "purchase item") {
                if (strtolower($coa->type) == strtolower('Inventory')) {
                    $coa->coa;
                    $item_coa[] = $coa;
                } else {
                    unset($item_type_coa[$key]);
                }
            } elseif (in_array(strtolower($item->item_category->item_type->nama), [strtolower("asset"), strtolower("biaya dibayar dimuka")])) {
                $coa->coa;
                $item_coa[] = $coa;
            } elseif (strtolower($item->item_category->item_type->nama) == strtolower("service")) {
                if (strtolower($coa->type) == strtolower('expense')) {
                    $coa->coa;
                    $item_coa[] = $coa;
                } else {
                    unset($item_type_coa[$key]);
                }
            } elseif (strtolower($item->item_category->item_type->nama) == strtolower('manufacture item')) {
                if (strtolower($coa->type) == strtolower('inventory')) {
                    $coa->coa;
                    $item_coa[] = $coa;
                } else {
                    unset($item_type_coa[$key]);
                }
            } else {
                unset($item_type_coa[$key]);
            }
        }

        // * item coa
        foreach ($item_coa as $key => $item) {
            $item_coa_data[] = [
                'item_receiving_report_id' => $this->item_receiving_report_id,
                'reference_model' => PurchaseTransport::class,
                'reference_id' => $this->id,
                'type' => $item['type'],
                'coa_id' => $item['coa']['id'],
                'bind_to' => 'CREDIT',
                'item_receiving_report_detail_id' => null,
            ];
        }

        /**
         * ! =================================================================================
         * ! TAX
         * ! COA PURCHASE
         * ! =================================================================================
         * * get coa purchase from tax
         * * CREDIT
         */
        $taxes_coa_data = [];
        foreach ($taxes as $key => $tax_value) {
            $tax_coa = $tax_value->tax->coa_purchase_data;

            array_push($taxes_coa_data, [
                'item_receiving_report_id' => $this->item_receiving_report_id,
                'reference_model' => PurchaseTransportTax::class,
                'reference_id' => $tax_value->id,
                'type' => 'Tax',
                'coa_id' => $tax_coa->id,
                'bind_to' => 'CREDIT',
                'item_receiving_report_detail_id' => null,
            ]);
        }

        /**
         * ! =================================================================================
         * ! VENDOR PAYABLE COA
         * ! =================================================================================
         * * DEBIT
         */
        $vendor_coa_data = [];
        foreach ($vendor->vendor_coas as $key => $value) {
            if (strtolower($value->type) == strtolower('Account Payable Coa')) {
                $vendor_coa_data[] = [
                    'item_receiving_report_id' => $this->item_receiving_report_id,
                    'reference_model' => Vendor::class,
                    'reference_id' => $vendor->id,
                    'type' => $value['type'],
                    'coa_id' => $value['coa']['id'],
                    'bind_to' => 'DEBIT',
                    'item_receiving_report_detail_id' => null,
                ];
            }
        }

        return [
            'item_coa' => $item_coa_data,
            'vendor_coa' => $vendor_coa_data,
            'tax' => $taxes_coa_data,
        ];
    }
}
