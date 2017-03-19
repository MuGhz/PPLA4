@extends('layouts.app')

@section('content')
<div class="container"><form action="" method="">
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
    <button class="btn btn-primary btn-block" type="submit">Cari Hotel</button>
  </div>
</form>
  <div class="tiket-root" data-widget="tiket-boxsearchwidget" data-businessid="22693559" data-lang="id" data-size_type="normal" data-width="800" data-height="800" data-position="middle-content" data-product_type="hotel">
  </div>
</div>
@endsection
@section('js')
  <script type="text/javascript" src="https://sandbox.tiket.com/js/new_widget/tiket_widgetframe_v3.js" async="true"></script>
@endsection
