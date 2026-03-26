<?php

namespace App\Http\Helpers;

use App\Models\ItemReceivingReport;
use App\Models\PoTrading;
use App\Models\PurchaseOrderGeneral;
use App\Models\PurchaseOrderGeneralDetailItem;
use App\Models\PurchaseOrderGeneralDetailItemTax;
use App\Models\PurchaseOrderService;
use App\Models\PurchaseTransport;
use App\Models\PurchaseTransportTax;
use App\Models\Vendor;
use Exception;

class ItemReceivingReportCoaHelpers
{
    /**
     * method type
     *
     * @var null|string
     */
    private null|string $type = null;

    /**
     * purchase order id
     *
     * @var string|null|int
     */
    private $purchase_order_id = null;

    /**
     * item receiving report id
     *
     * @var string|null|int
     */
    private $item_receiving_report_id = null;

    /**
     * item receiving report data
     *
     * @var null|ItemReceivingReport
     */
    private null|ItemReceivingReport $item_receiving_report = null;

    /**
     * vendor data
     *
     * @var any
     */
    private $vendor;

    /**
     * purchase order
     *
     * @var null|PurchaseOrderGeneral|PurchaseOrderService|PoTrading|PurchaseTransport
     */
    private null|PurchaseOrderGeneral|PurchaseOrderService|PoTrading|PurchaseTransport $purchase_order;

    /**
     * initial
     *
     * @param string $type
     * @param int|string $purchase_order_id
     */
    public function __construct($type, $purchase_order_id, $item_receiving_report_id)
    {
        if (!in_array($type, ['general', 'jasa', 'trading', 'transport'])) {
            $this->throw_invalid_type();
        }

        $this->type = $type;
        $this->purchase_order_id = $purchase_order_id;
        $this->item_receiving_report_id = $item_receiving_report_id;

        $this->getSetPurchaseAndVendorAndItemReceivingReportData();
    }

    /**
     * Throw exception invalid type
     *
     * @param
     * @return Throw
     */
    private function throw_invalid_type()
    {
        throw new Exception("Method get coa for type $this->type not found", 1);
    }

    /**
     * get set purchase and vendor data
     *
     * @return void
     */
    private function getSetPurchaseAndVendorAndItemReceivingReportData()
    {
        if ($this->type == 'general') {
            $this->purchase_order = PurchaseOrderGeneral::find($this->purchase_order_id);
        } elseif ($this->type == 'jasa') {
            $this->purchase_order = PurchaseOrderService::find($this->purchase_order_id);
        } elseif ($this->type == 'trading') {
            $this->purchase_order = PoTrading::find($this->purchase_order_id);
        } elseif ($this->type == 'transport') {
            $this->purchase_order = PurchaseTransport::find($this->purchase_order_id);
        } else {
            $this->throw_invalid_type();
        }

        if (!$this->purchase_order) {
            throw new Exception("Purchase order not found", 1);
        }

        $this->item_receiving_report = ItemReceivingReport::find($this->item_receiving_report_id);
        if (!$this->item_receiving_report) {
            throw new Exception("Item receiving report not found", 1);
        }

        $this->vendor = $this->purchase_order->vendor;
    }

