<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;

use App\Http\Controllers\OrderController;

use App\Company;
use App\User;
use App\Claim;

use Carbon\Carbon;


class OrderTest extends TestCase
{

	use DatabaseTransactions;
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	 protected $token;
	 protected $hotelList;
	 protected $hotelDetail;
	 protected $orderHotelJson;
	 protected $planeList;
	 protected $airport;
	 protected $confirm;

	public function setUp()
	{
		parent::setUp();
		$this->token = '{"diagnostic":{"status":200},"output_type":"json","token":"3b00ae1956bac189967cfff807fff929c4e6415b"}';
		$this->hotelList = '{"diagnostic":{"status":200},"output_type":"json","search_queries":{"q":"jakarta","uid":"","startdate":"2017-04-20","enddate":"2017-04-21","night":"1","room":1,"adult":"1","child":0,"sort":false,"minstar":0,"maxstar":5,"minprice":"213000.00","maxprice":"1000000000.00","distance":100000},"results":{"result":[{"province_name":"DKI Jakarta","kecamatan_name":"Gambir","kelurahan_name":"Petojo Selatan","business_uri":"https:\/\/api-sandbox.tiket.com\/hotel\/indonesia\/jakarta\/jakarta\/neo-hotel-cideng-jakarta?startdate=2017-04-20&night=1&room=1&adult=1&child=0&is_partner=0&star_rating%5B0%5D=0&star_rating%5B1%5D=1&star_rating%5B2%5D=2&star_rating%5B3%5D=3&star_rating%5B4%5D=4&star_rating%5B5%5D=5&hotel_chain=0&facilities=0&latitude=0&longitude=0&distance=0&uid=business%3A222778",hotel_id":"21427344"}]},"pagination":{"total_found":62,"current_page":"1","offset":"20","lastPage":4},"login_status":"false","token":"25599b029a0c3e3a87bde80091cf15bed064c099"}';
		$this->hotelDetail = '{"diagnostic":{"status":200},"output_type":"json","primaryPhotos":"https:\/\/sandbox.tiket.com\/img\/business\/f\/a\/business-favehotel-tanah-abang-cideng-jakarta-hotel-jakarta-pusat2677.s.jpg","breadcrumb":{"business_id":"222778","business_uri":"https:\/\/api-sandbox.tiket.com\/neo-hotel-cideng-jakarta","business_name":"Favehotel Tanah Abang","area_id":"87528","area_name":"Monas","kelurahan_id":"69","kelurahan_name":"Petojo Selatan","kecamatan_id":"19","kecamatan_name":"Gambir","city_id":"21681","city_name":"Jakarta","province_id":"13","province_name":"DKI Jakarta","country_id":"id","country_name":"Indonesia","continent_id":"1","continent_name":"Asia","kelurahan_uri":"https:\/\/api-sandbox.tiket.com\/search\/hotel?uid=city:21681"},"login_status":"false","token":"c8d2e6aa8d84d93cde61fdc9d63e6215f6a1ef69"}';
		$this->orderHotelJson = '{"diagnostic":{"status":200,"elapsetime":"0.5010","memoryusage":"26.98MB","unix_timestamp":1493224283,"confirm":"success","lang":"id","currency":"IDR"},"output_type":"json","myorder":{"order_id":"33440545","data":[{"expire":214,"order_detail_id":"43221263","order_expire_datetime":"2017-04-27 02:49:08","order_type":"hotel","order_name":"Maven Buncit","order_name_detail":"Business Room","order_detail_status":"active","tenor":"0","detail":{"order_detail_id":"43221263","room_id":"6002","rooms":"1","adult":"1","child":"0","startdate":"27 Apr 2017","enddate":"28 Apr 2017","nights":"1","total_charge":"0.00","startdate_original":"1493226000","enddate_original":"1493312400","price":310000,"price_per_night":310000,"compulsory_data":[]},"order_photo":"https:\/\/sandbox.tiket.com\/img\/business\/b\/u\/business-business-room-mavenbuncit-jakarta-selatan7412.s.jpg","tax":0,"item_charge":0,"subtotal_and_charge":"310000.00","delete_uri":"https:\/\/api-sandbox.tiket.com\/order\/delete_order?order_detail_id=43221263","business_id":"19580417","is_expedia":0}],"total":310000,"total_tax":0,"total_without_tax":310000,"count_installment":0,"promo":[],"discount":0,"discount_amount":0},"checkout":"https:\/\/api-sandbox.tiket.com\/order\/checkout\/33440545\/IDR","login_status":"true","guest_id":"24115406","login_email":"totorvo901@gmail.com","token":"4e0a576142f35e8ee7d4c7d8b1f8b9bd8ad06526"}';
		$this->flightList = '{"diagnostic":{"status":200,"elapsetime":"0.4998","memoryusage":"28.34MB","unix_timestamp":1494475178,"confirm":"success","lang":"id","currency":"IDR"},"output_type":"json","round_trip":true,"search_queries":{"from":"CGK","to":"DPS","date":"2017-05-25","ret_date":"2017-05-30","adult":1,"child":0,"infant":0,"sort":false},"go_det":{"dep_airport":{"airport_code":"CGK","international":"1","trans_name_id":"7574","banner_image":"","short_name_trans_id":"1193637","business_name":"Soekarno Hatta","business_name_trans_id":"5935","business_country":"id","business_id":"20361","country_name":"Indonesia","city_name":"Jakarta","province_name":"DKI Jakarta","short_name":"Jakarta","location_name":"Jakarta - Cengkareng"},"arr_airport":{"airport_code":"DPS","international":"1","trans_name_id":"7572","banner_image":"banner-denpasar1.jpg","short_name_trans_id":"1193606","business_name":"Ngurah Rai","business_name_trans_id":"5931","business_country":"id","business_id":"20357","country_name":"Indonesia","city_name":"Denpasar","province_name":"Bali","short_name":"Denpasar","location_name":"Denpasar, Bali"},"date":"2017-05-25","formatted_date":"25 Mei 2017"},"ret_det":{"dep_airport":{"airport_code":"DPS","international":"1","trans_name_id":"7572","banner_image":"banner-denpasar1.jpg","short_name_trans_id":"1193606","business_name":"Ngurah Rai","business_name_trans_id":"5931","business_country":"id","business_id":"20357","country_name":"Indonesia","city_name":"Denpasar","province_name":"Bali","short_name":"Denpasar","location_name":"Denpasar, Bali"},"arr_airport":{"airport_code":"CGK","international":"1","trans_name_id":"7574","banner_image":"","short_name_trans_id":"1193637","business_name":"Soekarno Hatta","business_name_trans_id":"5935","business_country":"id","business_id":"20361","country_name":"Indonesia","city_name":"Jakarta","province_name":"DKI Jakarta","short_name":"Jakarta","location_name":"Jakarta - Cengkareng"},"date":"2017-05-30","formatted_date":"30 Mei 2017"},"departures":{"result":[{"flight_id":"13650959","airlines_name":"CITILINK","flight_number":"QG-856","departure_city":"CGK","arrival_city":"DPS","stop":"Langsung","price_value":"908050.00","price_adult":"908050.00","price_child":"0.00","price_infant":"0.00","timestamp":"2017-05-09 15:15:26","has_food":"0","check_in_baggage":"20","is_promo":0,"airport_tax":true,"check_in_baggage_unit":"Kg","simple_departure_time":"04:55","simple_arrival_time":"07:55","long_via":"","departure_city_name":"Jakarta","arrival_city_name":"Denpasar","full_via":"CGK - DPS (04:55 - 07:55)","markup_price_string":"","need_baggage":0,"best_deal":false,"duration":"2 j 0 m","image":"https:\/\/sandbox.tiket.com\/images\/flight\/logo\/icon_citilink.png","departure_flight_date":"2017-05-25 04:55:00","departure_flight_date_str":"Kamis, 25 Mei 2017","departure_flight_date_str_short":"Kam, 25 Mei 2017","arrival_flight_date":"2017-05-25 07:55:00","arrival_flight_date_str":"Kamis, 25 Mei 2017","arrival_flight_date_str_short":"Kam, 25 Mei 2017","flight_infos":{"flight_info":[{"flight_number":"QG-856","class":"L","departure_city":"CGK","departure_city_name":"Jakarta","arrival_city":"DPS","arrival_city_name":"Denpasar","airlines_name":"CITILINK","departure_date_time":"2017-05-25 04:55:00","string_departure_date":"Kamis, 25 Mei 2017","string_departure_date_short":"Kam, 25 Mei 2017","simple_departure_time":"04:55","arrival_date_time":"2017-05-25 07:55:00","string_arrival_date":"Kamis, 25 Mei 2017","string_arrival_date_short":"Kam, 25 Mei 2017","simple_arrival_time":"07:55","img_src":"https:\/\/sandbox.tiket.com\/images\/flight\/logo\/icon_citilink.png","duration_time":7200,"duration_hour":"2j","duration_minute":"","check_in_baggage":20,"check_in_baggage_unit":"Kg","terminal":"","transit_duration_hour":0,"transit_duration_minute":0,"transit_arrival_text_city":"","transit_arrival_text_time":""}]},"sss_key":null}]},"nearby_go_date":{"nearby":[{"date":"2017-05-20"},{"date":"2017-05-21"},{"date":"2017-05-22","price":"528100.00"},{"date":"2017-05-23"},{"date":"2017-05-24","price":"519200.00"},{"date":"2017-05-25","price":"908050.00"},{"date":"2017-05-26"},{"date":"2017-05-27"},{"date":"2017-05-28"},{"date":"2017-05-29"},{"date":"2017-05-30"}]},"nearby_ret_date":{"nearby":[{"date":"2017-05-25"},{"date":"2017-05-26"},{"date":"2017-05-27"},{"date":"2017-05-28"},{"date":"2017-05-29"},{"date":"2017-05-30","price":"0.00"},{"date":"2017-05-31"},{"date":"2017-06-01","price":"543100.00"},{"date":"2017-06-02"},{"date":"2017-06-03"},{"date":"2017-06-04"}]},"login_status":"false","token":"aa2ab72ac20e0a551cbf52c9a4211607e2963664"}';
		$this->confirmFail = '{"diagnostic":{"error_msgs":"not enought deposit ,0","status":"224","elapsetime":"0.2753","memoryusage":"20.04MB","unix_timestamp":1493225286,"lang":"id","currency":"IDR"},"output_type":"json","login_status":"false","token":"135552a2851810b2f014d6f6c8868c54ea2a9c41"}';
		$this->confirmSuccess = '{"diagnostic":{"error_msgs":"not enought deposit ,0","status":"200","elapsetime":"0.2753","memoryusage":"20.04MB","unix_timestamp":1493225286,"lang":"id","currency":"IDR"},"output_type":"json","login_status":"false","token":"135552a2851810b2f014d6f6c8868c54ea2a9c41"}';
		$this->airport = '{"diagnostic":{"status":200,"elapsetime":"0.0504","memoryusage":"5.58MB","unix_timestamp":1399962811,"confirm":"success","lang":"id","currency":"IDR"},"output_type":"json","all_airport":{"airport":[{"airport_name":"PATTIMURA","airport_code":"AMQ","location_name":"Ambon","country_id":"id"},{"airport_name":"SOA","airport_code":"BJW","location_name":"Bajawa","country_id":"id"}}';
	}

