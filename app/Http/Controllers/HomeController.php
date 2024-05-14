<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use App\Models\ReturnStock;
use App\Models\Delivery;
use App\Models\Picker;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

    // get the authenticated user
    $user = auth()->user();

    //For Admin

    $productsCount = Product::count();
    $usersCount = User::count();

    //For Picker


    //For Customer


    return view('home');


    }

    
}
