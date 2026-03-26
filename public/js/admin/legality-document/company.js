const init_company_document = () => {
    const company_legality_table = $("#company_legality_table").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        destroy: true,
        order: [[1, "desc"]],
        ajax: {
            url: `${base_url}/legality-document`,
            type: "get",
            data: {
                _token: token,
                type: "company",
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
                data: "name",
                name: "legality_documents.name",
            },
            {
                data: "effective_date",
                name: "legality_documents.effective_date",
            },
            {
                data: "end_date",
                name: "legality_documents.end_date",
            },
            {
                data: "status",
                name: "legality_documents.id",
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

const show_create_modal = (type) => {
    $("#document-form-modal").modal("show");
    $('#document-form')[0].reset();
    $('#document-form').find('input[name="type"]').val(type);
    $('#document-form').attr('action', `${base_url}/legality-document`);
    $('#document-form').find('input[name="_method"]').val('POST');
    $('#document-form').find('input[name="file"]').attr('required', true);
}

$('#document-form').submit(function (e) {
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

            $('#document-form-modal').modal('toggle');
            $('#document-form')[0].reset();
            $('#company_legality_table').DataTable().ajax.reload(null, false);
            $('#finance_legality_table').DataTable().ajax.reload(null, false);

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
                    $("#" + key + "_error").html(value[0]);
                    $("#" + key + "_error").removeClass('d-none');
                });
            }
        }
    });
});

const show_edit_modal = (id) => {
    $.ajax({
        url: `${base_url}/legality-document/${id}/edit`,
        method: "get",
        headers: {
            'X-CSRF-TOKEN': token,
        },
        success: function (res) {
            $('#document-form').find('input[name="type"]').val(res.result.type);
            $('#document-form').find('input[name="name"]').val(res.result.name);
            $('#document-form').find('input[name="transaction_date"]').val(res.result.transaction_date);
            $('#document-form').find('input[name="effective_date"]').val(res.result.effective_date);
            $('#document-form').find('input[name="end_date"]').val(res.result.end_date);
            $('#document-form').find('input[name="due_date"]').val(res.result.due_date);
            $('#document-form').find('input[name="file"]').attr('required', false);
            $('#document-form').find('textarea[name="description"]').val(res.result.description);
        },
        error: function (res) {
            if (res.status === 500) {
                showAlert('', res.responseJSON.message, 'error');
            }
        }
    });

    // reset form
    $("#document-form-modal").modal("show");
    $('#document-form').attr('action', `${base_url}/legality-document/${id}`);
    $('#document-form').find('input[name="_method"]').val('PUT');
}

const show_delete_confirmation = (delete_url, datatable) => {
    Swal.fire({
        title: 'Apakah anda yakin?',
        text: 'Anda tidak akan dapat mengembalikan ini!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: delete_url,
                method: "POST",
                data: {
                    _method: 'DELETE',
                    _token: token,
                },
                success: function (res) {
                    showAlert('', res.message, 'success');

                    $(datatable).DataTable().ajax.reload();
                },
                error: function (res) {
                    showAlert('', res.responseJSON.message, 'error');
                }
            });
        }
    });
}
