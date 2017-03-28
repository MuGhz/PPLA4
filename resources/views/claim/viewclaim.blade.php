@extends('layouts.app')

@section('content')
@foreach($detailClaim as $key => $value)
@php
$id = $value -> id;
$token = $value -> claim_data_id ; 
$status = $value -> claim_status;
@endphp
@endforeach

<div class="container">
	<center><h3 class="box-title">Detail Claim</h3></center>
	<div class="detail">
	
	</div>
	<div class="row">
      <div class="form-group col-md-11">
        <div class="form-group col-md-4">
			<a class="btn btn-primary btn-block btn-danger" href="{{URL::to('home/claim/delete')}}/{{$id}}">Cancel claim</a>
		</div>
		<div class="form-group col-md-4">
	        <button class="btn btn-primary btn-block">Upload proof</button>
		</div>
		<div class="form-group col-md-4">
	        <a class="btn btn-primary btn-block"href="{{URL::to('home')}}">Return</a>
		</div>
      </div>
    </div>
</div>
@endsection

@section('js')
<script>
	 $.post("{{action('OrderController@getOrder')}}",{$token}).done(function(e){
		e = JSON.parse(e);
        console.log(e);
		temp = "<div class='row'>";
			temp+= "<p>Tipe :"+e.myorder.data.order_type+"</p>";
			temp+= "<p>"+e.myorder.data.order_name+" - "+e.myorder.data.order_name_detail+"</p>";
			temp+= "<p>Nomor kamar :"+e.myorder.data.detail.room_id+"</p>";
            temp+="<img src='"+e.myorder.data.order_photo+"'>";
			temp+= "<div class='form-group col-md-8'>";
				temp+= "<div class='form-group col-md-4'>";
				temp+= "<p>Dewasa :"+e.myorder.data.detail.adult+"</p>";
				temp+= "</div>";
				temp+= "<div class='form-group col-md-4'>";
				temp+= "<p>Anak-anak :"+e.myorder.data.detail.child+"</p>";
				temp+= "</div>";
			temp+= "</div>";
			temp+= "<div class='form-group col-md-8'>";
				temp+= "<div class='form-group col-md-6'>";
				temp+= "<p>Dari :"+e.myorder.data.detail.startdate+"</p>";
				temp+= "</div>";
				temp+= "<div class='form-group col-md-6'>";
				temp+= "<p>Sampai :"+e.myorder.data.detail.enddate+"</p>";
				temp+= "</div>";
			temp+= "</div>";
			temp+="<p>Status : "+$status+"</p>";
			temp+="<p>Total : Rp. "+e.myorder.total	+"</p>";
		temp+= "</div>";
		$('#detail).html(temp);
	 });
</script>
@endsection