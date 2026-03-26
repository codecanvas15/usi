const get_data = () => {
    $.ajax({
        type: "post",
        url: `${base_url}/profit-loss-setting/get-data`,
        dataType: 'html',
        data: {
            _token: token,
        },
        success: function (data) {
            $('#table-data').html(data);

            setTimeout(() => {
                init_sortable();
            }, 5000);
        }
    });
}

const refresh = () => {
    $.ajax({
        type: "post",
        url: `${base_url}/profit-loss-setting/refresh`,
        data: {
            _token: token,
        },
        success: function (data) {
            get_data();
        }
    });
}

function init_sortable() {
    $("tbody.connectedSortable").on('click', 'tr', function () {
        $(this).toggleClass('selected');
    })
        .sortable({
            connectWith: ".connectedSortable",
            items: "tr",
            appendTo: "parent",
            helper: "clone",
            cursor: "move",
            zIndex: 999990,
            helper: function (e, item) {
                if (!item.hasClass('selected')) {
                    item.addClass('selected').siblings().removeClass('selected');
                }

                var elements = item.parent().children('.selected').clone();
                item.data('multidrag', elements).siblings('.selected').remove();

                var helper = $('<tr/>');
                return helper.append(elements);
            },
            stop: function (e, ui) {
                var elements = ui.item.data('multidrag');
                ui.item.after(elements).remove();
            },
            receive: function (event, ui) {
                let destination_id = $(event.target).data('subcategory-id');
                if ($(event.target).find('tr:not("empty-row")').length > 0) {
                    $(event.target).find('.empty-row').remove();
                } else {
                    $(event.target).append(`<tr class="text-center empty-row">
                    <td>Tidak ada data</td></tr>`);
                }

                var elements = ui.item.data('multidrag');
                if ($(ui.sender).find('tr:not("empty-row")').length > 0) {
                    $(ui.sender).find('.empty-row').remove();
                } else
                    $(ui.sender).append(`<tr class="text-center empty-row">
                            <td>Tidak ada data</td></tr>`);

                $.each(elements, function (i, e) {
                    let dragged_element = $(ui.item.data('multidrag')[i]).data('detail-id');
                    let index = ui.item.index();
                    $.ajax({
                        type: "post",
                        url: `${base_url}/profit-loss-setting/update-position`,
                        data: {
                            _token: token,
                            detail_id: dragged_element,
                            subcategory_id: destination_id,
                            position: index,
                        },
                        success: function (data) {
                            console.log(data);
                        }
                    });
                })
            }
        });
}


function updateOrder(e) {
    $.ajax({
        type: "post",
        url: `${base_url}/profit-loss-setting/update-order`,
        data: {
            _token: token,
            id: e.data('id'),
            position: e.val(),
        },
        success: function (data) {

        }
    });
}