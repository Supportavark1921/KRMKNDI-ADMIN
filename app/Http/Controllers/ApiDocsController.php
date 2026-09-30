<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ApiDocsController extends Controller
{
    public function index(): View
    {
        Gate::authorize('manage-appointments');

        return view('api.docs');
    }
}
