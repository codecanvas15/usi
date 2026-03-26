const init_asset_document = () => {
    const asset_legality_table = $("#asset_legality_table").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        destroy: true,
        order: [[1, "desc"]],
        ajax: {
            url: `${base_url}/asset-document`,
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
                data: "asset_name",
                name: "assets.asset_name",
            },
            {
                data: "name",
                name: "asset_documents.name",
            },
            {
                data: "effective_date",
                name: "asset_documents.effective_date",
            },
            {
                data: "end_date",
                name: "asset_documents.end_date",
            },
            {
                data: "status",
                name: "asset_documents.id",
            },
            {
                data: "export",
                name: "export",
                orderable: false,
                searchable: false,
            },
        ],
    });

    $("#asset_list_table").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        destroy: true,
        order: [[1, "desc"]],
        ajax: {
            url: `${base_url}/asset-document/asset`,
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
                data: "asset_name",
                name: "assets.asset_name",
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

const show_create_modal_asset = (asset_id) => {
    $("#asset-document-form-modal").modal("show");
    $('#asset-document-form')[0].reset();
    $('#asset-document-form').attr('action', `${base_url}/asset-document`);
    $('#asset-document-form').find('input[name="asset_id"]').val(asset_id);
    $('#asset-document-form').find('input[name="_method"]').val('POST');
    $('#asset-document-form').find('input[name="file"]').attr('required', true);
}

$('#asset-document-form').submit(function (e) {
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

            $('#asset-document-form-modal').modal('toggle');
            $('#asset-document-form')[0].reset();
            $('#asset_legality_table').DataTable().ajax.reload(null, false);

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
                    $("#asset_" + key + "_error").html(value[0]);
                    $("#asset_" + key + "_error").removeClass('d-none');
                });
            }
        }
    });
});

const show_asset_edit_modal = (id) => {
    $.ajax({
        url: `${base_url}/asset-document/${id}/edit`,
        method: "get",
        headers: {
            'X-CSRF-TOKEN': token,
        },
        success: function (res) {
            $('#asset-document-form').find('input[name="asset_id"]').val(res.result.asset_id);
            $('#asset-document-form').find('input[name="name"]').val(res.result.name);
            $('#asset-document-form').find('input[name="transaction_date"]').val(res.result.transaction_date);
            $('#asset-document-form').find('input[name="effective_date"]').val(res.result.effective_date);
            $('#asset-document-form').find('input[name="end_date"]').val(res.result.end_date);
            $('#asset-document-form').find('input[name="due_date"]').val(res.result.due_date);
            $('#asset-document-form').find('input[name="file"]').attr('required', false);
            $('#asset-document-form').find('textarea[name="description"]').val(res.result.description);
        },
        error: function (res) {
            if (res.status === 500) {
                showAlert('', res.responseJSON.message, 'error');
            }
        }
    });

    // reset form
    $("#asset-document-form-modal").modal("show");
    $('#asset-document-form').attr('action', `${base_url}/asset-document/${id}`);
    $('#asset-document-form').find('input[name="_method"]').val('PUT');
}
