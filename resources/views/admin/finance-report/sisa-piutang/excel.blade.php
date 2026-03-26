 @php
     $row_start = 7;
 @endphp
 <table>
     <tr>
         <td>
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
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td>
             {{-- <center><img src="{{ storage_path('/app/public/' . getCompany()->logo) }}" width="120px"></center> --}}
         </td>
     </tr>
     <tr>
         <td></td>
     </tr>
     <tr>
         <td colspan="10" align="center">
             <p><b>LAPORAN {{ Str::upper(Str::headline($type)) }}</b></p>
         </td>
     </tr>
     <tr>
         <td colspan="10" align="center">
             <p><b>TANGGAL : {{ localDate($to_date) }}</b></p>
         </td>
     </tr>
     @if ($customer)
         @php
             $row_start++;
         @endphp
         <tr>
             <td colspan="10" align="center">
                 <p><b>CUSTOMER : {{ $customer->nama }} - {{ $customer->code }}</b></p>
             </td>
         </tr>
     @endif
     @if ($currency)
         @php
             $row_start++;
         @endphp
         <tr>
             <td colspan="10" align="center">
                 <p><b>MATA UANG : {{ $currency->kode }} - {{ $currency->nama }}</b></p>
             </td>
         </tr>
     @endif
 </table>
 <table>
     <thead>
         <tr>
             <th align="center"><b>NO.</b></th>
             <th align="center"><b>NO TRANSAKSI</b></th>
             <th align="center"><b>KODE CUSTOMER</b></th>
             <th align="center"><b>NAMA CUSTOMER</b></th>
             <th align="center"><b>TANGGAL</b></th>
             <th align="center"><b>JATUH TEMPO</b></th>
             <th align="center"><b>OVERDUE</b></th>
             <th align="center"><b>TOTAL</b></th>
             <th align="center"><b>TERBAYAR</b></th>
             <th align="center"><b>SISA</b></th>
             <th align="center"><b>KURS</b></th>
             <th align="center"><b>TOTAL</b></th>
             <th align="center"><b>TERBAYAR</b></th>
             <th align="center"><b>AKUMULASI SELISIH KURS</b></th>
             <th align="center"><b>SISA</b></th>
         </tr>
     </thead>
     <tbody>
         @forelse ($data as $key => $d)
             <tr>
                 <td align="center">{{ $key + 1 }}.</td>
                 <td align="center">{{ $d->code }}</td>
                 <td>{{ $d->customer_code }}</td>
                 <td>{{ $d->customer_nama }}</td>
                 <td align="center">{{ localDate($d->date) }}</td>
                 <td align="center">{{ localDate($d->due_date) }}</td>
                 <td align="right">{{ $d->overdue }}</td>
                 <td align="right">{{ $d->total }}</td>
                 <td align="right">{{ $d->paid_amount }}</td>
                 <td align="right">{{ $d->outstanding_amount }}</td>
                 <td align="right">{{ $d->exchange_rate }}</td>
                 <td align="right">{{ $d->total_exchanged }}</td>
                 <td align="right">{{ $d->paid_amount_exchanged }}</td>
                 <td align="right">{{ $d->acumulated_exchange_rate_gap }}</td>
                 <td align="right">{{ $d->outstanding_amount_exchanged }}</td>
             </tr>
         @empty
             @php
                 $row_start++;
             @endphp
             <tr>
                 <td align="center" colspan="15">
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
             <th align="right"></th>
             <th align="right"><b>=SUM(O{{ $row_start }}:O{{ $row_start + count($data) - 1 }})</b></th>
         </tr>
     </tfoot>
 </table>
