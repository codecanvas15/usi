<script src="{{ asset('js/admin/receivables-payment/datatable.js?v=1.1') }}"></script>
<script>
    var csrf = $('input[name="_token"]').val();

    $(document).ready(function() {
        initSelect2Search('customer_id-receivable-payment', `{{ route('admin.select.customer') }}`, {
            id: "id",
            text: "nama"
        });

        initSelect2Search('branch-select-receivable-payment', '{{ route('admin.select.branch') }}', {
            'id': 'id',
            'text': 'name'
        });
    });
</script>
