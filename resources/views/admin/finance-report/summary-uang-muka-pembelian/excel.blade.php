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
         <td colspan="14" align="center">
             <p><b>LAPORAN {{ Str::upper(Str::headline($type)) }}</b></p>
         </td>
     </tr>
     <tr>
         <td colspan="14" align="center">
             <p><b>TANGGAL : {{ localDate($from_date) }}/{{ localDate($to_date) }}</b></p>
         </td>
     </tr>
     @if ($vendor)
         @php
             $row_start++;
         @endphp
         <tr>
             <td colspan="14" align="center">
                 <p><b>VENDOR : {{ $vendor->nama }}</b></p>
             </td>
         </tr>
     @endif
 </table>
 <table>
     <thead>
         @include('admin.finance-report.summary-uang-muka-pembelian.table.header')
     </thead>
     <tbody>
         @include('admin.finance-report.summary-uang-muka-pembelian.table.body', ['number_format' => false])
     </tbody>
     <tfoot>
         @include('admin.finance-report.summary-uang-muka-pembelian.table.footer', ['number_format' => false])
     </tfoot>
 </table>
