<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;

use App\Claim;
use App\User;

use App\Library\HttpRequest\CurlRequest;

class OrderController extends Controller
{
    //this method returns Token required for Tiket.com API call
    protected $key = '07ff7126e34ff51b9564cd9848b339b9';

    public function getToken()
    {
        $key = $this->key;
        $url = "https://api-sandbox.tiket.com/apiv1/payexpress?method=getToken&secretkey=$key&output=json";

        $reply = $this->curlCall($url);

        echo $reply;
    }

    //Returns list of hotels
    public function getHotel(Request $request)
    {
        $sd = 0;
        $in = $request->input('in');
        $out = $request->input('out');
        $city = $request->input('city');
        $room = $request->input('room');
        $adult = $request->input('adult');
        $child = $request->input('child');
        $token = $request->input('token');
        $night = strtotime($out)-strtotime($in);
        $page = $request->input('page');
        $today = date_diff(date_create_from_format('Y-m-d',date('Y-m-d')),date_create_from_format('Y-m-d',"$in"))->format('%R%a');
        if($night < 1 || $today < 0)  {
            echo "error";
            return;
        }
        $url = "https://api-sandbox.tiket.com/search/hotel?q=$city&startdate=$in&night=1&enddate=$out&room=$room&adult=$adult&child=$child&page=$page&token=$token&output=json";

        $reply = $this->curlCall($url);

        echo $reply;
    }

    //return the detail of 1 hotel
    public function getHotelDetail(Request $request)
    {
        $target = $request->input('target');
        $token = $request->input('token');
        $url = "$target&token=$token&output=json";

        echo $this->curlCall($url);
    }

    public function bookHotel(Request $request)
    {
        $target = $request->input('target');
        $token = $request->input('token');
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
            $claim->save();
        }

        return "true";
    }


    //do curl call to url
    public function curlCall($url)
    {
        $curl = curl_init();
        curl_setopt($curl,CURLOPT_URL,$url);
        curl_setopt_array($curl, array(
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
            echo "cURL Error";
        } else {
            return $response;
        }
    }
}
