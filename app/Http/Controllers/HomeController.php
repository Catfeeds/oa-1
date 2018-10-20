<?php

namespace App\Http\Controllers;

use App\Models\Sys\Bulletin;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bulletContent = Bulletin::where([[\DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d')"), '=', date('Y-m-d', time())]])->first(['content']);
        return view('home', compact('bulletContent'));
    }
}
