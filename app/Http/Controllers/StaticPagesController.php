<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaticPagesController extends Controller
{
    public function howToUseUSC()
    {
        return view('pages.faq'); // Ensure this matches your Blade view path
    }
}


