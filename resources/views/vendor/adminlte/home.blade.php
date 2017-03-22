@extends('adminlte::layouts.app')

@section('htmlheader_title')
	{{ trans('adminlte_lang::message.home') }}
@endsection


@section('main-content')
	<div class="container-fluid spark-screen">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">

				<!-- Default box -->
				<div class="box">
					<div class="box-header with-border">
						<h3 class="box-title">List of My Claims</h3>
						<!--
						<div class="box-tools pull-right">
							<button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
								<i class="fa fa-minus"></i></button>
							<button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
								<i class="fa fa-times"></i></button>
						</div>
						-->
						<div class="panel-body">
							<table class="table table-striped" id="domestic">
								<thead>
									<tr>
										<th>ID</th>
										<th>Claim Type</th>
										<th>Claim Data</th>
										<th>Created At</th>
										<th>Updated At</th>
										<th>Claim Status</th>
									</tr>
								</thead>
								<tbody>
									@foreach($allClaim as $key => $value)
									<tr>
										<td>{{ $value->id }}</td>
										<td>{{ $value->claim_type }}</td>
										<td>{{ $value->claim_data_id }}</td>
										<td>{{ $value->created_at }}</td>
										<td>{{ $value->updated_at }}</td>
										<td>{{ $value->claim_status }}</td>
										<td><button class="btn btn-default btn-flat" >Detail</button></td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>

					</div>
					<div class="box-body">
						
					</div>
					<!-- /.box-body -->
				</div>
				<!-- /.box -->

			</div>
		</div>
	</div>
@endsection
