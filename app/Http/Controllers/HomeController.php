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
        $bulletContent = Bulletin::where([
            [\DB::raw("created_at + valid_time * 3600 * 24"), '>=', time()]
        ])
            ->orderBy('weight', 'desc')
            ->first(['content']);
        return view('home', compact('bulletContent'));
    }
}