	private function makeCompany($name)
	{
		return factory(Company::class)->create([
			 'name' => $name
		]);
	}

	private function makeUser($name, $email, $companyId, $role)
	{
		return factory(User::class)->create([
			'name' => $name,
			'email' => $email,
			'company' => $companyId,
			'role' => $role
		]);
	}

	public function makeClaim($claim_type, $claimer_id, $approver_id,$finance_id,$claim_status,$description = "")
	   {
			 return factory(Claim::class)->create([
					'claim_type' => $claim_type,
					'claimer_id' => $claimer_id,
					'approver_id' => $approver_id,
					'finance_id' => $finance_id,
					'claim_status' => $claim_status,
					'description' => $description
				  ]);
		}

	public function curlMockWithoutJson($returnValue)
	{
		$order = $this->getMockBuilder('App\Http\Controllers\OrderController')
					->setMethods(array('curlCall'))
					->getMock();

		$order->expects($this->any())
			  ->method("curlCall")
			  ->will($this->returnValue($returnValue));
		return $order;
	}

	public function curlMockForHotelOrder($returnValue, $returnValueElse)
	{
		$order = $this->getMockBuilder('App\Http\Controllers\OrderController')
					->setMethods(array('curlCall','decodeJsonToken'))
					->getMock();
		$order->expects($this->exactly(6))
			  ->method("curlCall")
			  ->will($this->onConsecutiveCalls(null,$returnValue,$returnValueElse,$returnValueElse,$returnValueElse,$returnValueElse));
		$order->expects($this->any())
				->method("decodeJsonToken")
				->will($this->returnValue("token"));
		return $order;
	}

