<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    //
    public function getResult(Request $request) {
      $key = '059651551ad205d5fc25173a554776a4';

      // $response = Curl::to('http://api.rajaongkir.com/basic/waybill')
      //                 ->withContentType('application/x-www-form-urlencoded')
      //                 ->withData( array( "waybill"=>$resi, "courier"=>"jne" ) )
      //                 ->withHeader(array("key" => $key))
      //                 ->post();
      //
      // echo $response;
      $curl = curl_init();

      curl_setopt_array($curl, array(
        CURLOPT_URL => "http://sandbox.tiket.com",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "",
        CURLOPT_HTTPHEADER => array(
          "content-type: application/x-www-form-urlencoded",
          "key: $key"
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