    /**
     * get_item_receiving_report_general
     *
     * @return array
     */
    private function get_item_receiving_report_general(): array
    {
        $details = $this->item_receiving_report->item_receiving_report_details;
        $purchase_order = $this->item_receiving_report->reference;
        $vendor = $purchase_order->vendor;

        // * item coa and tax
        $item_coa_data = [];
        $taxes_coa_data = [];
        foreach ($details as $detail) {
            // * if item receive greater than 0
            if ($detail->jumlah_diterima > 0) {
                // * item coa
                $item = $detail->item;
                $item_type_coa = $item->item_category->item_category_coas;
                if (count($item_type_coa) == 0) {
                    $item_type_coa = $item->item_category->item_category_coas;
                }

                if (count($item_type_coa) == 0) {
                    throw new Exception("Item type coa not found for item: " . $item->nama, 1);
                }

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
                        'reference_model' => PurchaseOrderGeneralDetailItem::class,
                        'reference_id' => $detail->reference_id,
                        'type' => $item['type'],
                        'coa_id' => $item['coa']['id'],
                        'bind_to' => 'CREDIT',
                        'item_receiving_report_detail_id' => $detail->id,
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
                $taxes = $detail->reference?->purchase_order_general_detail_item_taxes;
                foreach ($taxes as $key => $tax_value) {
                    $tax_coa = $tax_value->tax->coa_purchase_data;

                    array_push($taxes_coa_data, [
                        'item_receiving_report_id' => $this->item_receiving_report_id,
                        'reference_model' => PurchaseOrderGeneralDetailItemTax::class,
                        'reference_id' => $tax_value->id,
                        'type' => 'Tax',
                        'coa_id' => $tax_coa->id,
                        'bind_to' => 'CREDIT',
                        'item_receiving_report_detail_id' => $detail->id,
                    ]);
                }
            }
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
                if (isset($value['coa']['id'])) {
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
        }

        if (count($vendor_coa_data) == 0) {
            throw new Exception('Vendor coa not found');
        }

        return array_merge(
            $item_coa_data,
            $taxes_coa_data,
            $vendor_coa_data,
        );
    }

    /**
     * get_item_receiving_report_service
     *
     * @return array
     */
    private function get_item_receiving_report_service(): array
    {
        $details = $this->item_receiving_report->item_receiving_report_details;
        $purchase_order = $this->item_receiving_report->reference;
        $vendor = $purchase_order->vendor;

        // * item coa and tax
        $item_coa_data = [];
        $taxes_coa_data = [];
        foreach ($details as $detail) {
            // * if item receive greater than 0
            if ($detail->jumlah_diterima > 0) {
                // * item coa
                $item = $detail->item;
                $item_type_coa = $item->item_category->item_category_coas;
                if (!$item_type_coa) {
                    $item_type_coa = $item->item_category->item_category_coas;
                }

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
                        'reference_model' => \App\Models\PurchaseOrderServiceDetailItem::class,
                        'reference_id' => $detail->reference_id,
                        'type' => $item['type'],
                        'coa_id' => $item['coa']['id'],
                        'bind_to' => 'CREDIT',
                        'item_receiving_report_detail_id' => $detail->id,
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
                $taxes = $detail->reference?->purchase_order_service_detail_item_taxes;
                foreach ($taxes as $key => $tax_value) {
                    $tax_coa = $tax_value->tax->coa_purchase_data;

                    array_push($taxes_coa_data, [
                        'item_receiving_report_id' => $this->item_receiving_report_id,
                        'reference_model' => \App\Models\PurchaseOrderServiceDetailItemTax::class,
                        'reference_id' => $tax_value->id,
                        'type' => 'Tax',
                        'coa_id' => $tax_coa->id,
                        'bind_to' => 'CREDIT',
                        'item_receiving_report_detail_id' => $detail->id,
                    ]);
                }
            }
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
                if (isset($value['coa']['id'])) {
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
        }

        if (count($vendor_coa_data) == 0) {
            throw new Exception("Vendor coa not found", 1);
        }

        return array_merge(
            $item_coa_data,
            $taxes_coa_data,
            $vendor_coa_data,
        );
    }

    /**
     * get_item_receiving_report_trading
     *
     * @return array
     */
    private function get_item_receiving_report_trading(): array
    {
        $item = $this->purchase_order->po_trading_detail->item;
        $vendor = $this->vendor;

        // * item coa
        $item_type_coa = $item->item_category->item_category_coas;
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
                'item_receiving_report_id' => $this->item_receiving_report_id,
                'reference_model' => PoTrading::class,
                'reference_id' => $this->purchase_order_id,
                'type' => $item['type'],
                'coa_id' => $item['coa']['id'],
                'bind_to' => 'CREDIT',
                'item_receiving_report_detail_id' => null,
            ];
        }

        // * vendor coa
        $vendor_coa_data = [];
        foreach ($vendor->vendor_coas as $key => $value) {
            if (strtolower($value->type) == strtolower('Account Payable Coa')) {
                if (isset($value['coa']['id'])) {
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
        }

        if (count($vendor_coa_data) == 0) {
            throw new Exception("Vendor coa not found", 1);
        }

        // * tax
        $taxes_coa_data =  [];
        foreach ($this->purchase_order->purchase_order_taxes as $key => $data) {
            if (!$data->tax_trading_id) {
                $tax_coa = $data->tax->coa_purchase_data;

                array_push($taxes_coa_data, [
                    'item_receiving_report_id' => $this->item_receiving_report_id,
                    'reference_model' => \App\Models\PurchaseOrderTax::class,
                    'reference_id' => $data->id,
                    'type' => 'TAX',
                    'coa_id' => $tax_coa->id,
                    'bind_to' => 'CREDIT',
                    'item_receiving_report_detail_id' => null,
                ]);
            } else {
                $tax_coa = $data->tax_trading->coa_purchase_data;

                array_push($taxes_coa_data, [
                    'item_receiving_report_id' => $this->item_receiving_report_id,
                    'reference_model' => \App\Models\PurchaseOrderTax::class,
                    'reference_id' => $data->id,
                    'type' => 'TAX',
                    'coa_id' => $tax_coa->id,
                    'bind_to' => 'CREDIT',
                    'item_receiving_report_detail_id' => null,
                ]);
            }
        }

        return array_merge($item_coa_data, $taxes_coa_data, $vendor_coa_data);
    }

    /**
     * get_item_receiving_report_transport
     *
     * @return array
     */
    private function get_item_receiving_report_transport(): array
    {
        $item = $this->purchase_order->item;
        $vendor = $this->vendor;

        // * item coa
        $item_type_coa = $item->item_category->item_category_coas;
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
                'item_receiving_report_id' => $this->item_receiving_report_id,
                'reference_model' => PurchaseTransport::class,
                'reference_id' => $this->purchase_order_id,
                'type' => $item['type'],
                'coa_id' => $item['coa']['id'],
                'bind_to' => 'CREDIT',
                'item_receiving_report_detail_id' => null,
            ];
        }

        // * vendor coa
        $vendor_coa_data = [];
        foreach ($vendor->vendor_coas as $key => $value) {
            if (strtolower($value->type) == strtolower('Account Payable Coa')) {
                if (isset($value['coa']['id'])) {
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
        }

        if (count($vendor_coa_data) == 0) {
            throw new Exception("Vendor coa not found", 1);
        }

        // * tax
        $taxes_coa_data =  [];
        foreach ($this->purchase_order->purchase_transport_taxes as $key => $data) {
            $tax_coa = $data->tax->coa_purchase_data;

            array_push($taxes_coa_data, [
                'item_receiving_report_id' => $this->item_receiving_report_id,
                'reference_model' => PurchaseTransportTax::class,
                'reference_id' => $data->id,
                'type' => 'TAX',
                'coa_id' => $tax_coa->id,
                'bind_to' => 'CREDIT',
                'item_receiving_report_detail_id' => null,
            ]);
        }

        return array_merge(
            $item_coa_data,
            $taxes_coa_data,
            $vendor_coa_data
        );
    }

    /**
     * get coa for item receiving report
     *
     * @return array
     */
    private function getCoaFromItemReceivingReport(): array
    {
        if ($this->type == 'general') {
            $coa = $this->get_item_receiving_report_general();
        } elseif ($this->type == 'jasa') {
            $coa = $this->get_item_receiving_report_service();
        } elseif ($this->type == 'trading') {
            $coa = $this->get_item_receiving_report_trading();
        } elseif ($this->type == 'transport') {
            $coa = $this->get_item_receiving_report_transport();
        } else {
            $this->throw_invalid_type();
        }

        return $coa;
    }

    /**
     * generate
     *
     * @return void
     */
    public function create_item_receiving_report_coa(): void
    {
        $item_receiving_report_coa = $this->getCoaFromItemReceivingReport();

        foreach ($item_receiving_report_coa as $coa_data_key => $coa_data) {
            $this->item_receiving_report->item_receiving_report_coas()->updateOrCreate([
                'item_receiving_report_id' => $coa_data['item_receiving_report_id'],
                'item_receiving_report_detail_id' => $coa_data['item_receiving_report_detail_id'] ?? null,
                'type' => $coa_data['type'],
                'reference_model' => $coa_data['reference_model'],
                'reference_id' => $coa_data['reference_id'],
                'bind_to' => $coa_data['bind_to'],
            ], [
                'item_receiving_report_id' => $coa_data['item_receiving_report_id'],
                'item_receiving_report_detail_id' => $coa_data['item_receiving_report_detail_id'] ?? null,
                'coa_id' => $coa_data['coa_id'],
                'type' => $coa_data['type'],
                'reference_model' => $coa_data['reference_model'],
                'reference_id' => $coa_data['reference_id'],
                'bind_to' => $coa_data['bind_to'],
            ]);
        }

        return;
    }
}
