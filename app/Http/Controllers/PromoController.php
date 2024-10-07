<?php

namespace App\Http\Controllers;

use App\Models\Promos;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    public function index()
    {
        // Retrieve all promos
        $promos = Promos::all();

        // Return the Blade view with promos data
        return view('pages.promos', compact('promos'));
    }
}
