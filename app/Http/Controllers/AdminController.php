<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        abort_if(Auth::user()->role !== 'admin', 403);

        return view('admin.dashboard');
    }
}
