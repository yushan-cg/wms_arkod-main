@extends('backend.Layouts.app')
@section('content')
<title>Delivery Order List</title>
<style>
.fixed-row {
    width: 50%; /* Adjust width as needed */
}
.fixed-col {
    width: 50%; /* Adjust width as needed */
}

.fixed-col img {
    width: 100%; /* Make image fill the column */
    height: auto; /* Maintain aspect ratio */
    max-height: 150px; /* Maximum height of images */
}

.text-danger {
	border: none;
	padding: 0;
	background: none;
}
</style>

<div>
	<!-- Content Header -->
	<x-content-header title="Delivery Order List">
		<button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addWaybillModal">
			Add New Waybill
		</button>
		{{-- <x-modal-form 	
			modalId="addWaybillModal" 
			modalTitle="Add Waybill" 
			formId="addWaybillForm" 
			formAction="{{ route('insert_product') }}" 
			submitButton="Add this product">
			@include('backend.product.create_product')
		</x-modal-form> --}}
	</x-content-header>
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
							<th>No.</th>
                            <th>Waybill No.</th>
							<th>Customer/Company</th>
							<th>Status</th>
                            <th>Documents</th>
							<th>Date updated</th>
							@if (Auth::user()->role == 1)
							<th>Action</th>
							@endif
							</tr>
						</thead>
						<tbody>
						@foreach ($list as $index => $row)
							<tr class="fixed-row fixed-col">
								<td>{{ $loop->iteration }}</td>
								<td>{{ $row->waybillno }}</td>
								<td>{{ $row->partner_name }}</td>
								<td>{{ $row->status }}</td>
								<td>{{ $row->product_name }}</td>
								<td>{{ $row->product_desc }}</td>
								<td>{{ $row->expired_date }}</td>
								<td>
									<img src="{{ $row->Img }}" alt="{{ $row->product_name }}" width="50" height="50">
								</td>
								@if (Auth::user()->role == 1)
									<td>
										<!-- Add any action buttons or links here -->
										<button type="button" class="btn btn-primary" style="width: 100px;" data-bs-toggle="modal" data-bs-target="#editProductModal{{ $row->id }}">
											Edit
										</button>
										<!-- Modal for editing product -->
										<x-modal-form 
											modalId="editProductModal{{ $row->id }}" 
											modalTitle="Edit Product" 
											formId="editProductForm{{ $row->id }}" 
											formAction="{{ route('update_product', $row->id) }}" 
											submitButton="Save edit">
											<form id="editProductForm{{ $row->id }}" action="{{ route('update_product', $row->id) }}" method="POST" enctype="multipart/form-data">
												@csrf
												@method('PATCH')
												<!-- Form fields -->
												<div class="modal-body">
													@include('backend.product.edit_product', ['product' => $row])
												</div>
										</x-modal-form>
										<!-- end modal -->
										<button type="button" class="btn btn-danger" style="width: 75px;" onclick="confirmDelete({{ $row->id }});">
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

	<script>
		function confirmDelete(id) {
			if (confirm('Are you sure you want to ?')) {
				document.getElementById('delete-product-form-' + id).submit();
			}
		}
	</script>
@endsection
