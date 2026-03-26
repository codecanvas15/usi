 @php
     $row_start = 5;
 @endphp
 <table>
     <tr>
         <td colspan="2">
             <p><b>{{ getCompany()->name }}</b></p>
             <p><b>{{ getCompany()->address }}</b></p>
             <p><b>Telp. {{ getCompany()->phone }}</b></p>
         </td>
         <td></td>
         <td>
             {{-- <center><img src="{{ storage_path('/app/public/' . getCompany()->logo) }}" width="120px"></center> --}}
         </td>
     </tr>
     <tr>
         <td></td>
     </tr>
     <tr>
         <td colspan="4" align="center">
             <p><b>LAPORAN {{ Str::upper(Str::headline($type)) }}</b></p>
         </td>
     </tr>
     <tr>
         <td colspan="4" align="center">
             <p><b>PERIODE : {{ $period }}</b></p>
         </td>
     </tr>
     @if ($branch)
         @php
             $row_start++;
         @endphp
         <tr>
             <td colspan="4" align="center">
                 <p><b>Branch : {{ $branch->name }}</b></p>
             </td>
         </tr>
     @endif
 </table>
 @include('admin.finance-report.profit-loss-multiperiod.body', ['format_number' => false])
