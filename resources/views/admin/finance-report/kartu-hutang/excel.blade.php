 @php
     $row_start = 4;

     $total_balance_formula = '';
     $total_debit_formula = '';
     $total_credit_formula = '';
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
     @if ($vendor)
         @php
             $row_start++;
         @endphp
         <tr>
             <td colspan="13" align="center">
                 <p><b>VENDOR : {{ $vendor->nama }} - {{ $vendor->code }}</b></p>
             </td>
         </tr>
     @endif
 </table>
 <table>
     @php
         $row_start++;
     @endphp
     <tbody>
         @forelse ($data as $d)
             @php
                 $row_start += 3;
                 $data_start = $row_start;
             @endphp
             <tr>
                 <th align="left" colspan="14"><b>{{ $d->nama }}</b></th>
             </tr>
             <tr>
                 <th align="center"><b>TANGGAL</b></th>
                 <th align="center"><b>TRANSAKSI</b></th>
                 <th align="center"><b>NO TRANSAKSI</b></th>
                 <th align="center"><b>NO Bank</b></th>
                 <th align="center"><b>KETERANGAN</b></th>
                 <th align="center"><b>LPB</b></th>
                 <th align="center"><b>REF.</b></th>
                 <th align="center"><b>DEBIT</b></th>
                 <th align="center"><b>KREDIT</b></th>
                 <th align="center"><b>SALDO</b></th>
                 <th align="center"><b>KURS</b></th>
                 <th align="center"><b>DEBIT {{ get_local_currency()->kode }}</b></th>
                 <th align="center"><b>KREDIT {{ get_local_currency()->kode }}</b></th>
                 <th align="center"><b>SALDO {{ get_local_currency()->kode }}</b></th>
             </tr>
             @php
                 $balance = $d->beginning_balance;
                 $balance_exchanged = $d->beginning_balance_exchanged;

                 $total_balance_formula .= "N$row_start";
                 if ($loop->iteration < count($data)) {
                     $total_balance_formula .= '+';
                 }
             @endphp
             <tr>
                 <td align="center">SALDO</td>
                 <td align="center"></td>
                 <td align="center"></td>
                 <td align="center"></td>
                 <td align="center"></td>
                 <td align="center"></td>
                 <td align="center"></td>
                 <td align="right"></td>
                 <td align="right"></td>
                 <td align="right">{{ $balance }}</td>
                 <td align="right"></td>
                 <td align="right"></td>
                 <td align="right"></td>
                 <td align="right">{{ $balance_exchanged }}</td>
             </tr>
             @forelse ($d->current_data as $key => $current)
                 @php
                     $balance -= $current->debit;
                     $balance += $current->credit;
                     $balance_exchanged -= $current->debit_exchanged;
                     $balance_exchanged += $current->credit_exchanged;
                 @endphp
                 @php
                     $data_start++;
                 @endphp
                 <tr>
                     {{-- !! A --}}
                     <td align="center">{{ localDate($current->date) }}</td>
                     {{-- !! B --}}
                     <td align="center">{{ $current->transaction }}</td>
                     {{-- !! C --}}
                     <td>
                         <a href="{{ $current->link }}" target="_blank">
                             {{ $current->transaction_code }}
                         </a>
                     </td>
                     {{-- !! D --}}
                     <td align="center">
                         {{ $current->bank_code ?? '' }}
                     </td>
                     {{-- !! E --}}
                     <td>{{ $current->note }}</td>
                     {{-- !! F --}}
                     <td>{!! $current->lpb_number !!}</td>
                     {{-- !! G --}}
                     <td>{!! $current->po_number !!}</td>
                     {{-- !! H --}}
                     <td align="right">{{ $current->debit }}</td>
                     {{-- !! I --}}
                     <td align="right">{{ $current->credit }}</td>
                     {{-- !! J --}}
                     <td align="right">{{ $balance }}</td>
                     {{-- !! K --}}
                     <td align="right">{{ $current->exchange_rate }}</td>
                     {{-- !! L --}}
                     <td align="right">=H{{ $data_start }}*K{{ $data_start }}</td>
                     {{-- !! M --}}
                     <td align="right">=I{{ $data_start }}*K{{ $data_start }}</td>
                     {{-- !! N --}}
                     <td align="right">=N{{ $data_start - 1 }}+M{{ $data_start }}-L{{ $data_start }}</td>
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
             <tr>
                 <th align="center"></th>
                 <th align="center"></th>
                 <th align="center"></th>
                 <th align="center"></th>
                 <th align="center"></th>
                 <th align="center"></th>
                 <th align="right"></th>
                 <th align="right"></th>
                 <th align="right"></th>
                 <th align="right"></th>
                 <th align="right"><b>TOTAL</b></th>
                 <th align="right"><b>=SUM(L{{ $row_start }}:L{{ $row_start + count($d->current_data) }})</b></th>
                 <th align="right"><b>=SUM(M{{ $row_start }}:M{{ $row_start + count($d->current_data) }})</b></th>
                 <th align="right"></th>
             </tr>
             @php
                 $total_debit_formula .= 'L' . $row_start + count($d->current_data) + 1;
                 $total_credit_formula .= 'M' . $row_start + count($d->current_data) + 1;

                 if ($loop->iteration < count($data)) {
                     $total_debit_formula .= '+';
                     $total_credit_formula .= '+';
                 }

                 $row_start++;
                 $row_start += count($d->current_data);
             @endphp
         @empty
             @php
                 $row_start++;
             @endphp
             <tr>
                 <th align="center" colspan="14">Tidak ada data</th>
             </tr>
         @endforelse
         @php
             $row_start += 2;
             $total_balance_formula .= '-L' . $row_start;
             $total_balance_formula .= '+M' . $row_start;
         @endphp
         <tr>
             <th align="center"></th>
             <th align="center"></th>
             <th align="center"></th>
             <th align="center"></th>
             <th align="center"></th>
             <th align="center"></th>
             <th align="center"></th>
             <th align="right"></th>
             <th align="right"></th>
             <th align="right"></th>
             <th align="right"></th>
             <th align="right"></th>
             <th align="right"></th>
             <th align="right"></th>
         </tr>
         <tr>
             <th align="center"></th>
             <th align="center"></th>
             <th align="center"></th>
             <th align="center"></th>
             <th align="center"></th>
             <th align="center"></th>
             <th align="right"></th>
             <th align="right"></th>
             <th align="right"></th>
             <th align="right"></th>
             <th align="right"><b>TOTAL</b></th>
             <th align="right"><b>={!! $total_debit_formula !!}</b></th>
             <th align="right"><b>={!! $total_credit_formula !!}</b></th>
             <th align="right"><b>={!! $total_balance_formula !!}</b></th>
         </tr>
     </tbody>
 </table>
