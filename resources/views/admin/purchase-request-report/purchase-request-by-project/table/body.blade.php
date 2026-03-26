    @php
        $status = $itemReport->purchase_request_status;
        $background = 'border:1px solid #000; background-color: #fff; color: black;';
        if ($itemReport->purchase_request_status == 'done' && $itemReport->outstanding_quantity > 0) {
            $background = 'border:1px solid #000; background-color: #ffb3c6; color: black;';
            $status = 'Closed';
        } elseif ($itemReport->purchase_request_status == 'done' && $itemReport->outstanding_quantity == 0) {
            $background = 'border:1px solid #000; background-color: #c7f9cc; color: black;';
        }
    @endphp

    <tr>
        <td style="{{ $background }}">{{ localDate($itemReport->purchase_request_date) }}</td>
        <td style="{{ $background }}">{{ $itemReport->purchase_request_code }}</td>
        <td style="{{ $background }}">{{ $itemReport->purchase_request_note ?? '' }}</td>
        <td style="{{ $background }}">{{ $itemReport->item_name }}</td>
        <td style="{{ $background }}">
            @if (is_array($itemReport->vendor_name))
                @foreach ($itemReport->vendor_name as $item)
                    <p style="margin-bottom:0">{{ $item }}</p>
                @endforeach
            @else
                <p style="margin-bottom: 0">{{ $itemReport->vendor_name }}</p>

            @endif
        </td>
        <td style="{{ $background }}">{{ $formatNumber ? formatNumber($itemReport->purchase_request_quantity) : $itemReport->purchase_request_quantity }}</td>
        <td style="{{ $background }}">{{ $formatNumber ? formatNumber($itemReport->purchase_request_quantity_approved) : $itemReport->purchase_request_quantity_approved }}</td>
        <td style="{{ $background }}">
            @if (is_array($itemReport->purchase_date))
                @foreach ($itemReport->purchase_date as $item)
                    <p style="margin-bottom:0">{{ $item }}</p>
                @endforeach
            @else
                <p style="margin-bottom: 0">{{ $itemReport->purchase_date }}</p>

            @endif
        </td>
        <td style="{{ $background }}">
            @if (is_array($itemReport->purchase_code))
                @foreach ($itemReport->purchase_code as $item)
                    <p style="margin-bottom:0">{{ $item }}</p>
                @endforeach
            @else
                <p style="margin-bottom: 0">{{ $itemReport->purchase_code }}</p>
            @endif
        </td>
        <td style="{{ $background }}">{{ $formatNumber ? formatNumber($itemReport->purchase_quantity) : $itemReport->purchase_quantity }}</td>
        <td style="{{ $background }}">{{ $formatNumber ? formatNumber($itemReport->outstanding_quantity) : $itemReport->outstanding_quantity }}</td>
        <td style="{{ $background }}">
            @if (is_array($itemReport->receiving_report_code))
                @foreach ($itemReport->receiving_report_code as $item)
                    <p style="margin-bottom:0">{{ $item }}</p>
                @endforeach
            @else
                <p style="margin-bottom: 0">{{ $itemReport->receiving_report_code }}</p>

            @endif
        </td>
        <td style="{{ $background }}">{{ $itemReport->receiving_report_quantity }}</td>
        <td style="{{ $background }}">{{ Str::headline($status) }}</td>
    </tr>
