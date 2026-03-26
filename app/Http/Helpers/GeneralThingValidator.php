<?php

const except_status = [
    'approve',
    'reject',
    'done',
    'void',
];


/**
 * * check if user can access the page
 *
 * @param string $data_status
 * @return void
 */
function general_things_validator(string $data_status): void
{
    if (in_array($data_status, except_status)) {
        abort(403);
    }
}
