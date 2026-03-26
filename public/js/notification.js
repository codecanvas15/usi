let parsed_notification_count = 0;
$(document).ready(function () {
    get_notification_counter();
    get_autorization_status_pending()
    $('#notification_list_wrap').bind('scroll', check_scroll);
});

function get_notification_counter() {
    $.ajax({
        url: base_url + "/notification-counter",
        type: "get",
        data: {
            _token: token,
        },
        dataType: "JSON",
        success: function (response) {
            if (response.data != 0) {
                $('#notif-toggle').addClass('notempty');
                $('#notif-toggle').attr('counter', response.data);
            } else {
                $('#notif-toggle').removeClass('notempty');
                $('#notif-toggle').attr('counter', '');
            }
        },
        error: function (err) {
            console.log(err);
        },
    });
}

function clear_notification() {
    $.ajax({
        url: base_url + "/notification-clear",
        type: "get",
        data: {
            _token: token,
        },
        dataType: "JSON",
        success: function (response) {
            $('#notification_list_wrap').html('');
            parsed_notification_count = 0;
            get_notification();
        },
        error: function (err) {
            console.log(err);
        },
    });
}

function get_notification() {
    get_notification_counter();
    $.ajax({
        url: base_url + "/notification-data?offset=" + parsed_notification_count,
        type: "get",
        data: {
            _token: token,
        },
        dataType: "JSON",
        success: function (response) {
            parsed_notification_count += response.data.length;
            let html = '';
            if (parsed_notification_count == 0) {
                html = `<li id="empty-notification">
                            <a href="#">
                                <i class="fa fa-warning text-danger"></i> tidak ada notifikasi untuk ditampilkan
                            </a>
                        </li>`;

                $('#notification_list_wrap').html(html);
            } else {
                $('#empty-notification').remove();
                $.each(response.data, function (index, value) {
                    let bg = '';
                    if (value.read_at == null) {
                        bg = 'bg-light';
                    }
                    html += `<li class="${bg}">
                    <a href="${base_url}/notification/${value.id}">
                        <div class="row">
                            <div class="col-6 text-overflow-ellipsis">
                                <span>${value.data.title}</span>
                            </div>
                            <div class="col-6 text-end">
                                <small>${value.created_at}</small>
                            </div>
                            <div class="col-12 text-overflow-ellipsis">
                                <b>${value.data.text}</b>
                            </div>
                        </div>
                    </a>
                    </li>`;
                });

                $('#notification_list_wrap').append(html);
            }
        },
        error: function (err) {
            console.log(err);
        },
    });
}

function check_scroll(e) {
    var elem = $(e.currentTarget);
    if (elem[0].scrollHeight - elem.scrollTop() == elem.outerHeight()) {
        get_notification();
    }
}

function get_autorization_status_pending() {
    $.ajax({
        url: base_url + "/authorization/getCountTotalAuthorizationForSidebar",
        type: "get",
        data: {
            _token: token,
        },
        dataType: "JSON",
        success: function (response) {
            if (response.data?.total_count != 0) {
                $('#notif-sidebar').addClass('notempty');
                $('#notif-sidebar').attr('counter', response.data?.total_count);
                $('#authorization-alert').html(`<div class="col-md-12">
                <a href="${base_url}/authorization">
                <div class="alert alert-danger">${response.data?.total_count} Otorisasi menunggu persetujuan anda </div>
                </a>
            </div>`);
            } else {
                $('#notif-sidebar').removeClass('notempty');
                $('#notif-sidebar').attr('counter', '');
                $('#authorization-alert').html('');
            }
        },
        error: function (err) {
            console.log(err);
        },
    });
}
