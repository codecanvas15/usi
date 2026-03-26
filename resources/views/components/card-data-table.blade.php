@if ($breadcrumbs && $breadcrumbs != '')
    <div class="box">
        <div class="box-body">
            {{ $breadcrumbs }}
        </div>
    </div>
@endif

<div class="box" id="{{ $id }}">

    @if ($title && $title != '')
        <div class="box-header">
            <h3 class="box-title">{{ Str::headline($title) }}</h3>
        </div>
    @endif

    <div class="box-body">
        @if ($header_content && $header_content != '')
            {{ $header_content }}
        @endif
        @if ($table_content && $table_content != '')
            {{ $table_content }}
        @endif
    </div>
    @if ($footer && $footer != '')
        <div class="box-footer">
            {{ $footer }}
        </div>
    @endif
</div>
