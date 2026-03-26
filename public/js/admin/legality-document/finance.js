const init_finance_document = () => {
    const finance_legality_table = $("#finance_legality_table").DataTable({
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
                type: "finance",
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
