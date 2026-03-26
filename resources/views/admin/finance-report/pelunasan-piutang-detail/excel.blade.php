 @php
     $row_start = 5;
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
             <th align="center"><b>NO.</b></th>
             <th align="center"><b>TANGGAL</b></th>
             <th align="center"><b>NO VOUCHER</b></th>
             <th align="center"><b>CUSTOMER</b></th>
             <th align="center"><b>KAS/BANK</b></th>
             <th align="center"><b>KETERANGAN</b></th>
             <th align="center"><b>CURRENCY</b></th>
             <th align="center"><b>TOTAL</b></th>
             <th align="center"><b>RATE</b></th>
             <th align="center"><b>TOTAL ({{ get_local_currency()->kode }})</b></th>
         </tr>
     </thead>
     <tbody>
         @forelse ($data as $key => $d)
             @if ($d->code != ($data[$key - 1]->code ?? '') || $key == 0)
                 @php
                     $row_start++;
                 @endphp
                 <tr>
                     <td style="background-color: #dedede" align="center">{{ $key + 1 }}.</td>
                     <td style="background-color: #dedede" align="center">{{ localDate($d->date) }}</td>
                     <td style="background-color: #dedede" align="left">
                        @if ($d->receivables_payment_id)
                            <a href="{{ route('admin.receivables-payment.show', $d->receivables_payment_id) }}" target="_blank">
                                {{ $d->bank_code_mutation ?? $d->code }}
                            </a>
                        @else
                            {{ $invoice->bank_code }}
                        @endif
                     </td>
                     <td style="background-color: #dedede" align="left">{{ $d->nama }}</td>
                     <td style="background-color: #dedede" align="">{{ $d->coa_account_code }} - {{ $d->coa_name }}</td>
                     <td style="background-color: #dedede" align="left"></td>
                     <td style="background-color: #dedede" align="left"></td>
                     <td style="background-color: #dedede" align="right"></td>
                     <td style="background-color: #dedede" align="right"></td>
                     <td style="background-color: #dedede" align="right"></td>
                 </tr>
             @endif
             <tr>
                 <td align="left" colspan="5">
                    @php
                        $modelClass = class_basename($d->model_reference);
                        $modelMap = [
                            'InvoiceGeneral' => 'admin.invoice-general.show',
                            'InvoiceDownPayment' => 'admin.invoice-down-payment.show',
                            'InvoiceTrading' => 'admin.invoice-trading.show',
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
                 <td align="left">{{ $d->note }}</td>
                 <td align="right">{{ $d->currency_simbol }}</td>
                 <td align="right">{{ $d->receive_amount }}</td>
                 <td align="right">{{ $d->exchange_rate }}</td>
                 <td align="right">{{ $d->receive_amount_local }}</td>
             </tr>
         @empty
             @php
                 $row_start++;
             @endphp
             <tr>
                 <td align="center" colspan="10">
                     Tidak ada data
                 </td>
             </tr>
         @endforelse
     </tbody>
     <tfoot>
         <tr>
             <th colspan="2"><b>TOTAL</b></th>
             <th align="right"></th>
             <th align="right"></th>
             <th align="right"></th>
             <th align="right"></th>
             <th align="right"></th>
             <th align="right"></th>
             <th align="right"></th>
             <th align="right"><b>=SUM(J{{ $row_start }}:J{{ $row_start + 1 + count($data) }})</b></th>
         </tr>
     </tfoot>
 </table>
