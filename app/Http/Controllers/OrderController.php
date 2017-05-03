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

    protected $key = '1c9c54d06eac9e8f7dd6ae643e6797a6';

    public function getToken()  {
        $key = $this->key;
        $url = "http://api-sandbox.tiket.com/apiv1/payexpress?method=getToken&secretkey=$key&output=json";

        echo $this->curlCall($url);
    }

    public function decodeJsonToken()
    {
        $key = $this->key;
        $url = "http://api-sandbox.tiket.com/apiv1/payexpress?method=getToken&secretkey=$key&output=json";
        $response = $this->curlCall($url);
        echo json_decode($response,true)['token'];
        return json_decode($response,true)['token'];
    }

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

    //return the detail of 1 hotel
    public function getHotelDetail(Request $request)  {

        $target = $request->input('target');
        $token = $request->input('token');
        $url = "$target&token=$token&output=json";

        echo $this->curlCall($url);
    }

    public function getOrder(Request $request){
		$id = $request->input('id');
        $token = $request->input('token');
        $url = "https://api-sandbox.tiket.com/order?token=$token&output=json";
		$claimDescription = Claim::where('id','=',$id)->first()->description;
		$response = $this->curlCall($url);
		echo '{"api_data":'.$response.',"description":"'.$claimDescription.'"}';
    }

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
    // public function checkoutRequest($id,$checkout){
    //     $claim = Claim::where('id','=',$id)->first();
    //     $token = $claim->claim_data_id;
    //     $url = "$checkout?token=$token&output=json";
    //     $response = $this->curlCall($url);
    // }
    // public function checkoutLogin($id){
    //     $claim = Claim::where('id','=',$id)->first();
    //     $token = $claim->claim_data_id;
    //     $url = "https://api-sandbox.tiket.com/checkout/checkout_customer?token=$token&salutation=Mr&firstName=PPLA4&lastName=DIO&emailAddress=$email&phone=08212253891&saveContinue=2&output=json";
    //     $response=$this->curlCall($url);
    // }
    //do curl call to url
    public function curlCall($url)  {

      $curl = new CurlRequest($url);

      $response = $curl->execute();
      $err = $curl->getError();
      $curl->close();
      return $response;
    }
}
