const init_lease_document = () => {
    const lease_legality_table = $("#lease_legality_table").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        destroy: true,
        order: [[1, "desc"]],
        ajax: {
            url: `${base_url}/lease-document`,
            type: "get",
            data: {
                _token: token,
            },
        },
        columns: [
            {
                data: "DT_RowIndex",
                name: "DT_RowIndex",
                orderable: false,
                searchable: false,
            },
            {
                data: "lease_name",
                name: "leases.lease_name",
            },
            {
                data: "name",
                name: "lease_documents.name",
            },
            {
                data: "effective_date",
                name: "lease_documents.effective_date",
            },
            {
                data: "end_date",
                name: "lease_documents.end_date",
            },
            {
                data: "status",
                name: "lease_documents.id",
            },
            {
                data: "export",
                name: "export",
                orderable: false,
                searchable: false,
            },
        ],
    });

    $("#lease_list_table").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        destroy: true,
        order: [[1, "desc"]],
        ajax: {
            url: `${base_url}/lease-document/lease`,
            type: "post",
            data: {
                _token: token,
            },
        },
        columns: [
            {
                data: "DT_RowIndex",
                name: "DT_RowIndex",
                orderable: false,
                searchable: false,
            },
            {
                data: "lease_name",
                name: "leases.lease_name",
            },
            {
                data: "export",
                name: "export",
                orderable: false,
                searchable: false,
            },
        ],
    });

    $("table").css("width", "100%");
}

const show_create_modal_lease = (lease_id) => {
    $("#lease-document-form-modal").modal("show");
    $('#lease-document-form')[0].reset();
    $('#lease-document-form').attr('action', `${base_url}/lease-document`);
    $('#lease-document-form').find('input[name="_method"]').val('POST');
    $('#lease-document-form').find('input[name="lease_id"]').val(lease_id);
    $('#lease-document-form').find('input[name="file"]').attr('required', true);
}

$('#lease-document-form').submit(function (e) {
    e.preventDefault();
    Swal.showLoading();
    $('.validation-error-message').text('').addClass('d-none');

    var formData = new FormData(this)
    $.ajax({
        url: $(this).attr('action'),
        method: "POST",
        headers: {
            'X-CSRF-TOKEN': token,
        },
        data: formData,
        processData: false,
        contentType: false,
        enctype: 'multipart/form-data',
        success: function (res) {
            Swal.close();
            showAlert('', res.message, 'success')

            $('#lease-document-form-modal').modal('toggle');
            $('#lease-document-form')[0].reset();
            $('#lease_legality_table').DataTable().ajax.reload(null, false);

            $('button[type="submit"]').attr('disabled', false);
        },
        error: function (res) {
            Swal.close();
            $('button[type="submit"]').attr('disabled', false);

            if (res.status === 500) {
                showAlert('', res.responseJSON.message, 'error');
            }

            if (res.status === 422) {
                var errors = res.responseJSON;
                $.each(res.responseJSON.errors, function (key, value) {
                    $("#lease_" + key + "_error").html(value[0]);
                    $("#lease_" + key + "_error").removeClass('d-none');
                });
            }
        }
    });
});

const show_lease_edit_modal = (id) => {
    $.ajax({
        url: `${base_url}/lease-document/${id}/edit`,
        method: "get",
        headers: {
            'X-CSRF-TOKEN': token,
        },
        success: function (res) {
            $('#lease-document-form').find('input[name="lease_id"]').val(res.result.lease_id);
            $('#lease-document-form').find('input[name="name"]').val(res.result.name);
            $('#lease-document-form').find('input[name="transaction_date"]').val(res.result.transaction_date);
            $('#lease-document-form').find('input[name="effective_date"]').val(res.result.effective_date);
            $('#lease-document-form').find('input[name="end_date"]').val(res.result.end_date);
            $('#lease-document-form').find('input[name="due_date"]').val(res.result.due_date);
            $('#lease-document-form').find('input[name="file"]').attr('required', false);
            $('#lease-document-form').find('textarea[name="description"]').val(res.result.description);
        },
        error: function (res) {
            if (res.status === 500) {
                showAlert('', res.responseJSON.message, 'error');
            }
        }
    });

    // reset form
    $("#lease-document-form-modal").modal("show");
    $('#lease-document-form').attr('action', `${base_url}/lease-document/${id}`);
    $('#lease-document-form').find('input[name="_method"]').val('PUT');
}
