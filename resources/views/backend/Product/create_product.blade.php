@extends('backend.Layouts.app')
@section('content')
<title>Add Product</title>
<div class="container-full">
		<!-- Content Header (Page header) -->
		<div class="content-header">
			<div class="d-flex align-items-center">
				<div class="me-auto">
					<h4 class="page-title">General Form</h4>
					<div class="d-inline-block align-items-center">
						<nav>
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ URL::to('/home') }}"><i class="mdi mdi-home-outline"></i></a></li>
								<li class="breadcrumb-item" aria-current="page">Forms</li>
								<li class="breadcrumb-item active" aria-current="page">Add Product</li>
							</ol>
						</nav>
					</div>
				</div>

			</div>
		</div>

		<!-- Main content -->
		<section class="content">
			<div class="row">
				<div class="col-md-6 mx-auto">
					  <div class="box">
						<div class="box-header with-border">
						  <h4 class="box-title">Add Product</h4>
						</div>
						<!-- /.box-header -->
						<form role="form" action="{{ URL::to('/insert_product') }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
							<div class="box-body">
								<h4 class="box-title text-info mb-0"><i class="ti-briefcase"></i> Company Info</h4>
								<hr class="my-15">
								<div class="row">
								<div class="form-group">
								  <label class="form-label">Company Name</label>
								  <select name="company_id" class="form-control" id="company_id">
                                    <option value="">Select Company Name</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}" >{{ $company->company_name }}</option>
                                    @endforeach
                                </select>
                                @error('company_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
								</div>
								</div>
								<h4 class="box-title text-info mb-0 mt-20"><i class="ti-package"></i> Storage Location</h4>
								<hr class="my-15">
								<div class="form-group">
								  <label class="form-label">Rack Location</label>
								  <select name="rack_id" class="form-control" id="rack_id">
                                    <option value="">Select Rack Location</option>
                                    @foreach ($racks as $location)
                                        <option value="{{ $location->id }}">{{ $location->location_code }}</option>
                                    @endforeach
                                </select>
                                @error('rack_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
								</div>
								<div class="form-group">
								  <label class="form-label">Floor Location</label>
								  <select name="floor_id" class="form-control" id="floor_id">
                      				<option value="">Select Floor Location</option>
                      				@foreach($floors as $location)
                          				<option value="{{ $location->id }}">{{ $location->location_codes }}</option>
                      				@endforeach
                  				</select>
                  				@error('floor_id')
                      				<span class="invalid-feedback" role="alert">
                          				<strong>{{ $message }}</strong>
                      				</span>
                  				@enderror
								</div>
                                <h4 class="box-title text-info mb-0 mt-20"><i class="ti-light-bulb"></i> Product Info</h4>
								<hr class="my-15">
								<div class="form-group">
								  <label class="form-label">Product Name</label>
								  <input class="form-control" id="product_name" type="text" name="product_name" placeholder="Enter Product Name" value="{{ old('product_name') }}">

								</div>
								<div class="form-group">
								  <label class="form-label">Product Price (RM Per Item)</label>
								  <input class="form-control" id="product_price" type="text" name="product_price" placeholder="Enter Product Price">
								</div>
                                <div class="form-group">
								  <label class="form-label">Product Description</label>
								  <input class="form-control" id="product_desc" type="text" name="product_desc" placeholder="Enter Product Description" value="{{ old('product_desc') }}">
								</div>
                                <div class="form-group">
								  <label class="form-label">Total Carton Quantity</label>
								  <input class="form-control" id="carton_quantity" type="number" name="carton_quantity" placeholder="Enter Quantity" value="{{ old('carton_quantity') }}">
								</div>
                                <div class="form-group">
								  <label class="form-label">Item Per Carton</label>
								  <input class="form-control" id="item_per_carton" type="number" name="item_per_carton" placeholder="Enter Quantity" value="{{ old('item_per_carton') }}">
								</div>
                                <div class="form-group">
								  <label class="form-label">Product Dimension (cm x cm x cm)</label>
								  <input class="form-control" id="product_dimensions" type="text" name="product_dimensions" placeholder="Enter Product Dimensions">
								</div>
                                <div class="form-group">
								  <label class="form-label">Weight Per Item (kg)</label>
								  <input class="form-control" id="weight_per_item" type="text" name="weight_per_item" placeholder="Enter Weight Per Item" step="0.1">
								</div>
								<div class="form-group">
								  <label class="form-label">Weight Per Carton(kg)</label>
								  <input class="form-control" id="weight_per_carton" type="text" name="weight_per_carton" placeholder="Enter Weight Per Carton" step="0.1">
								</div>
                                <div class="form-group">
								  <label class="form-label">Total Weight (kg) [Total Weight Must Not Exceed 200kg When Adding Product to Rack]</label>
								  <input class="form-control" id="total_weight" type="text" name="total_weight" placeholder="" readonly>
								</div>
                                <div class="form-group">
								  <label class="form-label">Product Image</label>
								  <input class="form-control" id="product_image" type="file" name="product_image" placeholder="Upload Image">
								</div>
                                <div class="form-group">
								  <label class="form-label">Date to be stored</label>
								  <input class="form-control" id="date" type="date" name="date_to_be_stored" placeholder="Date" >
								</div>
							<!-- /.box-body -->
							<div class="box-footer text-end">
								<button type="button" class="btn btn-warning me-1">
								  <i class="ti-trash"></i> Cancel
								</button>
								<button type="submit" class="btn btn-primary">
								  <i class="ti-save-alt"></i> Save
								</button>
							</div>
						</form>
					  </div>
					  <!-- /.box -->
		</section>
		<!-- /.content -->
	  </div>
@endsection
@section('page content overlay')
	<!-- Page Content overlay -->


	<!-- Vendor JS -->
	<script src="{{ asset('assets/js/vendors.min.js') }}"></script>
	<script src="{{ asset('assets/js/pages/chat-popup.js') }}"></script>
    <script src="{{ asset('assets/icons/feather-icons/feather.min.js') }}"></script>

	<!-- Deposito Admin App -->
	<script src="{{ asset('assets/js/template.js') }}"></script>

    <script type="text/javascript">
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#image')
                        .attr('src', e.target.result)
                        .width(80)
                        .height(80);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        const cartonQuantity = document.getElementById('carton_quantity');
        const weightPerCarton = document.getElementById('weight_per_carton');
        const totalWeightOutput = document.getElementById('total_weight');

        cartonQuantity.addEventListener('input', updateTotalWeight);
        weightPerCarton.addEventListener('input', updateTotalWeight);

        function updateTotalWeight() {
            const cartonQuantityValue = parseFloat(cartonQuantity.value) || 0;
            const weightPerCartonValue = parseFloat(weightPerCarton.value) || 0;
            const totalWeight = cartonQuantityValue * weightPerCartonValue;
            totalWeightOutput.value = totalWeight.toFixed(2);
        }

        $(document).ready(function() {
    $("#rack_id").change(function() {
        if ($(this).val() !== "") {
            $("#floor_id").prop("disabled", true);
        } else {
            $("#floor_id").prop("disabled", false);
        }
    });

    $("#floor_id").change(function() {
        if ($(this).val() !== "") {
            $("#rack_id").prop("disabled", true);
        } else {
            $("#rack_id").prop("disabled", false);
        }
    });
    });
        // Get today's date in yyyy-mm-dd format
        const today = new Date().toISOString().split('T')[0];

    // Set the min attribute of the date input to today
    document.getElementById('date').setAttribute('min', today);
    </script>
@endsection
