<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;

use App\Library\HttpRequest\CurlRequest;

use Illuminate\Support\Facades\Log;

use App\Claim;
use App\User;

use Carbon\Carbon;

class OrderController extends Controller
{
    //this method returns Token required for Tiket.com API call

    protected $key = '07ff7126e34ff51b9564cd9848b339b9';

    /**
    * Returns Token from tiket.com
    * @return json
    */
    public function getToken()  {
        $key = $this->key;
        $url = "http://api-sandbox.tiket.com/apiv1/payexpress?method=getToken&secretkey=$key&output=json";

        echo $this->curlCall($url);
    }

    /**
    * Returns decoded token json
    * @return string
    */
    public function decodeJsonToken()
    {
        $key = $this->key;
        $url = "http://api-sandbox.tiket.com/apiv1/payexpress?method=getToken&secretkey=$key&output=json";
        $response = $this->curlCall($url);
        return json_decode($response,true)['token'];
    }

    /**
    * Returns hotel list in json
    * @param HTTP request
    * @return json
    */
    public function getHotel(Request $request) {
        $sd = 0;
        $in = $request->input('in');
        $out = $request->input('out');
        $city = $request->input('city');
        $room = $request->input('room');
        $adult = $request->input('adult');
        $child = $request->input('child');
        $token = $request->input('token');
        $page = $request->input('page');
        $night = strtotime($out)-strtotime($in);
        $page = $request->input('page');
        $today = date_diff(date_create_from_format('Y-m-d',date('Y-m-d')),date_create_from_format('Y-m-d',"$in"))->format('%R%a');
        if($night < 1 || $today < 0)  {
            echo "error";
            return;
        }
        $url = "https://api-sandbox.tiket.com/search/hotel?q=$city&startdate=$in&night=1&enddate=$out&room=$room&adult=$adult&child=$child&page=$page&token=$token&output=json";
        echo $this->curlCall($url);
    }

    /**
    * Returns detail of hotel in json
    * @param HTTP request
    * @return json
    */
    public function getHotelDetail(Request $request)  {

        $target = $request->input('target');
        $token = $request->input('token');
        $url = "$target&token=$token&output=json";

        echo $this->curlCall($url);
    }

    /**
    * Returns detail of claim
    * @param HTTP request
    * @return json
    */
    public function getOrder(Request $request){
        $id = $request->input('id');
        $token = $request->input('token');
        $url = "https://api-sandbox.tiket.com/order?token=$token&output=json";
        $claimDescription = Claim::where('id','=',$id)->first()->description;
        $response = $this->curlCall($url);
        echo '{"api_data":'.$response.',"description":"'.$claimDescription.'"}';
    }

    /**
    * Insert claim into database
    * @param HTTP request
    * @return boolean
    */
    public function bookHotel(Request $request) {
        $description = $request->input('description');
        $target = $request->input('target');
        $token = $request->input('token');
        $url = "$target&token=$token&output=json";

        $success = $this->curlCall($url);
        if($success) {
            $claimer = Auth::user();
            $claim = new Claim();
            $claim->claim_type = 1;
            $claim->claim_data_id = $token;
            $claim->claimer_id = $claimer->id;
            $claim->approver_id = User::approver($claimer)->id;
            $claim->finance_id = User::finance($claimer)->id;
            $claim->claim_status = 1;
            $claim->description = $description;
            $claim->order_information=$target;
            $claim->alasan_reject="";
            $claim->save();
            Log::info('claim '.($claim->id)." created by \(".Auth::id().") ".Auth::user()->name);
        }

        return "true";
    }

    /**
    * This method will be called when a token has been expired
    * @param claim Id that will be renewed
    * @return claim with new token
    */
    public function rebookHotel($id)
    {
      $claim = Claim::where('id','=',$id)->first();
      $target = $claim->order_information;
      $old_token = $claim->claim_data_id;
      $token = $this->decodeJsonToken();
      $url = "$target&token=$token&output=json";
      $this->curlCall($url);
      $claim->claim_data_id = $token;
      $claim->updated_at = date("Y-m-d H:i:s");
      $claim->save();
      $new_token = $claim->claim_data_id;
      Log::info('update claim token '.$old_token.' -> '.$new_token);

      return $claim;
    }

