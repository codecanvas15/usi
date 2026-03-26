<style>
    #notif-sidebar.notempty::after {
        content: attr(counter);
        background-color: red;
        width: 50px;
        height: 50px;
        padding: 5px;
        border-radius: 10px;
        margin-left: 100%;
        font-size: 11px;
        color: #fff;
        text-align: center;
    }

    @media only screen and (max-width: 767px) {
        #notif-sidebar.notempty::after {
            top: 8px;
        }
    }

    .sidebar-collapse .treeview .parent-treeview:first-of-type {
        transform: translateY(-10%) !important;
    }
</style>
<aside class="main-sidebar">
    <!-- sidebar-->
    <section class="sidebar position-relative">
        <div class="multinav">
            <div class="multinav-scroll" style="height: 100%;">
                <ul class="sidebar-menu" data-widget="tree">
                    <li id="dashboard">
                        <a href="{{ route('admin.index') }}"><i data-feather="monitor"></i><span>Dashboard</span></a>
                    </li>
                    <li id="otorisasi">
                        <a href="{{ route('admin.authorization.index') }}" class="d-flex align-items-center"><i data-feather="check-circle"></i><span id="notif-sidebar">Otorisasi</span></a>
                    </li>
                    @canany(sidebar_master_permissions)
                        <li class="treeview scroll-top" id="master-sidebar">
                            <a href="#">
                                <i data-feather="archive"></i>
                                <span>Master</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-right pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu parent-treeview">
                                @can('view branch')
                                    <li id="branch">
                                        <a href="{{ route('admin.branch.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Branch</a>
                                    </li>
                                @endcan

                                @canany(['view master-evaluation', 'view master-user-assessment', 'view master-hrd-assessment'])
                                    @can('view master-evaluation')
                                        <li class="treeview" id="master-hrd-evaluation-sidebar">
                                            <a href="#">
                                                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Evaluation<span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
                                            </a>
                                            <ul class="treeview-menu">
                                                <li id="master-gp-evaluation-sidebar">
                                                    <a href="{{ route('admin.master-gp-evaluation.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>General <br>Performance Evaluation</a>
                                                </li>
                                            </ul>
                                        </li>
                                    @endcan

                                    @canany(['view master-hrd-assessment', 'view master-user-assessment'])
                                        <li class="treeview" id="master-hrd-assessment-sidebar">
                                            <a href="#">
                                                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Assessment<span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
                                            </a>
                                            <ul class="treeview-menu">
                                                @can('view master-user-assessment')
                                                    <li id="user-assessment-sidebar">
                                                        <a href="{{ route('admin.master-user-assessment.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>User</a>
                                                    </li>
                                                @endcan
                                                @can('view master-hrd-assessment')
                                                    <li id="hrd-assessment-sidebar">
                                                        <a href="{{ route('admin.master-hrd-assessment.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>HRD</a>
                                                    </li>
                                                @endcan
                                            </ul>
                                        </li>
                                    @endcanany
                                @endcanany
                                @canany(['view role', 'view user', 'view user-activity'])
                                    <li class="treeview" id="master-user-sidebar">
                                        <a href="#">
                                            <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Pengguna<span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
                                        </a>
                                        <ul class="treeview-menu">
                                            @can('view role')
                                                <li id="role-sidebar">
                                                    <a href="{{ route('admin.role.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Akses</a>
                                                </li>
                                            @endcan
                                            @can('view user')
                                                <li id="user-sidebar">
                                                    <a href="{{ route('admin.user.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Pengguna</a>
                                                </li>
                                            @endcan
                                            @can('view user-activity')
                                                <li id="user-activity-sidebar">
                                                    <a href="{{ route('admin.user-activity.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Aktivitas Pengguna</a>
                                                </li>
                                            @endcan
                                        </ul>
                                    </li>
                                @endcan

                                @canany(['view education', 'view degree', 'view division', 'view employee', 'view position', 'view employment-status'])
                                    <li class="treeview" id="master-employee-sidebar">
                                        <a href="#">
                                            <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Pegawai<span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
                                        </a>

                                        <ul class="treeview-menu">
                                            @can('view education')
                                                <li id="education-sidebar">
                                                    <a href="{{ route('admin.education.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Pendidikan</a>
                                                </li>
                                            @endcan
                                            @can('view degree')
                                                <li id="degree-sidebar">
                                                    <a href="{{ route('admin.degree.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Jurusan</a>
                                                </li>
                                            @endcan
                                            @can('view division')
                                                <li id="division-sidebar">
                                                    <a href="{{ route('admin.division.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Divisi Departement</a>
                                                </li>
                                            @endcan
                                            @can('view employee')
                                                <li id="employee-sidebar">
                                                    <a href="{{ route('admin.employee.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Karyawan</a>
                                                </li>
                                            @endcan
                                            @can('view position')
                                                <li id="position-sidebar">
                                                    <a href="{{ route('admin.position.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Jabatan</a>
                                                </li>
                                            @endcan
                                            @can('view employment-status')
                                                <li id="employment-status-sidebar">
                                                    <a href="{{ route('admin.employment-status.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Status Karyawan</a>
                                                </li>
                                            @endcan
                                        </ul>
                                    </li>
                                @endcanany

                                @can('view currency')
                                    <li id="currency">
                                        <a href="{{ route('admin.currency.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Currency</a>
                                    </li>
                                @endcan

                                @canany(['view coa', 'view default-coa'])
                                    <li class="treeview" id="master-coa-sidebar">
                                        <a href="#">
                                            <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Coa<span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
                                        </a>

                                        <ul class="treeview-menu">
                                            @can('view coa')
                                                <li id="coa-sidebar">
                                                    <a href="{{ route('admin.coa.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Coa Tree</a>
                                                </li>
                                            @endcan
                                            @can('view default-coa')
                                                <li id="default-coa-sidebar">
                                                    <a href="{{ route('admin.default-coa.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Default Coa</a>
                                                </li>
                                                <li id="warning-coa-sidebar">
                                                    <a href="{{ route('admin.coa-warning.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Coa Warning</a>
                                                </li>
                                                <li id="item-type">
                                                    <a href="{{ route('admin.item-type.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Default Coa Item</a>
                                                </li>
                                            @endcan
                                        </ul>
                                    </li>
                                @endcanany

                                @can('view bank-internal')
                                    <li id="bank-internal-sidebar">
                                        <a href="{{ route('admin.bank-internal.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Bank Internal</a>
                                    </li>
                                @endcan

                                @can('view project')
                                    <li id="project-sidebar">
                                        <a href="{{ route('admin.project.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Project</span></a>
                                    </li>
                                @endcan

                                @can('view customer')
                                    <li id="customer-sidebar">
                                        <a href="{{ route('admin.customer.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Customer</a>
                                    </li>
                                @endcan

                                @canany(['view vendor', 'view business-field'])
                                    <li class="treeview" id="master-vendor-sidebar">
                                        <a href="#">
                                            <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Vendor<span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
                                        </a>

                                        <ul class="treeview-menu">
                                            @can('view vendor')
                                                <li id="vendor-sidebar">
                                                    <a href="{{ route('admin.vendor.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Vendor</a>
                                                </li>
                                            @endcan

                                            @can('view business-field')
                                                <li id="business-field-sidebar">
                                                    <a href="{{ route('admin.business-field.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Bidang Usaha</a>
                                                </li>
                                            @endcan
                                        </ul>
                                    </li>
                                @endcanany

                                @canany(['view item', 'view item-category', 'view unit'])
                                    <li class="treeview" id="master-item-sidebar">
                                        <a href="#">
                                            <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Item<span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
                                        </a>
                                        <ul class="treeview-menu">
                                            @can('view item')
                                                <li id="item">
                                                    <a href="{{ route('admin.item.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Item</a>
                                                </li>
                                            @endcan
                                            @can('view item-category')
                                                <li id="item-category">
                                                    <a href="{{ route('admin.item-category.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Kategori Item</a>
                                                </li>
                                            @endcan
                                            @can('view unit')
                                                <li id="unit">
                                                    <a href="{{ route('admin.unit.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Satuan</a>
                                                </li>
                                            @endcan
                                        </ul>
                                    </li>
                                @endcanany

                                @canany(['view asset-category', 'view asset-document-type', 'view master-asset', 'view lease'])
                                    <li class="treeview" id="master-asset-sidebar">
                                        <a href="#">
                                            <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Asset<span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
                                        </a>
                                        <ul class="treeview-menu">
                                            @can('view asset-category')
                                                <li id="asset-category-sidebar">
                                                    <a href="{{ route('admin.asset-category.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Kategori Asset</span></a>
                                                </li>
                                            @endcan
                                            @can('view asset-document-type')
                                                <li id="asset-document-type-sidebar">
                                                    <a href="{{ route('admin.asset-document-type.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Tipe Dokumen Asset</span></a>
                                                </li>
                                            @endcan
                                            @can('view master-asset')
                                                <li id="asset-sidebar">
                                                    <a href="{{ route('admin.asset.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Daftar Aktiva Tetap</span></a>
                                                </li>
                                            @endcan
                                            @can('view lease')
                                                <li id="lease-sidebar">
                                                    <a href="{{ route('admin.lease.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Biaya Dibayar Dimuka</span></a>
                                                </li>
                                            @endcan

                                        </ul>
                                    </li>
                                @endcanany

                                @canany(['view period', 'view price', 'view tax', 'view tax-trading'])
                                    <li class="treeview" id="master-price-sidebar">
                                        <a href="#">
                                            <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Harga & Pajak<span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
                                        </a>
                                        <ul class="treeview-menu">
                                            @can('view period')
                                                <li id="period">
                                                    <a href="{{ route('admin.period.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Periode</a>
                                                </li>
                                            @endcan
                                            @can('view price')
                                                <li id="price">
                                                    <a href="{{ route('admin.price.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Harga</a>
                                                </li>
                                            @endcan
                                            @can('view tax')
                                                <li id="tax">
                                                    <a href="{{ route('admin.tax.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Pajak</a>
                                                </li>
                                            @endcan
                                            @can('view tax-trading')
                                                <li id="tax-trading">
                                                    <a href="{{ route('admin.tax-trading.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Pajak Trading</a>
                                                </li>
                                            @endcan
                                        </ul>
                                    </li>
                                @endcanany

                                @can('view quotation-add-on-type')
                                    <li id="quotation-add-on-type">
                                        <a href="{{ route('admin.quotation-add-on-type.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Quotation Add On Type</a>
                                    </li>
                                @endcan

                                @canany(['view garage', 'view fleet'])
                                    <li class="treeview" id="master-garage-sidebar">
                                        <a href="#">
                                            <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Garasi & Armada<span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
                                        </a>
                                        <ul class="treeview-menu">
                                            @can('view garage')
                                                <li id="garage">
                                                    <a href="{{ route('admin.garage.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Garasi</a>
                                                </li>
                                            @endcan
                                            @can('view fleet')
                                                <li id="fleet">
                                                    <a href="{{ route('admin.fleet.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Armada</a>
                                                </li>
                                            @endcan
                                        </ul>
                                    </li>
                                @endcanany

                                @can('view ware-house')
                                    <li id="ware-house">
                                        <a href="{{ route('admin.ware-house.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Gudang</a>
                                    </li>
                                @endcan

                                @can('view model')
                                    <li id="model">
                                        <a href="{{ route('admin.model.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Master Otorisasi</a>
                                    </li>
                                @endcan

                                @canany(['view salary-item', 'view income-tax', 'view non-taxable-income'])
                                    <li class="treeview" id="master-salary-sidebar">
                                        <a href="#">
                                            <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Penggajian<span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
                                        </a>
                                        <ul class="treeview-menu">
                                            @can('view salary-item')
                                                <li id="salary-item">
                                                    <a href="{{ route('admin.salary-item.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Komponen Upah</a>
                                                </li>
                                            @endcan
                                            @can('view income-tax')
                                                <li id="income-tax">
                                                    <a href="{{ route('admin.income-tax.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>PPh 21</a>
                                                </li>
                                            @endcan
                                            @can('view non-taxable-income')
                                                <li id="non-taxable-income">
                                                    <a href="{{ route('admin.non-taxable-income.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>PTKP</a>
                                                </li>
                                            @endcan
                                        </ul>
                                    </li>
                                @endcanany
                                @can('view master-letter')
                                    <li id="master-letter">
                                        <a href="{{ route('admin.master-letter.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Master Dokumen</a>
                                    </li>
                                @endcan
                                @can('view reset-leave')
                                    <li id="reset-leave">
                                        <a href="{{ route('admin.reset-leave.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Cut off Cuti</a>
                                    </li>
                                @endcan
                                <li id="master-print">
                                    <a href="{{ route('admin.master-print-authorization.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Master Print</a>
                                </li>
                            </ul>
                        </li>
                    @endcanany
                    @can('view company')
                        <li id="setting">
                            <a href="{{ route('admin.company.index') }}"><i data-feather="settings"></i><span>Company Profile</span></a>
                        </li>
                    @endcan
                    <li class="no-hover border-top border-white" id="menu-section-transaction">
                        <a href="javascriptt:;"><span>Transaksional</span></a>
                    </li>
                    @canany(sidebar_sales_permissions)
                        <li class="treeview scroll-top" id="trading">
                            <a href="#">
                                <i data-feather="arrow-left"></i>
                                <span>Penjualan</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-right pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu parent-treeview">
                                @can('view quotation')
                                    <li id="quotation">
                                        <a href="{{ route('admin.quotation.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Penawaran</a>
                                    </li>
                                @endcan
                                @canany(['view sales-order', 'view sales-order-general'])
                                    <li id="sales-order">
                                        <a href="{{ route('admin.sales.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Sales Order</a>
                                    </li>
                                @endcanany
                                @canany(['view delivery-order', 'view delivery-order-general'])
                                    <li id="delivery-order">
                                        <a href="{{ route('admin.delivery.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Delivery Order</a>
                                    </li>
                                @endcanany
                                @canany(['view invoice-trading', 'view invoice-general', 'view invoice-down-payment'])
                                    <li id="invoice-trading">
                                        <a href="{{ route('admin.invoice.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Invoice</a>
                                    </li>
                                @endcanany
                                @can('view invoice-return')
                                    <li id="invoice-return">
                                        <a href="{{ route('admin.invoice-return.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Retur Penjualan</span></a>
                                    </li>
                                @endcan
                                @canany(['penjualan general report', 'penjualan trading report'])
                                    <li class="treeview" id="sale-order-report">
                                        <a href="#">
                                            <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Laporan<span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
                                        </a>
                                        <ul class="treeview-menu">
                                            @can('penjualan general report')
                                                <li id="sale-order-general">
                                                    <a href="{{ route('admin.sale-order-general.report') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Sale order general</span></a>
                                                </li>
                                            @endcan
                                            @can('penjualan trading report')
                                                <li id="sale-order-trading">
                                                    <a href="{{ route('admin.sale-order-trading-report.report') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Sale order trading</span></a>
                                                </li>
                                            @endcan
                                        </ul>
                                    </li>
                                @endcanany
                            </ul>
                        </li>
                    @endcanany

                    @canany(sidebar_purchase_permissions)
                        <li class="treeview scroll-top" id="purchase-menu">
                            <a href="#">
                                <i data-feather="arrow-right"></i>
                                <span>Pembelian</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-right pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu parent-treeview">
                                @canany(['view purchase-request', 'view purchase-request-service', 'view purchase-request-general', 'view purchase-request-transport', 'view purchase-return'])
                                    <li id="purchase-request">
                                        <a href="{{ route('admin.purchase-request.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Purchase Request</span></a>
                                    </li>
                                @endcanany
                                @canany(['view purchase-order', 'view purchase-transport', 'view purchase-service', 'view purchase-general'])
                                    <li id="purchase">
                                        <a href="{{ route('admin.purchase.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Purchase Order</span></a>
                                    </li>
                                @endcanany
                                @canany(['view item-receiving-report', 'view item-receiving-report-general', 'view item-receiving-service', 'view item-receiving-trading', 'view item-receiving-report-transport'])
                                    <li id="item-receiving-report">
                                        <a href="{{ route('admin.item-receiving-report.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Penerimaan Barang</span></a>
                                    </li>
                                @endcan
                                @can('view purchase-return')
                                    <li id="purchase-return">
                                        <a href="{{ route('admin.purchase-return.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Retur Pembelian</span></a>
                                    </li>
                                @endcan
                                @canany(['purchase request report', 'purchase order report', 'purchase order trading report', 'purchase order general report', 'purchase order service report', 'purchase order transport report'])
                                    <li class="treeview" id="purchase-order-report">
                                        <a href="#">
                                            <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Laporan<span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
                                        </a>
                                        <ul class="treeview-menu">
                                            @can('purchase request report')
                                                <li id="purchase-request-report">
                                                    <a href="{{ route('admin.purchase-request-report.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Purchase Request</span></a>
                                                </li>
                                            @endcan
                                            @can('purchase order trading report')
                                                <li id="purchase-order-trading">
                                                    <a href="{{ route('admin.purchase-order-trading.report') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Purchase order trading</span></a>
                                                </li>
                                            @endcan
                                            @can('purchase order general report')
                                                <li id="purchase-order-general">
                                                    <a href="{{ route('admin.purchase-order-general.report') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Purchase order general</span></a>
                                                </li>
                                            @endcan
                                            @can('purchase order service report')
                                                <li id="purchase-order-service">
                                                    <a href="{{ route('admin.purchase-order-service.report') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Purchase order service</span></a>
                                                </li>
                                            @endcan
                                            @can('purchase order transport report')
                                                <li id="purchase-order-transport">
                                                    <a href="{{ route('admin.purchase-order-transport.report') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Purchase order transport</span></a>
                                                </li>
                                            @endcan
                                            @can('purchase order report')
                                                <li id="purchase-order-report-menu">
                                                    <a href="{{ route('admin.purchase-order-report-all.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Purchase order</span></a>
                                                </li>
                                            @endcan
                                        </ul>
                                    </li>
                                @endcanany
                            </ul>
                        </li>
                    @endcanany

                    @canany(sidebar_warehouse_permissions)
                        <li class="treeview scroll-top" id="stock-sidebar">
                            <a href="#">
                                <i data-feather="box"></i>
                                <span>Gudang</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-right pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu parent-treeview">
                                @can('view stock-card')
                                    <li id="stock-card">
                                        <a href="{{ route('admin.stock-card.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Stock Card</span></a>
                                    </li>
                                @endcan
                                @can('view stock-card-value')
                                    <li id="stock-value">
                                        <a href="{{ route('admin.stock-value.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Stock Card Value</span></a>
                                    </li>
                                @endcan
                                @can('view stock-mutation')
                                    <li id="stock-mutation">
                                        <a href="{{ route('admin.stock-mutation.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Stock Mutation</span></a>
                                    </li>
                                @endcan
                                @can('view stock-adjustment')
                                    <li id="stock-adjustment">
                                        <a href="{{ route('admin.stock-adjustment.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Stock Adjustment</span></a>
                                    </li>
                                @endcan
                                @can('view stock-usage')
                                    <li id="stock-usage">
                                        <a href="{{ route('admin.stock-usage.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Stock Usage</span></a>
                                    </li>
                                @endcan
                                @can('view stock-transfer')
                                    <li id="stock-transfer">
                                        <a href="{{ route('admin.stock-transfer.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Stock Transfer</span></a>
                                    </li>
                                @endcan
                                @can('view closing-delivery-order-ship')
                                    <li id="closing-delivery-order-ship">
                                        <a href="{{ route('admin.closing-delivery-order-ship.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Losses Kapal</span></a>
                                    </li>
                                @endcan
                                @can('warehouse report')
                                    <li id="inventory-report">
                                        <a href="{{ route('admin.inventory-report.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Laporan</span></a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcanany

                    @canany(sidebar_hrd_permissions)
                        <li class="treeview scroll-top" id="hrd">
                            <a href="#">
                                <i data-feather="users"></i>
                                <span>HRD</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-right pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu parent-treeview">
                                @canany(['view labor-demand', 'view labor-application', 'view hrd-assessment', 'view user-assessment', 'view offering-letter'])
                                    <li class="treeview" id="rekrutment-sidebar">
                                        <a href="#">
                                            <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Rekrutment<span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
                                        </a>
                                        <ul class="treeview-menu">
                                            @can('view labor-demand')
                                                <li id="labor-demand">
                                                    <a href="{{ route('admin.labor-demand.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Permintaan</span></a>
                                                </li>
                                            @endcan
                                            @can('view labor-application')
                                                <li id="labor-application">
                                                    <a href="{{ route('admin.labor-application.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Lamaran Pekerjaan</span></a>
                                                </li>
                                            @endcan
                                            @can('view hrd-assessment')
                                                <li id="hrd-assessment">
                                                    <a href="{{ route('admin.hrd-assessment.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Interview HRD</span></a>
                                                </li>
                                            @endcan
                                            @can('view user-assessment')
                                                <li id="user-assessment">
                                                    <a href="{{ route('admin.user-assessment.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Interview User</span></a>
                                                </li>
                                            @endcan
                                            @can('view offering-letter')
                                                <li id="offering-letter">
                                                    <a href="{{ route('admin.offering-letter.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Letter of Intent</span></a>
                                                </li>
                                            @endcan
                                        </ul>
                                    </li>
                                @endcanany

                                @canany(['view contract-extension', 'view specific-time-work-agreement', 'view labor-transfer-form'])
                                    <li class="treeview" id="contract-sidebar">
                                        <a href="#">
                                            <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Kontrak<span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
                                        </a>
                                        <ul class="treeview-menu">
                                            @can('view contract-extension')
                                                <li id="contract-extension">
                                                    <a href="{{ route('admin.contract-extension.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Pembaharuan Kontrak</span></a>
                                                </li>
                                            @endcan
                                            @can('view specific-time-work-agreement')
                                                <li id="specific-time-work-agreement">
                                                    <a href="{{ route('admin.specific-time-work-agreement.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>PKWT</span></a>
                                                </li>
                                            @endcan
                                            @can('view labor-transfer-form')
                                                <li id="labor-transfer-form">
                                                    <a href="{{ route('admin.labor-transfer-form.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Pindah Kerja</span></a>
                                                </li>
                                            @endcan
                                        </ul>
                                    </li>
                                @endcanany

                                @canany(['view presensi', 'view leave', 'view permission-letter-employee', 'view mass-leave'])
                                    <li class="treeview" id="hrd-permission-sidebar">
                                        <a href="#">
                                            <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Presensi<span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
                                        </a>
                                        <ul class="treeview-menu">
                                            @can('view presensi')
                                                <li id="attendance">
                                                    <a href="{{ route('admin.attendance.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Absensi</span></a>
                                                </li>
                                            @endcan
                                            @can('view leave')
                                                <li id="cuti">
                                                    <a href="{{ route('admin.leave.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Cuti/Tidak Masuk</span></a>
                                                </li>
                                            @endcan
                                            @can('view mass-leave')
                                                <li id="mass-leave">
                                                    <a href="{{ route('admin.mass-leave.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Cuti Bersama</span></a>
                                                </li>
                                            @endcan
                                            @can('view permission-letter-employee')
                                                <li id="permission-letter-employee">
                                                    <a href="{{ route('admin.permission-letter-employee.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Surat izin pegawai</span></a>
                                                </li>
                                            @endcan
                                        </ul>
                                    </li>
                                @endcanany

                                @canany(['view payroll-period', 'view payroll'])
                                    <li class="treeview" id="payroll-sidebar">
                                        <a href="#">
                                            <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Penggajian<span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
                                        </a>
                                        <ul class="treeview-menu">
                                            @can('view payroll-period')
                                                <li id="payroll-period">
                                                    <a href="{{ route('admin.payroll-period.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Periode</span></a>
                                                </li>
                                            @endcan
                                            @can('view payroll')
                                                <li id="payroll">
                                                    <a href="{{ route('admin.payroll.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Pencairan Gaji</span></a>
                                                </li>
                                            @endcan
                                        </ul>
                                    </li>
                                @endcanany

                                @can('view evaluation')
                                    <li id="gp-evaluation">
                                        <a href="{{ route('admin.gp-evaluation.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Assessment Karyawan</span></a>
                                    </li>
                                @endcan

                                @canany(['view legality-document', 'view asset-document', 'view lease-document'])
                                    <li id="legality-document">
                                        <a href="{{ route('admin.legality-document.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Dokumen Legal</span></a>
                                    </li>
                                @endcan

                                @can('hrd report')
                                    <li id="human-resource-report">
                                        <a href="{{ route('admin.human-resource-report.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Laporan</span></a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcanany

                    @canany(sidebar_finance_permissions)
                        <li class="treeview scroll-top" id="finance-main-sidebar">
                            <a href="#">
                                <i data-feather="dollar-sign"></i>
                                <span>Keuangan & Akuntansi</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-right pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu parent-treeview">
                                @canany(['view cash-advance-receive', 'view receive-payment', 'view receivables-payment', 'view incoming-payment', 'view cash-advance-return'])
                                    <li class="treeview" id="incoming-payment-sidebar">
                                        <a href="#">
                                            <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Penerimaan Dana<span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
                                        </a>
                                        <ul class="treeview-menu">
                                            @can('view receive-payment')
                                                <li id="receive-payment">
                                                    <a href="{{ route('admin.receive-payment.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Giro Masuk</span></a>
                                                </li>
                                            @endcan
                                            @canany(['view incoming-payment', 'view receivables-payment', 'view cash-advance-receive'])
                                                <li id="incoming-payment">
                                                    <a href="{{ route('admin.incoming-payment.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Kas Masuk</span></a>
                                                </li>
                                            @endcanany
                                            @can('view cash-advance-return')
                                                <li id="cash-advance-return-customer">
                                                    <a href="{{ route('admin.cash-advance-return-customer.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Pengembalian Uang Muka</span></a>
                                                </li>
                                            @endcan
                                        </ul>
                                    </li>
                                @endcanany

                                @canany(['view fund-submission', 'view account-payable', 'view outgoing-payment', 'view cash-advance-payment', 'view cash-advance-return'])
                                    <li class="treeview" id="outgoing-payment-sidebar">
                                        <a href="#">
                                            <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Pengeluaran Dana<span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
                                        </a>
                                        <ul class="treeview-menu">
                                            @can('view fund-submission')
                                                <li id="fund-submission">
                                                    <a href="{{ route('admin.fund-submission.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Pengajuan Dana</span></a>
                                                </li>
                                            @endcan
                                            @canany(['view outgoing-payment', 'view account-payable', 'view cash-advance-payment'])
                                                <li id="outgoing-payment">
                                                    <a href="{{ route('admin.outgoing-payment.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Kas Keluar</span></a>
                                                </li>
                                            @endcanany
                                            @can('view cash-advance-return')
                                                <li id="cash-advance-return-vendor">
                                                    <a href="{{ route('admin.cash-advance-return-vendor.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Pengembalian Uang Muka</span></a>
                                                </li>
                                            @endcan
                                        </ul>
                                    </li>
                                @endcanany

                                @canany(['view supplier-invoice', 'view supplier-invoice-general'])
                                    <li class="treeview" id="supplier-invoice-sidebar">
                                        <a href="#">
                                            <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Tagihan<span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
                                        </a>
                                        <ul class="treeview-menu">
                                            @can('view supplier-invoice')
                                                <li id="supplier-invoice">
                                                    <a href="{{ route('admin.supplier-invoice.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Purchase Invoice (LPB)</span></a>
                                                </li>
                                            @endcan
                                            @can('view supplier-invoice-general')
                                                <li id="supplier-invoice-general">
                                                    <a href="{{ route('admin.supplier-invoice-general.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Puchase Invoice (Non LPB)</span></a>
                                                </li>
                                            @endcan
                                        </ul>
                                    </li>
                                @endcanany

                                @canany(['view cash-bond', 'view cash-bond-return'])
                                    <li class="treeview" id="cash-bond-sidebar">
                                        <a href="#">
                                            <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Kasbon<span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
                                        </a>
                                        <ul class="treeview-menu">
                                            @can('view cash-bond')
                                                <li id="cash-bond">
                                                    <a href="{{ route('admin.cash-bond.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Kasbon</span></a>
                                                </li>
                                            @endcan
                                            @can('view cash-bond-return')
                                                <li id="cash-bond-return">
                                                    <a href="{{ route('admin.cash-bond-return.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Pengembalian Kasbon</span></a>
                                                </li>
                                            @endcan
                                        </ul>
                                    </li>
                                @endcanany

                                @can('view tax-reconciliation')
                                    <li id="tax-reconciliation">
                                        <a href="{{ route('admin.tax-reconciliation.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Rekonsiliasi pajak</span></a>
                                    </li>
                                @endcan

                                @canany(['view asset-disposition', 'view depreciation', 'view amortization'])
                                    <li class="treeview" id="asset-finance-sidebar">
                                        <a href="#">
                                            <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Aset<span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
                                        </a>
                                        <ul class="treeview-menu">
                                            @can('view asset-disposition')
                                                <li id="disposition">
                                                    <a href="{{ route('admin.disposition.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Disposisi</span></a>
                                                </li>
                                            @endcan
                                            @can('view depreciation')
                                                <li id="depreciation">
                                                    <a href="{{ route('admin.depreciation.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Depresiasi</span></a>
                                                </li>
                                            @endcan
                                            @can('view amortization')
                                                <li id="amortization">
                                                    <a href="{{ route('admin.amortization.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Amortisasi</span></a>
                                                </li>
                                            @endcan
                                        </ul>
                                    </li>
                                @endcanany

                                @can('view closing-period')
                                    <li id="closing-period">
                                        <a href="{{ route('admin.closing-period.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Closing</span></a>
                                    </li>
                                @endcan

                                @can('view journal')
                                    <li id="journal">
                                        <a href="{{ route('admin.journal.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Jurnal</span></a>
                                    </li>
                                @endcan

                                @can('finance report')
                                    <li id="finance-report">
                                        <a href="{{ route('admin.finance-report.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Laporan Finance</span></a>
                                    </li>
                                @endcan

                                @can('accounting report')
                                    <li id="accounting-report">
                                        <a href="{{ route('admin.accounting-report.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Laporan Akuntansi</span></a>
                                    </li>
                                @endcan

                                @can('cashier report')
                                    <li id="cashier-report">
                                        <a href="{{ route('admin.cashier-report.index') }}"> <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span>Laporan Kasir</span></a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcanany
                </ul>

                <div class="sidebar-widgets">
                    <div class="copyright text-center m-25 text-white-50">
                        <p><strong class="d-block">{{ getCompany()->name }}</strong>
                            ©
                            <script>
                                document.write(new Date().getFullYear())
                            </script> All Rights Reserved
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</aside>