	public function curlMock($returnValue)
	{
		$order = $this->getMockBuilder('App\Http\Controllers\OrderController')
					->setMethods(array('curlCall','decodeJsonToken'))
					->getMock();

		$order->expects($this->any())
			  ->method("curlCall")
			  ->will($this->returnValue("$returnValue"));
		$order->expects($this->any())
				->method("decodeJsonToken")
				->will($this->returnValue("token"));
		return $order;
	}

	public function curlMockMap($map)
	{
		$order = $this->getMockBuilder('App\Http\Controllers\OrderController')
					->setMethods(array('curlCall'))
					->getMock();
		$order->expects($this->any())
					->method('curlCall')
					->will($this->returnValueMap($map));
		return $order;
	}

	public function requestMock($map)
	{
		$request = $this->getMockBuilder('Illuminate\Http\Request')
					->setMethods(array('input'))
					->getMock();
		$request->expects($this->any())
					->method('input')
					->will($this->returnValueMap($map));
		return $request;
	}

	public function testToken()
	{
		$order = $this->curlMock("$this->token");

		$this->expectOutputString("$this->token");
		$order->getToken();
	}

	public function testGetHotelWithAcceptedParameter()
	{
		$order = $this->curlMock("$this->hotelList");

		$this->expectOutputString("$this->hotelList");

		$map = [
			["in",null,date('Y-m-d')],
			["out",null,date("Y-m-d", strtotime('tomorrow'))],
			["city",null,"jakarta"],
			["room",null,"1"],
			["adult",null,"1"],
			["child",null,"0"],
			["token",null,"token"],
			["page",null,"1"]
		];

		$request = $this->requestMock($map);
		$order->getHotel($request);
	}

