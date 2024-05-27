<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
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
    public function listProduct(Request $request)
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
                DB::raw("CONCAT('/storage/assets/images/product/', products.Img) as Img"),
                'products.UOM',
                'products.weight_per_unit',
                'products.updated_at',
                'partners.id as partner_id',
                'partners.name as partner_name',
                'users.name as user_name'
            );

        // Check user role and modify the query accordingly
        if ($user->role == 1) {
            // Admin: get all products
            $list = $query->get();
            $partners = Partner::all();
        } elseif ($user->role == 2) {
            // Picker: get products owned by the user
            //$list = $query->where('products.uid', $user_id)->get();  WRONG!! picker not associated with any product
        } elseif ($user->role == 3) {
            // Partner: get products owned by the user (simplified selection)
            $list = $query->where('products.uid', $user_id)->get();
            $partners = Partner::where('uid', $user_id)->get();
        } else {
            // Handle case where user has an unknown role
            abort(403, 'Unauthorized action.');
        }

        // Return the view with the list of products
        return view('backend.product.list_product', compact('list', 'partners'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function insertProduct(Request $request)
    {
        //return $request;
        // Define validation rules
        $validationRules = [
            'product_name' => 'required|string|max:255',
            'SKU' => 'required|string|max:255',
            'product_desc' => 'nullable|string',
            'expired_date' => 'nullable|date',
            'UOM' => 'required|string|max:50',
            'weight_per_unit' => 'numeric|nullable',
            'partner_id' => 'required|integer|exists:partners,id',
            'Img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];

        // Validate the incoming request data
        $validated = $request->validate($validationRules);

        // Prepare the data for insertion
        $data = [
            'name' => $validated['product_name'],
            'SKU' => $validated['SKU'],
            'product_desc' => $validated['product_desc'] ?? null,
            'expired_date' => $validated['expired_date'] ?? null,
            'UOM' => $validated['UOM'],
            'weight_per_unit' => $validated['weight_per_unit'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
            'uid' => auth()->user()->id,
            'partner_id' => $validated['partner_id'],
        ];
        return $data;

        // Handle file upload
        if ($request->hasFile('Img')) {
            $file = $request->file('Img');
            $filename = date('YmdHi') . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/assets/images/product', $filename);
            $data['Img'] = $filename;
        }

        DB::beginTransaction();
        try {
            $insert = DB::table('products')->insert($data);
            DB::commit();

            if ($insert) {
                return 'success';
                //return redirect()->route('product.index')->with('success', 'Product Created Successfully!');
            } else {
                return 'fail';
                //return redirect()->route('product.index')->with('error', 'Failed to create product.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            //return redirect()->route('product.index')->with('error', 'Failed to create product.');
            return 'error';
        }
    }

    public function editProduct($id)
    {
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
                'partners.id as partner_id',
                'partners.name as partner_name',
                'users.name as user_name',
                'users.id as uid'
            );

        $edit = $query->where('products.id', $id)->first();
        $partners = DB::table('partners')->where('partners.uid', 'uid');

        return view('backend.product.edit_product', compact('edit', 'partners'));
    }

    public function updateProduct(Request $request, $id)
    {
        // return $request;  {DEBUGGING}
        
        // Define validation rules
        $validationRules = [
            'product_name' => 'required|string|max:255',
            'SKU' => 'required|string|max:255',
            'product_desc' => 'nullable|string',
            'expired_date' => 'nullable|date',
            'UOM' => 'required|string|max:50',
            'weight_per_unit' => 'numeric',
            'partner_id' => 'required|integer|exists:partners,id',
            'Img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];

        // Validate the incoming request data
        $validated = $request->validate($validationRules);

        // Prepare the data for updating
        $data = [
            'name' => $validated['product_name'],
            'SKU' => $validated['SKU'],
            'product_desc' => $validated['product_desc'] ?? null,
            'expired_date' => $validated['expired_date'] ?? null,
            'UOM' => $validated['UOM'],
            'weight_per_unit' => $validated['weight_per_unit'] ?? null,
            'updated_at' => now(),
            'partner_id' => $validated['partner_id'],
        ];
        // Handle file upload
        if ($request->hasFile('Img')) {
            $file = $request->file('Img');
            $filename = date('YmdHi') . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/assets/images/product', $filename);
            $data['Img'] = $filename;
        }
        
        DB::beginTransaction();
        try {
            $update = DB::table('products')->where('id', $id)->update($data);
            DB::commit();

            if ($update) {
                //return "a";
                return redirect()->route('product.index')->with('success', 'Product Updated Successfully!');
            } else {
                //return "b";
                return redirect()->route('product.index')->with('error', 'Failed to update product.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
                //return $e;
            return redirect()->route('product.index')->with('error', 'Failed to update product.');
        }
    }

    public function deleteProduct($id)
    {
        // Retrieve the product by ID, if not found throw a 404 error
        $product = Product::findOrFail($id);

        // Begin a transaction to ensure both product and image are deleted together
        DB::beginTransaction();
        try {
            // If the product has an image, delete it from storage
            if ($product->Img) {
                // Construct the full path to the image
                $imagePath = storage_path('app/public/assets/images/product/' . $product->Img);

                // Check if the image file exists and delete it
                if (File::exists($imagePath)) {
                    File::delete($imagePath);
                }
            }

            // Delete the product from the database
            $product->delete();

            // Commit the transaction
            DB::commit();

            // Prepare the success notification
            $notification = array(
                'message' => 'Product Deleted Successfully',
                'alert-type' => 'success'
            );

        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();

            // Prepare the error notification
            $notification = array(
                'message' => 'Error: ' . $e->getMessage(),
                'alert-type' => 'error'
            );
        }

        // Redirect back with the notification
        return redirect()->back()->with($notification);
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
