<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get the currently authenticated user
        $user = auth()->user();

        // Get the customers that belong to the user
        $customers = Customer::where('UID', $user->id)->get();

        // Return the view with the companies data
        return view('backend.customer.detail_customer', compact('customers'));

    }

    public function showAll()
    {
        $customers = Customer::all();

        return view('backend.customer.customer_list', compact('customers'));
    }
            
} 