	public function testGetHotelWithCheckInBeforeToday()
	{
		$order = $this->curlMock($this->hotelList);

		$this->expectOutputString("error");

		$map = [
			["in",null,date("Y-m-d", strtotime('yesterday'))],
			["out",null,date("Y-m-d", strtotime('tomorrow'))],
			["city",null,"jakarta"],
			["room",null,"1"],
			["adult",null,"1"],
			["child",null,"0"],
			["token",null,"token"],
			["page",null,"1"]
		];

		$request = $this->requestMock($map);
		$order->getHotel($request);
	}

	public function testGetHotelWithCheckOutBeforeCheckIn()
	{
		$order = $this->curlMock($this->hotelList);

		$this->expectOutputString("error");

		$map = [
			["in",null,date("Y-m-d", strtotime('tomorrow'))],
			["out",null,date("Y-m-d", strtotime('yesterday'))],
			["city",null,"jakarta"],
			["room",null,"1"],
			["adult",null,"1"],
			["child",null,"0"],
			["token",null,"token"],
			["page",null,"1"]
		];

		$request = $this->requestMock($map);

		$order->getHotel($request);
	}

	public function testGetHotelDetail()
	{
		$order = $this->curlMock($this->hotelDetail);

		$this->expectOutputString("$this->hotelDetail");
		$map = [
			["target",null,"target"],
			["token",null,"token"]
		];

		$request = $this->requestMock($map);

		$order->getHotelDetail($request);
	}

