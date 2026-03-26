<?php

namespace App\Http\Resources\Admin;

use App\Models\Customer;
use App\Models\SaleOrderTax;
use App\Models\SoTrading;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class SoTradingCoaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $item = $this->so_trading_detail->item;
        $customer = $this->customer;

        // * item coa
        $item_type_coa = $item->item_category->item_type->item_type_coas;
        $item_coa = [];
        foreach ($item_type_coa as $key => $coa) {
            if (strtolower($coa->type) == strtolower('Sales')) {
                $coa->coa;
                $item_coa[] = $coa;
            } else {
                unset($item_type_coa[$key]);
            }
        }

        $item_coa_data = [];
        foreach ($item_coa as $key => $item) {
            unset($item['coa']['parent_id'], $item['coa']['can_have_children'], $item['coa']['is_parent'], $item['coa']['deleted_at']);
            $item_coa_data[] = [
                'reference_model' => SoTrading::class,
                'reference_id' => $this->id,
                'type' => $item['type'],
                'coa' => $item['coa'],
            ];
        }

        // * customer coa
        $customer_coas = $customer->customer_coas;
        foreach ($customer_coas as $key => $customer_coa) {
            if (strtolower($customer_coa->tipe) == strtolower('Account Receivable Coa') or strtolower($customer_coa->tipe) == strtolower('Sale Discounts Coa')) {
                $customer_coa->coa;
            } else {
                unset($customer_coas[$key]);
            }
        };

        $customer_coa_data = [];
        foreach ($customer_coas as $key => $value) {
            unset($value['coa']['parent_id'], $value['coa']['can_have_children'], $value['coa']['is_parent'], $value['coa']['deleted_at']);
            $customer_coa_data[] = [
                'reference_model' => Customer::class,
                'reference_id' => $this->customer_id,
                'type' => $value['tipe'],
                'coa' => $value['coa'],
                'name' => $value['tipe'] == 'Account Receivable Coa' ? Str::snake('coa_customer') : Str::snake('coa_sale_discount'),
                'label' => $value['tipe'] == 'Account Receivable Coa' ? 'Account Receivable Coa' : 'Sale Discounts Coa',
            ];
        }

        // * tax
        $sale_order_taxes = $this->sale_order_taxes;
        $taxes_coa_data =  [];
        foreach ($sale_order_taxes as $key => $value) {
            $tax_coa = $value->tax->coa_sale_data;
            unset($tax_coa['parent_id'], $tax_coa['can_have_children'], $tax_coa['is_parent'], $tax_coa['deleted_at']);

            $data = $value->tax;
            $data->coa_purchase_data;
            unset($data['description'], $data['coa_sale'], $data['coa_purchase'], $data['type'], $data['deleted_at']);
            unset($data['coa_purchase_data']['parent_id'], $data['coa_purchase_data']['can_have_children'], $data['coa_purchase_data']['is_parent'], $data['coa_purchase_data']['deleted_at']);

            $item = $this->so_trading_detail->item;
            unset($item['item_category'], $item['type'], $item['satuan_kirim'], $item['unit_id'], $item['status'], $item['deskripsi'], $item['item_category_id'], $item['deleted_at']);


            $tax = $value->tax;
            unset($tax['coa_purchase_data'], $tax['coa_sale_data']);

            array_push($taxes_coa_data, [
                'reference_model' => SaleOrderTax::class,
                'reference_id' => $value->id,
                'type' => 'coa_tax',
                'coa' => $tax_coa,
                'name'  => 'tax_coa',
                'label' => $tax->name,
                'tax' => $tax,
                'item' => $item,
            ]);
            // $taxes_coa_data[] = $data;
        }

        return [
            'item_coa' => $item_coa_data[0],
            'customer_coa' => $customer_coa_data,
            'taxes_coa_data' => $taxes_coa_data
        ];
    }
}
