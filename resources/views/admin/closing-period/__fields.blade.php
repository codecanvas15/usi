<form action="{{ !$model ? route("admin.$main.store") : route("admin.$main.update", $model) }}" method="post">
    @csrf
    @if ($model)
        @method('PUT')
    @endif
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <x-input type="text" label="sampai tanggal" name="to_date" required class="datepicker-input" required value="{{ $model ? localDate($model->to_date) : '' }}" />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status-closing" label="status" required autofocus class="form-select">
                    <option value="">----------------------</option>
                    <option value="open" {{ $model ? ($model->status == 'open' ? 'selected' : '') : '' }}>Open</option>
                    <option value="close" {{ $model ? ($model->status == 'close' ? 'selected' : '') : '' }}>Close</option>
                </select>
            </div>
        </div>
    </div>

    <div id="row-currencies">

    </div>

    <div class="box-footer">
        <div class="d-flex justify-content-end gap-3">
            <x-button type="reset" color="secondary" label="cancel" link="{{ route('admin.closing-period.index') }}" />
            @if ((isset($model) && ($model->approval_status ?? '' != 'pending')) || !isset($model))
                <x-button type="submit" color="primary" label="submit" />
            @endif
        </div>
    </div>
</form>

@push('script')

    <script src="{{ asset('js/helpers/helpers.js') }}"></script>

    @if (!$model)
        <script>
            $(document).ready(function() {

                let html = `@foreach ($currencies as $currency)
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input type="text" label="currency" name="currency" readonly required value="{{ $currency->nama }} - {{ $currency->negara }}" />
                                        <input type="hidden" name="currency_id[]" value="{{ $currency->id }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input type="text" name="exchange_rate[]" label="rate" class="commas-form" required value="{{ formatNumber($currency->default_rate ?? 0) }}" />
                                    </div>
                                </div>
                            </div>
                        @endforeach`;

                $('#status-closing').change(function(e) {
                    e.preventDefault();

                    if ($(this).val() === 'close') {
                        $('#row-currencies').html(html);
                        initCommasForm();
                        return;
                    }

                    $('#row-currencies').html('');
                });

                $('#status-closing').trigger('change');
            });
        </script>
    @else
        <script>
            $(document).ready(function() {

                let html = `
                    @foreach ($model->closingPeriodCurrencies as $currency)
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" label="currency" name="currency" readonly required value="{{ $currency->currency->nama }} - {{ $currency->currency->negara }}" />
                                    <input type="hidden" name="currency_id[]" value="{{ $currency->currency->id }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" name="exchange_rate[]" label="rate" class="commas-form" required value="{{ formatNumber($currency->exchange_rate) }}" />
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @foreach ($NotInCurrencies as $currency)
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" label="currency" name="currency" readonly required value="{{ $currency->nama }} - {{ $currency->negara }}" />
                                    <input type="hidden" name="currency_id[]" value="{{ $currency->id }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" name="exchange_rate[]" label="rate" class="commas-form" required />
                                </div>
                            </div>
                        </div>
                    @endforeach
                `;

                $('#status-closing').change(function(e) {
                    e.preventDefault();

                    if ($(this).val() === 'close') {
                        $('#row-currencies').html(html);
                        initCommasForm();
                        return;
                    }

                    $('#row-currencies').html('');
                });

                $('#status-closing').trigger('change');
            });
        </script>
    @endif
@endpush
