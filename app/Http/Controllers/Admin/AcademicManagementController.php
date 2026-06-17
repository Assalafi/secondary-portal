<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AcademicManagementController extends Controller
{
    /**
     * Display the Academic Management landing page.
     */
    public function index()
    {
        return view('admin.academic-management.index');
    }
}
