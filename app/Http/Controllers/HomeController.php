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
            [\DB::raw('UNIX_TIMESTAMP(end_date)'), '>=', time()],
            [\DB::raw('UNIX_TIMESTAMP(start_date)'), '<=', time()],
            ['show', '=', 1],
        ])
            ->orderBy('weight', 'desc')->orderBy('created_at', 'desc')->first(['content']);
        return view('home', compact('bulletContent'));
    }
}
