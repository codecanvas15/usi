<!DOCTYPE html>
<html>

<head>
    <title></title>
    <style type="text/css">
        @font-face {
            font-family: 'montserrat';
            src: url('fonts/montserrat.ttf') format("truetype");
            font-weight: 400; // use the matching font-weight here ( 100, 200, 300, 400, etc).
            font-style: normal; // use the matching font-style here
        }

        @font-face {
            font-family: 'montserrat-bold';
            src: url('fonts/montserrat-bold.ttf') format("truetype");
            font-weight: 500; // use the matching font-weight here ( 100, 200, 300, 400, etc).
            font-style: normal; // use the matching font-style here
        }

        body {
            font-family: "montserrat";
        }

        .bold {
            font-family: "montserrat-bold";
            font-size: 11px;
        }

        table {
            border-spacing: 0px;
        }

        #footer {
            position: fixed;
            left: 0px;
            bottom: 0;
            right: 0px;
        }

        #footer .page:after {
            content: counter(page, upper-roman);
        }
    </style>
</head>

<body style="font-size: 10px; left: 0; right: 0">
    <div class="container" style="color: black">
        <div class="row" style="max-width: 100%">
            <table style="width: 100%">
                <tr>
                    <td style="width: 65%; vertical-align: top">
                        <span class="bold" style="color:red; font-size:25px;">{{ getCompany()->name }}</span><br>
                        <p style="margin-top: 5px;font-size:16px;">{{ getCompany()->address }}<br>
                    </td>
                    <td style="width: 25%">
                        <center><img src="{{ public_path('/images/icon.png') }}" style="width: 70%"></center>
                    </td>
                </tr>
            </table>
        </div>
        <div class="row" style="max-width:100%;margin-top:10px;">
            <center><span class="bold text-center" style="justify-content:center;font-size:25px;margin-bottom:10px">Purchase Order</span></center><br>
            <hr style="border: 1px solid grey;">
        </div>
        <div class="row" style="max-width:100%;margin-top:10px;">
            <table style="width: 100%">
                <tr>
                    <td style="width: 50%;font-size:18px;">
                        <span>Date</span> : <span> 25-09-2022</span>
                    </td>
                    <td style="width: 50%;text-align:right;font-size:18px;">
                        <span>Kepada YTH</span> : <span> PT Pertamina</span>
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%;font-size:18px;">
                        <span>Number</span> : <span> 25-09-2022</span>
                    </td>
                    <td style="width: 50%;text-align:right;font-size:18px;">
                        <span>Alamat</span> : <span> JL Besar Indah No. 01 Kenjeran, Surabaya</span>
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%;font-size:18px;">
                        <span>Client</span> : <span> PT. Sejahtera Abadi Sentosa</span>
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%;font-size:18px;">
                        <span>SH No.</span> : <span> 858902</span>
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%;font-size:18px;">
                        <span>Drop Point</span> : <span> 858902</span>
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%;font-size:18px;">
                        <span>Supply Point</span> : <span> SURABAYA</span>
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%;font-size:18px;">
                        <span>Transportir</span> : <span> SURABAYA</span>
                    </td>
                </tr>
            </table>
        </div>
        <div class="row showTable" style="margin-top: 20px;max-width: 100%">
            <table width="100%" style="margin-top: 15px;border: 1px solid black;">
                <tr style="background-color: black; color:white; height: 50px;">
                    <th style="text-align: center; width: 80px; padding: 5px;border: 1px solid black;"><span class="bold">Description</span></th>
                    <th style="text-align: center; padding: 5px;border: 1px solid black;"><span class="bold">Qty</span></th>
                    <th style="text-align: center; padding: 5px;border: 1px solid black;"><span class="bold">Unit Price</span></th>
                    <th style="text-align: center; padding: 5px;border: 1px solid black;"><span class="bold">Amount</span></th>
                </tr>

                <tr style="color:white; height: 50px;">
                    <td style="text-align: center; padding: 4px;border: 1px solid black;">&nbsp;</td>
                    <td style="text-align: center; padding: 4px;border: 1px solid black;">&nbsp;</td>
                    <td style="text-align: center; padding: 4px;border: 1px solid black;">&nbsp;</td>
                    <td style="text-align: center; padding: 4px;border: 1px solid black;">&nbsp;</td>
                </tr>
                <tr style="color:white; height: 50px;">
                    <td style="text-align: center; padding: 4px;border: 1px solid black;">&nbsp;</td>
                    <td style="text-align: center; padding: 4px;border: 1px solid black;">&nbsp;</td>
                    <td style="text-align: center; padding: 4px;border: 1px solid black;">&nbsp;</td>
                    <td style="text-align: center; padding: 4px;border: 1px solid black;">&nbsp;</td>
                </tr>
                <tr style="color:white; height: 50px;">
                    <td style="text-align: center; padding: 4px;border: 1px solid black;">&nbsp;</td>
                    <td style="text-align: center; padding: 4px;border: 1px solid black;">&nbsp;</td>
                    <td style="text-align: center; padding: 4px;border: 1px solid black;">&nbsp;</td>
                    <td style="text-align: center; padding: 4px;border: 1px solid black;">&nbsp;</td>
                </tr>
            </table>
        </div>
        <div class="row" style="max-width:100%;text-align:right;margin-top:10px">
            <span style="font-size:26px;">
                Subtotal
            </span>
            <span style="font-size:18px;">
                Rp. 100.000.000
            </span>
        </div>

        <div class="row" style="max-width:100%;text-align:right;margin-top:10px">
            <span style="font-size:26px;">
                PPN
            </span>
            <span style="font-size:18px;">
                Rp. 100.000.000
            </span>
        </div>

        <div class="row" style="max-width:100%;text-align:right;margin-top:10px">
            <span style="font-size:26px;">
                Total
            </span>
            <span style="font-size:18px;">
                Rp. 100.000.000
            </span>
        </div>
        <div class="row" style="max-width:100%;text-align:right;margin-top:30px;text-align:right;">
            <span style="font-size:18px;margin-right:70px">Mengetahui</span>
        </div>
        <div class="row" style="max-width:100%;text-align:right;margin-top:10px">
            <img src="{{ public_path('/images/ttd.png') }}" style="width: 30%;margin-right:30px">
        </div>
        <div class="row" style="max-width:100%;text-align:right;margin-top:10px;display:flex;justify-content:between;">
            <span style="margin-right:150px">(</span>
            <span style="margin-right:30px">)</span>
        </div>
        <div id="footer">
            <div class="row">
                <table style="width: 100%;margin-top:20px;">
                    <tr>
                        <td style="width: 65%; vertical-align: top">
                            <div>
                                <img src="{{ public_path('/images/Qr-code.png') }}" style="width: 20%">
                            </div>
                            <span class="bold" style="color:red; font-size:18px;">Terms & Conditions</span>
                            <br>
                            <p style="margin-top: 5px;font-size:18px;">Payment is due within 15 days<br>
                        </td>
                        <td style="width: 25%">
                            <p class="bold" style="font-size:18px;"> BRI(Bank Rakyat Indonesia) </p>
                            <p>Account Number : 0096.0100.426.3306</p>
                            <p>Name Account : {{ getCompany()->name }}</p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
