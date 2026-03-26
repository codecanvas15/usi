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
         <td>
             {{-- <center><img src="{{ storage_path('/app/public/' . getCompany()->logo) }}" width="120px"></center> --}}
         </td>
     </tr>
     <tr>
         <td></td>
     </tr>
     <tr>
         <td colspan="12" align="center">
             <p><b>LAPORAN {{ Str::upper(Str::headline($type)) }}</b></p>
         </td>
     </tr>
     <tr>
         <td colspan="12" align="center">
             <p><b>TANGGAL : {{ localDate($from_date) }}/{{ localDate($to_date) }}</b></p>
         </td>
     </tr>

     <tr>
         <td colspan="12" align="center">
             <p><b>KAS/BANK : @if ($coa)
                         {{ $coa->account_code }} - {{ $coa->name }}
                     @endif
                 </b></p>
         </td>
     </tr>
 </table>
 <table class="table table-bordered table-striped">
     @php
         $row_position = 6;
         $row_start = 6;
     @endphp
     <tbody>
         @foreach ($data as $d)
             <tr>
                 <th style="border: 1px solid #000" align="left" colspan="15"><b>{{ $d->name }}</b></th>
             </tr>
             <tr>
                 <th style="border: 1px solid #000" align="center" rowspan="2"><b>TANGGAL</b></th>
                 <th style="border: 1px solid #000" align="center" rowspan="2"><b>NO BUKTI</b></th>
                 <th style="border: 1px solid #000" align="center" rowspan="2"><b>NO DOKUMEN</b></th>
                 <th style="border: 1px solid #000" align="center" rowspan="2"><b>NO CHECK/GIRO</b></th>
                 <th style="border: 1px solid #000" align="center" rowspan="2"><b>NAMA</b></th>
                 <th style="border: 1px solid #000" align="center" rowspan="2"><b>URAIAN</b></th>
                 <th style="border: 1px solid #000" align="center" rowspan="2"><b>NO DETAIL/KODE ACCT</b></th>
                 <th style="border: 1px solid #000" align="center" colspan="3"><b>MUTASI</b></th>
                 <th style="border: 1px solid #000" align="center" rowspan="2"><b>SALDO AKHIR</b></th>
                 <th style="border: 1px solid #000" align="center" rowspan="2"><b>KURS</b></th>
                 <th style="border: 1px solid #000" align="center" colspan="2"><b>MUTASI ({{ get_local_currency()->simbol }})</b></th>
                 <th style="border: 1px solid #000" align="center" rowspan="2"><b>SALDO AKHIR</b></th>
             </tr>
             <tr>
                 <th style="border: 1px solid #000" align="center"><b>CURRENCY</b></th>
                 <th style="border: 1px solid #000" align="center"><b>PENERIMAAN</b></th>
                 <th style="border: 1px solid #000" align="center"><b>PENGELUARAN</b></th>
                 <th style="border: 1px solid #000" align="center"><b>PENERIMAAN</b></th>
                 <th style="border: 1px solid #000" align="center"><b>PENGELUARAN</b></th>
             </tr>
             @php
                 $row_position += 4;
                 $row_start += 4;
             @endphp
             <tr>
                 <td style="border: 1px solid #000">SALDO AWAL</td>
                 <td style="border: 1px solid #000"></td>
                 <td style="border: 1px solid #000"></td>
                 <td style="border: 1px solid #000"></td>
                 <td style="border: 1px solid #000"></td>
                 <td style="border: 1px solid #000"></td>
                 <td style="border: 1px solid #000"></td>
                 <td style="border: 1px solid #000"></td>
                 <td style="border: 1px solid #000"></td>
                 <td style="border: 1px solid #000"></td>
                 <td style="border: 1px solid #000" align="right">{{ $d->foreign_beginning_balance }}</td>
                 <td style="border: 1px solid #000"></td>
                 <td style="border: 1px solid #000"></td>
                 <td style="border: 1px solid #000"></td>
                 <td style="border: 1px solid #000" align="right">{{ $d->beginning_balance }}</td>
             </tr>
             @forelse ($d->transactions as $key => $transaction)
                 <tr>
                     <td style="border: 1px solid #000" align="center">{{ localDate($transaction->date) }}</td>
                     <td style="border: 1px solid #000" align="center">{{ $transaction->bank_code_mutation }}</td>
                     <td style="border: 1px solid #000" align="left">{{ $transaction->document_reference->code ?? '' }}</td>
                     <td style="border: 1px solid #000" align="center">{{ $transaction->giro_in ?? $transaction->giro_out }}</td>
                     <td style="border: 1px solid #000" align="left">{{ $transaction->vendor_customer->nama ?? '' }}</td>
                     <td style="border: 1px solid #000">{{ $transaction->remark }}</td>
                     <td style="border: 1px solid #000">{{ $transaction->opponent_account_code ?? '' }} {{ $transaction->opponent_name ?? '' }}</td>
                     <td style="border: 1px solid #000" align="center">{{ $transaction->simbol }}</td>
                     <td style="border: 1px solid #000" align="right">{{ $transaction->debit }}</td>
                     <td style="border: 1px solid #000" align="right">{{ $transaction->credit }}</td>
                     <td style="border: 1px solid #000" align="right">
                         =K{{ $row_position }}+I{{ $row_position + 1 }}-J{{ $row_position + 1 }}
                     </td>
                     <td style="border: 1px solid #000" align="right">{{ $transaction->exchange_rate }}</td>
                     <td style="border: 1px solid #000" align="right">{{ $transaction->debit_exchanged }}</td>
                     <td style="border: 1px solid #000" align="right">{{ $transaction->credit_exchanged }}</td>
                     <td style="border: 1px solid #000" align="right">
                         =O{{ $row_position }}+M{{ $row_position + 1 }}-N{{ $row_position + 1 }}
                     </td>
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
                     <td align="center" colspan="14" style="border: 1px solid #000">
                         Tidak ada data
                     </td>
                 </tr>
             @endforelse
             <tr>
                 <th style="border: 1px solid #000" colspan="7" align="right"><b>TOTAL</b></th>
                 <th style="border: 1px solid #000" align="right"></th>
                 <th style="border: 1px solid #000" align="right">
                     <b>=SUM(I{{ $row_start }}:I{{ $row_start + count($d->transactions) }})</b>
                 </th>
                 <th style="border: 1px solid #000" align="right">
                     <b>=SUM(J{{ $row_start }}:J{{ $row_start + count($d->transactions) }})</b>
                 </th>
                 <th style="border: 1px solid #000" align="right">
                     <b>=K{{ $row_start }}+SUM(I{{ $row_start }}:I{{ $row_start + count($d->transactions) }})-SUM(J{{ $row_start }}:J{{ $row_start + count($d->transactions) }})</b>
                 </th>
                 <th style="border: 1px solid #000" align="right"></th>
                 <th style="border: 1px solid #000" align="right">
                     <b>=SUM(M{{ $row_start }}:M{{ $row_start + count($d->transactions) }})</b>
                 </th>
                 <th style="border: 1px solid #000" align="right">
                     <b>=SUM(N{{ $row_start }}:N{{ $row_start + count($d->transactions) }})</b>
                 </th>
                 <th style="border: 1px solid #000" align="right">
                     <b>=O{{ $row_start }}+SUM(M{{ $row_start }}:M{{ $row_start + count($d->transactions) }})-SUM(N{{ $row_start }}:N{{ $row_start + count($d->transactions) }})</b>
                 </th>
             </tr>
             <tr>
                 <td></td>
             </tr>
             <tr>
                 <td></td>
             </tr>
             @php
                 if (count($d->transactions) > 0) {
                     $row_start += count($d->transactions);
                 }
                 $row_start += 3;
                 $row_position += 3;
             @endphp
         @endforeach
     </tbody>
 </table>
