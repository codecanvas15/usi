let add_authorization_list = $('#add-authorization-list');
let authorization_list = $('#authorization-list');
let need_to_check_amount = $('#need_to_check_amount');

let key = 0;
add_authorization_list.on('click', function () {
    let need_to_check_amount_value = need_to_check_amount.is(':checked') ? 1 : 0;
    let html = `<tr id="row_${key}">
                    <td>
                        <input type="hidden" name="model_authorization_id[${key}]" id="model_authorization_id_${key}" value="">
                        <input type="number" class="form-control" name="level[${key}]" id="level_${key}" required value="">
                    </td>
                    <td>
                        <select name="branch_id[${key}][]" label="branch" id="branch_id_${key}" class="form-control form-select branch_id" multiple>

                        </select>
                    </td>
                    <td>
                        <select name="division_id[${key}][]" label="divisi" id="division_id_${key}" class="form-control form-select division_id" multiple></select>
                    </td>
                    <td>
                        <select name="user_id[${key}]" label="pegawai" id="user_id_${key}" required class="form-control form-select"></select>
                    </td>
                    <td>
                        <input type="text" class="form-control commas-form text-end" name="minimum_value[${key}]" id="minimum_value_${key}" required value="0" ${!need_to_check_amount_value ? 'readonly' : ''}>
                    </td>
                    <td>
                        <input type="text" class="form-control" name="role[${key}]" id="role_${key}" required>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-danger btn-sm" type="button" onclick="$('#row_${key}').remove()">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>`;

    authorization_list.append(html);
    initCommasForm();
    initSelect2Search(`user_id_${key}`, `${base_url}/select/user`, {
        id: "id",
        text: "name,email,position_name"
    }, 0, []);

    initSelect2Search(`division_id_${key}`, `${base_url}/select/division`, {
        id: "id",
        text: "name"
    }, 0, []);

    initSelect2Search(`branch_id_${key}`, `${base_url}/select/branch`, {
        id: "id",
        text: "name"
    }, 0, []);

    key += 1;
});

need_to_check_amount.on('change', function () {
    if ($(this).is(':checked')) {
        $('input[name="minimum_value[]"]').val(0).attr('readonly', false);
    } else {
        $('input[name="minimum_value[]"]').val(0).attr('readonly', true);
    }
});
