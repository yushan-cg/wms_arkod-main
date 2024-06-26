@extends('backend.layouts.app')
@section('content')
<!-- Content Header (Page header) -->
<title>Customer List</title>
		<div class="content-header">
			<div class="d-flex align-items-center">
				<div class="me-auto">
					<h4 class="page-title">Data Tables</h4>
					<div class="d-inline-block align-items-center">
						<nav>
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ URL::to('/home') }}"><i class="mdi mdi-home-outline"></i></a></li>
								<li class="breadcrumb-item" aria-current="page">List</li>
								<li class="breadcrumb-item active" aria-current="page">Customer List</li>
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
				<div class="box-header with-border">
				  <h3 class="box-title">Customer List</h3>
				</div>
				<!-- /.box-header -->
				<div class="box-body">
					<div class="table-responsive">
					  <table id="example1" class="table table-bordered table-striped">
						<thead>
                        <tr>
                        <th>#</th>
                        <th>Customer/Company Name</th>
						<th>Address</th>
						<th>Contact</th>
						<th>Email</th>
						<th>Attention</th>
                        <th>Action</th>
                    </tr>
						</thead>
						<tbody>
                        @foreach($customers as $customer)
                        <tr>
                            <td>{{ $customer->CustomerID }}</td>
                            <td>{{ $customer->CustomerName }}</td>
							<td>{{ $customer->CustomerAddr }}</td>
							<td>{{ $customer->CustomerTel }}</td>
							<td>{{ $customer->CustomerEmail }}</td>
							<td>{{ $customer->CustomerAttn }}</td>
                            <td>
                                <a href="#" class="btn btn-primary btn-sm">Check Invoice</a>
                            </td>
                        </tr>
                    @endforeach
						</tbody>
					  </table>
					</div>
				</div>
				<!-- /.box-body -->
			  </div>
			  <!-- /.box -->
			</div>
			<!-- /.col -->
		  </div>
		  <!-- /.row -->
		</section>
		<!-- /.content -->
@endsection
@section('page content overlay')
	<!-- Page Content overlay -->
	
	
	<!-- Vendor JS -->
	<!-- Vendor JS -->
	<script src="{{ asset('assets/js/vendors.min.js') }}"></script>
	<script src="{{ asset('assets/js/pages/chat-popup.js') }}"></script>
    <script src="{{ asset('assets/icons/feather-icons/feather.min.js') }}"></script>
	<script src="{{ asset('assets/vendor_components/datatable/datatables.min.js') }}"></script>
	
	<!-- Deposito Admin App -->
	<script src="{{ asset('assets/js/template.js') }}"></script>
	
	<script src="{{ asset('assets/js/pages/data-table.js') }}"></script>
	

@endsection
