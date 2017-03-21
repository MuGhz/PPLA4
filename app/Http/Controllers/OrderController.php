<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class OrderController extends Controller
{
    //
    public function getToken(Request $request)  {
      $key = '059651551ad205d5fc25173a554776a4';

      $curl = curl_init();


      curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api-sandbox.tiket.com/apiv1/payexpress?method=getToken&secretkey=$key&output=json",
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_HTTPHEADER => array(
          "Accept: application/json",
          'Content-Type: application/json'
        ),
        CURLINFO_HEADER_OUT => true,
      ));
      $response = curl_exec($curl);
      $err = curl_error($curl);

      curl_close($curl);
      if ($err) {
        echo "cURL Error #:" . $err;
      } else {
        echo $response;
      }
    }

    public function getHotel(Request $request) {
      $sd = 0;
      $in = Input::get('in');
      $out = Input::get('out');
      $city = Input::get('city');
      $room = Input::get('room');
      $adult = Input::get('adult');
      $child = Input::get('child');
      $token = Input::get('token');
      $night = $out-$in;

      $curl = curl_init();

      curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api-sandbox.tiket.com/search/hotel?q=$city&startdate=$in&night=1&enddate=$out&room=$room&adult=$adult&child=$child&token=$token&output=json",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_POSTFIELDS => "",
        CURLOPT_HTTPHEADER => array(
          "content-type: application/json",
        ),
      ));

      $response = curl_exec($curl);
      $err = curl_error($curl);

      curl_close($curl);

      if ($err) {
        echo "cURL Error #:" . $err;
      } else {
        echo $response;
      }
    }

    public function getHotelDetail()  {

      $target = Input::get('target');
      $token = Input::get('token');
      $curl = curl_init();

      curl_setopt_array($curl, array(
        CURLOPT_URL => "$target&token=$token&output=json",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_POSTFIELDS => "",
        CURLOPT_HTTPHEADER => array(
          "content-type: application/json",
        ),
      ));

      $response = curl_exec($curl);
      $err = curl_error($curl);

      curl_close($curl);

      if ($err) {
        echo "cURL Error #:" . $err;
      } else {
        echo $response;
      }
    }
}