	public function testReorder()
	{
		$order = $this->curlMock($this->hotelDetail);
		$company = $this->makeCompany('Test Company');
		$claimer = $this->makeUser('Claimer 1', 'Claimer1@Company.test', $company->id, 'claimer');
		$approver = $this->makeUser('Approver', 'Appover@Company.test', $company->id, 'approver');
		$finance = $this->makeUser('Finance', 'Finance@Company.test', $company->id, 'finance');
		$claim = $this->makeClaim(1,$claimer->id,$approver->id,$finance->id,2);
		$date = Carbon::create(2017,1,1,12);
		$now = Carbon::now();
		$claim->created_at=$date;
		$claim->save();
		$order->rebookHotel($claim->id);
		$difference = $claim->updated_at->diff($now)->days;
		$this->assertEquals(0,$difference);
	}

	public function testOrderHotelSuccess()
	{
		$order = $this->curlMockForHotelOrder($this->orderHotelJson,$this->confirmSuccess);

		$map = [
			["target",null,"target"],
			["token",null,"token"]
		];
		$request = $this->requestMock($map);

		$company = $this->makeCompany('Test Company');
		$claimer = $this->makeUser('Claimer 1', 'Claimer1@Company.test', $company->id, 'claimer');
		$approver = $this->makeUser('Approver', 'Appover@Company.test', $company->id, 'approver');
		$finance = $this->makeUser('Finance', 'Finance@Company.test', $company->id, 'finance');
		$claim = $this->makeClaim(1,$claimer->id,$approver->id,$finance->id,2);
		$date = Carbon::create(2017,1,1,12);
		$now = Carbon::now();
		$claim->created_at=$date;
		$claim->save();


		$order->orderHotel($request,$claim->id);
		$this->assertDatabaseHas("claims",[
			"claim_data_id" => "token",
			"claim_type" => "1",
			"claim_status" => "3",
			"claimer_id" => $claimer->id,
			"approver_id" => $approver->id,
			"finance_id" => $finance->id,
		]);
	}

	public function testOrderHotelFail()
	{
	  $order = $this->curlMockForHotelOrder($this->orderHotelJson, $this->confirmFail);
	  $map = [
		  ["target",null,"target"],
		  ["token",null,"token"]
	  ];
	  $request = $this->requestMock($map);

	  $company = $this->makeCompany('Test Company');
	  $claimer = $this->makeUser('Claimer 1', 'Claimer1@Company.test', $company->id, 'claimer');
	  $approver = $this->makeUser('Approver', 'Appover@Company.test', $company->id, 'approver');
	  $finance = $this->makeUser('Finance', 'Finance@Company.test', $company->id, 'finance');
	  $claim = $this->makeClaim(1,$claimer->id,$approver->id,$finance->id,2);
	  $date = Carbon::create(2017,1,1,12);
	  $now = Carbon::now();
	  $claim->created_at=$date;
	  $claim->save();

	  $order->orderHotel($request,$claim->id);
	  $this->assertDatabaseHas("claims",[
		  "claim_data_id" => "token",
		  "claim_type" => "1",
		  "claim_status" => "2",
		  "claimer_id" => $claimer->id,
		  "approver_id" => $approver->id,
		  "finance_id" => $finance->id,
	  ]);
	}

	public function testDecodeJson()
	{
		$order = $this->curlMockWithoutJson($this->token);
		$response = $order->decodeJsonToken();
		$this->assertEquals(json_decode($this->token,true)['token'],$response);
	}

