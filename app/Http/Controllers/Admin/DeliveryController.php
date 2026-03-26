<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DeliveryController extends Controller
{
    public function index()
    {
        return view('admin.delivery.index');
    }
}
