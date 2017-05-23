<?php
    $secure = App::environment('production') ? true : NULL;
?>
<!DOCTYPE html>
<html lang="en">
@section('htmlheader')
    @include('adminlte::layouts.partials.htmlheader')

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ asset('css/app.css',$secure) }}" rel="stylesheet">
</head>
<body >
    <h1>Tiket.com login</h1>
    <form action="{{action('OrderController@checkoutCustomer')}}" method="POST">
        <input type="text" hidden value="{{$id}}" name="id" required>
        <input type="text" hidden value="{{$token}}" name="id" required>

        <div class="row">
            <div class="col-md-3">
                <select name="salutation" required>
                    <option value="Tuan">Tuan</option>
                    <option value="Nyonya">Nyonya</option>
                    <option value="Nona">Nona</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>First Name</label>
                <input name="firstName" type="text" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label>Last Name</label>
                <input name="lastName" type="text" class="form-control" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <label>Phone</label>
                +62-<input type="text" name="phone" required>
            </div>
            <div class="col-md-3">
                <label>Email Address</label>
                    <input type="text" name="emailAddress" required>
            </div>
        </div>
        <div class="row">
            @php
                $claim = App\Claim::find($id);
            @endphp
            @if($claim->claim_type == 1)
            <div class="row">
                <div class="col-md-3">
                    <select name="conSalutation" required>
                        <option value="Tuan">Tuan</option>
                        <option value="Nyonya">Nyonya</option>
                        <option value="Nona">Nona</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>First Name</label>
                    <input name="conFirstName" type="text" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label>Last Name</label>
                    <input name="conLastName" type="text" class="form-control" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <label>Phone</label>
                    +62-<input type="text" name="conPhone" required>
                </div>
                <div class="col-md-3">
                    <label>Email Address</label>
                        <input type="text" name="emailAddress" required>
                </div>
            </div>
            @endif
        </div>
        <div class="row">
            <button class="btn btn-primary">Submit</button>
        </div>

        {{csrf_field()}}
    </form>
</body>
</html>
