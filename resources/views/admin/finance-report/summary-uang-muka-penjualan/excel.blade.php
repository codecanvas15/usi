 @php
     $row_start = 7;
 @endphp
 <table>
     <tr>
         <td colspan="5">
             <p><b>{{ getCompany()->name }}</b></p>
             <p><b>{{ getCompany()->address }}</b></p>
             <p><b>Telp. {{ getCompany()->phone }}</b></p>
         </td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td colspan="2">
             {{-- <center><img src="{{ storage_path('/app/public/' . getCompany()->logo) }}" width="120px"></center> --}}
         </td>
     </tr>
     <tr>
         <td></td>
     </tr>
     <tr>
         <td colspan="13" align="center">
             <p><b>LAPORAN {{ Str::upper(Str::headline($type)) }}</b></p>
         </td>
     </tr>
     <tr>
         <td colspan="13" align="center">
             <p><b>TANGGAL : {{ localDate($from_date) }}/{{ localDate($to_date) }}</b></p>
         </td>
     </tr>
     @if ($customer)
         @php
             $row_start++;
         @endphp
         <tr>
             <td colspan="13" align="center">
                 <p><b>CUSTOMER : {{ $customer->nama }}</b></p>
             </td>
         </tr>
     @endif
 </table>
 <table>
     <thead>
         <tr>
             <th align="center"><b>NO.</b></th>
             <th align="center"><b>CUSTOMER</b></th>
             <th align="center"><b>BKM</b></th>
             <th align="center"><b>NO REF</b></th>
             <th align="center"><b>NO SO</b></th>
             <th align="center"><b>TANGGAL</b></th>
             <th align="center"><b>MATA UANG</b></th>
             <th align="center"><b>TOTAL</b></th>
             <th align="center"><b>DIGUNAKAN</b></th>
             <th align="center"><b>SALDO</b></th>
             <th align="center"><b>KURS</b></th>
             <th align="center"><b>TOTAL {{ get_local_currency()->kode }}</b></th>
             <th align="center"><b>DIGUNAKAN {{ get_local_currency()->kode }}</b></th>
             <th align="center"><b>SALDO {{ get_local_currency()->kode }}</b></th>
         </tr>
     </thead>
     <tbody>
         @forelse ($data as $key => $d)
             <tr>
                 <td align="center">{{ $loop->iteration }}</td>
                 <td align="center">{{ $d->customer_nama }}</td>
                 <td align="center">{{ $d->bank_code }}</td>
                 <td align="center">
                     @php
                         $link = '#';

                         if (isset($d->cash_advance_receive_id)) {
                             $link = route('admin.cash-advance-receive.show', ['cash_advance_receive' => $d->cash_advance_receive_id]);
                         } elseif ($d->invoice_down_payment_id) {
                             $link = route('admin.invoice-down-payment.show', ['invoice_down_payment' => $d->invoice_down_payment_id]);
                         }
                     @endphp
                     <a href="{{ $link }}" target="_blank">{{ $d->reference }}</a>
                 </td>
                 <td align="center">
                     @php
                         $link = '#';

                         if (isset($d->sale_order_model_id)) {
                             if ($d->so_code) {
                                 $link = route('admin.sales-order-general.show', ['sales_order_general' => $d->sale_order_model_id]);
                             } elseif ($d->so_trading_code) {
                                 $link = route('admin.sales-order.show', ['sales_order' => $d->sale_order_model_id]);
                             }
                         }
                     @endphp
                     <a href="{{ $link }}" target="_blank">{{ $d->so_code ?? ($d->so_trading_code ?? '') }}</a>
                 </td>
                 <td align="center">{{ localDate($d->cash_advance_date) }}</td>
                 <td align="center">{{ $d->currency_nama }}</td>
                 <td align="right">{{ $d->cash_advance_amount }}</td>
                 <td align="right">{{ $d->returned_amount }}</td>
                 <td align="right">{{ $d->cash_advance_remaining_amount }}</td>
                 <td align="right">{{ $d->exchange_rate }}</td>
                 <td align="right">{{ $d->cash_advance_amount_exchanged }}</td>
                 <td align="right">{{ $d->returned_amount_exchanged }}</td>
                 <td align="right">{{ $d->cash_advance_remaining_amount_exchanged }}</td>
             </tr>
         @empty
             @php
                 $row_start++;
             @endphp
             <tr>
                 <td align="center" colspan="14">
                     Tidak ada data
                 </td>
             </tr>
         @endforelse
     </tbody>
     <tfoot>
         <tr>
             <th align="center"></th>
             <th colspan="10" align="center"><b>TOTAL</b></th>
             <th align="right"><b>=SUM(L{{ $row_start }}:L{{ $row_start + count($data) - 1 }})</b></th>
             <th align="right"><b>=SUM(M{{ $row_start }}:M{{ $row_start + count($data) - 1 }})</b></th>
             <th align="right"><b>=SUM(N{{ $row_start }}:N{{ $row_start + count($data) - 1 }})</b></th>
         </tr>
     </tfoot>
 </table>
