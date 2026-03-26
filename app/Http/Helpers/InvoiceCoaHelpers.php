<?php

namespace App\Http\Helpers;

use App\Models\Customer;
use App\Models\InvoiceGeneralAdditional;
use App\Models\InvoiceGeneralAdditionalTax;
use App\Models\InvoiceGeneralCoa;
use App\Models\InvoiceGeneralDetail;
use App\Models\InvoiceGeneralDetailTax;
use App\Models\InvoiceTradingCoa;
use App\Models\InvoiceTradingTax;
use App\Models\InvTradingAddOn;
use App\Models\InvTradingAddOnTax;
use Illuminate\Database\Eloquent\Collection;

class InvoiceCoaHelpers
{
    /**
     * invoice id
     *
     * @var int
     */
    public int|null $invoiceId = null;

    /**
     * invoice type
     *
     * @var string
     */
    public string|null $type = null;

    /**
     * data model
     *
     * \App\Models\InvoiceTrading
     */
    public \App\Models\InvoiceTrading|null|Collection|\App\Models\InvoiceGeneral $invoiceData = null;

    /**
     * initial function and variables
     *
     * @param
     * @return void
     */
    public function __construct($invoiceId, $type)
    {
        $this->invoiceId = $invoiceId;
        $this->type = $type;

        $this->getInvoiceData();

        if (is_null($this->invoiceData)) {
            throw new \Exception("Invoice data not found.");
        }
    }

    /**
     * thrownInvalidType
     *
     * @return \Exception|\Throwable
     */
    public function thrownInvalidType()
    {
        throw new \Exception("Invalid type for generate coa data. Type {$this->type} does not exist.");
    }

    /**
     * getInvoiceData
     *
     * @return void
     */
    public function getInvoiceData()
    {
        if ($this->type == 'invoice-trading') {
            $this->invoiceData = \App\Models\InvoiceTrading::with([
                'so_trading',
                'customer',
                'invoice_trading_taxes',
                'inv_trading_add_on',
                'inv_trading_add_on.inv_trading_add_on_tax',
                'inv_trading_add_on.inv_trading_add_on_tax.tax',
                'invoice_trading_details',
            ])
                ->find($this->invoiceId);

            return;
        } elseif ($this->type == 'invoice-general') {
            $this->invoiceData = \App\Models\InvoiceGeneral::with([
                'customer',
                'currency',
                'invoice_general_details.delivery_order_general_detail.sale_order_general_detail',
                'invoice_general_additionals.item',
            ])->find($this->invoiceId);

            return;
        }

        $this->thrownInvalidType();
    }