	public function testGetOrder(){
	  $company = $this->makeCompany('Test Company');
	  $claimer = $this->makeUser('Claimer', 'Claimer1@Company.test', $company->id, 'claimer');
	  $approver = $this->makeUser('Approver', 'Appover@Company.test', $company->id, 'approver');
	  $finance = $this->makeUser('Finance', 'Finance@Company.test', $company->id, 'finance');
	  $claim = $this->makeClaim(1,$claimer->id,$approver->id,$finance->id,1,"Test Description");

	  $order = $this->curlMock($this->orderHotelJson);
	  $expectedOutput = '{"api_data":'.$this->orderHotelJson.',"description":"'.$claim->description.'"}';
	  $map = [
		  ["id",null,$claim->id],
		  ["target",null,"target"],
		  ["token",null,"token"]
	  ];

	  $request = $this->requestMock($map);
	  $order->getOrder($request);
	  $this->expectOutputString($expectedOutput);

	}

	public function testCurl()
	{
		$controller = new OrderController();
		$ret = $controller->curlCall(url('/api'));
		$this->assertJson('{"status":200}',$ret);
	}

	public function testBookHotel()
	{
		$order = $this->curlMock("success");
		$description = "I had too many deadlines, I need some days off!";
		$map = [
			["description",null,$description],
			["target",null,"target"],
			["token",null,"token"]
		];

		$request = $this->requestMock($map);

	  $company = $this->makeCompany('Test Company');
		$claimer = $this->makeUser('Claimer 1', 'Claimer1@Company.test', $company->id, 'claimer');
		$approver = $this->makeUser('Approver', 'Appover@Company.test', $company->id, 'approver');
		$finance = $this->makeUser('Finance', 'Finance@Company.test', $company->id, 'finance');
		$this->actingAs($claimer);
		$order->bookHotel($request);
		$this->assertDatabaseHas("claims",[
			"claim_data_id" => "token",
			"claim_type" => "1",
			"claim_status" => "1",
			"claimer_id" => $claimer->id,
			"approver_id" => $approver->id,
			"finance_id" => $finance->id,
			"description" => $description
		]);
	}

	public function testOrderFlightSuccess()
	{
		$order = $this->curlMockForHotelOrder($this->orderHotelJson,$this->confirmSuccess);

		$map = [
			["target",null,"target"],
			["token",null,"token"]
		];
		$request = $this->requestMock($map);

		$company = $this->makeCompany('Test Company');
		$claimer = $this->makeUser('Claimer 1', 'Claimer1@Company.test', $company->id, 'claimer');
		$approver = $this->makeUser('Approver', 'Appover@Company.test', $company->id, 'approver');
		$finance = $this->makeUser('Finance', 'Finance@Company.test', $company->id, 'finance');
		$claim = $this->makeClaim(1,$claimer->id,$approver->id,$finance->id,2);
		$date = Carbon::create(2017,1,1,12);
		$now = Carbon::now();
		$claim->created_at=$date;
		$claim->save();

		$order->orderFlight($request,$claim->id);
		$this->assertDatabaseHas("claims",[
			"claim_data_id" => "token",
			"claim_type" => "1",
			"claim_status" => "3",
			"claimer_id" => $claimer->id,
			"approver_id" => $approver->id,
			"finance_id" => $finance->id,
		]);
	}

	public function testOrderFlightFail()
	{
		$order = $this->curlMockForHotelOrder($this->orderHotelJson,$this->confirmSuccess);

		$map = [
			["target",null,"target"],
			["token",null,"token"]
		];
		$request = $this->requestMock($map);

		$company = $this->makeCompany('Test Company');
		$claimer = $this->makeUser('Claimer 1', 'Claimer1@Company.test', $company->id, 'claimer');
		$approver = $this->makeUser('Approver', 'Appover@Company.test', $company->id, 'approver');
		$finance = $this->makeUser('Finance', 'Finance@Company.test', $company->id, 'finance');
		$claim = $this->makeClaim(1,$claimer->id,$approver->id,$finance->id,2);
		$date = Carbon::create(2017,1,1,12);
		$now = Carbon::now();
		$claim->created_at=$date;
		$claim->save();

		$order->orderFlight($request,$claim->id);
		$this->assertDatabaseHas("claims",[
			"claim_data_id" => "token",
			"claim_type" => "1",
			"claim_status" => "3",
			"claimer_id" => $claimer->id,
			"approver_id" => $approver->id,
			"finance_id" => $finance->id,
		]);
	}

