<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

/** Dashboard dell'amministratore. */
class DashboardController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }
}
