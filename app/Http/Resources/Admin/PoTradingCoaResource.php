<?php

namespace App\Http\Resources\Admin;

use App\Models\Item;
use App\Models\PoTrading;
use App\Models\PurchaseOrderTax;
use App\Models\Vendor;
use Illuminate\Http\Resources\Json\JsonResource;

class PoTradingCoaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $item = $this->po_trading_detail->item;
        $vendor = $this->vendor;

        // * item coa
        $item_type_coa = $item->item_category->item_type->item_type_coas;
        $item_coa = [];
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

        $item_coa_data = [];
        foreach ($item_coa as $key => $item) {
            $item_coa_data[] = [
                'reference_model' => PoTrading::class,
                'reference_id' => $this->id,
                'type' => $item['type'],
                'coa_id' => $item['coa']['id'],
                'bind_to' => 'CREDIT',
            ];
        }

        // * vendor coa
        $vendor_coa_data = [];
        foreach ($vendor->vendor_coas as $key => $value) {
            if (strtolower($value->type) == strtolower('Account Payable Coa')) {
                $vendor_coa_data[] = [
                    'reference_model' => Vendor::class,
                    'reference_id' => $vendor->id,
                    'type' => $value['type'],
                    'coa_id' => $value['coa']['id'],
                    'bind_to' => 'DEBIT',
                ];
            }
        }

        // * tax
        $taxes_coa_data =  [];
        foreach ($this->purchase_order_taxes as $key => $data) {
            $tax_coa = $data->tax->coa_purchase_data;

            array_push($taxes_coa_data, [
                'reference_model' => PurchaseOrderTax::class,
                'reference_id' => $data->id,
                'type' => 'TAX',
                'coa_id' => $tax_coa->id,
                'bind_to' => 'CREDIT',
            ]);
        }

        return array_merge($item_coa_data, $taxes_coa_data, $vendor_coa_data);
    }
}
