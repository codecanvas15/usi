@extends('layouts.admin.layout.index')

@php
    $main = 'coa';
@endphp

@section('title', Str::headline("Edit $main") . ' - ')

@section('css')
    <style>
        /* original idea http://www.bootply.com/phf8mnMtpe */
        .tree-coa {
            min-height: 20px;
            margin-bottom: 20px;
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
            -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05)
        }

        .tree-coa li {
            list-style-type: none;
            margin: 0;
            padding: 10px 5px 0 5px;
            position: relative
        }

        .tree-coa li::before,
        .tree-coa li::after {
            content: '';
            left: -20px;
            position: absolute;
            right: auto
        }

        .tree-coa li::before {
            border-left: 1px solid #999;
            bottom: 50px;
            height: 100%;
            top: 0;
            width: 1px
        }

        .tree-coa li::after {
            border-top: 1px solid #999;
            height: 20px;
            top: 30px;
            width: 25px
        }

        .tree-coa li span {
            -moz-border-radius: 5px;
            -webkit-border-radius: 5px;
            border: 1px solid #999;
            border-radius: 5px;
            display: block;
            padding: 3px 8px;
            text-decoration: none
        }

        .tree-coa li.parent_li>span {
            cursor: pointer
        }

        .tree-coa>ul>li::before,
        .tree-coa>ul>li::after {
            border: 0
        }

        .tree-coa li:last-child::before {
            height: 30px
        }

        .tree-coa li.parent_li>span:hover,
        .tree-coa li.parent_li>span:hover+ul li span {
            background: #eee;
            border: 1px solid #94a0b4;
            color: #000
        }
    </style>
@endsection

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($main) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Edit ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("edit $main")
        <div class="row">
            <div class="col-md-8">
                <x-card-data-table title="{{ 'edit ' . $main }}">
                    <x-slot name="header_content">
                        @if ($model->account_type == 'Cash & Bank' && !$model->bank_internal)
                            <div class="alert alert-danger">Belum Ada Bank Internal! <a href="{{ route('admin.bank-internal.create') }}?coa_id={{ $model->id }}">Lengkapi sekarang</a> </div>
                        @endif
                    </x-slot>
                    <x-slot name="table_content">
                        @include('components.validate-error')
                        @include("admin.$main.__fields")
                    </x-slot>

                </x-card-data-table>
            </div>
            <div class="col-md-4">
                <x-card-data-table title='{{ "$main Tree" }}'>
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">

                        <div style="max-height: 900px; overflow-y: scroll" id="coa-tree">
                            {!! $tree !!}
                        </div>

                    </x-slot>
                </x-card-data-table>
            </div>
        </div>
    @endcan
@endsection

@section('js')

    <script>
        $('.tree-coa li:has(ul)').addClass('parent_li').find(' > span').attr('title', 'Collapse this branch');

        $('.tree-coa li.parent_li > span').parent('li.parent_li').find(' > ul > li').hide('fast');

        $('.tree-coa li.parent_li > span').on('click', function(e) {
            var children = $(this).parent('li.parent_li').find(' > ul > li');

            if (children.is(":visible")) {
                children.hide('fast');
                $(this).attr('title', 'Expand this branch').find(' > i').addClass('fa-plus-square').removeClass('fa-minus-square');
            } else {
                children.show('fast');
                $(this).attr('title', 'Collapse this branch').find(' > i').addClass('fa-minus-square').removeClass('fa-plus-square');
            }
            e.stopPropagation();
        });
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-coa-sidebar');
        sidebarActive('#coa-sidebar');
    </script>
@endsection
