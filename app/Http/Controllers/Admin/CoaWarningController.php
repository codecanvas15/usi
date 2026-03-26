<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CoaWarningController extends Controller
{
    /**
     * View path
     *
     * @var string
     */
    protected string $view_folder = 'coa-warning';

    /**
     * Route name
     *
     * @var string
     */
    protected string $route_name = 'coa-warning';

    /**
     * Permission name
     *
     * @var string
     */
    protected string $permission_name = 'coa-warning';

    /**
     * Warning type list
     *
     * @var array $warning_type_list
     */
    protected array $warning_type_list = [
        'customer' => 'Customer',
        'vendor' => 'Vendor',
        'item-category' => 'Item Category',
        'tax' => 'Tax',
        'default-coa' => 'Default Coa'
    ];

    /**
     * Display a listing of warning coa types
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("admin.$this->view_folder.index", [
            'types' => $this->warning_type_list,
        ]);
    }

    /**
     * Get a list of warning coa base on request
     *
     * @param Request $request
     * @param string $type
     * @return \Illuminate\Http\Response
     */
    public function getListOfWarningCoa(Request $request)
    {
        $type = $request->type;
        if (!in_array($type, array_keys($this->warning_type_list))) {
            return $this->ResponseJsonMessage('Invalid type', 422);
        }

        $data = $this->getListOfWarningCoaData($request, $type);

        return $this->ResponseJsonData($data);
    }

    /**
     * Get a list of warning coa data
     *
     * @param Request $request
     * @param string $type
     * @return array|Collection
     */
    protected function getListOfWarningCoaData($request, string $type): array|Collection
    {
        $data = [];

        switch ($type) {
            case 'customer':
                $data = $this->getListOfWarningCoaDataCustomer($request);
                break;
            case 'vendor':
                $data = $this->getListOfWarningCoaDataVendor($request);
                break;
            case 'item-category':
                $data = $this->getListOfWarningCoaDataItemCategory($request);
                break;
            case 'tax':
                $data = $this->getListOfWarningCoaDataTax($request);
                break;
            case 'default-coa':
                $data = $this->getListOfWarningCoaDataDefaultCoa($request);
                break;
        }

        return $data;
    }

    /**
     * Get a list of warning coa data customer
     *
     * @param Request $request
     * @return array|Collection
     */
    protected function getListOfWarningCoaDataCustomer($request): array|Collection
    {
        $data = DB::table('customer_coas')
            ->leftJoin('coas', 'customer_coas.coa_id', '=', 'coas.id')
            ->leftJoin('customers', 'customer_coas.customer_id', '=', 'customers.id')
            ->whereNull('customers.deleted_at')
            ->whereNotNull('coas.deleted_at')
            ->selectRaw('
                customers.id as parent_id,
                customers.nama as parent_name,
                customers.code as parent_code,
                coas.id as coa_id,
                coas.name as coa_name,
                coas.account_code as coa_code
            ')
            ->get();

        $results = $data->map(function ($row) {
            $row->route = route("admin.customer.show", $row->parent_id);
            return $row;
        });

        return $results;
    }

    /**
     * Get a list of warning coa data vendor
     *
     * @param Request $request
     * @return array|Collection
     */
    protected function getListOfWarningCoaDataVendor($request): array|Collection
    {
        $data = DB::table('vendor_coas')
            ->leftJoin('coas', 'vendor_coas.coa_id', '=', 'coas.id')
            ->leftJoin('vendors', 'vendor_coas.vendor_id', '=', 'vendors.id')
            ->whereNull('vendors.deleted_at')
            ->whereNotNull('coas.deleted_at')
            ->selectRaw('
                vendors.id as parent_id,
                vendors.nama as parent_name,
                vendors.code as parent_code,
                coas.id as coa_id,
                coas.name as coa_name,
                coas.account_code as coa_code
            ')
            ->get();

        $results = $data->map(function ($row) {
            $row->route = route("admin.vendor.show", $row->parent_id);
            return $row;
        });

        return $results;
    }

    /**
     * Get a list of warning coa data item category
     *
     * @param Request $request
     * @return array|Collection
     */
    protected function getListOfWarningCoaDataItemCategory($request): array|Collection
    {
        $data = DB::table('item_category_coas')
            ->leftJoin('coas', 'item_category_coas.coa_id', '=', 'coas.id')
            ->leftJoin('item_categories', 'item_category_coas.item_category_id', '=', 'item_categories.id')
            ->whereNull('item_categories.deleted_at')
            ->whereNotNull('coas.deleted_at')
            ->selectRaw('
                item_categories.id as parent_id,
                item_categories.nama as parent_name,
                item_categories.kode as parent_code,
                coas.id as coa_id,
                coas.name as coa_name,
                coas.account_code as coa_code
            ')
            ->get();

        $results = $data->map(function ($row) {
            $row->route = route("admin.item-category.show", $row->parent_id);
            return $row;
        });

        return $results;
    }

    /**
     * Get a list of warning coa data tax
     *
     * @param Request $request
     * @return array|Collection
     */
    protected function getListOfWarningCoaDataTax($request): array|Collection
    {
        $data = DB::table('taxes')
            ->leftJoin('coas as coa_sale_data', 'taxes.coa_sale', '=', 'coa_sale_data.id')
            ->leftJoin('coas as coa_purchase_data', 'taxes.coa_purchase', '=', 'coa_purchase_data.id')
            ->whereNull('taxes.deleted_at')
            ->whereNotNull('coa_sale_data.deleted_at')
            ->whereNotNull('coa_purchase_data.deleted_at')
            ->selectRaw('
                taxes.id as parent_id,
                taxes.name as parent_name,
                concat(format(taxes.value * 100, 2), "%") as parent_code,

                coa_sale_data.id as coa_sale_data_coa_id,
                coa_sale_data.name as coa_sale_data_coa_name,
                coa_sale_data.account_code as coa_sale_data_coa_code,

                coa_purchase_data.id as coa_purchase_data_coa_id,
                coa_purchase_data.name as coa_purchase_data_coa_name,
                coa_purchase_data.account_code as coa_purchase_data_coa_code
            ')
            ->distinct('taxes.id')
            ->get();

        $results = $data->map(function ($row) {
            $row->route = route("admin.tax.show", $row->parent_id);
            return $row;
        });

        $tax_trading = DB::table('tax_tradings')
            ->leftJoin('coas as coa_sale_data', 'tax_tradings.coa_sale_id', '=', 'coa_sale_data.id')
            ->leftJoin('coas as coa_purchase_data', 'tax_tradings.coa_purchase_id', '=', 'coa_purchase_data.id')
            ->whereNull('tax_tradings.deleted_at')
            ->whereNotNull('coa_sale_data.deleted_at')
            ->whereNotNull('coa_purchase_data.deleted_at')
            ->selectRaw('
                tax_tradings.id as parent_id,
                tax_tradings.name as parent_name,
                concat(format(tax_tradings.value * 100, 2), "%") as parent_code,

                coa_sale_data.id as coa_sale_data_coa_id,
                coa_sale_data.name as coa_sale_data_coa_name,
                coa_sale_data.account_code as coa_sale_data_coa_code,

                coa_purchase_data.id as coa_purchase_data_coa_id,
                coa_purchase_data.name as coa_purchase_data_coa_name,
                coa_purchase_data.account_code as coa_purchase_data_coa_code
            ')
            ->distinct('tax_tradings.id')
            ->get();

        $results = $results->merge($tax_trading->map(function ($row) {
            $row->route = route("admin.tax-trading.index");
            return $row;
        }));

        return $results;
    }

    /**
     * Get a list of warning coa data default coa
     *
     * @param Request $request
     * @return array|Collection
     */
    protected function getListOfWarningCoaDataDefaultCoa($request): array|Collection
    {
        $data = DB::table('default_coas')
            ->leftJoin('coas', 'default_coas.coa_id', '=', 'coas.id')
            ->whereNotNull('coas.deleted_at')
            ->selectRaw('
                default_coas.id as parent_id,
                default_coas.name as parent_name,
                default_coas.type as parent_code,

                coas.id as coa_id,
                coas.name as coa_name,
                coas.account_code as coa_code
            ')
            ->get();

        $results = $data->map(function ($row) {
            $row->route = route("admin.default-coa.index");
            return $row;
        });

        return $results;
    }
}
