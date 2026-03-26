<?php

/**
 * get user branch data
 *
 * @return collection
 */
function get_current_branch()
{
    return auth()?->user()?->branch;
}

/**
 * get user branch id
 *
 * @return integer
 */
function get_current_branch_id()
{
    return auth()?->user()?->branch_id;
}

/**
 * get user branch name
 *
 * @return string
 */
function get_current_branch_name()
{
    return auth()?->user()?->branch?->nama;
}

/**
 * get user branch sort
 *
 * @return string
 */

function get_current_branch_sort()
{
    return auth()?->user()?->branch?->sort;
}

/**
 * validate data
 *
 * @param int $branch_id
 * @return void
 */
function validate_branch($branch_id): void
{
    if (!auth()->user()->branch->is_primary) {
        if (auth()?->user()?->branch_id != $branch_id) {
            abort(403);
        }
    }
}

/**
 * Get primary branch
 *
 * @return collection
 */
function get_primary_branch()
{
    return \App\Models\Branch::where('is_primary', true)->first();
}
