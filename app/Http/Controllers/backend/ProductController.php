<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Restock;
use App\Models\ProductRequest;



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
        // get the authenticated user
        $user = auth()->user();
        $user_id = auth()->user()->id;

        // check if the authenticated user is an admin (role 1)
        if ($user->role == 1) {
            // if admin, get all products from the database
            $list = DB::table('products')
                ->leftJoin('customers', 'products.CID', '=', 'customers.CustomerID') // Ensure correct field names
                ->select('products.ProductID', 'products.ProductName', 'customers.CustomerName', 'products.SKU', 'products.ProductLabel', 
                        'products.ProductExpiredDate', 'products.ProductImg', 'products.UOM', 'products.WeightPerUnit', 
                        'products.updated_at')
                ->get();

        } else {
            // if not admin, get products owned by the user
            // if ($user->role == 3) {
            //     // if the user is of role 3, get products owned by the user with role 3
            //     $list = DB::table('products')
            //         ->join('quantities', 'products.id', '=', 'quantities.product_id')
            //         ->join('companies', 'products.company_id', '=', 'companies.id')
            //         ->join('weights', 'products.id', '=', 'weights.product_id')
            //         ->select('products.id' ,'products.product_code', 'weights.weight_of_product', 'companies.company_name', 'products.product_name', 'products.product_desc', 'products.item_per_carton', 'products.carton_quantity', 'quantities.total_quantity', 'quantities.remaining_quantity', 'products.weight_per_item', 'products.weight_per_carton', 'products.product_dimensions', 'products.product_image', 'products.date_to_be_stored')
            //         ->where('products.user_id', $user_id)
            //         ->get();
            // } else {
            //     // if the user is not an admin or of role 3, get products owned by the user
            //     $list = DB::table('products')->where('user_id', $user->id)->get();
            // }
        }
        //dd($list);
        // return the view with the list of products
        return view('backend.product.list_product', compact('list'));
    }

    public function getUsers(Request $request)
    {
        $customer = Customer::find($request->CustomerID);
        $users = $company->users;

        return response()->json($users);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function ProductAdd(Request $request)
    {
        // Get all companies
        $companies = Company::all();

        // Get all the racks
        $racks = Rack::all();

        // Get all the floors
        $floors = Floor::all();

        // Get the selected company's ID
        $company_id = $request->input('company');

        // Get the users associated with the selected company
        $users = DB::table('users')
            ->join('companies', 'users.id', '=', 'companies.user_id')
            ->where('companies.id', $company_id)
            ->select('users.id', 'users.name')
            ->get();

        // Get all products
        $allProducts = DB::table('products')->get();

        // Return the view with the companies, users, and products
        return view('backend.product.create_product', compact('companies', 'users', 'allProducts', 'racks', 'floors'));
    }



    /**
     * Store a newly created resource in storage.
     */
    public function ProductInsert(Request $request)
    {
        // $number = mt_rand(1000000000, 9999999999);

        // if ($this->productCodeExists($number)) {
        //     $number = mt_rand(1000000000, 9999999999);
        // }
        // $request['product_code'] = $number;

        // Calculate the total quantity
        $total_quantity = $request->carton_quantity * $request->item_per_carton;
        $total_weight = $total_quantity * $request->weight_per_item;
        $rack_id = $request->rack_id;
        $floor_id = $request->floor_id;

        // Check if the total weight exceeds the limit of 200
        if ($total_weight > 200) {
            return redirect()->back()->with('error', 'Total weight exceeds limit of 200. Please adjust your inputs.')->withInput();
        }

        if ($rack_id != null) {
            // Get the rack capacity and occupied weight
            $rack_data = DB::table('rack_locations')
                ->where('id', $rack_id)
                ->select('capacity', 'occupied')
                ->first();

            $rack_capacity = $rack_data->capacity;
            $occupied_weight = $rack_data->occupied;

            // Calculate the remaining capacity
            $remaining_capacity = $rack_capacity - $occupied_weight;

        } else if ($floor_id != null) {
            // Get the floor capacity and occupied weight
            $floor_data = DB::table('floor_locations')
                ->where('id', $floor_id)
                ->select('capacity', 'occupied')
                ->first();

            $floor_capacity = $floor_data->capacity;
            $occupied_weight = $floor_data->occupied;

            // Calculate the remaining capacity
            $remaining_capacity = $floor_capacity - $occupied_weight;
        }

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

        $customer = DB::table('customers')
            ->where('id', $request->CustomerID)
            ->first();

        $data = [
            'user_id' => $Customer->UID,
            'customer_id' => $request->CustomerID,
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


        //Check if the remaining capacity is less than the weight of the new product
        if ($remaining_capacity < $total_weight) {
            return redirect()->back()->with('error', 'Capacity exceeded. Remaining capacity: ' . $remaining_capacity . '. Please adjust your inputs.')->withInput();
        } else {
            // Insert data into the products table
            $product_id = DB::table('products')->insertGetId($data);

            if ($product_id) {
                // Insert data into the quantity table
                DB::table('quantities')->insert([
                    'product_id' => $product_id,
                    'total_quantity' => $total_quantity,
                    'sold_carton_quantity' => 0,
                    'sold_item_quantity' => 0,
                    'remaining_quantity' => $total_quantity,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $rack_id = $request->rack_id;
                $floor_id = $request->floor_id;

                if ($rack_id != null) {
                    // Insert data into the weights table
                    DB::table('weights')->insert([
                        'product_id' => $product_id,
                        'weight_of_product' => $total_weight,
                        'rack_id' => $data['rack_id'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Get the total weight of products in the current rack
                    $total_weight_in_rack = DB::table('weights')
                        ->join('products', 'weights.product_id', '=', 'products.id')
                        ->where('products.rack_id', $rack_id)
                        ->sum('weight_of_product');

                    // Update rack_locations table with the occupied weight
                    DB::table('rack_locations')
                        ->where('id', $rack_id)
                        ->update(['occupied' => $total_weight_in_rack]);

                    return redirect()->route('product.index')->with('success', 'Product added successfully');

                } else if ($floor_id != null) {
                    // Insert data into the weights table
                    DB::table('weights')->insert([
                        'product_id' => $product_id,
                        'weight_of_product' => $total_weight,
                        'floor_id' => $data['floor_id'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Get the total weight of products in the current floor
                    $total_weight_in_floor = DB::table('weights')
                        ->join('products', 'weights.product_id', '=', 'products.id')
                        ->where('products.floor_id', $floor_id)
                        ->sum('weight_of_product');

                    // Update rack_locations table with the occupied weight
                    DB::table('floor_locations')
                        ->where('id', $floor_id)
                        ->update(['occupied' => $total_weight_in_floor]);

                    return redirect()->route('product.index')->with('success', 'Product added successfully');

                } else {
                    return redirect()->route('product.index')->with('error', 'Product added unsuccessfully');
                }
            }
        }
    }
    
    // public function productCodeExists($number)
    // {
    //     return product::whereProductCode($number)->exists();
    // }

    public function ProductEdit($id)
    {
        $edit = DB::table('products')
                ->leftJoin('customers', 'products.CID', '=', 'customers.CustomerID') // Ensure correct field names
                ->select('products.ProductID', 'products.ProductName', 'customers.CustomerName', 'products.SKU', 'products.ProductLabel', 'products.ProductExpiredDate', 'products.ProductImg', 'products.UOM', 'products.WeightPerUnit')
                ->where('products.ProductID', $id)
                ->first();
        $customers = Customer::all();

        return view('backend.product.edit_product', compact('edit', 'customers'));
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

        //$rack_id = $product->rack_id;
        //$floor_id = $product->floor_id;

        // if ($floor_id != null && $rack_id === null) {
        //     $newOccupied = DB::table('weights')
        //         ->join('floor_locations', 'floor_locations.id', '=', 'weights.floor_id')
        //         ->where('floor_locations.id', '=', $floor_id)
        //         ->where('weights.product_id', '!=', $id) // exclude the product being deleted
        //         ->sum('weights.weight_of_product');


        //     DB::table('floor_locations')
        //         ->where('id', '=', $floor_id)
        //         ->update(['occupied' => $newOccupied]);

        // } else if ($rack_id != null && $floor_id === null) {
        //     $newOccupied = DB::table('weights')
        //         ->join('rack_locations', 'rack_locations.id', '=', 'weights.rack_id')
        //         ->where('rack_locations.id', '=', $rack_id)
        //         ->where('weights.product_id', '!=', $id) // exclude the product being deleted
        //         ->sum('weights.weight_of_product');

        //     $newOccupiedFloor = DB::table('weights')
        //         ->join('floor_locations', 'floor_locations.id', '=', 'weights.floor_id')
        //         ->where('floor_locations.id', '=', $floor_id)
        //         ->where('weights.product_id', '!=', $id) // exclude the product being deleted
        //         ->sum('weights.weight_of_product');

        //     DB::table('rack_locations')
        //         ->where('id', '=', $rack_id)
        //         ->update(['occupied' => $newOccupied]);

        // } else {
        //     return redirect()->back()->with('error', 'Cannot delete product.')->withInput();
        // }


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


    //Customer Side For Product Adding

    public function RestockForm(Request $request)
    {
        // Get the authenticated user's ID
        $user_id = auth()->user()->id;

        // Get the authenticated user with their associated companies and products
        $user = User::with(['companies.products'])->find($user_id);

        // Get the authenticated user's company
        $company = $user->companies->first();

        // Get the products of the authenticated user's company
        $products = $company->products;

        // Return the view with the user's company and products
        return view('backend.product.restock_form', compact('company', 'products', 'user'));
    }



    //view chosen item to be restocked
    public function RestockItem($id)
    {
        $user_id = auth()->user()->id;

        $restock = DB::table('products')
            ->join('companies', 'products.company_id', '=', 'companies.id')
            ->leftJoin('rack_locations', 'products.rack_id', '=', 'rack_locations.id')
            ->leftJoin('floor_locations', 'products.floor_id', '=', 'floor_locations.id')
            ->select('products.id', 'products.company_id', 'rack_locations.id AS rack_id', 'floor_locations.id AS floor_id', 'companies.id AS company_id', 'companies.company_name', 'product_name', 'product_desc', 'product_image', 'product_dimensions', 'date_to_be_stored', 'weight_per_item', 'item_per_carton', 'weight_per_carton')
            ->where('products.id', $id)
            ->where('companies.user_id', $user_id) // Add condition to check if the company belongs to the user
            ->first();

        $companies = Company::all();

        if (!$restock) {
            // Product not found or does not belong to the user's company
            return redirect()->back()->with('error', 'Invalid product.');
        }

        return view('backend.product.restock_form_test', compact('restock', 'companies'));
    }



    public function SendRequestProduct(Request $request)
    {
        $total_quantity = $request->carton_quantity * $request->item_per_carton;
        $total_weight = $total_quantity * $request->weight_per_item;

        // Check if the total weight exceeds the limit of 200
        if ($total_weight > 200) {
            return redirect()->back()->with('error', 'Total weight exceeds the limit of 200. Please adjust your inputs.')->withInput();
        }

        // Get the user's ID
        $user_id = auth()->user()->id;

        $restock = DB::table('restock_request')
            ->join('products', 'restock_request.product_id', '=', 'products.id')
            ->select('restock_request.*', 'products.product_name', 'products.product_desc', 'products.weight_per_item')
            ->where('restock_request.user_id', $user_id)
            ->get();

        // Insert data into the restock_request table
        DB::table('restock_request')->insert([
            'total_weight' => $total_weight,
            'total_quantity' => $total_quantity,
            'product_id' => $request->product_id,
            'rack_id' => $request->rack_id,
            'floor_id' => $request->floor_id,
            'status' => 'Under Review',
            'user_id' => $user_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('showstatus')->with('success', 'Request has been send');
    }

    public function showRestockRequests()
    {
        $user_id = auth()->user()->id;

        $restock = DB::table('restock_request')
            ->join('products', 'restock_request.product_id', '=', 'products.id')
            ->select('restock_request.*', 'products.product_name', 'products.product_desc', 'products.weight_per_item')
            ->where('restock_request.user_id', $user_id)
            ->get();

        return view('backend.product.restock_status_customer', compact('restock'));
    }

    public function reviewRestockRequest()
    {
        $user_id = auth()->user()->id;

        $restock = DB::table('restock_request')
            ->join('products', 'restock_request.product_id', '=', 'products.id')
            ->join('companies', 'products.company_id', '=', 'companies.id')
            ->select('restock_request.*', 'products.product_name', 'companies.company_name', 'products.product_desc', 'products.weight_per_item', 'products.weight_per_carton', 'products.product_image')
            ->get();

        return view('backend.product.review_restock_request', compact('restock'));
    }

    public function approveRequest($id) //for restock request view admin
    {
        $restock = Restock::findOrFail($id);

        // Update the status to "Approved"
        $restock->status = 'Approved';
        $restock->save();

        $notification = array(
            'message' => 'Request Approved Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }


    // public function RemoveRequest($id) //for restock request view admin
// {

    //     $restock = Restock::findOrFail($id);

    //     // Update the status to "Rejected"
//     $restock->status = 'Rejected';
//     $restock->save();

    //     $notification = array(
//         'message' => 'Request Rejected Successfully',
//         'alert-type' => 'success'
//     );
//     return redirect()->back()->with($notification);
// }

    public function CancelReorderRequestCust($id)
    {
        // Find the request by ID
        $request = Restock::find($id);

        if (!$request) {
            return redirect()->back()->with('error', 'Request not found.');
        }

        // Check if the request status is "Under Review"
        if ($request->status !== 'Under Review') {
            return redirect()->back()->with('error', 'Error.');
        }

        // Delete the request from the database
        $request->delete();

        return redirect()->back()->with('success', 'Request removed successfully.');
    }

    //customer add new product

    public function CustomerAddProductForm(Request $request)
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


    public function storeProductRequest(Request $request)
    {
        $number = mt_rand(1000000000, 9999999999);

        if ($this->productCodeExists($number)) {
            $number = mt_rand(1000000000, 9999999999);
        }
        $request['product_code'] = $number;
        // Validate the incoming request data
        $validatedData = $request->validate([
            'company_id' => 'required',
            'product_name' => 'required',
            'product_desc' => 'required',
            'carton_quantity' => 'required|numeric',
            'total_weight' => 'required|numeric',
            'item_per_carton' => 'required|numeric',
            'weight_per_carton' => 'required|numeric',
            'weight_per_item' => 'required|numeric',
            'product_dimensions' => 'required',
            'product_price' => 'required',
            'product_code'=>'required',
            // 'product_image' => 'required|image',
            'address' => 'required',
            'phone_number' => 'required',
            'email' => 'required|email',
        ]);

        if ($validatedData['total_weight'] > 200) {
            return redirect()->back()->with('error', 'Total weight cannot exceed 200kg.');
        }

        // Handle the product image upload
        if ($request->hasFile('product_image')) {
            $file = $request->file('product_image');
            $filename = date('YmdHi') . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/Image', $filename);
            $data['product_image'] = $filename;

            // Move the file to the desired folder
            Storage::move('public/' . $filename, 'public/Image/' . $filename);
        }

        // Insert the product request into the database using the DB::table() method
        DB::table('product_request')->insert([
            'company_id' => $validatedData['company_id'],
            'product_name' => $validatedData['product_name'],
            'product_desc' => $validatedData['product_desc'],
            'product_code' => $validatedData['product_code'],
            'carton_quantity' => $validatedData['carton_quantity'],
            'item_per_carton' => $validatedData['item_per_carton'],
            'weight_per_carton' => $validatedData['weight_per_carton'],
            'weight_per_item' => $validatedData['weight_per_item'],
            'product_dimensions' => $validatedData['product_dimensions'],
            'total_weight' => $validatedData['total_weight'],
            'product_price' => $validatedData['product_price'],
            'product_image' => $filename ?? null,
            'address' => $validatedData['address'],
            'phone_number' => $validatedData['phone_number'],
            'email' => $validatedData['email'],
            'status' => $validatedData = 'Under Review'
        ]);

        dd($validatedData);

        // Redirect the user or return a response
        // (You can customize this based on your application's logic)
        return redirect()->back()->with('success', 'Product request submitted successfully.');
    }


    public function viewRequestProductList()
    {
        $user = Auth::user();
        $company_id = $user->company_id;

        $allRequestProduct = ProductRequest::where('company_id', $company_id)
            ->get();

        return view('backend.product.product_request_list', compact('allRequestProduct'));
    }

    public function DisplayIntoChoosenProductForm()
    {

        $user_id = auth()->user()->id;

        $racks = Rack::all();

        $floors = Floor::all();

        $list = DB::table('product_request')
            ->join('quantities', 'products.id', '=', 'quantities.product_id')
            ->join('companies', 'product_request.company_id', '=', 'companies.id')
            ->leftJoin('rack_locations', 'products.rack_id', '=', 'rack_locations.id')
            ->leftJoin('floor_locations', 'products.floor_id', '=', 'floor_locations.id')
            ->join('weights', 'products.id', '=', 'weights.product_id')
            ->select('products.id', 'rack_locations.location_code', 'floor_locations.location_codes','companies.company_name', 'products.product_name', 'products.product_desc', 'products.item_per_carton', 'products.carton_quantity', 'quantities.total_quantity', 'quantities.remaining_quantity', 'products.weight_per_item', 'products.weight_per_carton', 'weights.weight_of_product', 'products.product_dimensions', 'products.product_image', 'products.date_to_be_stored')
            ->get();

        return view('backend.product.product_request_list', compact('allRequestProduct'));
    }


    public function adminCheckNewProductRequest()
    {
        $user_id = auth()->user()->id;

        $racks = Rack::where('occupied', '=', 0.00)->get();

        $floors = Floor::where('occupied', '=', 0.00)->get();

        $newrequest = DB::table('product_request')
            ->join('companies', 'product_request.company_id', '=', 'companies.id')
            ->select('product_request.id', 'companies.company_name', 'companies.address', 'companies.phone_number', 'companies.email', 'product_request.product_name', 'product_request.carton_quantity', 'product_request.item_per_carton', 'product_request.product_dimensions', 'product_request.total_weight', 'product_request.product_price', 'product_request.product_image', 'product_request.product_desc', 'product_request.weight_per_carton', 'product_request.weight_per_item')
            ->whereNotIn('product_request.status', ['Approved', 'Rejected']) // Exclude rows with status 'Approved' or 'Rejected'
            ->get();

        return view('backend.product.retrieve_product', compact('newrequest', 'racks', 'floors'));
    }


    // public function approveProductRequest($id, Request $request)
    // {
    //     // Retrieve the product request by ID
    //     $productRequest = ProductRequest::findOrFail($id);

    //     // Retrieve the company by company_id
    //     $company = Company::findOrFail($productRequest->company_id);

    //     // Retrieve the user associated with the company
    //     $user = User::findOrFail($company->user_id);

    //     // Calculate the total quantity and total weight
    //     $total_quantity = $productRequest->carton_quantity * $productRequest->item_per_carton;
    //     $total_weight = $total_quantity * $productRequest->weight_per_item;

    //     // Get the rack capacity and occupied weight
    //     $rack_data = DB::table('rack_locations')
    //         ->where('id', $request->input('hidden_rack_id'))
    //         ->select('capacity', 'occupied')
    //         ->first();

    //     $rack_capacity = $rack_data->capacity;
    //     $occupied_weight = $rack_data->occupied;

    //     // Calculate the remaining capacity
    //     $remaining_capacity = $rack_capacity - $occupied_weight;

    //     // Check if the total weight exceeds the limit of 200
    //     if ($total_weight > 200) {
    //         return redirect()->back()->with('error', 'Total weight exceeds limit of 200. Please adjust your inputs.')->withInput();
    //     }

    //     // Check if the remaining capacity is less than the weight of the new product
    //     if ($remaining_capacity < $total_weight) {
    //         return redirect()->back()->with('error', 'Rack capacity exceeded. Remaining capacity: ' . $remaining_capacity . '. Please adjust your inputs.')->withInput();
    //     }

    //     // Insert data into the products table
    //     $product = new Product();
    //     $product->product_name = $productRequest->product_name;
    //     $product->product_desc = $productRequest->product_desc;
    //     $product->product_price = $productRequest->product_price;
    //     $product->carton_quantity = $productRequest->carton_quantity;
    //     $product->item_per_carton = $productRequest->item_per_carton;
    //     $product->product_dimensions = $productRequest->product_dimensions;
    //     $product->weight_per_item = $productRequest->weight_per_item;
    //     $product->weight_per_carton = $productRequest->weight_per_carton;
    //     $product->product_image = $productRequest->product_image;
    //     $product->company_id = $productRequest->company_id;
    //     $product->user_id = $user->id; // Assign the user_id associated with the company
    //     $product->rack_id = $request->input('hidden_rack_id'); // Get the selected rack_id from the hidden input field
    //     $product->date_to_be_stored = $request->input('date_to_be_stored');
    //     $product->save();

    //     // Insert data into the quantities table
    //     $product_id = $product->id;
    //     DB::table('quantities')->insert([
    //         'product_id' => $product_id,
    //         'total_quantity' => $total_quantity,
    //         'sold_carton_quantity' => 0,
    //         'sold_item_quantity' => 0,
    //         'remaining_quantity' => $total_quantity,
    //         'created_at' => now(),
    //         'updated_at' => now(),
    //     ]);

    //     // Insert data into the weights table
    //     DB::table('weights')->insert([
    //         'product_id' => $product_id,
    //         'weight_of_product' => $total_weight,
    //         'rack_id' => $product->rack_id,
    //         'created_at' => now(),
    //         'updated_at' => now(),
    //     ]);

    //     // Update the occupied weight in the rack_locations table
    //     DB::table('rack_locations')
    //         ->where('id', $request->input('hidden_rack_id'))
    //         ->update(['occupied' => $occupied_weight + $total_weight]);

    //     // Delete the product request from the database
    //     $productRequest->status = 'Approved';
    //     $productRequest->save();

    //     // Redirect back or to a success page
    //     return redirect()->back()->with('success', 'Product request approved and added to products.');

    //     // Alternatively, you can redirect to a specific route or page
    //     // return redirect()->route('products.index')->with('success', 'Product request approved and added to products.');
    // }

    public function approveProductRequest($id, Request $request)
    {
        // To be used when debugging error
        //return response()->json($request, 200);

        // Retrieve the product request by ID
        $productRequest = ProductRequest::findOrFail($id);

        // Retrieve the company by company_id
        $company = Company::findOrFail($productRequest->company_id);

        // Retrieve the user associated with the company
        $user = User::findOrFail($company->user_id);

        // Calculate the total quantity and total weight
        $total_quantity = $productRequest->carton_quantity * $productRequest->item_per_carton;
        $total_weight = $total_quantity * $productRequest->weight_per_item;

        // Set the rack and floor id from user's form input
        $rack_id = $request->rack_id;
        $floor_id = $request->floor_id;

        if ($floor_id != null && $rack_id === null) {
            // Get the floor capacity and occupied weight
            $floor_data = DB::table('floor_locations')
                ->where('id', $floor_id)
                ->select('capacity', 'occupied')
                ->first();

            if ($floor_data === null) {
                return redirect()->back()->with('error', 'Invalid floor location selected. Please choose a valid floor location.')->withInput();
            }

            $floor_capacity = $floor_data->capacity;
            $occupied_weight = $floor_data->occupied;

            // Calculate the remaining capacity
            $remaining_capacity = $floor_capacity - $occupied_weight;

            // Check if the total weight exceeds the limit of 200
            if ($total_weight > 200) {
                return redirect()->back()->with('error', 'Total weight exceeds limit of 200. Please adjust your inputs.')->withInput();
            }

            // Check if the remaining capacity is less than the weight of the new product
            if ($remaining_capacity < $total_weight) {
                return redirect()->back()->with('error', 'Floor capacity exceeded. Remaining capacity: ' . $remaining_capacity . '. Please adjust your inputs.')->withInput();

            }
        } else if ($rack_id != null && $floor_id === null) {
            // Get the rack capacity and occupied weight
            $rack_data = DB::table('rack_locations')
                ->where('id', $rack_id)
                ->select('capacity', 'occupied')
                ->first();

            if ($rack_data === null) {
                return redirect()->back()->with('error', 'Invalid rack location selected. Please choose a valid rack location.')->withInput();
            }

            $rack_capacity = $rack_data->capacity;
            $occupied_weight = $rack_data->occupied;

            // Calculate the remaining capacity
            $remaining_capacity = $rack_capacity - $occupied_weight;

            // Check if the total weight exceeds the limit of 200
            if ($total_weight > 200) {
                return redirect()->back()->with('error', 'Total weight exceeds limit of 200. Please adjust your inputs.')->withInput();
            }

            // Check if the remaining capacity is less than the weight of the new product
            if ($remaining_capacity < $total_weight) {
                return redirect()->back()->with('error', 'Rack capacity exceeded. Remaining capacity: ' . $remaining_capacity . '. Please adjust your inputs.')->withInput();
            }

        } else {
            return redirect()->back()->with('error', 'Please select storage location.')->withInput();
        }

        // Insert data into the products table
        $product = new Product();
        $product->product_name = $productRequest->product_name;
        $product->product_desc = $productRequest->product_desc;
        $product->carton_quantity = $productRequest->carton_quantity;
        $product->item_per_carton = $productRequest->item_per_carton;
        $product->product_dimensions = $productRequest->product_dimensions;
        $product->weight_per_item = $productRequest->weight_per_item;
        $product->weight_per_carton = $productRequest->weight_per_carton;
        $product->product_image = $productRequest->product_image;
        $product->product_price = $productRequest->product_price;
        $product->company_id = $productRequest->company_id;
        $product->user_id = $user->id; // Assign the user_id associated with the company
        $product->rack_id = $request->input('rack_id'); // Get the selected rack_id from the hidden input field
        $product->floor_id = $request->input('floor_id'); // Get the selected floor_id from the hidden input field
        $product->date_to_be_stored = $request->input('date_to_be_stored');
        $product->save();

        // Insert data into the quantities table
        $product_id = $product->id;
        DB::table('quantities')->insert([
            'product_id' => $product_id,
            'total_quantity' => $total_quantity,
            'sold_carton_quantity' => 0,
            'sold_item_quantity' => 0,
            'remaining_quantity' => $total_quantity,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($request->input('rack_id') === null) {
            // Insert data into the weights table
            DB::table('weights')->insert([
                'product_id' => $product_id,
                'weight_of_product' => $total_weight,
                'floor_id' => $product->floor_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update the occupied weight in the rack_locations table
            DB::table('floor_locations')
                ->where('id', $request->input('floor_id'))
                ->update(['occupied' => $occupied_weight + $total_weight]);

            // Delete the product request from the database
            $productRequest->delete();

            // Redirect back or to a success page
            return redirect()->back()->with('success', 'Product request approved and added to products.');


        } else if ($request->input('floor_id') === null) {
            // Insert data into the weights table
            DB::table('weights')->insert([
                'product_id' => $product_id,
                'weight_of_product' => $total_weight,
                'rack_id' => $product->rack_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update the occupied weight in the rack_locations table
            DB::table('rack_locations')
                ->where('id', $request->input('rack_id'))
                ->update(['occupied' => $occupied_weight + $total_weight]);

            // Delete the product request from the database
            $productRequest->delete();

            // Redirect back or to a success page
            return redirect()->back()->with('success', 'Product request approved and added to products.');

        } else {
            return redirect()->back()->with('error', 'Product added unsucessfully.');
        }

        // Alternatively, you can redirect to a specific route or page
        // return redirect()->route('products.index')->with('success', 'Product request approved and added to products.');
    }



    public function rejectProductRequest($id)
    {
        // Find the product request by ID
        $productRequest = ProductRequest::findOrFail($id);

        // Delete the product request from the database
        $productRequest->status = 'Rejected';
        $productRequest->save();

        // Redirect back or to a success page
        return redirect()->back()->with('success', 'Product request rejected.');

        // Alternatively, you can redirect to a specific route or page
        // return redirect()->route('productRequests.index')->with('success', 'Product request rejected.');
    }

    public function showAddRequestStatus(Request $request)
    {
        // Get the user's ID
        $user_id = auth()->user()->id;

        // Get the user's company
        $company = Company::where('user_id', $user_id)->first();

        // Make sure the company exists before proceeding
        if (!$company) {
            return redirect()->route('home')->with('error', 'Company not found.');
        }

        // Filter the product requests based on the company_id
        $newproduct = DB::table('product_request')
            ->where('company_id', $company->id) // Use $company->id to get the company ID
            ->get();

        return view('backend.product.request_add_product_status', compact('newproduct', 'company', 'user_id'));
    }


    public function CancelNewAddRequestCust($id)
    {
        // Find the request by ID
        $request = ProductRequest::find($id);

        if (!$request) {
            return redirect()->back()->with('error', 'Request not found.');
        }

        // Check if the request status is "Under Review"
        if ($request->status !== 'Under Review') {
            return redirect()->back()->with('error', 'Error.');
        }

        // Delete the request from the database
        $request->delete();

        return redirect()->back()->with('success', 'Request removed successfully.');
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
        return view('backend.product.qr_product');
    }
    public function getProductQRInfo($productCode)
    {
        // Query the database using $productCode and return the product information as JSON
        $productInfo = Product::where('product_code', $productCode)->first();

        return response()->json($productInfo);
    }
}
