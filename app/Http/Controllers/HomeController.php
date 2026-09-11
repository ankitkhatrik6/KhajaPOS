<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    /**
     * Public landing / hero page with a login entry point.
     */
    public function index()
    {
        return view('home.index');
    }
}