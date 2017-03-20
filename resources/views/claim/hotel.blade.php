@extends('layouts.app')

@section('content')
<div class="container">
  <form action="" method="POST">
    <div class="row">
      <div class="form-group col-md-8">
        <label>Kota: </label>
        <input class="form-control" name="city" placeholder="Nama Kota atau Tempat Terkenal" id="city">
      </div>
    </div>
    <div class="row">
      <div class="form-group col-md-8">
        <label>Check in: </label>
        <input class="form-control" name="in" id="in">
      </div>
    </div>
    <div class="row">
      <div class="form-group col-md-8">
        <label>Check out: </label>
        <input class="form-control" name="out" id="out">
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
  <div class="results">

  </div>
</div>
@endsection
@section('js')
  <script>
    var token = "";
    $("#submit").click(function() {
      $.post("{{action('OrderController@getToken')}}", { _token: "{{csrf_token()}}"}).done(function(e){
          // Display the returned data in browser
          //console.log(data.result);
          e = JSON.parse(e);
          token = e.token;
          var ins = $("#in").val();
          var out = $("#out").val();
          var room = $("#room").val();
          var city = $("#city").val();
          var adult = $("#adult").val();
          var child = $("#child").val();
          $.post("{{action('OrderController@getHotel')}}", {in:ins,out:out,room:room,city:city,adult:adult,child:child,token:token,_token:"{{csrf_token()}}"}).done(function(e){
              // Display the returned data in browser
              //console.log(data.result);
              e = JSON.parse(e);
              console.log(e['results']['result']);
              var temp = "";
              e.results.result.forEach(function(f)  {
                temp+="<div class='col-md-6'>";
                temp+= "";
                temp+="</div>";
              });
              $(".results").html(temp);
          });
      });
    });
  </script>
@endsection
