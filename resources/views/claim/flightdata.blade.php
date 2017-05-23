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
        <input type="text" hidden value="{{$req->session()->get('token')}}" name="token" />
        <label>Contact Person</label>
        <div class="form-group">
            <div class="row">
                <div class="col-md-3">
                    <label>Salutation</label>
                    <select name="conSalutation" class="form-control">
                        <option value="Tuan">Tuan</option>
                        <option value="Nyonya">Nyonya</option>
                        <option value="Nona">Nona</option>
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
        <h3>Order Information : </h3>
        @php
            $adult = $req->session()->get('adult');
            $child = $req->session()->get('child');
            $infant = $req->session()->get('infant');
        @endphp
            <p>Adult : {{$adult}}</p>
            <p>Child : {{$child}}</p>
            <p>Infant : {{$infant}}</p>
        <hr/>
        <div class="row form-group">
            <div class="adult_info">
                <h3>Adult Information :</h3>
                <div class="row">

                    @for($i = 0; $i < $adult; $i++)
                        <div class="col-md-3">
                            <label>Salutation</label>
                            <select name="titlea[]" class="form-control">
                                <option value="Tuan">Tuan</option>
                                <option value="Nyonya">Nyonya</option>
                                <option value="Nona">Nona</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>First Name</label>
                            <input name="firstnamea[]" required type="text" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>Last Name</label>
                            <input name="lastnamea[]" required type="text" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>Date of Birth</label>
                            <input type="date" name="birthdatea[]" required class="form-control datepicker">
                        </div>
                    @endfor
                </div>
            </div>
            <div class="child_info">
                @if($child > 0)
                <h3>Child Information</h3>
                @endif
                <div class="row">
                    @for($i = 0; $i < $child; $i++)
                        <div class="col-md-3">
                            <label>Salutation</label>
                            <select name="titlec[]" class="form-control">
                                <option value="Tuan">Tuan</option>
                                <option value="Nyonya">Nyonya</option>
                                <option value="Nona">Nona</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>First Name</label>
                            <input name="firstnamec[]" required type="text" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>Last Name</label>
                            <input name="lastnamec[]" required type="text" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>Date of Birth</label>
                            <input type="date" name="birthdatec[]" required class="form-control datepicker">
                        </div>
                    @endfor
                </div>
            </div>
            <div class="infant_info">
                @if($infant > 0)
                <h3>Infant Information</h3>
                @endif
                <div class="row">
                    @for($i = 0; $i < $infant; $i++)
                        <div class="col-md-3">
                            <label>Salutation</label>
                            <select name="titlei[]" class="form-control">
                                <option value="Tuan">Tuan</option>
                                <option value="Nyonya">Nyonya</option>
                                <option value="Nona">Nona</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>First Name</label>
                            <input name="firstnamei[]" required type="text" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>Last Name</label>
                            <input name="lastnamei[]" required type="text" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>Date of Birth</label>
                            <input type="date" name="birthdatei[]" required class="form-control datepicker">
                        </div>
                    @endfor
                </div>
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
