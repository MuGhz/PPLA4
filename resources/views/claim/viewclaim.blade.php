@extends('layouts.app')

@section('content')
<div class="container">
	<center><h3 class="box-title">Detail Claim</h3></center>
	<div class="row">
      <div class="form-group col-md-8">
        <label>Type: </label>
      </div>
    </div>
	<div class="row">
      <div class="form-group col-md-8">
        <label>Title: </label>
      </div>
    </div>
	<div class="row">
      <div class="form-group col-md-8">
        <label>Description: </label>
      </div>
    </div>
	<div class="row">
      <div class="form-group col-md-8">
        <label>Date: </label>
      </div>
    </div>
	<div class="row">
      <div class="form-group col-md-8">
        <label>From - To: </label>
      </div>
    </div>
	<div class="row">
      <div class="form-group col-md-8">
        <label>Passenger: </label>
      </div>
    </div>
	<div class="row">
      <div class="form-group col-md-8">
        <label>Status: </label>
      </div>
    </div>
	<div class="row">
      <div class="form-group col-md-8">
        <label>Notes: </label>
      </div>
    </div>
	<div class="row">
      <div class="form-group col-md-8">
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

