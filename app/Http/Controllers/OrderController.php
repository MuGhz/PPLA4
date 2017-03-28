<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class OrderController extends Controller
{
    //this method returns Token required for Tiket.com API call
    public function getToken(Request $request)  {
      $key = '4723b888e4285907f058245a7c52f8bc';
      $url = "https://api-sandbox.tiket.com/apiv1/payexpress?method=getToken&secretkey=$key&output=json";
      echo $this->curlCall($url);

    }

    //Returns list of hotels
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

      $url = "https://api-sandbox.tiket.com/search/hotel?q=$city&startdate=$in&night=1&enddate=$out&room=$room&adult=$adult&child=$child&token=$token&output=json";
      echo $this->curlCall($url);
    }

    //return the detail of 1 hotel
    public function getHotelDetail()  {

      $target = Input::get('target');
      $token = Input::get('token');
      $url = "$target&token=$token&output=json";

      echo $this->curlCall($url);
    }


    //do curl call to url
    public function curlCall($url)  {
      //dd($url);
      $curl = curl_init();
      curl_setopt($curl,CURLOPT_URL,$url);
      curl_setopt_array($curl, array(
      //  CURLOPT_URL => "$url",
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
        return $response;
      }
    }
}
