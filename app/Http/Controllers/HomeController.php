<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Index
     *
     * @return Application|Factory|View
     */
    public function index(): View|Factory|Application
    {
        return view('home');
    }
}
