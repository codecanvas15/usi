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

     <tr>
         <th colspan="4" align="center"><b>AKTIVA</b></th>
     </tr>
     <tr>
         <th align="center"><b>KODE REK.</b></th>
         <th align="center"><b>KETERANGAN</b></th>
         <th align="center"><b>BULAN INI</b></th>
         <th align="center"><b>BULAN LALU</b></th>
     </tr>
     @foreach ($aktiva as $key => $item)
         <tr>
             <td align="center">
                 @if ($item['is_parent'] || $item['is_total'])
                     <b>
                 @endif
                 {{ $item['code'] ?? '' }}
                 @if ($item['is_parent'] || $item['is_total'])
                     </b>
                 @endif
             </td>
             <td align="{{ $item['is_total'] ? 'right' : 'left' }}">
                 @for ($i = 0; $i < $item['indent']; $i++)
                     &nbsp;
                 @endfor
                 @if ($item['is_parent'] || $item['is_total'])
                     <b>
                 @endif
                 {{ $item['name'] ?? '' }}
                 @if ($item['is_parent'] || $item['is_total'])
                     </b>
                 @endif
             </td>
             <td align="right">
                 @if ($item['is_parent'] || $item['is_total'])
                     <b>
                 @endif
                 @if ($item['balance'] != 0)
                     {{ $item['balance'] }}
                 @endif
                 @if ($item['total_balance'] != 0)
                     {{ $item['total_balance'] }}
                 @endif
                 @if ($item['is_parent'] || $item['is_total'])
                     </b>
                 @endif
             </td>
             <td align="right">
                 @if ($item['is_parent'] || $item['is_total'])
                     <b>
                 @endif
                 @if ($item['prev_balance'] != 0)
                     {{ $item['prev_balance'] }}
                 @endif
                 @if ($item['total_prev_balance'] != 0)
                     {{ $item['total_prev_balance'] }}
                 @endif
                 @if ($item['is_parent'] || $item['is_total'])
                     </b>
                 @endif
             </td>
         </tr>
     @endforeach
     <tr>
         <th></th>
         <th align="right">
             <b>TOTAL AKTIVA</b>
         </th>
         <th align="right">
             <b>{{ array_sum(array_column($aktiva, 'balance')) }}</b>
         </th>
         <th align="right">
             <b>{{ array_sum(array_column($aktiva, 'prev_balance')) }}</b>
         </th>
     </tr>
     <tr>
         <td></td>
     </tr>
     <tr>
         <td></td>
     </tr>
     <tr>
         <th colspan="4" align="center"><b>PASIVA (KEWAJIBAN dan EKUITAS)</b></th>
     </tr>
     <tr>
         <th align="center"><b>KODE REK.</b></th>
         <th align="center"><b>KETERANGAN</b></th>
         <th align="center"><b>BULAN INI</b></th>
         <th align="center"><b>BULAN LALU</b></th>
     </tr>
     @foreach ($pasiva as $key => $item)
         <tr>
             <td align="center">
                 @if ($item['is_parent'] || $item['is_total'])
                     <b>
                 @endif
                 {{ $item['code'] ?? '' }}
                 @if ($item['is_parent'] || $item['is_total'])
                     </b>
                 @endif
             </td>
             <td align="{{ $item['is_total'] ? 'right' : 'left' }}">
                 @for ($i = 0; $i < $item['indent']; $i++)
                     &nbsp;
                 @endfor
                 @if ($item['is_parent'] || $item['is_total'])
                     <b>
                 @endif
                 {{ $item['name'] ?? '' }}
                 @if ($item['is_parent'] || $item['is_total'])
                     </b>
                 @endif
             </td>
             <td align="right">
                 @if ($item['is_parent'] || $item['is_total'])
                     <b>
                 @endif
                 @if ($item['balance'] != 0)
                     {{ $item['balance'] }}
                 @endif
                 @if ($item['total_balance'] != 0)
                     {{ $item['total_balance'] }}
                 @endif
                 @if ($item['is_parent'] || $item['is_total'])
                     </b>
                 @endif
             </td>
             <td align="right">
                 @if ($item['is_parent'] || $item['is_total'])
                     <b>
                 @endif
                 @if ($item['prev_balance'] != 0)
                     {{ $item['prev_balance'] }}
                 @endif
                 @if ($item['total_prev_balance'] != 0)
                     {{ $item['total_prev_balance'] }}
                 @endif
                 @if ($item['is_parent'] || $item['is_total'])
                     </b>
                 @endif
             </td>
         </tr>
     @endforeach
     <tr>
         <th></th>
         <th align="right">
             <b>TOTAL PASIVA</b>
         </th>
         <th align="right">
             <b>{{ array_sum(array_column($pasiva, 'balance')) }}</b>
         </th>
         <th align="right">
             <b>{{ array_sum(array_column($pasiva, 'prev_balance')) }}</b>
         </th>
     </tr>
     <tr>
         <td></td>
     </tr>
     <tr>
         <td></td>
         <th align="right">
             <b>BALANCE (AKTIVA - PASIVA)</b>
         </th>
         <th align="right">
             <b>{{ array_sum(array_column($aktiva, 'balance')) - array_sum(array_column($pasiva, 'balance')) }}</b>
         </th>
         <th align="right">
             <b>{{ array_sum(array_column($aktiva, 'prev_balance')) - array_sum(array_column($pasiva, 'prev_balance')) }}</b>
         </th>
     </tr>
 </table>
