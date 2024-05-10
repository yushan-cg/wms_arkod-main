@extends('backend.Layouts.app')
@section('content')
<title>Product List</title>
<style>
.text-danger {
	border: none;
  padding: 0;
  background: none;
}
</style>
<div class="content-header">
			<div class="d-flex align-items-center">
				<div class="me-auto">
					<h4 class="page-title">Data Tables</h4>
					<div class="d-inline-block align-items-center">
						<nav>
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ URL::to('/home') }}"><i class="mdi mdi-home-outline"></i></a></li>
								<li class="breadcrumb-item" aria-current="page">List</li>
								<li class="breadcrumb-item active" aria-current="page">Product List</li>
							</ol>
						</nav>
					</div>
				</div>

			</div>
		</div>

		<!-- Main content -->
		<section class="content">

		  <div class="row">
			  <div class="col-12">
				<div class="box">
				  <div class="box-body">
					<div class="table-responsive">
						<table id="productorder" class="table table-hover no-wrap product-order" data-page-size="10">
							<thead>
								<tr>
								<th>ID</th>
								<th>Company</th>
								@if (Auth::user()->role == 1 || Auth::user()->role == 2)
								<th>Rack Location</th>
								<th>Floor Location</th>
								@endif
								<th>Product Image</th>
								<th>Product Name</th>
                                <th>Product Code</th>
								<th>Quantity</th>
								<th>Weight(KG)</th>
								@if (Auth::user()->role == 1)
                                <th>Action</th>
								@endif
								</tr>
							</thead>
							<tbody>
                            @foreach ($list as $row)
                                <?php $P_code=$row->product_code ?>

                                <tr>
                                    <td>{{ $row->id }}</td>
                                    <td>{{ $row->company_name }}</td>
                                    @if (Auth::user()->role == 1 || Auth::user()->role == 2)
                                        <td>{{ $row->location_code ?? '-' }}</td>
                                        <td>{{ $row->location_codes ?? '-' }}</td>
                                    @endif
									<td>
                                        <img src="{{ asset('storage/Image/' . $row->product_image) }}" width="50" height="50">
                                    </td>
                                    <td>{{ $row->product_name }}</td>
                                    <td>{!! DNS2D::getBarCodeSVG($row->product_code,'QRCODE') !!}
                                    p-{{$row->product_code}}</td>
                                    <td>{{ $row->remaining_quantity }}</td>
                                    <td>{{ $row->weight_of_product }}</td>
                                    @if (Auth::user()->role == 1)
									<td><a href="{{ URL::to('/edit_product/' . $row->id) }}" class="text-info me-10" data-bs-toggle="tooltip" data-bs-original-title="Edit">
											<i class="ti-marker-alt"></i>
										</a>
										<button data-href="{{ URL::to('delete_product/' . $row->id) }}" class="text-danger sa-params" data-bs-original-title="Delete" data-bs-toggle="tooltip" alt="alert">
											<i class="ti-trash" alt="alert"></i>
										</button>
									</td>
                                    @endif
                                </tr>
                            @endforeach
							</tbody>
						</table>
					</div>
				  </div>
				</div>
			  </div>
		  </div>

		</section>
		<!-- /.content -->
@endsection
@section('page content overlay')
	<!-- Page Content overlay -->

	<!-- Vendor JS -->
	<script src="{{ asset('assets/js/vendors.min.js') }}"></script>
	<script src="{{ asset('assets/js/pages/chat-popup.js') }}"></script>
    <script src="{{ asset('assets/icons/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/datatable/datatables.min.js') }}"></script>
	<script src="{{ asset('assets/vendor_components/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/sweetalert/jquery.sweet-alert.custom.js') }}"></script>

	<!-- Deposito Admin App -->
	<script src="{{ asset('assets/js/template.js') }}"></script>

	<script src="{{ asset('assets/js/pages/data-table.js') }}"></script>
@endsection
