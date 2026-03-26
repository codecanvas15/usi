 @php
     $current_row = 6;
     $total_exchanged = [];
     $paid_amount_exchanged = [];
     $outstanding_amount_exchanged = [];
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
     @if ($currency)
         @php
             $current_row++;
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
             <th align="center"><b>TANGGAL</b></th>
             <th align="center"><b>NO TRANSAKSI</b></th>
             <th align="center"><b>JATUH TEMPO</b></th>
             <th align="center"><b>TOTAL</b></th>
             <th align="center"><b>TERBAYAR</b></th>
             <th align="center"><b>SISA</b></th>
             <th align="center"><b>KURS</b></th>
             <th align="center"><b>TOTAL</b></th>
             <th align="center"><b>TERBAYAR</b></th>
             <th align="center"><b>SISA</b></th>
         </tr>
     </thead>
     <tbody>
         @forelse ($data as $key => $d)
             <tr>
                 <td></td>
                 <td><b>{{ $d->vendor_nama }}</b></td>
                 <td></td>
                 <td></td>
                 <td></td>
                 <td></td>
                 <td></td>
                 <td></td>
                 <td></td>
                 <td></td>
                 <td></td>
             </tr>
             @php
                 $current_row += 1;
             @endphp
             @forelse ($d->data as $key2 => $data)
                 <tr>
                     <td align="center">{{ $loop->iteration }}.</td>
                     <td>{{ localDate($data->date) }}</td>
                     <td>{{ $data->code }}</td>
                     <td>{{ localDate($data->due_date ?? '') }}</td>
                     <td align="right">{{ $data->total }}</td>
                     <td align="right">{{ $data->paid_amount }}</td>
                     <td align="right">{{ $data->outstanding_amount }}</td>
                     <td align="right">{{ $data->exchange_rate }}</td>
                     <td align="right">{{ $data->total_exchanged }}</td>
                     <td align="right">{{ $data->paid_amount_exchanged }}</td>
                     <td align="right">{{ $data->outstanding_amount_exchanged }}</td>
                 </tr>

             @empty
                 <tr>
                     <td align="center">
                         Tidak ada data
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
                 </tr>
             @endforelse
             <tr>
                 <td></td>
                 <th align="center"><b>TOTAL</b></th>
                 <td></td>
                 <td></td>
                 <td></td>
                 <td></td>
                 <td></td>
                 <td></td>
                 <th align="right"><b>=SUM(I{{ $current_row + 1 }}:I{{ $current_row + $d->data->count() }})</b></th>
                 <th align="right"><b>=SUM(J{{ $current_row + 1 }}:J{{ $current_row + $d->data->count() }})</b></th>
                 <th align="right"><b>=SUM(K{{ $current_row + 1 }}:K{{ $current_row + $d->data->count() }})</b></th>
                 @php
                     $total_exchanged[] = 'I' . $current_row + $d->data->count() + 1;
                     $paid_amount_exchanged[] = 'J' . $current_row + $d->data->count() + 1;
                     $outstanding_amount_exchanged[] = 'K' . $current_row + $d->data->count() + 1;
                 @endphp
             </tr>
             <tr>
                 <td></td>
             </tr>

             @php
                 $current_row += ($d->data->count() > 0 ? $d->data->count() : 1) + 2;
             @endphp
         @empty
             <tr>
                 <td align="center" class="font-small-1">
                     Tidak ada data
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
             </tr>

             @php
                 $current_row++;
             @endphp
         @endforelse
     </tbody>
     <tfoot>
         <tr>
             <td></td>
             <th align="center"><b>TOTAL</b></th>
             <td></td>
             <td></td>
             <td></td>
             <td></td>
             <td></td>
             <td></td>
             <th align="right"><b>=SUM({{ implode('+', $total_exchanged) }})</b></th>
             <th align="right"><b>=SUM({{ implode('+', $paid_amount_exchanged) }})</b></th>
             <th align="right"><b>=SUM({{ implode('+', $outstanding_amount_exchanged) }})</b></th>
         </tr>
     </tfoot>
 </table>
