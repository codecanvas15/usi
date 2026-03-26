@php
    $className = 'bg-';
    
    $theadColor ? ($className .= $theadColor) : ($className .= 'dark');
@endphp

<div class="table-responsive">
    <table class="table {{ $isStriped }} {{ $class }}" id="{{ $id ?? '' }}">
        @isset($table_head)
            <thead class="{{ $className }}">
                <tr>
                    {{ $table_head }}
                </tr>
            </thead>
        @endisset

        @isset($table_body)
            <tbody>
                {{ $table_body }}
            </tbody>
        @endisset

        @isset($table_foot)
            <tfoot>
                {{ $table_foot }}
            </tfoot>
        @endisset
    </table>
</div>
