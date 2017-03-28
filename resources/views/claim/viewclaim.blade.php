@extends('layouts.app')

@section('content')
@foreach($detailClaim as $key => $value)
@php
$token = $value -> claim_data_id ; 
@endphp
@endforeach

<div class="container">
	<center><h3 class="box-title">Detail Claim</h3></center>
	<div class="detail">
	</div>
	<div class="row">
      <div class="form-group col-md-11">
        <div class="form-group col-md-4">
			<button class="btn btn-primary btn-block">Cancel claim</button>
		</div>
		<div class="form-group col-md-4">
	        <button class="btn btn-primary btn-block">Upload proof</button>
		</div>
		<div class="form-group col-md-4">
	        <button class="btn btn-primary btn-block">Return</button>
		</div>
      </div>
    </div>
</div>
@endsection

@section('js')


@endsection