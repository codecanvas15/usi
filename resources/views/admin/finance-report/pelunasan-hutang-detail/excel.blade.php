 @php
     $row_start = 6;
     $border = 'border:1px solid #000;';
 @endphp
 <table>
     <tr>
         <td colspan="8">
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
         <td colspan="9" align="left">
             <p><b>LAPORAN {{ Str::upper(Str::headline($type)) }}</b></p>
         </td>
     </tr>
     <tr>
         <td colspan="9" align="left">
             <p><b>TANGGAL : {{ localDate($from_date) }}/{{ localDate($to_date) }}</b></p>
         </td>
     </tr>
     @if ($coa)
         @php
             $row_start++;
         @endphp
         <tr>
             <td colspan="9" align="left">
                 <p><b>KAS/BANK : {{ $coa->account_code }} - {{ $coa->name }}</b></p>
             </td>
         </tr>
     @endif
 </table>
 <table>
     <thead>
         <tr>
             <th style="{{ $border }}" align="center"><b>NO.</b></th>
             <th style="{{ $border }}" align="center"><b>TANGGAL</b></th>
             <th style="{{ $border }}" align="center"><b>NO VOUCHER</b></th>
             <th style="{{ $border }}" align="center"><b>VENDOR</b></th>
             <th style="{{ $border }}" align="center"><b>KAS/BANK</b></th>
             <th style="{{ $border }}" align="center"><b>KETERANGAN</b></th>
             <th style="{{ $border }}" align="center"><b>CURRENCY</b></th>
             <th style="{{ $border }}" align="center"><b>TOTAL</b></th>
             <th style="{{ $border }}" align="center"><b>RATE</b></th>
             <th style="{{ $border }}" align="center"><b>TOTAL ({{ get_local_currency()->kode }})</b></th>
         </tr>
     </thead>
     <tbody>
         @php
             $no = 1;
         @endphp
         @forelse ($data as $key => $d)
             @if ($d->code != ($data[$key - 1]->code ?? '') || $key == 0)
                 @php
                     $row_start++;
                 @endphp
                 <tr>
                     <td style="{{ $border }};background-color: #dedede" align="center">{{ $no }}.</td>
                     <td style="{{ $border }};background-color: #dedede" align="center">{{ localDate($d->date) }}</td>
                     <td style="{{ $border }};background-color: #dedede" align="center">
                         @php
                             $modelClass = class_basename($d->bank_code_mutation_ref_model); // e.g. InvoiceGeneral
                             $routeMap = [
                                 'OutgoingPayment' => 'admin.outgoing-payment.show',
                                 'AccountPayable' => 'admin.account-payable.show',
                             ];
                         @endphp
                         @if (isset($routeMap[$modelClass]))
                             <a href="{{ route($routeMap[$modelClass], $d->bank_code_mutation_ref_id) }}" target="_blank">
                                 {{ $d->bank_code_mutation ?? $d->code }}
                             </a>
                         @else
                             {{ $d->bank_code_mutation ?? $d->code }}
                         @endif
                     </td>
                     <td style="{{ $border }};background-color: #dedede" align="center">{{ $d->nama }}</td>
                     <td style="{{ $border }};background-color: #dedede">{{ $d->coa_account_code }} - {{ $d->coa_name }}</td>
                     <td style="{{ $border }};background-color: #dedede"></td>
                     <td style="{{ $border }};background-color: #dedede"></td>
                     <td style="{{ $border }};background-color: #dedede"></td>
                     <td style="{{ $border }};background-color: #dedede"></td>
                     <td style="{{ $border }};background-color: #dedede"></td>
                 </tr>
                 @php
                     $no++;
                 @endphp
             @endif
             <tr>
                 <td style="{{ $border }}" align="left" colspan="5">
                     @php
                         $modelClass = class_basename($d->model_reference);
                         $modelMap = [
                             'SupplierInvoice' => 'admin.supplier-invoice.show',
                             'SupplierInvoiceGeneral' => 'admin.supplier-invoice-general.show',
                         ];

                         $routeName = $modelMap[$modelClass] ?? null;
                     @endphp

                     @if ($routeName && Route::has($routeName))
                         <a href="{{ route($routeName, $d->reference_id) }}" target="_blank">
                             {{ $d->invoice_code }}
                         </a>
                     @else
                         {{ $d->invoice_code }}
                     @endif
                 </td>
                 <td style="{{ $border }}" align="left">{{ $d->note }}</td>
                 <td style="{{ $border }}" align="center">{{ $d->currency_simbol }}</td>
                 <td style="{{ $border }}" align="right">{{ $d->amount }}</td>
                 <td style="{{ $border }}" align="right">{{ $d->exchange_rate }}</td>
                 <td style="{{ $border }}" align="right">{{ $d->amount_local }}</td>
             </tr>
             @php
                 $row_start++;
             @endphp
         @empty
             @php
                 $row_start++;
             @endphp
             <tr>
                 <td style="{{ $border }}" align="center" colspan="10">
                     Tidak ada data
                 </td>
             </tr>
         @endforelse
     </tbody>
     <tfoot>
         <tr>
             <th style="{{ $border }}" colspan="2"><b>TOTAL</b></th>
             <th style="{{ $border }}"></th>
             <th style="{{ $border }}"></th>
             <th style="{{ $border }}"></th>
             <th style="{{ $border }}"></th>
             <th style="{{ $border }}"></th>
             <th style="{{ $border }}"></th>
             <th style="{{ $border }}"></th>
             <th style="{{ $border }}" align="right"><b>=SUM(J{{ 6 + 1 }}:J{{ $row_start }})</b></th>
         </tr>
     </tfoot>
 </table>