    /**
     * generateCoaDataForInvoiceTrading
     *
     * @return void
     */
    public function generateCoaDataForInvoiceTrading()
    {
        $invoiceData = $this->invoiceData;
        $ITEM_COA = [];
        $ITEM_ADD_COA = [];

        // ! CUSTOMER COA ######################################################
        $data_customer = $invoiceData->customer;
        $data_customer_coa = $data_customer->customer_coas;

        InvoiceTradingCoa::where('invoice_trading_id', $this->invoiceId)->delete();
        foreach ($data_customer_coa as $key => $value) {
            // * get coa Customer Deposite Coa
            if (strtolower($value->tipe) == strtolower('Account Receivable Coa')) {
                // ? create data
                $newCoaData = new InvoiceTradingCoa();
                $newCoaData->loadModel([
                    'invoice_trading_id' => $this->invoiceId,
                    'coa_id' => $value->coa_id,
                    'type' => 'customer',
                    'reference_id' => $data_customer->id,
                    'reference_model' => Customer::class,
                ]);

                // ? save data
                try {
                    $newCoaData->save();
                } catch (\Throwable $th) {
                    throw $th;
                }
            }
        }
        // ! END CUSTOMER COA ######################################################

        // ! TRADING ITEM COA ######################################################

        // * Trading item coa
        $data_item = $invoiceData->item;
        $data_item_type = $data_item->item_category->item_type;
        $data_item_category = $data_item->item_category;

        if (is_null($data_item_type)) {
            throw new \Exception("Item type not found.");
        } else {
            $data_item_type_name = $data_item_type->nama;
            $data_item_type_coas = $data_item_type->item_type_coas;
            $data_item_category_coas = $data_item_category->item_category_coas;

            /**
             *
             * NOTE: This function is for get coa for item-type purchase item, asset, service
             *
             * ! ==============================================================================================
             * ! purchase item
             * ! ==============================================================================================
             * ? get item type coa sales
             * * SALES
             *
             * ! ==============================================================================================
             * ! asset
             * ! ==============================================================================================
             * ? get item type coa asset
             * * SALES
             *
             * ! ==============================================================================================
             * ! service
             * ! ==============================================================================================
             * ? get item type coa sales
             * * ASSET
             *
             *
             */
            if (strtolower($data_item_type_name) == 'purchase item') {
                // * get item type coa sales
                foreach ($data_item_category_coas as $key => $value) {
                    if (strtolower($value->type) == 'sales') {
                        // ? create data
                        $newCoaData = new InvoiceTradingCoa();
                        $newCoaData->loadModel([
                            'invoice_trading_id' => $this->invoiceId,
                            'coa_id' => $value->coa_id,
                            'type' => 'sales',
                        ]);

                        // ? save data
                        try {
                            $newCoaData->save();
                        } catch (\Throwable $th) {
                            throw $th;
                        }
                    }
                }
            } elseif (strtolower($data_item_type_name) == 'service') {
                // * get item type coa sales
                foreach ($data_item_category_coas as $key => $value) {
                    if (strtolower($value->type) == 'sales') {
                        // ? create data
                        $newCoaData = new InvoiceTradingCoa();
                        $newCoaData->loadModel([
                            'invoice_trading_id' => $this->invoiceId,
                            'coa_id' => $value->coa_id,
                            'type' => 'sales',
                        ]);

                        // ? save data
                        try {
                            $newCoaData->save();
                        } catch (\Throwable $th) {
                            throw $th;
                        }
                    }
                }
            } elseif (strtolower($data_item_type_name) == 'asset') {
                // * get item type coa asset
                foreach ($data_item_category_coas as $key => $value) {
                    if (strtolower($value->type) == 'asset') {
                        // ? create data
                        $newCoaData = new InvoiceTradingCoa();
                        $newCoaData->loadModel([
                            'invoice_trading_id' => $this->invoiceId,
                            'coa_id' => $value->coa_id,
                            'type' => 'sales',
                        ]);

                        // ? save data
                        try {
                            $newCoaData->save();
                        } catch (\Throwable $th) {
                            throw $th;
                        }
                    }
                }
            } else {
                throw new \Exception("Item type not found.");
            }
        }


        // * Trading item tax coa
        $data_item_tax = $invoiceData->invoice_trading_taxes;
        $data_item_tax_coa = [];
        foreach ($data_item_tax as $key => $value) {
            $data_item_tax_coa[] = $value;
        }

        // ? create data
        foreach ($data_item_tax_coa as $key => $value) {
            $newCoaData = new InvoiceTradingCoa();
            $newCoaData->loadModel([
                'invoice_trading_id' => $this->invoiceId,
                'coa_id' => $value->tax->coa_sale,
                'type' => 'trading-tax',
                'reference_id' => $value->id,
                'reference_model' => InvoiceTradingTax::class,
            ]);

            // ? save data
            try {
                $newCoaData->save();
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        // ! END TRADING ITEM COA ######################################################

        // * Trading item add on coa
        $data_item_add_on_tax_coa = [];
        $data_item_add_ons = $invoiceData->inv_trading_add_on;

        foreach ($data_item_add_ons as $key => $value) {
            $data_item = $value->item;
            $data_item_type = $data_item->item_category->item_type;
            $data_item_category = $data_item->item_category;

            if (is_null($data_item_type)) {
                throw new \Exception("Item type not found.");
            } else {
                $data_item_type_name = $data_item_type->nama;
                $data_item_type_coas = $data_item_type->item_type_coas;
                $data_item_category_coas = $data_item_category->item_category_coas;

                /**
                 *
                 * NOTE: This function is for get coa for item-type purchase item, asset, service
                 *
                 * ! ==============================================================================================
                 * ! purchase item
                 * ! ==============================================================================================
                 * ? get item type coa sales
                 * * SALES
                 *
                 * ! ==============================================================================================
                 * ! asset
                 * ! ==============================================================================================
                 * ? get item type coa asset
                 * * SALES
                 *
                 * ! ==============================================================================================
                 * ! service
                 * ! ==============================================================================================
                 * ? get item type coa sales
                 * * ASSET
                 *
                 *
                 */

                if (strtolower($data_item_type_name) == 'purchase item') {
                    // * get item type coa sales
                    foreach ($data_item_category_coas as $key => $coa_value) {
                        if (strtolower($coa_value->type) == 'sales') {
                            // ? create data
                            $newCoaData = new InvoiceTradingCoa();
                            $newCoaData->loadModel([
                                'invoice_trading_id' => $this->invoiceId,
                                'coa_id' => $coa_value->coa_id,
                                'type' => 'item-additional',
                                'reference_id' => $value->id,
                                'reference_model' => InvTradingAddOn::class,
                            ]);

                            // ? save data
                            try {
                                $newCoaData->save();
                            } catch (\Throwable $th) {
                                throw $th;
                            }
                        }
                    }
                } elseif (strtolower($data_item_type_name) == 'service') {
                    // * get item type coa sales
                    foreach ($data_item_category_coas as $key => $coa_value) {
                        if (strtolower($coa_value->type) == 'sales') {
                            // ? create
                            $newCoaData = new InvoiceTradingCoa();
                            $newCoaData->loadModel([
                                'invoice_trading_id' => $this->invoiceId,
                                'coa_id' => $coa_value->coa_id,
                                'type' => 'item-additional',
                                'reference_id' => $value->id,
                                'reference_model' => InvTradingAddOn::class,
                            ]);

                            // ? save data
                            try {
                                $newCoaData->save();
                            } catch (\Throwable $th) {
                                throw $th;
                            }
                        }
                    }
                } elseif (strtolower($data_item_type_name) == 'asset') {
                    // * get item type coa asset
                    foreach ($data_item_category_coas as $key => $coa_value) {
                        if (strtolower($coa_value->type) == 'asset') {
                            // ? create
                            $newCoaData = new InvoiceTradingCoa();
                            $newCoaData->loadModel([
                                'invoice_trading_id' => $this->invoiceId,
                                'coa_id' => $coa_value->coa_id,
                                'type' => 'item-additional',
                                'reference_id' => $value->id,
                                'reference_model' => InvTradingAddOn::class,
                            ]);

                            // ? save data
                            try {
                                $newCoaData->save();
                            } catch (\Throwable $th) {
                                throw $th;
                            }
                        }
                    }
                } else {
                    throw new \Exception("Item type not found.");
                }
            }

            // * Trading item add on tax coa
            $data_item_add_on_taxes = $value->inv_trading_add_on_tax;

            foreach ($data_item_add_on_taxes as $key => $tax_data) {
                // ? create data
                $newCoaData = new InvoiceTradingCoa();
                $newCoaData->loadModel([
                    'invoice_trading_id' => $this->invoiceId,
                    'coa_id' => $tax_data->tax->coa_sale,
                    'type' => 'item-additional-tax',
                    'reference_id' => $tax_data->id,
                    'reference_model' => InvTradingAddOnTax::class,
                ]);

                // ? save data
                try {
                    $newCoaData->save();
                } catch (\Throwable $th) {
                    throw $th;
                }
            }
        }

        return;
    }

    /**
     * generate coa for invoice general
     *
     * @return void
     */
    public function generateCoaDataInvoiceGeneral()
    {
        $invoiceData = $this->invoiceData;

        // ! CUSTOMER COA ######################################################
        $data_customer = $invoiceData->customer;
        $data_customer_coa = $data_customer->customer_coas;

        InvoiceGeneralCoa::where('invoice_general_id', $this->invoiceId)->delete();
        foreach ($data_customer_coa as $key => $value) {
            // * get coa Customer Deposite Coa
            if (strtolower($value->tipe) == strtolower('Account Receivable Coa')) {
                // ? create data
                $newCoaData = new InvoiceGeneralCoa();
                $newCoaData->loadModel([
                    'invoice_general_id' => $this->invoiceId,
                    'coa_id' => $value->coa_id,
                    'type' => 'customer',
                    'reference_id' => $data_customer->id,
                    'reference_model' => Customer::class,
                ]);

                // ? save data
                try {
                    $newCoaData->save();
                } catch (\Throwable $th) {
                    throw $th;
                }
            }
        }
        // ! END CUSTOMER COA ##################################################

        // ! DELIVERY ORDER COA ################################################
        $invoiceDetails = $invoiceData->invoice_general_details;

        foreach ($invoiceDetails as $invoiceGeneralKey => $invoiceDetail) {
            // ? ITEM COA ========================================================

            // * get data item and item type
            $data_item = $invoiceDetail->item;
            $data_item_type = $data_item->item_category->item_type;
            $data_item_type_coas = $data_item_type->item_type_coas;
            $data_item_category = $data_item->item_category;
            $data_item_category_coas = $data_item_category->item_category_coas;

            /**
             *
             * NOTE: This function is for get coa for item-type purchase item, asset, service
             *
             * ! ==============================================================================================
             * ! purchase item
             * ! ==============================================================================================
             * ? get item type coa sales
             * * SALES
             *
             * ! ==============================================================================================
             * ! asset
             * ! ==============================================================================================
             * ? get item type coa asset
             * * SALES
             *
             * ! ==============================================================================================
             * ! service
             * ! ==============================================================================================
             * ? get item type coa sales
             * * ASSET
             *
             *
             */
            if (strtolower($data_item_type->nama) == 'purchase item') {
                // * get item type coa sales
                foreach ($data_item_category_coas as $key => $coa_value) {
                    if (strtolower($coa_value->type) == 'sales') {
                        // ? create
                        $newCoaData = new InvoiceGeneralCoa();
                        $newCoaData->loadModel([
                            'invoice_general_id' => $this->invoiceId,
                            'coa_id' => $coa_value->coa_id,
                            'type' => 'sales',
                            'reference_id' => $invoiceDetail->id,
                            'reference_model' => InvoiceGeneralDetail::class,
                        ]);

                        // ? save data
                        try {
                            $newCoaData->save();
                        } catch (\Throwable $th) {
                            throw $th;
                        }
                    }
                }
            } elseif (strtolower($data_item_type->nama) == 'service') {
                // * get item type coa sales
                foreach ($data_item_category_coas as $key => $coa_value) {
                    if (strtolower($coa_value->type) == 'sales') {
                        // ? create
                        $newCoaData = new InvoiceGeneralCoa();
                        $newCoaData->loadModel([
                            'invoice_general_id' => $this->invoiceId,
                            'coa_id' => $coa_value->coa_id,
                            'type' => 'sales',
                            'reference_id' => $invoiceDetail->id,
                            'reference_model' => InvoiceGeneralDetail::class,
                        ]);

                        // ? save data
                        try {
                            $newCoaData->save();
                        } catch (\Throwable $th) {
                            throw $th;
                        }
                    }
                }
            } elseif (strtolower($data_item_type->nama) == 'asset') {
                // * get item type coa asset
                foreach ($data_item_category_coas as $key => $coa_value) {
                    if (strtolower($coa_value->type) == 'asset') {
                        // ? create
                        $newCoaData = new InvoiceGeneralCoa();
                        $newCoaData->loadModel([
                            'invoice_general_id' => $this->invoiceId,
                            'coa_id' => $coa_value->coa_id,
                            'type' => 'sales',
                            'reference_id' => $invoiceDetail->id,
                            'reference_model' => InvoiceGeneralDetail::class,
                        ]);

                        // ? save data
                        try {
                            $newCoaData->save();
                        } catch (\Throwable $th) {
                            throw $th;
                        }
                    }
                }
            } else {
                throw new \Exception("Item Type not found");
            }

            // ? ITEM COA ========================================================

            // ? TAX COA =========================================================
            $invoiceDetailTaxes = $invoiceDetail->invoice_general_detail_taxes;
            foreach ($invoiceDetailTaxes as $key => $value) {
                // * get data tax
                $data_tax = $value->tax;

                $newCoaData = new InvoiceGeneralCoa();
                $newCoaData->loadModel([
                    'invoice_general_id' => $this->invoiceId,
                    'coa_id' => $data_tax->coa_sale,
                    'type' => 'tax',
                    'reference_id' => $value->id,
                    'reference_model' => InvoiceGeneralDetailTax::class,
                ]);

                // ? save data
                try {
                    $newCoaData->save();
                } catch (\Throwable $th) {
                    throw $th;
                }
            }
            // ? END TAX COA =====================================================
        }
        // ! END DELIVERY ORDER COA ############################################

        // ! ADDITIONAL ITEM COA ###############################################
        $invoiceAdditionalItems = $invoiceData->invoice_general_additionals;
        foreach ($invoiceAdditionalItems as $key => $value) {
            // ? ITEM COA ========================================================

            // * get data additional item
            $data_additional_item = $value->item;
            $data_item_type = $data_additional_item->item_category->item_type;
            $data_item_type_coas = $data_item_type->item_type_coas;
            $data_item_category = $data_additional_item->item_category;
            $data_item_category_coas = $data_item_category->item_category_coas;

            /**
             *
             * NOTE: This function is for get coa for item-type purchase item, asset, service
             *
             * ! ==============================================================================================
             * ! purchase item
             * ! ==============================================================================================
             * ? get item type coa sales
             * * SALES
             *
             * ! ==============================================================================================
             * ! asset
             * ! ==============================================================================================
             * ? get item type coa asset
             * * SALES
             *
             * ! ==============================================================================================
             * ! service
             * ! ==============================================================================================
             * ? get item type coa sales
             * * ASSET
             *
             *
             */

            if (strtolower($data_item_type->nama) == 'purchase item') {
                // * get item type coa sales
                foreach ($data_item_category_coas as $key => $coa_value) {
                    if (strtolower($coa_value->type) == 'sales') {
                        // ? create
                        $newCoaData = new InvoiceGeneralCoa();
                        $newCoaData->loadModel([
                            'invoice_general_id' => $this->invoiceId,
                            'coa_id' => $coa_value->coa_id,
                            'type' => 'item-additional',
                            'reference_id' => $value->id,
                            'reference_model' => InvoiceGeneralAdditional::class,
                        ]);

                        // ? save data
                        try {
                            $newCoaData->save();
                        } catch (\Throwable $th) {
                            throw $th;
                        }
                    }
                }
            } elseif (strtolower($data_item_type->nama) == 'service') {
                // * get item type coa sales
                foreach ($data_item_category_coas as $key => $coa_value) {
                    if (strtolower($coa_value->type) == 'sales') {
                        // ? create
                        $newCoaData = new InvoiceGeneralCoa();
                        $newCoaData->loadModel([
                            'invoice_general_id' => $this->invoiceId,
                            'coa_id' => $coa_value->coa_id,
                            'type' => 'item-additional',
                            'reference_id' => $value->id,
                            'reference_model' => InvoiceGeneralAdditional::class,
                        ]);

                        // ? save data
                        try {
                            $newCoaData->save();
                        } catch (\Throwable $th) {
                            throw $th;
                        }
                    }
                }
            } elseif (strtolower($data_item_type->nama) == 'asset') {
                // * get item type coa asset
                foreach ($data_item_category_coas as $key => $coa_value) {
                    if (strtolower($coa_value->type) == 'asset') {
                        // ? create
                        $newCoaData = new InvoiceGeneralCoa();
                        $newCoaData->loadModel([
                            'invoice_general_id' => $this->invoiceId,
                            'coa_id' => $coa_value->coa_id,
                            'type' => 'item-additional',
                            'reference_id' => $value->id,
                            'reference_model' => InvoiceGeneralAdditional::class,
                        ]);
                    }
                }
            } else {
                throw new \Exception("Item Type not found");
            }

            // ? END ITEM COA ========================================================

            // ? ITE TAX ========================================================
            $invoiceAdditionalItemTaxes = $value->invoice_general_additional_taxes;
            foreach ($invoiceAdditionalItemTaxes as $key => $value) {
                // * get data tax
                $data_tax = $value->tax;

                $newCoaData = new InvoiceGeneralCoa();
                $newCoaData->loadModel([
                    'invoice_general_id' => $this->invoiceId,
                    'coa_id' => $data_tax->coa_sale,
                    'type' => 'item-additional-tax',
                    'reference_id' => $value->id,
                    'reference_model' => InvoiceGeneralAdditionalTax::class,
                ]);

                // ? save data
                try {
                    $newCoaData->save();
                } catch (\Throwable $th) {
                    throw $th;
                }
            }
            // ? END ITE TAX ========================================================
        }
        // ! END ADDITIONAL ITEM COA ###########################################
    }
}
