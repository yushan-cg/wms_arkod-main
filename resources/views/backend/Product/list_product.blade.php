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
                                    <td>{{ $row->partner_name }}</td>
									<td>{{ $row->SKU }}</td>
									<td>{{ $row->product_name }}</td>
									<td>{{ $row->product_desc }}</td>
									<td>{{ $row->expired_date }}</td>
									<td>
                                        <img src="{{ asset('storage/Image/' . $row->Img) }}" width="50" height="50">
                                    </td>
                                    @if (Auth::user()->role == 1)
										<td>
											<!-- Add any action buttons or links here -->
                    						<a href="{{ URL::to('/edit_product/' . $row->id) }}" class="btn btn-primary btn-sm">Edit</a>
											<button type="button" class="btn btn-danger btn-sm" onclick="event.preventDefault(); document.getElementById('delete-product-form-{{ $row->id }}').submit();">
												Delete
											</button>
											<form id="delete-product-form-{{ $row->id }}" action="{{ route('delete_product', $row->id) }}" method="POST" style="display: none;">
												@csrf
												@method('DELETE')
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
