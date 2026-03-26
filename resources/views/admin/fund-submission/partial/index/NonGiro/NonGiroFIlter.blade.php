<div class="row justify-content-between mb-4">
    <div class="row mb-4">
        @if (get_current_branch()->is_primary)
            <div class="col-md-2">
                <div class="form-group">
                    <x-select id="branch-select" label="branch">

                    </x-select>
                </div>
            </div>
        @endif
        <div class="col-md-2">
            <div class="form-group">
                <x-select id="is_used" label="status pencairan">
                    <option value="">Semua</option>
                    <option value="0">Belum Cair</option>
                    <option value="1">Sudah Cair</option>
                </x-select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <x-select id="status" label="status">
                    <option value="">Semua</option>
                    @foreach (fund_submission_status() as $key => $item)
                        <option value="{{ $key }}">{{ $item['label'] }}</option>
                    @endforeach
                </x-select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <x-input class="datepicker-input" id="from_date" name="from_date" label="tanggal awal" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" required />
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <x-input class="datepicker-input" id="to_date" name="to" label="tanggal akhir" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
            </div>
        </div>
        <div class="col-md-3 row align-self-end">
            <div class="form-group">
                <x-button type="button" color="info" id="set-service-table" icon="search" fontawesome onclick="$('table#TableNonGiro').DataTable().ajax.reload()" />
                <x-button color="info" icon="download" fontawesome dataToggle="modal" dataTarget="#download-modal" />
                <x-modal title="download rekap pengajuan dana" id="download-modal" headerColor="success">
                    <x-slot name="modal_body">
                        <x-button type="button" color="info" label="excel" onclick="download_recap('excel')" />
                        <x-button type="button" color="info" label="PDF" onclick="download_recap('pdf')" />
                    </x-slot>
                </x-modal>
            </div>
        </div>
    </div>
</div>
