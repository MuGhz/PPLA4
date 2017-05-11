@extends('layouts.app')

@section('content')
@foreach($detailClaim as $key => $value)
@php
$id = $value -> id;
$token = $value -> claim_data_id ;
$status = $value -> claim_status;
$status = ($status==1?"Sent":($status==2?"Approved":($status==3?"Reported":($status==4?"Disbursed":($status==5?"Closed":"Rejected")))));
$alasan = $status=="Rejected"?$value->alasan_reject:"";
$isSelf = Auth::id() == $value->claimer_id;

$action = Auth::user()["role"];
$isFinished = ($status == "Closed") || ($status == "Reported") || ($status == "Disbursed");

$buttonLabel=
(($isSelf && !$isFinished)?"Cancel claim"
:((!$isSelf && $status=="Sent" && ($action=="approver"))?"Reject claim"
:((!$isSelf && $status=="Approved" && ($action=="finance"))?"Reject claim":"nothing")));

$action="";
if($buttonLabel == "Cancel claim") {
  $action = URL::to('home/claim/delete');
} else if ($buttonLabel == "Reject claim"){
  $action = URL::to('home/claim/reject');
}

$namaClaimer  = App\User::find($value->claimer_id)->name;
$namaApprover = App\User::find($value->approver_id)->name;
$namaFinance  = App\User::find($value->finance_id)->name;

@endphp
@endforeach
@if(session('error') != null)
	 <div class="panel panel-danger">
      <div class="panel-heading">ERROR</div>
      <div class="panel-body"><b>{{session('error')}}</b></div>
    </div>
@endif
<div class="container">
	<center><h3 class="box-title">Detail Claim</h3></center>
	<div id="detailClaim">
	</div>
	<div class="row">

	<div class="form-group col-md-6">
        <div class="form-group col-md-4">
      @if ($buttonLabel != "nothing")
        @if($buttonLabel == "Cancel claim")
           <a class="btn btn-primary btn-block btn-danger" href="{{$action}}/{{$id}}">{{$buttonLabel}}</a>
        @elseif($buttonLabel =="Reject claim")
          <a class="btn btn-primary btn-block btn-danger" id='show'>{{$buttonLabel}}</a>
        @endif
      @endif
		</div>
		<div class="form-group col-md-4">
      @if($isSelf && ($value->claim_status == 4))
	        <button class="btn btn-primary btn-block">Upload proof</button>
      @elseif(($value->claim_status == 2) && (Auth::user()["role"] == "finance"))
          <a href="{{URL::to('/home/finance/buy/'.$id)}}" class="btn btn-primary btn-block">Beli tiket</a>
      @elseif(($value->claim_status == 1)&&(Auth::user()["role"]=="approver"))
         <a href="{{URL::to('/home/approve/'.$id)}}" class="btn btn-primary btn-block">Approve pemesanan</a>
      @endif
		</div>
		<div class="form-group col-md-4">
	        <a class="btn btn-primary btn-block"href="{{URL::to('/home')}}">Return</a>
		</div>
    </div>

    </div>

    <div id='reject' hidden="true">
      <form action="{{$action}}/{{$id}}" method="post" class="container col-md-offset-2">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <div class="row">
            <div class="form-group col-md-8">
              <label>Alasan: </label>
              <input class="form-control" name="alasan_reject" placeholder="Jelaskan mengapa anda mereject claim" id="alasan_reject" required></textarea>
            </div>
          </div>
          <div class="form-group col-md-6">
            <div class="form-group col-md-6">
               <input type="submit" class="btn btn-primary btn-block" value="Submit">
        		</div>
          </div>
      </form>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function(){
        $("#show").click(function(){
            $("#reject").toggle();
        });
    });
	$.post("{{action('OrderController@getOrder')}}",{_token: "{{csrf_token()}}",token:"{{$token}}",id:"{{$id}}"}).done(function(e){
		e = JSON.parse(e);
		var description = e.description;
		e = e.api_data;
        console.log(e);
		temp = "<center><div class='row'>";
			temp+= "<div class='col-md-3'>";
			temp+= "</div>";
			temp+= "<div class='col-md-6 panel panel-default container' align='left'>";
			temp+= "<br>";
			temp+= "<p>Nama klaimer: " +'{{$namaClaimer}}'+"</p>";
			temp+= "<p>Nama approver: "+'{{$namaApprover}}'+"</p>";
			temp+= "<p>Nama finance: " +'{{$namaFinance}}'+"</p>";
			temp+= "<br>";
			temp+= "<p>Status klaim: " +'{{$status}}'+"</p>";
            @if ($status == "Rejected")
                temp+= "<p>Ditolak dengan alasan: " +'{{$alasan}}'+"</p>";
            @endif
			temp+= "<hr>";
			temp+= "<p>Tipe: "+e.myorder.data[0].order_type+"</p>";
			temp+= "<p>"+e.myorder.data[0].order_name+" - "+e.myorder.data[0].order_name_detail+"</p>";
			temp+= "<p>Nomor kamar: "+e.myorder.data[0].detail.room_id+"</p>";
  				temp+= "<div class='form-group col-md-6'>";
				temp+= "<p>Dewasa: "+e.myorder.data[0].detail.adult+"</p>";
				temp+= "</div>";
				temp+= "<div class='form-group col-md-6'>";
				temp+= "<p>Anak-anak: "+e.myorder.data[0].detail.child+"</p>";
				temp+= "</div>";
				temp+= "<div class='form-group col-md-6'>";
				temp+= "<p>Dari: "+e.myorder.data[0].detail.startdate+"</p>";
				temp+= "</div>";
				temp+= "<div class='form-group col-md-6'>";
				temp+= "<p>Sampai: "+e.myorder.data[0].detail.enddate+"</p>";
				temp+= "</div>";
			temp+= "<p>Description:</p>";
			temp+= "<p>"+description+"</p>";
			temp+= "<hr>";
			temp+= "<p>Total: Rp. "+e.myorder.total	+"</p>";
			temp+= "<p>Order Expired: "+e.myorder.data[0].order_expire_datetime+"</p>";
			temp+= "<br>";
			temp+= "</div>";
		temp+= "</div></center>";
		temp+= "<div class='col-md-3'>";
		temp+= "</div>";

		$("#detailClaim").html(temp);
	 });

</script>
@endsection
