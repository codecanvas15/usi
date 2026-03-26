<?php

/**
 * get permmissions list
 *
 * @return array
 */
function get_permissions(): array
{
    $permissions = [];

    return $permissions;
}

/**
 * get permissions description
 *
 * @return array
 */
function get_permissions_description(): array
{
    $permissions = [
        'view' => 'View data, can see list data and delete data',
        'create' => 'Create data, can create new data',
        'edit' => 'Edit data, can edit existing  data',
        'delete' => 'Delete data, can delete existing data',
        'export' => 'Export data, can export data as excel file',
        'import' => 'Import data, can import data from excel file',
        'approve' => 'Approve data, can approve data',
        'reject' => 'Reject data, can reject data',
        'cancel' => 'Cancel data, can cancel data',
        'close' => 'Close data, can close or set to done data',
        'pairing' => 'Pairing data, can pairing data for trading',
        'print' => 'Print data, can print data as pdf / requested file',
        'generate' => 'Generate, can generate data',
    ];

    return $permissions;
}


/**
 * get permissions not
 *
 * @return array
 */
function get_permission_notes(): array
{
    return [
        'Notes' => 'If you want to give permission create or edit, you must give permission view',
    ];
}
