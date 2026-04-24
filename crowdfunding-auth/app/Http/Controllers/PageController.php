<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    /**
     * Show registration page.
     */
    public function registerPage()
    {
        return view('register');
    }

    /**
     * Show login page.
     */
    public function loginPage()
    {
        return view('login');
    }

    /**
     * Show dashboard page.
     */
    public function dashboard()
    {
        return view('dashboard');
    }
}
