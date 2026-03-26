 <table>
     <tr>
         <td colspan="3">
             <p><b>{{ getCompany()->name }}</b></p>
             <p><b>{{ getCompany()->address }}</b></p>
             <p><b>Telp. {{ getCompany()->phone }}</b></p>
         </td>
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
         <td colspan="9" align="center">
             <p><b>REKAP PENGAJUAN DANA</b></p>
         </td>
     </tr>
     <tr>
         <td colspan="9" align="center">
             <p><b>TANGGAL : {{ localDate($from_date) }}/{{ localDate($to_date) }}</b></p>
         </td>
     </tr>
 </table>
 <table>
     <thead>
         <tr>
             <th align="center"><b>TANGGAL</b></th>
             <th align="center"><b>NO</b></th>
             <th align="center"><b>KEPADA</b></th>
             <th align="center"><b>JENIS</b></th>
             <th align="center"><b>TOTAL</b></th>
             <th align="center"><b>KURS</b></th>
             <th align="center"><b>RATE</b></th>
             <th align="center"><b>TOTAL {{ get_local_currency()->kode }}</b></th>
             <th align="center"><b>STATUS</b></th>
         </tr>
     </thead>
     <tbody>
         @php
             $total = 0;
         @endphp
         @forelse ($data as $key => $d)
             <tr>
                 <td align="center">{{ localDate($d->date) }}</td>
                 <td align="center">{{ $d->code }}</td>
                 <td align="center">{{ $d->to_name }}</td>
                 <td align="center">{{ Str::upper($d->item) }}</td>
                 <td align="right">{{ $d->total }}</td>
                 <td align="center">{{ $d->currency->nama }}</td>
                 <td align="right">{{ $d->exchange_rate }}</td>
                 <td align="right">{{ $d->total * $d->exchange_rate }}</td>
                 <td align="center">{{ Str::upper($d->status) }} / {{ $d->is_used ? 'CAIR' : 'BELUM CAIR' }}</td>
             </tr>
             @php
                 $total += $d->total * $d->exchange_rate;
             @endphp
         @empty
             <tr>
                 <td align="center" colspan="9">
                     Tidak ada data
                 </td>
             </tr>
         @endforelse
     </tbody>
     <tfoot>
         <tr>
             <th colspan="7" align="right"><b>TOTAL</b></th>
             <th align="right"><b>=SUM(H6:H{{ 6 + count($data) }})</b></th>
             <th></th>
         </tr>
     </tfoot>
 </table>