	public function testBookPesawat()
	{
		$order = $this->curlMock("success");
		$description = "TERANGKANLAH, TERANGKANLAH!";
		$map = [
			["description",null,$description],
			["target",null,"target"],
			["token",null,"token"]
		];

		$request = $this->requestMock($map);

	  $company = $this->makeCompany('Test Company');
		$claimer = $this->makeUser('Claimer 1', 'Claimer1@Company.test', $company->id, 'claimer');
		$approver = $this->makeUser('Approver', 'Appover@Company.test', $company->id, 'approver');
		$finance = $this->makeUser('Finance', 'Finance@Company.test', $company->id, 'finance');
		$this->actingAs($claimer);
		$order->bookPesawat($request);
		$this->assertDatabaseHas("claims",[
			"claim_data_id" => "token",
			"claim_type" => "2",
			"claim_status" => "1",
			"claimer_id" => $claimer->id,
			"approver_id" => $approver->id,
			"finance_id" => $finance->id,
			"description" => $description
		]);
	}
	public function testGetAirportList()
	{
		$order = $this->curlMock($this->airport);
		$this->expectOutputString($this->airport);
		$order->getAirport();
	}

	public function testGetFlightWithAcceptedParameter()
	{
		$order = $this->curlMock("$this->flightList");

		$this->expectOutputString("$this->flightList");

		$map = [
			["d",null,"CGK"],
			["a",null,"DPS"],
			["date",null,date('Y-m-d')],
			["ret_date",null,date("Y-m-d", strtotime('tomorrow'))],
			["adult",null,"1"],
			["child",null,"0"],
			["infant",null,"0"],
			["token",null,"token"],
			["page",null,"1"],
			["enable_ret_date",null,"true"]
		];

		$request = $this->requestMock($map);
		$order->getFlight($request);
	}
	
	public function testGetFlightWithoutReturnDate()
	{
		$order = $this->curlMock("$this->flightList");

		$this->expectOutputString("$this->flightList");

		$map = [
			["d",null,"CGK"],
			["a",null,"DPS"],
			["date",null,date('Y-m-d')],
			["adult",null,"1"],
			["child",null,"0"],
			["infant",null,"0"],
			["token",null,"token"],
			["page",null,"1"],
			["enable_ret_date",null,"false"]
		];

		$request = $this->requestMock($map);
		$order->getFlight($request);
	}

	public function testGetFlightWithCheckInBeforeToday()
	{
		$order = $this->curlMock($this->flightList);

		$this->expectOutputString("error");

		$map = [
			["d",null,"CGK"],
			["a",null,"DPS"],
			["date",null,date("Y-m-d", strtotime('yesterday'))],
			["ret_date",null,date('Y-m-d')],
			["adult",null,"1"],
			["child",null,"0"],
			["infant",null,"0"],
			["token",null,"token"],
			["page",null,"1"],
			["enable_ret_date",null,"true"]
		];

		$request = $this->requestMock($map);
		$order->getFlight($request);
	}

	public function testGetFlightWithCheckOutBeforeCheckIn()
	{
		$order = $this->curlMock($this->flightList);

		$this->expectOutputString("error");

		$map = [
			["d",null,"CGK"],
			["a",null,"DPS"],
			["date",null,date("Y-m-d", strtotime('tomorrow'))],
			["ret_date",null,date("Y-m-d", strtotime('yesterday'))],
			["adult",null,"1"],
			["child",null,"0"],
			["infant",null,"0"],
			["token",null,"token"],
			["page",null,"1"],
			["enable_ret_date",null,"true"]
		];

		$request = $this->requestMock($map);
		$order->getFlight($request);
	}
}
