   <x-card-data-table title="lain - lain">
       <x-slot name="table_content">
           <div class="row">
               <div class="col-md-12 mt-4">
                   <div class="table-responsive">
                       <table class="table table-striped">
                           <thead class="bg-info">
                               <tr>
                                   <th>{{ Str::headline('akun') }}</th>
                                   <th>{{ Str::headline('keterangan') }}</th>
                                   <th class="text-end">{{ Str::headline('jumlah') }}</th>
                                   <th></th>
                               </tr>
                           </thead>
                           <tbody id="receivables-payment-other-data">
                               <input type="hidden" id="count_rows" value="0">

                           </tbody>
                           <tfoot>
                               <tr>
                                   <th colspan="2">TOTAL</th>
                                   <th class="text-end" id="credit_total">0</th>
                                   <input type="hidden" id="credit_total_hide" value="0">
                                   <th></th>
                               </tr>
                               <tr>
                                   <th class="text-end" colspan="3">
                                       <x-button color="success" label="Tambah Baris +" type="button" onclick="addReceivablesPaymentOtherRow()" class="btn-sm" />
                                   </th>
                                   <th></th>
                               </tr>
                           </tfoot>
                       </table>
                   </div>
               </div>
           </div>
           <div class="table-responsive">
               <table class="table table-striped">
                   <thead class="bg-info">
                       <tr>
                           <th></th>
                           <th></th>
                       </tr>
                   </thead>
                   <tbody id="receivables-payment-other-data">
                       <tr>
                           <td class="text-end">{{ Str::headline('total invoice') }}</td>
                           <td class="text-end" id="total-data-invoice"></td>
                       </tr>
                       <tr>
                           <td class="text-end">{{ Str::headline('total retur') }}</td>
                           <td class="text-end" id="total-data-return"></td>
                       </tr>
                       <tr>
                           <td class="text-end">{{ Str::headline('total purchase invoice') }}</td>
                           <td class="text-end" id="total-data-supplier-invoice"></td>
                       </tr>
                       <tr>
                           <td class="text-end">{{ Str::headline('total adjustment') }}</td>
                           <td class="text-end" id="total-data-adjustment"></td>
                       </tr>
                       <tr>
                           <td class="text-end">{{ Str::headline('total') }}</td>
                           <td class="text-end" id="total-data-total"></td>
                       </tr>
                   </tbody>
               </table>
           </div>
           <div class="row">
               <div class="col-md-12 text-end">
                   <a href="{{ route('admin.incoming-payment.index') }}?tab=receivable-payment-tab" class="btn btn-secondary">Cancel</a>
                   <x-button type="submit" color="primary" label="Save data" />
               </div>
           </div>
       </x-slot>
   </x-card-data-table>
