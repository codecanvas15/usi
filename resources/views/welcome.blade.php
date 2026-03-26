@extends('layouts.admin.layout.index')

@section('title', 'Dashboard Page - ')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="box">
				<div class="box-header with-border">
                    <h4 class="box-title">Main Title</h4>
				</div>
				<!-- /.box-header -->
				<div class="box-body">
                    <form action="" method="get">
                        <div class="row">
                            <div class="col-md-12 col-lg-4 col-xl-3">
                                <div class="form-group">
                                    <label for="">Select Periode</label>
                                    <select name="" id="periode" class="form-control select2">
                                        <option value="">---------</option>
                                        <option value="January 1 - 15">January 1 - 15</option>
                                        <option value="January 16 - 31">January 16 - 31</option>
                                        <option value="February 1 - 15">February 1 - 15</option>
                                        <option value="February 16 - 28">February 16 - 28</option>
                                        <option value="March 1 - 15">March 1 - 15</option>
                                        <option value="March 16 - 31">March 16 - 31</option>
                                        <option value="April 1 - 15">April 1 - 15</option>
                                        <option value="April 16 - 30">April 16 - 30</option>
                                        <option value="Mei 1 - 15">Mei 1 - 15</option>
                                        <option value="Mei 16 - 31">Mei 16 - 31</option>
                                        <option value="June 1 - 15">June 1 - 15</option>
                                        <option value="June 16 - 30">June 16 - 30</option>
                                        <option value="July 1 - 15">July 1 - 15</option>
                                        <option value="July 16 - 31">July 16 - 31</option>
                                        <option value="August 1 - 15">August 1 - 15</option>
                                        <option value="August 16 - 31">August 16 - 31</option>
                                        <option value="September 1 - 15">September 1 - 15</option>
                                        <option value="September 16 - 30">September 16 - 30</option>
                                        <option value="October 1 - 15">October 1 - 15</option>
                                        <option value="October 16 - 31">October 16 - 31</option>
                                        <option value="November 1 - 15">November 1 - 15</option>
                                        <option value="November 16 - 30">November 16 - 30</option>
                                        <option value="December 1 - 15">December 1 - 15</option>
                                        <option value="December 16 - 31">December 16 - 31</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-md-12 col-lg-3 col-xl-2">
                                <div class="form-group">
                                    <label for="">Customer</label>
                                    <select name="" id="customer" class="form-control" disabled>
                                        <option value="">----</option>
                                        <option value="Data 1">Data 1</option>
                                        <option value="Data 1">Data 1</option>
                                        <option value="Data 1">Data 1</option>
                                        <option value="Data 1">Data 1</option>
                                        <option value="Data 1">Data 1</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-3 col-xl-2">
                                <div class="form-group">
                                    <label for="">Supply Point</label>
                                    <select name="" id="supply_point" class="form-control" disabled>
                                        <option value="">----</option>
                                        <option value="Data 1">Data 1</option>
                                        <option value="Data 1">Data 1</option>
                                        <option value="Data 1">Data 1</option>
                                        <option value="Data 1">Data 1</option>
                                        <option value="Data 1">Data 1</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-3 col-xl-2">
                                <div class="form-group">
                                    <label for="">Harga Beli</label>
                                    <select name="" id="harga_beli" class="form-control" disabled>
                                        <option value="">----</option>
                                        <option value="Data 1">Data 1</option>
                                        <option value="Data 1">Data 1</option>
                                        <option value="Data 1">Data 1</option>
                                        <option value="Data 1">Data 1</option>
                                        <option value="Data 1">Data 1</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-3 col-xl-2">
                                <div class="form-group">
                                    <label for="">Harga Jual + PPN</label>
                                    <input type="text" name="" id="harga_jual" class="form-control" disabled>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-3 col-xl-2">
                                <div class="form-group mb-0">
                                    <button class="btn btn-primary" id="submit" disabled><i data-feather="check-circle"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th>Supply Point</th>
                                    <th>Harga Beli</th>
                                    <th>Harga Jual</th>
                                    <th></th>
                                </thead>
                                <tbody id="data">
                                </tbody>
                            </table>
                        </div>

                        <div class="row justify-content-end">
                            <div class="col-12 col-md-6 col-xl-3 d-flex justify-content-end gap-3">
                                <a href="#" class="btn btn-warning">Save to Draft</a>
                                <a href="#" class="btn btn-info">Save</a>
                            </div>
                        </div>
                    </form>
				</div>
				<!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
@endsection

@section('js')
    {{-- <script src="{{ asset('/vendors/assets/vendor_components/apexcharts-bundle/dist/apexcharts.js')}}"></script>
    <script src="{{ asset('/vendors/assets/vendor_components/progressbar.js-master/dist/progressbar.js')}}"></script>
    <script src="{{ asset('/vendors/js/pages/dashboard.js')}}"></script> --}}


    <script>
        let array = [];
        let id = 0;

        $('#harga_jual').change(function (e) {
            console.log(e);
            e.target.value = numberWithCommas(e.target.value);
        });

        const numberWithCommas = (num) => {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        $('#periode').change(function (e) { 
            if (e.target.value != null || e.target.value != '') {
                let customer_val = $('#customer').removeAttr('disabled');
                let supply_point_val = $('#supply_point').removeAttr('disabled');
                let harga_beli = $('#harga_beli').removeAttr('disabled');
                let harga_jual = $('#harga_jual').removeAttr('disabled');
                let btn = $('#submit').removeAttr('disabled');
            } else {
                let customer_val = $('#customer').attr('disabled');
                let supply_point_val = $('#supply_point').attr('disabled');
                let harga_beli = $('#harga_beli').attr('disabled');
                let harga_jual = $('#harga_jual').attr('disabled');
                let btn = $('#submit').attr('disabled');
            }
        });

        const displayData = () => {
            let html = ``;
            array.map((e, index) => {
                html += `<tr>
                        <td>${index + 1}</td>
                        <td>${e.customer_val}</td>
                        <td>${e.supply_point_val}</td>
                        <td>${e.harga_beli}</td>
                        <td>${e.harga_jual}</td>
                        <td>
                            <a href="#" class="btn btn-danger" onclick="removeData(${e.id})">Delete</a>
                        </td>
                    </tr>`;
                return html;
            });
                
            $('#data').html(html);
        }

        const removeData = (index) => {
            array = array.filter((data) => {
                return data.id !== index;
            })
            displayData();
        }

        $('#submit').click(function (e) { 
            e.preventDefault();
            
            let customer_val = $('#customer');
            let supply_point_val = $('#supply_point');
            let harga_beli = $('#harga_beli');
            let harga_jual = $('#harga_jual');

            array.push({
                id: id, 
                customer_val : customer_val.val(),
                supply_point_val : supply_point_val.val(),
                harga_beli : harga_beli.val(),
                harga_jual : harga_jual.val(),
            });

            customer_val.val(null);
            supply_point_val.val(null);
            harga_beli.val(null);
            harga_jual.val(null);

            id++;

            displayData();
        });
    </script>
@endsection