    /**
    * This method will finalized user's order
    * @param HTTP request, claim Id
    * @return view
    */
    public function orderHotel(Request $request,$id)
    {
        $claim = Claim::where('id','=',$id)->first();
        $created = new Carbon($claim->created_at);
        $now = Carbon::now();
        $difference = $created->diff($now)->days;
        if($difference>1){
            $claim = $this->rebookHotel($id);
        }
        $url= "https://api-sandbox.tiket.com/order?token=$claim->claim_data_id&output=json";
        $response = $this->curlCall($url);
        $responseObject = json_decode($response,true);
        // Save Order: Get Order ID & Order Detail ID
        $checkout = $responseObject['checkout'];
        $orderId = $responseObject['myorder']['order_id'];
        // dd($responseObject);
        $orderDetailId = $responseObject['myorder']['data'][0]['order_detail_id'];

        // Request Checkout Page
        $url = "https://api-sandbox.tiket.com/order/checkout/$orderId/IDR?token=$claim->claim_data_id&output=json";
        $response = $this->curlCall($url);
        Log::info('user \('.Auth::id().') '.' request checkout page, redirecting to login page');

        // Login for Checkout Customer
        $url  = "https://api-sandbox.tiket.com/checkout/checkout_customer?token=$claim->claim_data_id&salutation=Mr&firstName=ghozi&lastName=jojo&emailAddress=totorvo901@ymail.com&phone=%2B6282138470931&saveContinue=2&output=json";
        $response = $this->curlCall($url);
        Log::info('user \('.Auth::id().') '.' login to tiket.com, redirecting to checkout cart');

        // Customer Checkout
        $url  = "https://api-sandbox.tiket.com/checkout/checkout_customer?token=$claim->claim_data_id&salutation=Mr&firstName=ghozi&lastName=jojo&emailAddress=totorvo901@ymail.com&phone=%2B6282138470931&conSalutation=Mr&conFirstName=ghozi&conLastName=jojo&conEmailAddress=totorvo901@ymail.com&conPhone=%2B6282138470931&detailId=$orderDetailId&country=id&output=json";
        $response = $this->curlCall($url);
        Log::info('user \('.Auth::id().') '.' checkout claim, confirming purchase...');

        // Confirm
        $url = "https://api-sandbox.tiket.com/partner/transactionApi/confirmPayment?order_id=$orderId&secretkey=$this->key&confirmkey=e1fdb5&username=totorvo901@ymail.com&textarea_note=test&tanggal=2012-12-06&output=json";
        $response = $this->curlCall($url);
        Log::info('user \('.Auth::id().') '.' claim confirmed, checking result...');

        $responseObject = json_decode($response,true);
        if ($responseObject['diagnostic']['status'] != '200') {
            Log::info('user \('.Auth::id().') '.' request failed',['error'=>$responseObject['diagnostic']['error_msgs'],'claim'=>$claim]);
            session()->flash('error',$responseObject['diagnostic']['error_msgs']);
            return back();
        }
        $claim->claim_status = 3;
        $claim->save();
        Log::info('user \('.Auth::id().') '.' claim succeed',['claim'=>$claim]);
        return redirect('/home');
    }

    /**
    * This method will process user's input in attempt to search for airports
    * @param Http request
    * @return json of airports that matches user's query
    */
    public function getAirport(Request $request)
    {
        $query = strtolower($request->input('query'));
        $token = $this->decodeJsonToken();
        $id = Auth::id();
        if(!$request->session()->has("$id"))  {
            $response = $this->curlCall("https://api-sandbox.tiket.com/flight_api/all_airport?token=$token&output=json");
            $airports = json_decode($response,true)['all_airport']['airport'];
            $request->session()->put("$id", $response);
        }
        else {
            $response = $request->session()->get("$id");
            $airports = json_decode($response,true)['all_airport']['airport'];
        }
        $values = array();
        foreach($airports as $airport) {
            $location_name = $airport['location_name'];
            $airport_name = $airport['airport_name'];
            if(strpos("x".strtolower($location_name),$query) || strpos("x".strtolower($airport_name),$query))    {
                $location = $location_name." (".$airport['airport_code']."), ".$airport_name;
                array_push($values, ["value"=>$location, "data"=>$airport['airport_code']]);
            }
        }
        $returnValue = array("query"=>"airports","suggestions"=>$values);
        return $returnValue;
    }

    /**
    * Do a curl call to url target
    * @param url target
    * @return response
    */
    public function curlCall($url)  {

        $curl = new CurlRequest($url);

        $response = $curl->execute();
        $err = $curl->getError();
        $curl->close();
        return $response;
    }

    /**
    * TODO: stub
    * Returns flight list in json
    * @param HTTP request
    * @return json
    */
    public function getFlight(Request $request) {
        $d = $request->input('d');
        $a = $request->input('a');
        $date = $request->input('date');
        $ret_date = $request->input('ret_date');
        $adult = $request->input('adult');
        $child = $request->input('child');
        $infant = $request->input('infant');
        $token = $request->input('token');
        $page = $request->input('page');
        $night = strtotime($ret_date)-strtotime($date);
        $today = date_diff(date_create_from_format('Y-m-d',date('Y-m-d')),date_create_from_format('Y-m-d',"$date"))->format('%R%a');
        if(($ret_date != "false") && ($night < 1 || $today < 0))  {
            echo "error";
            return;
        }
        if($ret_date) {
            
            $url = "http://api-sandbox.tiket.com/search/flight?d=$d&a=$a&date=$date&ret_date=$ret_date&adult=$adult&child=$child&infant=$infant&token=$token&page=$page&v=3&output=json";
        } else {
            $url = "http://api-sandbox.tiket.com/search/flight?d=$d&a=$a&date=$date&adult=$adult&child=$child&infant=$infant&token=$token&page=$page&v=3&output=json";
        }
        echo $this->curlCall($url);
    }

    public function bookPesawat(Request $request)
    {
        $description = $request->input('description');
        $token = $request->input('token');
        $target = $request-> input('target');
        $url = "https://api-sandbox.tiket.com/order/add/flight?token=$token&$target&output=json";
        $response = $this->curlCall($url);
        if($response){
            $claimer = Auth::user();
            $claim = new Claim();
            $claim->claim_type = 2;
            $claim->claim_data_id = $token;
            $claim->claimer_id = $claimer->id;
            $claim->approver_id = User::approver($claimer)->id;
            $claim->finance_id = User::finance($claimer)->id;
            $claim->claim_status = 1;
            $claim->description = $description;
            $claim->order_information=$target;
            $claim->alasan_reject="";
            $claim->save();
            Log::info('claim '.($claim->id)." created by \(".Auth::id().") ".Auth::user()->name);
        }
        return "true";
    }

    public function orderFlight(Request $request,$id)
    {
        $claim = Claim::where('id','=',$id)->first();
        return redirect('/home');
    }
}
