<!DOCTYPE html>
<html>

<head>
    <title></title>
    <style type="text/css">
        body {
            font-size: 14px;
            color: #000;
        }

        table {
            border-spacing: 0px;
        }

        span {
            font-size: 12px;
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

        td {
            vertical-align: top;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="{{ public_path() }}/css/pdf.css">
</head>

@php
    function display_coa($coa, $depth = 1)
    {
        foreach ($coa->childs ?? $coa as $key => $child) {
            echo '<tr>';
            $tab = '';
            for ($i = 0; $i <= $depth; $i++) {
                $tab .= '------';
            }
            echo '<td class="font-small-1">' . $tab . ' ' . $child->account_code . ' ' . $child->name . '</td>';
            echo '</tr>';

            if ($child->childs) {
                display_coa($child, $depth + 1);
            }
        }
    }
@endphp

<body>
    <div class="row" style="max-width: 100%">
        <table style="width: 100%">
            <tr>
                <td style="width: 65%; vertical-align: top">
                    <h1 class="font-medium-1 text-uppercase mb-0">{{ getCompany()->name }}</h1>
                    <p class="font-small-1 text-uppercase mt-0">{{ getCompany()->address }}</p>
                </td>
                <td style="width: 25%">
                    {{-- <center><img src="{{ storage_path('/app/public/' . getCompany()->logo) }}" width="120px"></center> --}}
                </td>
            </tr>
        </table>
        <div class="text-center">
            <h1 class="font-medium-1">COA TREE</h1>
        </div>
    </div>
    <table>
        <tbody>
            @foreach ($return_data as $key => $coa)
                <tr>
                    <td class="font-small-1">{{ $coa->account_code }} {{ $coa->name }}</td>
                </tr>
                @php
                    display_coa($coa);
                @endphp
            @endforeach
        </tbody>
    </table>
</body>

</html>
