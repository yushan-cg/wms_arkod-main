<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\User;
use App\Models\Partner;


class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function ProductList(Request $request)
    {
        // Get the authenticated user
        $user = auth()->user();
        $user_id = $user->id;

        // Initialize the base query
        $query = DB::table('products')
            ->leftJoin('users', 'products.uid', '=', 'users.id')
            ->leftJoin('partners', 'products.partner_id', '=', 'partners.id')
            ->select(
                'products.id',
                'products.name as product_name',
                'products.SKU',
                'products.product_desc',
                'products.expired_date',
                'products.Img',
                'products.UOM',
                'products.weight_per_unit',
                'products.updated_at',
                'partners.name as partner_name',
                'users.name as user_name'
            );

        // Check user role and modify the query accordingly
        if ($user->role == 1) {
            // Admin: get all products
            $list = $query->get();
        } elseif ($user->role == 2) {
            // Picker: get products owned by the user
            //$list = $query->where('products.uid', $user_id)->get();  WRONG!! picker not associated with any product
        } elseif ($user->role == 3) {
            // Partner: get products owned by the user (simplified selection)
            $list = $query->where('products.uid', $user_id)->get();
        } else {
            // Handle case where user has an unknown role
            abort(403, 'Unauthorized action.');
        }

        // Return the view with the list of products
        return view('backend.product.list_product', compact('list'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function ProductAdd(Request $request)
    {
        // Get all partners associated with the authenticated user
        $partners = Partner::where('user_id', auth()->user()->id)->get();

        // Get the users associated with the selected partner
        $users = DB::table('users')
            ->join('partners', 'users.id', '=', 'partners.user_id')
            ->where('partners.id', $partner_id)
            ->select('users.id', 'users.name')
            ->get();

        // Get all products
        $allProducts = DB::table('products')->get();

        // Return the view with the partners, users, and products
        return view('backend.product.create_product', compact('partners', 'users', 'allProducts'));
    }



    /**
     * Store a newly created resource in storage.
     */
    public function ProductInsert(Request $request)
    {
        $validatedData = $request->validate([
            'company_id' => 'required',
            'product_name' => 'required|string|max:255',
            'product_desc' => 'required|string',
            'weight_per_item' => 'required|numeric',
            'weight_per_carton' => 'required|numeric',
            'product_dimensions' => 'required|string|max:255',
            'date_to_be_stored' => 'required|date',
            'carton_quantity' => 'required|integer',
            'product_price' => 'required|numeric',
            'item_per_carton' => 'required|integer',
            'product_code' => 'required|string',
            'product_image' => 'required|image|max:2048',
            'rack_id' => 'required_without:floor_id',
            'floor_id' => 'required_without:rack_id'
        ]);

        $partner = DB::table('partners')
            ->where('id', $request->PartnerID)
            ->first();

        $data = [
            'user_id' => $Partner->UID,
            'partner_id' => $request->PartnerID,
            'product_name' => $request->product_name,
            'product_desc' => $request->product_desc,
            'product_code' => $request->product_code,
            'item_per_carton' => $request->item_per_carton,
            'carton_quantity' => $request->carton_quantity,
            'product_price' => $request->product_price,
            'weight_per_item' => $request->weight_per_item,
            'weight_per_carton' => $request->weight_per_carton,
            'product_dimensions' => $request->product_dimensions,
            'rack_id' => $request->rack_id,
            'floor_id' => $request->floor_id,
            'date_to_be_stored' => $request->date_to_be_stored,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if ($request->hasFile('product_image')) {
            $file = $request->file('product_image');
            $filename = date('YmdHi') . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/Image', $filename);
            $data['product_image'] = $filename;

            // Move the file to the desired folder
            Storage::move('public/' . $filename, 'public/Image/' . $filename);
        }
    }

    public function ProductEdit($id)
    {
        $edit = DB::table('products')
                ->leftJoin('partners', 'products.CID', '=', 'partners.PartnerID') // Ensure correct field names
                ->select('products.ProductID', 'products.ProductName', 'partners.PartnerName', 'products.SKU', 'products.ProductLabel', 'products.ProductExpiredDate', 'products.ProductImg', 'products.UOM', 'products.WeightPerUnit')
                ->where('products.ProductID', $id)
                ->first();
        $partners = Partner::all();

        return view('backend.product.edit_product', compact('edit', 'partners'));
    }

    public function ProductUpdate(Request $request, $id)
    {
        $validatedData = $request->validate([
            'company_id' => 'required',
            'product_name' => 'required|string|max:255',
            'product_desc' => 'required|string',
            'weight_per_item' => 'required|numeric',
            'weight_per_carton' => 'required|numeric',
            'product_price' => 'required|numeric',
            'product_dimensions' => 'required|string|max:255',
            'date_to_be_stored' => 'required|date',
            'product_image' => 'image|max:2048'
        ]);

        $data = [
            'company_id' => $request->company_id,
            'product_name' => $request->product_name,
            'product_desc' => $request->product_desc,
            'weight_per_item' => $request->weight_per_item,
            'weight_per_carton' => $request->weight_per_carton,
            'product_price' => $request->product_price,
            'product_dimensions' => $request->product_dimensions,
            'date_to_be_stored' => $request->date_to_be_stored,
            'updated_at' => now(),
        ];

        // Check if image is uploaded
        if ($request->hasFile('product_image')) {
            $file = $request->file('product_image');
            $filename = date('YmdHi') . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/Image', $filename);
            $data['product_image'] = $filename;

            // Move the file to the desired folder
            Storage::move('public/' . $filename, 'public/Image/' . $filename);
        }

        $update = DB::table('products')->where('id', $id)->update($data);

        if ($update) {
            return Redirect()->route('product.index')->with('success', 'Product Updated Successfully!');
        } else {
            $notification = array(
                'message' => 'error',
                'alert-type' => 'error'
            );
            return Redirect()->route('product.index')->with($notification);
        }
    }

    public function ProductDelete($id)
    {
        $product = Product::where('ProductID', $id)->firstOrFail();

        if ($product->delete()) {
            $notification = array(
                'message' => 'Product Deleted Successfully',
                'alert-type' => 'success'
            );
            return redirect()->back()->with($notification);
        } else {
            $notification = array(
                'message' => 'Error',
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }
    }


    public function getProducts($company_id)
    {
        $products = Product::where('company_id', $company_id)->pluck('product_name', 'id');
        return response()->json($products);
    }

    public function SendRequestProduct(Request $request)
    {
      
    }

    public function approveRequest($id) //for restock request view admin
    {
        // Retrieve the restock request by ID or fail if not found
        //$restock = Restock::findOrFail($id);

        // Set the status of the restock request to "Approved"
        //$restock->status = 'Approved';
        // Save the updated restock request
        //$restock->save();

        // Prepare a notification message for successful approval
        //$notification = array(
        //    'message' => 'Request Approved Successfully',
        //    'alert-type' => 'success'
        //);
        // Redirect back with success notification
        //return redirect()->back()->with($notification);
    }

    //partner add new product
    public function PartnerAddProductForm(Request $request)
    {
        // Get the user's ID
        $user_id = auth()->user()->id;

        // Get the user's company
        $company = Company::where('user_id', $user_id)->first();


        if (!$company) {
            // Redirect to the "add company" page with a message
            return redirect()->route('company.create')->with('error', 'Please register your company first.');
        }

        // Get all companies
        $companies = Company::all();

        // Return the view with the company and companies
        return view('backend.product.request_product', compact('company', 'companies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            // Other validation rules for your form fields
        ]);

        // Process the form data
    }

    //QR Products
    public function ProductQR()
    {
        //return view('backend.product.qr_product');
    }

    public function getProductQRInfo($productCode)
    {
    }
}
