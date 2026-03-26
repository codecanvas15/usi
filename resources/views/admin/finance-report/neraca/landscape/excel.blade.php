 <table>
     <tr>
         <td colspan="4">
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
         <td colspan="9" align="center">
             <p><b>LAPORAN {{ Str::upper(Str::headline($type)) }}</b></p>
         </td>
     </tr>
     <tr>
         <td colspan="9" align="center">
             <p><b>PERIODE : {{ $period }}</b></p>
         </td>
     </tr>
 </table>
 <table>
     <thead>
         <tr>
             <th align="center"><b>KODE REK.</b></th>
             <th align="center"><b>KETERANGAN</b></th>
             <th align="center"><b>BULAN INI</b></th>
             <th align="center"><b>BULAN LALU</b></th>
             <th align="center"></th>
             <th align="center"><b>KODE REK.</b></th>
             <th align="center"><b>KETERANGAN</b></th>
             <th align="center"><b>BULAN INI</b></th>
             <th align="center"><b>BULAN LALU</b></th>
         </tr>
     </thead>
     <tbody>
         @php
             $loop_data = $aktiva;
             $second_loop_data = $pasiva;
         @endphp

         @if (count($aktiva) > count($pasiva))
             @foreach ($loop_data as $key => $item)
                 <tr>
                     <td align="center">
                         @if ($item['is_parent'] || $item['is_total'])
                             <b>
                         @endif
                         {{ $item['code'] }}
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
                         {{ $item['name'] }}
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
                     <td></td>
                     @if (isset($second_loop_data[$key]))
                         <td align="center">
                             @if ($second_loop_data[$key]['is_parent'] || $second_loop_data[$key]['is_total'])
                                 <b>
                             @endif
                             {{ $second_loop_data[$key]['code'] }}
                             @if ($second_loop_data[$key]['is_parent'] || $second_loop_data[$key]['is_total'])
                                 </b>
                             @endif
                         </td>
                         <td align="{{ $second_loop_data[$key]['is_total'] ? 'right' : 'left' }}">
                             @for ($i = 0; $i < $second_loop_data[$key]['indent']; $i++)
                                 &nbsp;
                             @endfor
                             @if ($second_loop_data[$key]['is_parent'] || $second_loop_data[$key]['is_total'])
                                 <b>
                             @endif
                             {{ $second_loop_data[$key]['name'] }}
                             @if ($second_loop_data[$key]['is_parent'] || $second_loop_data[$key]['is_total'])
                                 </b>
                             @endif
                         </td>
                         <td align="right">
                             @if ($second_loop_data[$key]['is_parent'] || $second_loop_data[$key]['is_total'])
                                 <b>
                             @endif
                             @if ($second_loop_data[$key]['balance'] != 0)
                                 {{ $second_loop_data[$key]['balance'] }}
                             @endif
                             @if ($second_loop_data[$key]['total_balance'] != 0)
                                 {{ $second_loop_data[$key]['total_balance'] }}
                             @endif
                             @if ($second_loop_data[$key]['is_parent'] || $second_loop_data[$key]['is_total'])
                                 </b>
                             @endif
                         </td>
                         <td align="right">
                             @if ($second_loop_data[$key]['is_parent'] || $second_loop_data[$key]['is_total'])
                                 <b>
                             @endif
                             @if ($second_loop_data[$key]['prev_balance'] != 0)
                                 {{ $second_loop_data[$key]['prev_balance'] }}
                             @endif
                             @if ($second_loop_data[$key]['total_prev_balance'] != 0)
                                 {{ $second_loop_data[$key]['total_prev_balance'] }}
                             @endif
                             @if ($second_loop_data[$key]['is_parent'] || $second_loop_data[$key]['is_total'])
                                 </b>
                             @endif
                         </td>
                     @else
                         <td></td>
                         <td></td>
                         <td></td>
                         <td></td>
                     @endif
                 </tr>
             @endforeach
         @else
             @foreach ($second_loop_data as $key => $item)
                 <tr>
                     @if (isset($loop_data[$key]))
                         <td align="center">
                             @if ($loop_data[$key]['is_parent'] || $loop_data[$key]['is_total'])
                                 <b>
                             @endif
                             {{ $loop_data[$key]['code'] }}
                             @if ($loop_data[$key]['is_parent'] || $loop_data[$key]['is_total'])
                                 </b>
                             @endif
                         </td>
                         <td class="{{ $loop_data[$key]['is_total'] ? 'right' : 'left' }}">
                             @for ($i = 0; $i < $loop_data[$key]['indent']; $i++)
                                 &nbsp;
                             @endfor
                             @if ($loop_data[$key]['is_parent'] || $loop_data[$key]['is_total'])
                                 <b>
                             @endif
                             {{ $loop_data[$key]['name'] }}
                             @if ($loop_data[$key]['is_parent'] || $loop_data[$key]['is_total'])
                                 </b>
                             @endif
                         </td>
                         <td align="right">
                             @if ($loop_data[$key]['is_parent'] || $loop_data[$key]['is_total'])
                                 <b>
                             @endif
                             @if ($loop_data[$key]['balance'] != 0)
                                 {{ $loop_data[$key]['balance'] }}
                             @endif
                             @if ($loop_data[$key]['total_balance'] != 0)
                                 {{ $loop_data[$key]['total_balance'] }}
                             @endif
                             @if ($loop_data[$key]['is_parent'] || $loop_data[$key]['is_total'])
                                 </b>
                             @endif
                         </td>
                         <td align="right">
                             @if ($loop_data[$key]['is_parent'] || $loop_data[$key]['is_total'])
                                 <b>
                             @endif
                             @if ($loop_data[$key]['prev_balance'] != 0)
                                 {{ $loop_data[$key]['prev_balance'] }}
                             @endif
                             @if ($loop_data[$key]['total_prev_balance'] != 0)
                                 {{ $loop_data[$key]['total_prev_balance'] }}
                             @endif
                             @if ($loop_data[$key]['is_parent'] || $loop_data[$key]['is_total'])
                                 </b>
                             @endif
                         </td>
                     @else
                         <td></td>
                         <td></td>
                         <td></td>
                         <td></td>
                     @endif
                     <td></td>
                     <td align="center">
                         @if ($item['is_parent'] || $item['is_total'])
                             <b>
                         @endif
                         {{ $item['code'] }}
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
                         {{ $item['name'] }}
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
         @endif

     </tbody>
     <tfoot>
         <tr>
             <td></td>
             <td>
                 <b>TOTAL AKTIVA</b>
             </td>
             <td align="right">
                 <b>{{ array_sum(array_column($loop_data, 'balance')) }}</b>
             </td>
             <td align="right">
                 <b>{{ array_sum(array_column($loop_data, 'prev_balance')) }}</b>
             </td>
             <td></td>
             <td></td>
             <td>
                 <b>TOTAL PASIVA</b>
             </td>
             <td align="right">
                 <b>{{ array_sum(array_column($second_loop_data, 'balance')) }}</b>
             </td>
             <td align="right">
                 <b>{{ array_sum(array_column($second_loop_data, 'prev_balance')) }}</b>
             </td>
         </tr>
         <tr>
             <td></td>
             <td></td>
             <td></td>
             <td></td>
             <td></td>
             <td></td>
             <td>
                 <b>BALANCE (AKTIVA - PASIVA)</b>
             </td>
             <td align="right">
                 <b>{{ array_sum(array_column($loop_data, 'balance')) - array_sum(array_column($second_loop_data, 'balance')) }}</b>
             </td>
             <td align="right">
                 <b>{{ array_sum(array_column($loop_data, 'prev_balance')) - array_sum(array_column($second_loop_data, 'prev_balance')) }}</b>
             </td>
         </tr>
     </tfoot>
 </table>
