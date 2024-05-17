@props(['title', 'buttonRoute', 'buttonText'])

<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-3">
		  	  <div class="col-12 d-flex justify-content-between">
				<h1 class="page-title">{{ $title }}</h1>
		  		<a href="{{ $buttonRoute }}" class="btn btn-success">{{ $buttonText }}</a>
		  	  </div>
		    </div>
		</div>
</section>