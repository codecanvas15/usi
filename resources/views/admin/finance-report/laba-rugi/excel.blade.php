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
 <table>
     <thead>
         <tr>
             <th align="center">KODE REK.</th>
             <th align="center">KETERANGAN</th>
             <th align="center">BULAN INI</th>
             <th align="center">S/D BULAN INI</th>
         </tr>
     </thead>
     <tbody>
         @php
             $total_parent = 0;
             $total_parent_prev = 0;
         @endphp
         @foreach ($data as $key => $item)
             @foreach ($data[$key] as $key_subcategory => $subcategory)
                 @php
                     $row_start++;
                 @endphp
                 @php
                     $total_subcategory = 0;
                     $total_subcategory_prev = 0;
                 @endphp
                 <tr>
                     <td></td>
                     <td><b>{{ Str::upper(Str::headline($key_subcategory)) }}</b></td>
                     <td></td>
                     <td></td>
                 </tr>
                 @foreach ($item[$key_subcategory]['data'] as $detail)
                     <tr>
                         <td align="center">{{ Str::upper(Str::headline($detail['code'])) }}</td>
                         <td>{{ Str::upper(Str::headline($detail['coa'])) }}</td>
                         <td align="right">{{ $detail['current_period'] }}</td>
                         <td align="right">{{ $detail['prev_period'] }}</td>
                     </tr>
                     @php
                         if ($item[$key_subcategory]['type'] == 'plus') {
                             $total_subcategory += $detail['current_period'];
                             $total_subcategory_prev += $detail['prev_period'];
                         } else {
                             $total_subcategory -= $detail['current_period'];
                             $total_subcategory_prev -= $detail['prev_period'];
                         }
                     @endphp
                 @endforeach
                 <tr>
                     <td></td>
                     <td align="right"><b>TOTAL {{ Str::upper(Str::headline($key_subcategory)) }}</b></td>
                     <td align="right"><b>=SUM(C{{ $row_start + 1 }}:C{{ $row_start + 1 + count($item[$key_subcategory]['data']) }})</b></td>
                     <td align="right"><b>=SUM(D{{ $row_start + 1 }}:D{{ $row_start + 1 + count($item[$key_subcategory]['data']) }})</b></td>
                 </tr>
                 @php
                     $row_start += count($item[$key_subcategory]['data']) + 1;
                     $total_parent += $total_subcategory;
                     $total_parent_prev += $total_subcategory_prev;
                 @endphp
             @endforeach
             <tr>
                 <td colspan="2"><b>{{ Str::upper(Str::headline($key)) }}</b></td>
                 <td align="right"><b>{{ $total_parent }}</b></td>
                 <td align="right"><b>{{ $total_parent_prev }}</b></td>
             </tr>
             <tr>
                 <td></td>
                 <td></td>
                 <td></td>
                 <td></td>
             </tr>
             @php
                 $row_start += 2;
             @endphp
         @endforeach
     </tbody>
 </table>
