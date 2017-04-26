<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;

use App\Claim;
use App\User;

use Carbon\Carbon;

class OrderController extends Controller
{
    //this method returns Token required for Tiket.com API call
    public function getToken(Request $request)
    {
      $key = '07ff7126e34ff51b9564cd9848b339b9';
      $url = "https://api-sandbox.tiket.com/apiv1/payexpress?method=getToken&secretkey=$key&output=json";
      echo $this->curlCall($url);

    }
    public function decodeJsonToken()
    {
        $key = '07ff7126e34ff51b9564cd9848b339b9';
        $url = "https://api-sandbox.tiket.com/apiv1/payexpress?method=getToken&secretkey=$key&output=json";
        $response = $this->curlCall($url);
        if($response){
          return json_decode($response)->token;
        }else{
          return abort('404','404 - Page not found');
        }
    }

    //Returns list of hotels
    public function getHotel(Request $request)
    {
      $sd = 0;
      $in = Input::get('in');
      $out = Input::get('out');
      $city = Input::get('city');
      $room = Input::get('room');
      $adult = Input::get('adult');
      $child = Input::get('child');
      $token = Input::get('token');
      $page = Input::get('page');
      $night = strtotime($out)-strtotime($in);

      $url = "https://api-sandbox.tiket.com/search/hotel?q=$city&startdate=$in&night=1&enddate=$out&room=$room&adult=$adult&child=$child&page=$page&token=$token&output=json";
      echo $this->curlCall($url);
    }

    //return the detail of 1 hotel
    public function getHotelDetail()
    {
        $target = Input::get('target');
        $token = Input::get('token');
        $url = "$target&token=$token&output=json";

       echo $this->curlCall($url);
    }
	  public function getOrder()
    {
		    $token = Input::get('token');
		    $url = "https://api-sandbox.tiket.com/order?token=$token&output=json";
		    echo $this->curlCall($url);
	  }

    public function bookHotel()
    {
        $target = Input::get('target');
        $token = Input::get('token');
        $url = "$target&token=$token&output=json";

        $mess = $this->curlCall($url);
        if($mess) {
          $claimer = Auth::user();
          $claim = new Claim();
          $claim->claim_type = 1;
          $claim->claim_data_id = $token;
          $claim->claimer_id = $claimer->id;
          $claim->approver_id = User::approver($claimer)->id;
          $claim->finance_id = User::finance($claimer)->id;
          $claim->claim_status = 1;
          $claim->order_information = $target;
          $claim->save();
        //  dd($claim);
      }

      return "true";
    }

    public function rebookHotel($id)
    {
      $claim = Claim::where('id','=',$id)->first();
      $created = new Carbon($claim->created_at);
      $now = Carbon::now();
      $difference = $created->diff($now)->days;
      if($difference>1){
        $target = $claim->order_information;
        $token = $this->decodeJsonToken();
        $url = "$target&token=$token&output=json";
        $this->curlCall($url);
        $claim->claim_data_id = $token;
        $claim->updated_at = date("Y-m-d H:i:s");
        $claim->save();
      }
      return redirect('/home/');
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
