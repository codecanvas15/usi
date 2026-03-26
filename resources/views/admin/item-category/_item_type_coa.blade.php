@if (count($item_type_coas) != 0)
    @foreach ($item_type_coas as $item)
        @php
            $default = $default_item_type_coa->where('type', $item->type)->first() ?? $item;
        @endphp
        <div class="col-md-6">
            @if ($edit)
                <x-select name="coa_id[]" id="{{ Str::snake($item->type) }}" label="coa {{ $item->type }}" :helpers="$default?->coa?->account_code . ' - ' . $default?->coa?->name" required>
                    @if ($edit)
                        @if ($item->coa)
                            <option value="{{ $item->coa_id }}">{{ $item->coa->account_code . ' - ' . $item->coa->name }}</option>
                        @endif
                    @endif
                </x-select>
            @else
                <x-select name="coa_id[]" id="{{ Str::snake($item->type) }}" label="coa {{ $item->type }}" :helpers="$default?->coa?->account_code . ' - ' . $default?->coa?->name">
                    @if ($edit)
                        @if ($item->coa)
                            <option value="{{ $item->coa_id }}">{{ $item->coa->account_code . ' - ' . $item->coa->name }}</option>
                        @endif
                    @endif
                </x-select>
            @endif
            <input type="hidden" name="type[]" value="{{ $item->type }}">
        </div>
    @endforeach
@endif
