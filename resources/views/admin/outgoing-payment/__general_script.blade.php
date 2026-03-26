<script src="{{ asset('js/admin/outgoing-payment/datatable.js?v=1.1') }}"></script>
<script>
    initSelect2Search('branch-select', '{{ route('admin.select.branch') }}', {
        'id': 'id',
        'text': 'name'
    });
</script>
