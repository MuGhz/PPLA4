<?php
    $secure = App::environment('production') ? true : NULL;
?>
@extends('layouts.app')

@section('content')
<div class="container">
    <form action="{{action('OrderController@bookPesawat')}}">
        <h2>Flight Booking</h2>
        <hr/>
        <input type="text" hidden value="{{$req->session()->get('flight_id')}}" name="flight_id"/>
        <input type="text" hidden value="{{$req->session()->get('ret_flight_id')}}" name="ret_flight_id"/>
        <input type="text" hidden value="$req->session()->get('token')" name="token" />
        <label>Contact Person</label>
        <div class="form-group">
            <div class="row">
                <div class="col-md-3">
                    <label>Salutation</label>
                    <select name="conSalutation" class="form-control">
                        <option value="Mr.">Mr.</option>
                        <option value="Mrs.">Mrs.</option>
                        <option value="Ms.">Ms.</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>First Name:</label>
                    <input name="conFirstName" type="text" class="form-control"/>
                </div>
                <div class="col-md-3">
                    <label>Last Name:</label>
                    <input name="conLastName" type="text" class="form-control"/>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <label>Phone Number</label>
                    <input type="text" name="conPhone" class="form-control"/>
                </div>
                <div class="col-md-3">
                    <label>Email</label>
                    <input type="email" name="conEmailAddress" class="form-control"/>
                </div>
            </div>
        </div>
        <hr/>
        <label>Konfirmasi kembali jumlah penumpang</label>
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
        <div class="row form-group">
            <h3>Adult Information :</h3>
            <div class="adult_info">
                <div class="row">
                    <div class="col-md-3">
                        <label>Salutation</label>
                        <select name="titlea[]" class="form-control">
                            <option value="Mr.">Mr.</option>
                            <option value="Mrs.">Mrs.</option>
                            <option value="Ms.">Ms.</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>First Name</label>
                        <input name="firtnamea[]" required type="text" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Last Name</label>
                        <input name="lastnamea[]" required type="text" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Date of Birth</label>
                        <input type="date" name="birthdatea[]" required class="form-control datepicker">
                    </div>
                </div>
            </div>
            <div class="child_info">

            </div>
            <div class="infant_info">

            </div>
        </div>
        <hr/>
        <div class="row">
            <button type="submit" class="btn btn-primary btn-block btn-lg">Submit</button>
        </div>
    </form>
</div>
@endsection
@section('js')
    <script>
        $(".datepicker").datepicker({
          changeMonth: true,
          changeYear: true,
          dateFormat: "yy-mm-dd"
        });
    </script>
@endsection
