 <table>
     <tr>
         <td colspan="5">
             <p><b>{{ getCompany()->name }}</b></p>
             <p><b>{{ getCompany()->address }}</b></p>
             <p><b>Telp. {{ getCompany()->phone }}</b></p>
         </td>
         <td>
             {{-- <center><img src="{{ storage_path('/app/public/' . getCompany()->logo) }}" width="120px"></center> --}}
         </td>
     </tr>
     <tr>
         <td></td>
     </tr>
     <tr>
         <td colspan="5" align="center">
             <p><b>LAPORAN {{ Str::upper(Str::headline($type)) }}</b></p>
         </td>
     </tr>
     <tr>
         <td colspan="5" align="center">
             <p><b>TANGGAL : {{ localDate($from_date) }}/{{ localDate($to_date) }}</b></p>
         </td>
     </tr>
 </table>
 <table>
     <thead>
         <tr>
             <th align="center"><b>TANGGAL</b></th>
             <th align="center"><b>TRANSAKSI</b></th>
             <th align="center"><b>NO DOKUMEN</b></th>
             <th align="center"><b>NO REFERENSI</b></th>
             <th align="center"><b>ITEM</b></th>
             <th align="center" colspan="2"><b>ACCOUNT</b></th>
             <th align="center"><b>MATA UANG</b></th>
             <th align="center"><b>DEBIT</b></th>
             <th align="center"><b>KREDIT</b></th>
             <th align="center"><b>KURS</b></th>
             <th align="center"><b>DEBIT ({{ get_local_currency()->kode }})</b></th>
             <th align="center"><b>KREDIT ({{ get_local_currency()->kode }})</b></th>
             <th align="center"><b>KETERANGAN</b></th>
         </tr>
     </thead>
     <tbody>
         @php
             $increment = 0;
         @endphp
         @forelse ($data as $key => $d)
             @if ($d->journal_id != ($data[$key - 1]->journal_id ?? '') || $key == 0)
                 @php
                     $increment++;
                 @endphp
             @endif
             <tr>
                 @if ($d->journal_id != ($data[$key - 1]->journal_id ?? '') || $key == 0)
                     <td style="{{ $increment == 0 || $increment % 2 == 0 ? 'background-color: #dadada' : '' }}" align="center"><b>{{ localDate($d->journal_date) }}</b></td>
                     <td style="{{ $increment == 0 || $increment % 2 == 0 ? 'background-color: #dadada' : '' }}" align="left"><b>{{ $d->journal_type }}</b></td>
                     <td style="{{ $increment == 0 || $increment % 2 == 0 ? 'background-color: #dadada' : '' }}" align="center">
                         @if ($d->document_reference)
                             @if ($d->document_reference->link ?? null)
                                 <a href="{{ toLocalLink($d->document_reference->link) }}" target="_blank">{{ $d->document_reference->code }}</a>
                             @endif
                         @endif
                     </td>
                     <td style="{{ $increment == 0 || $increment % 2 == 0 ? 'background-color: #dadada' : '' }}" align="center">
                         @if ($d->reference)
                             @if ($d->reference->link ?? null)
                                 <a href="{{ toLocalLink($d->reference->link) }}" target="_blank">{{ $d->reference->code }}</a>
                             @endif
                         @endif
                     </td>
                 @else
                     <td style="{{ $increment == 0 || $increment % 2 == 0 ? 'background-color: #dadada' : '' }}"></td>
                     <td style="{{ $increment == 0 || $increment % 2 == 0 ? 'background-color: #dadada' : '' }}"></td>
                     <td style="{{ $increment == 0 || $increment % 2 == 0 ? 'background-color: #dadada' : '' }}"></td>
                     <td style="{{ $increment == 0 || $increment % 2 == 0 ? 'background-color: #dadada' : '' }}"></td>
                 @endif
                 <td style="{{ $increment == 0 || $increment % 2 == 0 ? 'background-color: #dadada' : '' }}" align="left">{{ $d->remark }}</td>
                 <td style="{{ $increment == 0 || $increment % 2 == 0 ? 'background-color: #dadada' : '' }}" align="left">{{ $d->coa_code }}</td>
                 <td style="{{ $increment == 0 || $increment % 2 == 0 ? 'background-color: #dadada' : '' }}" align="left">{{ $d->coa_name }}</td>
                 <td style="{{ $increment == 0 || $increment % 2 == 0 ? 'background-color: #dadada' : '' }}" align="center">{{ $d->currency_code }}</td>
                 <td style="{{ $increment == 0 || $increment % 2 == 0 ? 'background-color: #dadada' : '' }}" align="right">{{ $d->debit }}</td>
                 <td style="{{ $increment == 0 || $increment % 2 == 0 ? 'background-color: #dadada' : '' }}" align="right">{{ $d->credit }}</td>
                 <td style="{{ $increment == 0 || $increment % 2 == 0 ? 'background-color: #dadada' : '' }}" align="right">{{ $d->journal_exchange_rate }}</td>
                 <td style="{{ $increment == 0 || $increment % 2 == 0 ? 'background-color: #dadada' : '' }}" align="right">{{ $d->debit_exchanged }}</td>
                 <td style="{{ $increment == 0 || $increment % 2 == 0 ? 'background-color: #dadada' : '' }}" align="right">{{ $d->credit_exchanged }}</td>

             </tr>
         @empty
             <tr>
                 <td align="center" colspan="13">
                     Tidak ada data
                 </td>
             </tr>
         @endforelse
     </tbody>
     <tfoot>
         <tr>
             <th align="center" colspan="11"><b>TOTAL</b></th>
             <th align="right"><b>=SUM(L1:L{{ count($data) + 6 }})</b></th>
             <th align="right"><b>=SUM(M1:M{{ count($data) + 6 }})</b></th>
             <th align="right"></th>
         </tr>
     </tfoot>
 </table>
