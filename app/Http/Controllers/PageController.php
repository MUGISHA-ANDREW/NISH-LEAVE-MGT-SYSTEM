<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    
    public function privacy()
{
    return view('components.partials.privacy');
}

    public function terms()
    {
        return view('components.partials.terms');
    }
    public function helpCenter()
    {
        return view('components.partials.help');
    }
}
