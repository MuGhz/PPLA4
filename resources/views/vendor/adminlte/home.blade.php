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
										<th>No.</th>
										<th>Type</th>
										<th>Created</th>
										<th>Last Modified</th>
										<th>Claim Status</th>
									</tr>
								</thead>
								<tbody>
									@php
									$i= 1;
									@endphp
									@foreach($allClaim as $key => $value)
									@php
									$cType=$value->claim_type ;
									$cType= $cType==1?"Hotel":"Pesawat";
									$status=$value->claim_status;
									$status = ($status==1?"Sent":($status==2?"Approved":($status==3?"Reported":($status==4?"Disbursed":($status==5?"Closed":"Rejected")))));
									@endphp
									<tr>
										<td name="no" id="No">{{ $i }}</td>
										<td name="claim_type" id="claim_type">{{ $cType}}</td>
										<td name="created_at" id="created_at">{{ $value->created_at }}</td>
										<td name="updated_at" id="updated_at">{{ $value->updated_at }}</td>
										<td name="claim_status" id="claim_status">{{ $status }}</td>
										<td><a href="{{URL::to('home/claim/detail')}}/{{$value->id}}" class="btn btn-default btn-flat" >Detail</a></td>
									</tr>
									@php
									$i++;
									@endphp
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
