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

    public function setUp()
    {
        parent::setUp();
        $this->token = '{"diagnostic":{"status":200},"output_type":"json","token":"3b00ae1956bac189967cfff807fff929c4e6415b"}';
        $this->hotelList = '{"diagnostic":{"status":200},"output_type":"json","search_queries":{"q":"jakarta","uid":"","startdate":"2017-04-20","enddate":"2017-04-21","night":"1","room":1,"adult":"1","child":0,"sort":false,"minstar":0,"maxstar":5,"minprice":"213000.00","maxprice":"1000000000.00","distance":100000},"results":{"result":[{"province_name":"DKI Jakarta","kecamatan_name":"Gambir","kelurahan_name":"Petojo Selatan","business_uri":"https:\/\/api-sandbox.tiket.com\/hotel\/indonesia\/jakarta\/jakarta\/neo-hotel-cideng-jakarta?startdate=2017-04-20&night=1&room=1&adult=1&child=0&is_partner=0&star_rating%5B0%5D=0&star_rating%5B1%5D=1&star_rating%5B2%5D=2&star_rating%5B3%5D=3&star_rating%5B4%5D=4&star_rating%5B5%5D=5&hotel_chain=0&facilities=0&latitude=0&longitude=0&distance=0&uid=business%3A222778",hotel_id":"21427344"}]},"pagination":{"total_found":62,"current_page":"1","offset":"20","lastPage":4},"login_status":"false","token":"25599b029a0c3e3a87bde80091cf15bed064c099"}';
        $this->hotelDetail = '{"diagnostic":{"status":200},"output_type":"json","primaryPhotos":"https:\/\/sandbox.tiket.com\/img\/business\/f\/a\/business-favehotel-tanah-abang-cideng-jakarta-hotel-jakarta-pusat2677.s.jpg","breadcrumb":{"business_id":"222778","business_uri":"https:\/\/api-sandbox.tiket.com\/neo-hotel-cideng-jakarta","business_name":"Favehotel Tanah Abang","area_id":"87528","area_name":"Monas","kelurahan_id":"69","kelurahan_name":"Petojo Selatan","kecamatan_id":"19","kecamatan_name":"Gambir","city_id":"21681","city_name":"Jakarta","province_id":"13","province_name":"DKI Jakarta","country_id":"id","country_name":"Indonesia","continent_id":"1","continent_name":"Asia","kelurahan_uri":"https:\/\/api-sandbox.tiket.com\/search\/hotel?uid=city:21681"},"login_status":"false","token":"c8d2e6aa8d84d93cde61fdc9d63e6215f6a1ef69"}';
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

    public function curlMock($returnValue)
    {
        $order = $this->getMockBuilder('App\Http\Controllers\OrderController')
                    ->setMethods(array('curlCall'))
                    ->getMock();

        $order->expects($this->any())
              ->method("curlCall")
              ->will($this->returnValue("$returnValue"));
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

    public function testBookHotel()
    {
        $order = $this->curlMock("success");

        $map = [
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
        ]);
    }
}