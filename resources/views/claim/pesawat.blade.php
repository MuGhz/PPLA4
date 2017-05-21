<?php
    $secure = App::environment('production') ? true : NULL;
?>
@extends('layouts.app')
@section('css')
  <style>
  #loading {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      z-index: 100;
      width: 100vw;
      height: 100vh;
      background-color: rgba(192, 192, 192, 0.5);
      background-image: url("{{asset('img/balls(1).gif',$secure)}}");
      background-repeat: no-repeat;
      background-position: center;
  }
  .items  {
    border: 1px;
  }
  </style>
@endsection

@section('content')
<div class="container">
  <h2>Booking Pesawat</h2>
    <div class="btn-group" data-toggle="buttons">
        <label class="btn btn-primary active" id="1">
            <input type="radio" name="options" id="option1" autocomplete="off" checked> Pulang pergi
        </label>
        <label class="btn btn-primary" id="2">
            <input type="radio" name="options" id="option2" autocomplete="off"> Sekali jalan
        </label>
    </div>
    <hr/>
  <form action="" method="POST" class="container col-md-offset-2">
    <div class="row">
      <div class="col-md-8">
         <div class="col-md-6 form-group">
             <label>Bandara Asal</label>
             <input type="text" class="form-control" name="airport_origin" id="origin"/>
         </div>
         <div class="col-md-6 form-group">
             <label>Bandara Tujuan</label>
             <input type="text" class="form-control" name="airport_destination" id="destination"/>
         </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-8">
          <div class="col-md-6 form-group">
            <label>Tanggal Berangkat</label>
            <input class="form-control datepicker" name="depart" id="depart">
        </div>
          <div class="form-group col-md-6">
            <label>Tanggal Kembali: </label>
            <input class="form-control datepicker" name="return" id="return">
          </div>
      </div>
    </div>
    <div class="row">
      <div class="form-group col-md-8">
          <div class="form-group col-md-4">
            <label>Dewasa (> 12 tahun)</label>
            <select class="form-control" name="adult" id="adult">
              @for($i = 1; $i < 10;$i++)
              <option value="{{$i}}">{{$i}}</option>
              @endfor
            </select>
          </div>
            <div class="form-group col-md-4">
              <label>Anak (2 - 12 tahun)</label>
              <select class="form-control" name="child" id="child">
                @for($i = 0; $i < 10;$i++)
                <option value="{{$i}}">{{$i}}</option>
                @endfor
              </select>
            </div>
              <div class="form-group col-md-4">
                <label>Bayi (<= 23 bulan)</label>
                <select class="form-control" name="child" id="infant">
                  @for($i = 0; $i < 10;$i++)
                  <option value="{{$i}}">{{$i}}</option>
                  @endfor
                </select>
              </div>
      </div>
    </div>
	<div class="row">
		<div class="form-group col-md-8">
			<label>Deskripsi</label>
			<textarea class="form-control" id="description" name="description" rows="3" placeholder="Deskripsi Claim (tujuan penggunaan)"></textarea>
		</div>
	</div>
    <div class="col-md-8">
      <button class="btn btn-primary btn-block" type="button" id="submit">Cari Tiket</button>
    </div>
  </form>
  <hr>
  <div class="results container">

  </div>
  <!-- Modal -->
  <div id="detail" class="modal fade" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Detail</h4>
        </div>
        <div class="modal-body">
          <div id="det">

          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>

    </div>
  </div>
  <div id="error" class="modal fade" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-body">
          <div id="Error">
            <h2>Data Salah</h2>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <div id="loading"></div>
</div>
@endsection
@section('js')
    <script src="{{asset('js/jquery.autocomplete.min.js')}}"></script>
  <script>
    $(".datepicker").datepicker({
      changeMonth: true,
      changeYear: true,
      dateFormat: "yy-mm-dd"
    });

    function searchString()  {
        console.log('asd');
    }

    $('#origin').devbridgeAutocomplete({
        serviceUrl: "{{url('/api/airport')}}",
    });

    function show(id, value) {
      console.log('called');
      $("#"+id).css('display', (value ? 'block' : 'none'));
    }
    $('#1').click(function(){
        $('#out').attr('disabled',false);
    });
    $('#2').click(function(){
        $('#out').attr('disabled',true);
    });
    var token = "";
    $("#submit").click(function() {
      $.post("{{action('OrderController@getToken')}}", { _token: "{{csrf_token()}}"}).done(function(e){
          // Display the returned data in browser
          e = JSON.parse(e);
          token = e.token;
          localStorage.token = token;
          getTicket(1);
      });
    });

    function getTicket(page) {
      show('loading',true);
      var origin = $("#origin").val();
      var destination = $("#destination").val();
      var depart = $("#depart").val();
      var returns = $("#return").val();
      var adult = $("#adult").val();
      var child = $("#child").val();
      var infant = $("#infant").val();
      console.log(origin, destination, depart, returns, adult, child, infant)
	  // Buat ngehandle wait forever pas ins-nya kosong
	  if(!ins) {
		show('loading',false);
		$('#error').modal('show');
        return;
	  }

      $.post("{{action('OrderController@getFlight')}}", {d:origin,a:destination,date:depart,ret_date:returns,adult:adult,child:child,infant:infant,page:page,token:token,_token:"{{csrf_token()}}"}).done(function(e){
          // Display the returned data in browser
          //console.log(data.result);

          show('loading',false);
          console.log(e);
          if(e == "error")  {
            $('#error').modal('show');
            return;
          }

        });
    }

  </script>
@endsection
