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
  <h2>Booking Hotel</h2>
  <form action="" method="POST" class="container col-md-offset-2">
    <div class="row">
      <div class="form-group col-md-8">
        <label>Kota: </label>
        <input class="form-control" name="city" placeholder="Nama Kota atau Tempat Terkenal" id="city">
      </div>
    </div>
    <div class="row">
      <div class="form-group col-md-8">
        <label>Check in: </label>
        <input class="form-control datepicker" name="in" id="in">
      </div>
    </div>
    <div class="row">
      <div class="form-group col-md-8">
        <label>Check out: </label>
        <input class="form-control datepicker" name="out" id="out">
      </div>
    </div>
    <div class="row">
      <div class="form-group col-md-8">
        <div class="form-group col-md-4">
          <label>Kamar</label>
          <select class="form-control" name="room" id="room">
            @for($i = 1; $i < 10;$i++)
            <option value="{{$i}}">{{$i}}</option>
            @endfor
          </select>
        </div>
          <div class="form-group col-md-4">
            <label>Dewasa</label>
            <select class="form-control" name="adult" id="adult">
              @for($i = 1; $i < 10;$i++)
              <option value="{{$i}}">{{$i}}</option>
              @endfor
            </select>
          </div>
            <div class="form-group col-md-4">
              <label>Anak</label>
              <select class="form-control" name="child" id="child">
                @for($i = 0; $i < 10;$i++)
                <option value="{{$i}}">{{$i}}</option>
                @endfor
              </select>
            </div>
      </div>
    </div>
    <div class="col-md-8">
      <button class="btn btn-primary btn-block" type="button" id="submit">Cari Hotel</button>
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
  <script>
    $(".datepicker").datepicker({
      changeMonth: true,
      changeYear: true,
      dateFormat: "yy-mm-dd"
    });

    function show(id, value) {
      console.log('called');
      $("#"+id).css('display', (value ? 'block' : 'none'));
    }

    var token = "";
    $("#submit").click(function() {
      $.post("{{action('OrderController@getToken')}}", { _token: "{{csrf_token()}}"}).done(function(e){
          // Display the returned data in browser
          e = JSON.parse(e);
          token = e.token;
          localStorage.token = token;
          getHotel(1);
      });
    });

    function getHotel(page) {
      show('loading',true);
      var ins = $("#in").val();
      var out = $("#out").val();
      var room = $("#room").val();
      var city = $("#city").val();
      var adult = $("#adult").val();
      var child = $("#child").val();

	  // Buat ngehandle wait forever pas ins-nya kosong
	  if(!ins) {
		show('loading',false);
		$('#error').modal('show');
        return;
	  }

      $.post("{{action('OrderController@getHotel')}}", {in:ins,out:out,room:room,city:city,adult:adult,child:child,token:token,page:page,_token:"{{csrf_token()}}"}).done(function(e){
          // Display the returned data in browser
          //console.log(data.result);

          show('loading',false);
          console.log(e);
          if(e == "error")  {
            $('#error').modal('show');
            return;
          }

          e = JSON.parse(e);
          console.log(e);
          console.log(e['results']['result']);
			  if(e.results.result.length==0)  {
            $(".results").html("<h2>Tidak ada hotel</h2>");
          } else {
            var temp = "<div class='col-md-12 row row-eq-height'>";
            var length = e.results.result.length;
            e.results.result.forEach(function(f,i)  {
              if(i%2 == 0)
                temp+="<div class='row row-eq-height'>";
				temp+="<div class='col-md-6 panel panel-default container'>";
                temp+="<h2>"+f.name+"</h2>";
                temp+="<img src='"+f.photo_primary+"'>";
                temp+="<p>"+f.room_facility_name+"</p>";
                temp+="<p>Harga  : "+f.price+"</p>";
                temp+="<p>Daerah : "+f.regional+"</p>";
                temp+="<p>Rating : "+f.rating+"</p>";
                temp+="<p>Alamat : "+f.address+"</p>";
                temp+="<div class='form-group'>";
                temp+="<button class='btn btn-success' onclick=\"detail('"+f.business_uri+"')\">detail</button>"
                temp+="</div>";
              if(i%2 != 0 || i == length-1)
                temp+="</div>";
              temp+="</div>";
            });
            for(var i = 1; i <= e.pagination.lastPage; i++)  {
              temp+="<button class='btn btn-default' onclick='getHotel("+i+")'>"+i+"</button>";
            }
            temp+="</div>";
            $(".results").html(temp);
          }
        });
    }

    function detail(uri) {
      show('loading',true);
      $.post("{{action('OrderController@getHotelDetail')}}", {target:uri,token:localStorage.token,_token: "{{csrf_token()}}"}).done(function(e){
        show('loading',false);
        console.log(e);
        e = JSON.parse(e);
        console.log(e);
        // temp = "<div class='container'>";
        temp = "<div class='row'>";
        e.results.result.forEach(function(f)  {
          temp+="<div class='col-md-6 items'>";
            temp+="<p><b>"+f.room_name+"</b></p>";
            temp+="<img src='"+f.photo_url+"'><br>";
            temp+=f.room_description;
            temp+="<p>Harga : "+f.currency+" "+f.price+"</p>";
            temp+="<p>Sarapan : "+(f.with_breakfast == "1" ? "Ya" : "Tidak");
            temp+="<p>Minimum : "+f.minimum_stays+" malam</p>";
            temp+="<p>Kamar Kosong: "+f.room_available+"</p>";
            temp+="<div class='form-group'>";
              temp+="<button onclick=\"book('"+f.bookUri+"')\">Book</button>";
            temp+="</div><hr>";
          temp+="</div>";
        });
        // temp+="</div>";
        temp+="</div>";
        temp+="<div class='container'>";
        temp+="<div class='row'>"
        temp+="<p><b>Alamat</b> : "+e.general.address+"<p>"
        temp+="</div>";
        temp+="<div class='row'>";
        if(e.addinfos != null)  {
          temp+="<p><b>Informasi Tambahan</b></p>"
          e.addinfos.addinfo.forEach(function(f)  {
            temp+="<p>"+f+"</p>";
          });
        }
        temp+="<p><b>Fasilitas </b></p>";
        e.avail_facilities.avail_facilitiy.forEach(function(f) {
          temp+="<p>"+f.facility_name+"</p>"
        });
        temp+="</div>";
        temp+="</div>";
        $('#det').html(temp);
        $('#detail').modal('show');
      });
    }

    function book(uri) {
      show('loading',true);
      $.post("{{action('OrderController@bookHotel')}}", {target:uri,token:localStorage.token,_token: "{{csrf_token()}}"}).done(function(e){
        show('loading',false);
          if(e)
          window.location.replace("{{url('/home')}}");
      });
    }
  </script>
@endsection
