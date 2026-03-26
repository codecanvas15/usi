<tr>
    <th style="border: 1px solid #000" colspan="8" class="text-end text-right"><b>Main Total</b></th>
    <th style="border: 1px solid #000" class="text-end text-right"><b>{{ $formatNumber ? formatNumber($item->main_sub_total) : $item->main_sub_total }}</b></th>
    <th style="border: 1px solid #000" class="text-end text-right"><b>{{ $formatNumber ? formatNumber($item->main_total_tax) : $item->main_total_tax }}</b></th>
    <th style="border: 1px solid #000" class="text-end text-right"><b>{{ $formatNumber ? formatNumber($item->main_total) : $item->main_total }}</b></th>
    <th style="border: 1px solid #000"></th>
    <th style="border: 1px solid #000" class="text-end text-right"><b>{{ $formatNumber ? formatNumber($item->main_sub_total_final) : $item->main_sub_total_final }}</b></th>
    <th style="border: 1px solid #000" class="text-end text-right"><b>{{ $formatNumber ? formatNumber($item->main_total_tax_final) : $item->main_total_tax_final }}</b></th>
    <th style="border: 1px solid #000" class="text-end text-right"><b>{{ $formatNumber ? formatNumber($item->main_total_final) : $item->main_total_final }}</b></th>
</tr>
<tr>
    <th style="border: 1px solid #000" colspan="8" class="text-end text-right"><b>Additional Total</b></th>
    <th style="border: 1px solid #000" class="text-end text-right"><b>{{ $formatNumber ? formatNumber($item->additional_sub_total) : $item->additional_sub_total }}</b></th>
    <th style="border: 1px solid #000" class="text-end text-right"><b>{{ $formatNumber ? formatNumber($item->additional_total_tax) : $item->additional_total_tax }}</b></th>
    <th style="border: 1px solid #000" class="text-end text-right"><b>{{ $formatNumber ? formatNumber($item->additional_total) : $item->additional_total }}</b></th>
    <th style="border: 1px solid #000"></th>
    <th style="border: 1px solid #000" class="text-end text-right"><b>{{ $formatNumber ? formatNumber($item->additional_sub_total_final) : $item->additional_sub_total_final }}</b></th>
    <th style="border: 1px solid #000" class="text-end text-right"><b>{{ $formatNumber ? formatNumber($item->additional_total_tax_final) : $item->additional_total_tax_final }}</b></th>
    <th style="border: 1px solid #000" class="text-end text-right"><b>{{ $formatNumber ? formatNumber($item->additional_total_final) : $item->additional_total_final }}</b></th>
</tr>
<tr>
    <th style="border: 1px solid #000" colspan="8" class="text-end text-right"><b>Total</b></th>
    <th style="border: 1px solid #000" class="text-end text-right"><b>{{ $formatNumber ? formatNumber($item->sub_total_all) : $item->sub_total_all }}</b></th>
    <th style="border: 1px solid #000" class="text-end text-right"><b>{{ $formatNumber ? formatNumber($item->total_tax_all) : $item->total_tax_all }}</b></th>
    <th style="border: 1px solid #000" class="text-end text-right"><b>{{ $formatNumber ? formatNumber($item->total_all) : $item->total_all }}</b></th>
    <th style="border: 1px solid #000"></th>
    <th style="border: 1px solid #000" class="text-end text-right"><b>{{ $formatNumber ? formatNumber($item->sub_total_final_all) : $item->sub_total_final_all }}</b></th>
    <th style="border: 1px solid #000" class="text-end text-right"><b>{{ $formatNumber ? formatNumber($item->total_tax_final_all) : $item->total_tax_final_all }}</b></th>
    <th style="border: 1px solid #000" class="text-end text-right"><b>{{ $formatNumber ? formatNumber($item->total_final_all) : $item->total_final_all }}</b></th>
</tr>
