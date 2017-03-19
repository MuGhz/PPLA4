@extends('layouts.app')

@section('content')
<div class="container">
  <form action="" method="POST">
    <div class="row">
      <div class="form-group col-md-8">
        <label>Kota: </label>
        <input class="form-control" name="city" placeholder="Nama Kota atau Tempat Terkenal">
      </div>
    </div>
    <div class="row">
      <div class="form-group col-md-8">
        <label>Check in: </label>
        <input class="form-control" name="in">
      </div>
    </div>
    <div class="row">
      <div class="form-group col-md-8">
        <label>Check out: </label>
        <input class="form-control" name="out">
      </div>
    </div>
    <div class="row">
      <div class="form-group col-md-8">
        <div class="form-group col-md-4">
          <label>Kamar</label>
          <select class="form-control" name="room">
            @for($i = 1; $i < 10;$i++)
            <option value="{{$i}}">{{$i}}</option>
            @endfor
          </select>
        </div>
          <div class="form-group col-md-4">
            <label>Dewasa</label>
            <select class="form-control" name="adult">
              @for($i = 1; $i < 10;$i++)
              <option value="{{$i}}">{{$i}}</option>
              @endfor
            </select>
          </div>
            <div class="form-group col-md-4">
              <label>Anak</label>
              <select class="form-control" name="child">
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
</div>
@endsection
@section('js')
  <script>
    $("#submit").click(function() {
      $.post("{{action(OrderController@getResult)}}", {data~~, _token: "{{csrf_token()}}"}).done(function(e){
          // Display the returned data in browser
          //console.log(data.result);
          e = JSON.parse(e);
      });
    });
  </script>
@endsection
