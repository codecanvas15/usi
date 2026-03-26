 @foreach ($data as $item)
     <table id='subcategory-{{ $item->id }}' class="table mb-10" data-subcategory-id="{{ $item->id }}">
         <thead>
             <tr class="bg-dark">
                 <td>
                     <h4>{{ Str::headline($item->name) }}</h4>
                 </td>
                 {{-- <td>
                     <h4>Urutan</h4>
                 </td> --}}
             </tr>
         </thead>
         <tbody class="connectedSortable" data-subcategory-id="{{ $item->id }}">
             @forelse ($item->profit_loss_details as $detail)
                 <tr class="subcategory-{{ $item->id }}" data-detail-id="{{ $detail->id }}">
                     <td>{{ $detail->coa?->account_code }} - {{ $detail->coa?->name }} ({{ $detail->coa?->account_type }})</td>
                     {{-- <td>
                         <input type="number" data-id="{{ $detail->id }}" data-subcategory-id="{{ $item->id }}" class="form-control" name="position[{{ $detail->id }}]" id="position-{{ $detail->id }}" value="{{ $detail->position }}" min="0" onblur="updateOrder($(this))">
                     </td> --}}
                 </tr>
             @empty
                 <tr class="text-center subcategory-{{ $item->id }} empty-row">
                     <td>
                         Tidak ada data
                     </td>
                 </tr>
             @endforelse
         </tbody>
     </table>
 @endforeach
