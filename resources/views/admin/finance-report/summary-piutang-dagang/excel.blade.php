 @php
     $row_start = 7;
     $row_position = 7;
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
         <td>
             {{-- <center><img src="{{ storage_path('/app/public/' . getCompany()->logo) }}" width="120px"></center> --}}
         </td>
     </tr>
     <tr>
         <td></td>
     </tr>
     <tr>
         <td colspan="6" align="center">
             <p><b>LAPORAN {{ Str::upper(Str::headline($type)) }}</b></p>
         </td>
     </tr>
     <tr>
         <td colspan="6" align="center">
             <p><b>TANGGAL : {{ localDate($from_date) }}/{{ localDate($to_date) }}</b></p>
         </td>
     </tr>
 </table>
 <table>
     <thead>
         <tr>
             <th align="center"><b>NO.</b></th>
             <th align="center"><b>KODE CUSTOMER</b></th>
             <th align="center"><b>NAMA CUSTOMER</b></th>
             <th align="center"><b>SALDO AWAL</b></th>
             <th align="center"><b>PENJUALAN</b></th>
             <th align="center"><b>PELUNASAN</b></th>
             <th align="center"><b>SALDO AKHIR PIUTANG</b></th>
         </tr>
     </thead>
     <tbody>
         @forelse ($data as $key => $d)
             <tr>
                 <td align="center">{{ $key + 1 }}.</td>
                 <td align="">{{ $d->code }}</td>
                 <td align="">{{ $d->nama }}</td>
                 <td align="right">{{ $d->beginning }}</td>
                 <td align="right">{{ $d->current_in }}</td>
                 <td align="right">{{ $d->current_out }}</td>
                 <td align="right">=D{{ $row_position }}+E{{ $row_position }}-F{{ $row_position }}</td>
             </tr>
             @php
                 $row_position++;
             @endphp
         @empty
             @php
                 $row_position++;
                 $row_start++;
             @endphp
             <tr>
                 <td align="center" colspan="6">
                     Tidak ada data
                 </td>
             </tr>
         @endforelse
     </tbody>
     <tfoot>
         <tr>
             <th></th>
             <th align="center"><b>TOTAL</b></th>
             <th align="right"><b>=SUM(D{{ $row_start }}:D{{ $row_start + count($data) - 1 }})</b></th>
             <th align="right"><b>=SUM(E{{ $row_start }}:E{{ $row_start + count($data) - 1 }})</b></th>
             <th align="right"><b>=SUM(F{{ $row_start }}:F{{ $row_start + count($data) - 1 }})</b></th>
             <th align="right"><b>=D{{ $row_start + count($data) }}+E{{ $row_start + count($data) }}-F{{ $row_start + count($data) }}</b></th>
         </tr>
     </tfoot>
 </table>
