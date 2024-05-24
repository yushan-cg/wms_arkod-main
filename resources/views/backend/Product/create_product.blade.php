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
        <div class="container">
            <h2>Create Product</h2>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('product.insert') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="product_name">Product Name:</label>
                    <input type="text" class="form-control" id="product_name" name="product_name" value="{{ old('product_name') }}" required>
                </div>
                <div class="form-group">
                    <label for="SKU">SKU:</label>
                    <input type="text" class="form-control" id="SKU" name="SKU" value="{{ old('SKU') }}" required>
                </div>
                <div class="form-group">
                    <label for="product_desc">Product Description:</label>
                    <textarea class="form-control" id="product_desc" name="product_desc">{{ old('product_desc') }}</textarea>
                </div>
                <div class="form-group">
                    <label for="expired_date">Expired Date:</label>
                    <input type="date" class="form-control" id="expired_date" name="expired_date" value="{{ old('expired_date') }}">
                </div>
                <div class="form-group">
                    <label for="UOM">UOM:</label>
                    <input type="text" class="form-control" id="UOM" name="UOM" value="{{ old('UOM') }}" required>
                </div>
                <div class="form-group">
                    <label for="weight_per_unit">Weight per Unit:</label>
                    <input type="number" class="form-control" id="weight_per_unit" name="weight_per_unit" value="{{ old('weight_per_unit') }}">
                </div>
                <div class="form-group">
                    <label for="partner_id">Partner:</label>
                    <select class="form-control" id="partner_id" name="partner_id" required>
                        @foreach($partners as $partner)
                            <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="Img">Image:</label>
                    <input type="file" class="form-control-file" id="Img" name="Img">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
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

    // Get today's date in yyyy-mm-dd format
    const today = new Date().toISOString().split('T')[0];
    // Set the min attribute of the date input to today
    document.getElementById('expired_date').setAttribute('min', today);
</script>
@endsection
