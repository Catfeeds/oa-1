<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function index()
    {
        $title = 'CRM系统管理';
        return view('crm.index.index', compact('title'));
    }
}
