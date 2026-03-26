
$('.revert-void-form').submit(function (e) {
    e.preventDefault();
    $.ajax({
        url: $(this).attr('action'),
        method: $(this).attr('method'),
        data: $(this).serialize(),
        success: function (response) {
            if (response) {
                if (response.status == 'revert' && response.authorize_revert) {
                    $('#revert-form').find('input[name="message"]').val(response.message);
                    $('#revert-form').find('input[name="note"]').val(response.note);
                    $('#revert-form').find('input[name="authorization_detail_id"]').val(response.authorization_detail_id);
                    $('#revert-form').submit();
                } else if (response.status == 'void' && response.authorize_void) {
                    $('#void-form').find('input[name="message"]').val(response.message);
                    $('#void-form').find('input[name="note"]').val(response.note);
                    $('#void-form').find('input[name="authorization_detail_id"]').val(response.authorization_detail_id);
                    $('#void-form').submit();
                } else {
                    window.location.reload();
                }
            }
        }
    })
});
