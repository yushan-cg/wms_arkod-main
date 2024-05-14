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
								<th>#</th>
								<th>Customer/Company</th>
								<th>SKU</th>
								<th>Product Name</th>
								<th>Description</th>
								<th>Expired Date</th>
								@if (Auth::user()->role == 1 || Auth::user()->role == 2)
								{{-- <th>Location Code</th> --}}
								@endif
								<th>Product Image</th>
								@if (Auth::user()->role == 1)
                                <th>Action</th>
								@endif
								</tr>
							</thead>
							<tbody>
                            @foreach ($list as $index => $row)

                                <tr>
									<td>{{ $loop->iteration }}</td> <!-- Billing number -->
                                    <td>{{ $row->CustomerName }}</td>
									<td>{{ $row->SKU }}</td>
									<td>{{ $row->ProductName }}</td>
									<td>{{ $row->ProductLabel }}</td>
									<td>{{ $row->ProductExpiredDate }}</td>
                                    @if (Auth::user()->role == 1 || Auth::user()->role == 2)
                                        {{-- <td>{{ $row->location_code ?? '-' }}</td>
                                        <td>{{ $row->location_codes ?? '-' }}</td> --}}
                                    @endif
									<td>
                                        <img src="{{ asset('storage/Image/' . $row->ProductImg) }}" width="50" height="50">
                                    </td>
                                    @if (Auth::user()->role == 1)
										<td>
											<!-- Add any action buttons or links here -->
                    						<a href="{{ URL::to('/edit_product/' . $row->ProductID) }}">Edit</a>
											<form action="{{ route('delete_product', $row->ProductID) }}" method="POST" style="display:inline;">
												@csrf
												@method('DELETE')
												<button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
											</form>
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
