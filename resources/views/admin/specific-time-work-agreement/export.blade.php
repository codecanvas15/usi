<!DOCTYPE html>
<html>

<head>
    <title>PKWT {{ $model->kode }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif !important;
            font-size: 12px;
            /* margin-top: 4.5cm !important; */
        }

        header {
            position: fixed;
            top: 0cm;
            left: 0cm;
            right: 0cm;
            height: 4.2cm;
        }

        figure {
            margin: 0 0 1rem;
        }

        figure table {
            width: 100%;
            border-collapse: collapse;
        }

        figure table tr td {
            border: none !important;
            padding: 2px 0px !important;
        }

        h3,
        h4 {
            margin: 0 !important
        }
    </style>

    <link rel="stylesheet" type="text/css" href="{{ public_path() }}/css/pdf.css">
</head>

<body style="">
    {{-- <header class="row">
        <table>
            <tr>
                <td>
                    <h2 class="brand-head mb-0">{{ getCompany()->name }}</h2>
                    <p class="my-0">{{ getCompany()->address }}</p>
                    <p class="my-0">Telp. {{ getCompany()->phone }}</p>
                </td>
                <td style="width: 25%">
                </td>
            </tr>
        </table>
        <hr style="border: 1px solid grey; margin-bottom: 0px !important">
    </header> --}}
    {!! $model->description !!}
</body>

</html>
