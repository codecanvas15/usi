<script src="{{ asset('js/admin/incoming-payment/datatable.js?v=1.1') }}"></script>
<script src="{{ asset('js/admin/select/coa.js') }}"></script>
<script>
    initSelect2Search('branch-select-incoming-payment', '{{ route('admin.select.branch') }}', {
        'id': 'id',
        'text': 'name'
    });
</script>
