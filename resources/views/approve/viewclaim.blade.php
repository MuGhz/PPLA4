@extends('layouts.app')

@section('content')
@foreach($detailClaim as $key => $value)
@php
$id = $value -> id;
$token = $value -> claim_data_id ;
$status = $value -> claim_status;
$status = ($status==1?"Sent":($status==2?"Approved":($status==3?"Reported":($status==4?"Disbursed":($status==5?"Closed":"Rejected")))));
@endphp
@endforeach

<div class="container">
	<center><h3 class="box-title">Detail Claim</h3></center>
	<div id="detailClaim">

	</div>
	<div class="row">

	<div class="form-group col-md-6">
        <div class="form-group col-md-4">
			<a class="btn btn-primary btn-block btn-danger" href="{{URL::to('home/reject')}}/{{$id}}">Reject claim</a>
		</div>
		<div class="form-group col-md-4">
	        <a class="btn btn-primary btn-block btn-warning" href="{{URL::to('home/approve')}}/{{$id}}">Approve claim</a>
		</div>
		<div class="form-group col-md-4">
	        <a class="btn btn-primary btn-block"href="{{URL::to('home/approver/received')}}">Return</a>
		</div>
      </div>

    </div>
</div>
@endsection

@section('js')
<script>
	 $.post("{{action('OrderController@getOrder')}}",{_token: "{{csrf_token()}}",token:"{{$token}}"}).done(function(e){
		e = JSON.parse(e);
        console.log(e);
		temp = "<center><div class='row'>";
			temp+="<div class='col-md-3'>";
			temp+= "</div>";
			temp+="<div class='col-md-6 panel panel-default container' align='left'>";
			temp+= "<br><p>Tipe :"+e.myorder.data[0].order_type+"</p>";
			temp+= "<p>"+e.myorder.data[0].order_name+" - "+e.myorder.data[0].order_name_detail+"</p>";
			temp+= "<p>Nomor kamar :"+e.myorder.data[0].detail.room_id+"</p>";
  				temp+= "<div class='form-group col-md-6'>";
				temp+= "<p>Dewasa :"+e.myorder.data[0].detail.adult+"</p>";
				temp+= "</div>";
				temp+= "<div class='form-group col-md-6'>";
				temp+= "<p>Anak-anak :"+e.myorder.data[0].detail.child+"</p>";
				temp+= "</div>";
				temp+= "<div class='form-group col-md-6'>";
				temp+= "<p>Dari :"+e.myorder.data[0].detail.startdate+"</p>";
				temp+= "</div>";
				temp+= "<div class='form-group col-md-6'>";
				temp+= "<p>Sampai :"+e.myorder.data[0].detail.enddate+"</p>";
				temp+= "</div>";
			temp+="<p>Status : "+'{{$status}}'+"</p>";
			temp+="<p>Total : Rp. "+e.myorder.total	+"</p>";
			temp+="<p>Order Expired : "+e.myorder.data[0].order_expire_datetime+"</p><br>";
			temp+= "</div>";
		temp+= "</div></center>";
		temp+="<div class='col-md-3'>";
		temp+= "</div>";

		$("#detailClaim").html(temp);
	 });
</script>
@endsection
