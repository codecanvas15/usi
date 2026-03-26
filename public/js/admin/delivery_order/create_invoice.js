// var button_create_invoice = $('#button-create-invoice');

// function checkSelectedDeliveryOrder() {
//     var delivery_order_id = $('input[name="select_delivery_order_id[]"]:checked').length;

//     if (delivery_order_id > 0) {
//         button_create_invoice.removeClass('d-none');
//     } else {
//         button_create_invoice.addClass('d-none');
//     }
// }

// button_create_invoice.click(function () {
//     var delivery_order_id = $('input[name="select_delivery_order_id[]"]');
//     var selected_delivery_order_id = [];

//     delivery_order_id.each(function (e) {
//         if ($(this).is(":checked")) {
//             selected_delivery_order_id.push($(this).val());
//         }
//     });

//     var url = `${base_url}/invoice-trading-generate?delivery_order_id=${selected_delivery_order_id}`;
//     window.open(url);
// });
