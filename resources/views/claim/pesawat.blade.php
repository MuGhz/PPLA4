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
             <input type="text" class="form-control" name="d" id="departure"/>
         </div>
         <div class="col-md-6 form-group">
             <label>Bandara Tujuan</label>
             <input type="text" class="form-control" name="a" id="arrival"/>
         </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-8">
          <div class="col-md-6 form-group">
            <label>Tanggal Berangkat</label>
            <input class="form-control datepicker" name="date" id="date">
        </div>
          <div class="form-group col-md-6">
            <label>Tanggal Kembali: </label>
            <input class="form-control datepicker" name="ret_date" id="ret_date">
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
                <select class="form-control" name="infant" id="infant">
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
      <button class="btn btn-primary btn-block" type="button" id="submit">Cari Penerbangan</button>
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

    $('#departure').devbridgeAutocomplete({
        serviceUrl: "{{url('/api/airport')}}",
    });

    $('#arrival').devbridgeAutocomplete({
        serviceUrl: "{{url('/api/airport')}}",
    });
    
    function show(id, value) {
      console.log('called');
      $("#"+id).css('display', (value ? 'block' : 'none'));
    }
    $('#1').click(function(){
        $('#ret_date').attr('disabled',false);
    });
    $('#2').click(function(){
        $('#ret_date').attr('disabled',true);
    });

    // Kode dibawah bagian mesen token dan tiket pesawatnya
    var token = "";
    $("#submit").click(function() {
      $.post("{{action('OrderController@getToken')}}", { _token: "{{csrf_token()}}"}).done(function(e){
          // Display the returned data in browser
          e = JSON.parse(e);
          token = e.token;
          localStorage.token = token;
          getFlight(1); // Gak ada pagination?
      });
    });

    // TODO: rapihin
    function getFlight(page) {
      show('loading',true);
      var departure = $("#departure").val();
      var arrival = $("#arrival").val();
      var date = $("#date").val();
      var ret_date = $("#ret_date").val();
      if (document.getElementById('ret_date').disabled) {
        ret_date = false;
      }
      var adult = $("#adult").val();
      var child = $("#child").val();
      var infant = $("#infant").val();

      // Buat ngehandle wait forever pas date-nya kosong
      if(!date) {
        show('loading',false);
        $('#error').modal('show');
        return;
      }

      $.post("{{action('OrderController@getFlight')}}", {d:departure,a:arrival,date:date,ret_date:ret_date,adult:adult,child:child,infant:infant,token:token,page:page,_token:"{{csrf_token()}}"}).done(function(e){
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
          console.log(e['departures']['result']);
          
          var temp = "";
          // Departure flight
          if(typeof e.departures =='undefined' || e.departures.result.length==0)  {
            temp += "<h2>Tidak ada penerbangan berangkat</h2>";
          } else {
            temp = "<div class='col-md-12 row row-eq-height'>";
            temp = "<h2>Penerbangan berangkat</h2>";
            var length = e.departures.result.length;
            e.departures.result.forEach(function(f,i)  {
              if(i%2 == 0)
                temp+="<div class='row row-eq-height'>";
                temp+="<div class='col-md-6 panel panel-default container'>";
                temp+="<h2>"+f.airlines_name+"</h2>";
                temp+="<img src='"+f.image+"'>";
                temp+="<p>Harga  : "+f.price_value+"</p>";
                temp+="<p>Waktu Berangkat : "+f.departure_flight_date+"</p>";
                temp+="<p>Waktu Sampai : "+f.arrival_flight_date+"</p>";
                temp+="<p>Transit : "+f.stop+"</p>";
                temp+="<div class='form-group'>";
                temp+="<button class='btn btn-success' onclick=\"detail('"+f.business_uri+"')\">detail</button>"
                temp+="</div>";
              if(i%2 != 0 || i == length-1)
                temp+="</div>";
              temp+="</div>";
              });
              temp+="</div>";
            }
            // Return flight
          if(typeof e.returns =='undefined' || e.returns.result.length==0) {
            temp += "<h2>Tidak ada penerbangan pulang</h2>";
          } else {
            temp += "<div class='col-md-12 row row-eq-height'>";
            temp += "<h2>Penerbangan pulang</h2>";
            length = e.returns.result.length;
            e.departures.result.forEach(function(f,i)  {
              if(i%2 == 0)
                temp+="<div class='row row-eq-height'>";
                temp+="<div class='col-md-6 panel panel-default container'>";
                temp+="<h2>"+f.airlines_name+"</h2>";
                temp+="<img src='"+f.image+"'>";
                temp+="<p>Harga  : "+f.price_value+"</p>";
                temp+="<p>Waktu Berangkat : "+f.departure_flight_date+"</p>";
                temp+="<p>Waktu Sampai : "+f.arrival_flight_date+"</p>";
                temp+="<p>Transit : "+f.stop+"</p>";
                temp+="<div class='form-group'>";
                temp+="<button class='btn btn-success' onclick=\"detail('"+f.business_uri+"')\">detail</button>"
                temp+="</div>";
              if(i%2 != 0 || i == length-1)
                temp+="</div>";
              temp+="</div>";
              });
            temp+="</div>";
          }
            $(".results").html(temp);
        });
    }

    // TODO: belum kelar
    function detail(uri) {
      show('loading',true);
      $.post("{{action('OrderController@getHotelDetail')}}", {target:uri,token:localStorage.token,_token: "{{csrf_token()}}"}).done(function(e){
        show('loading',false);
        console.log(e);
        e = JSON.parse(e);
        console.log(e);
        // temp = "<div class='container'>";
        temp = "<div class='row'>";
        e.departures.result.forEach(function(f)  {
          temp+="<div class='col-md-6 items'>";
            temp+="<h2>"+f.name+"</h2>";
            temp+="<img src='"+f.image+"'>";
            temp+="<p>"+f.airlines_name+"</p>";
            temp+="<p>Harga  : "+f.price_value+"</p>";
            temp+="<p>Waktu Berangkat : "+f.simple_departure_time+"</p>";
            temp+="<p>Waktu Sampai : "+f.simple_arrival_time+"</p>";
            temp+="<p>Transit : "+f.Langsung+"</p>";
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
      var description = $("#description").val();
      show('loading',true);
      $.post("{{action('OrderController@bookHotel')}}", {description:description,target:uri,token:localStorage.token,_token: "{{csrf_token()}}"}).done(function(e){
        show('loading',false);
          if(e)
          window.location.replace("{{url('/home')}}");
      });
    }
  </script>
@endsection
