<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Http\Controllers\HomeController;
use App\Company;
use App\Claim;
use App\User;

class ClaimListTest extends TestCase
{
	use DatabaseTransactions;
	
    public function testReturnsView()
    {
		$company = factory(Company::class)->create(['name' => 'Test Company']);
		$user = factory(User::class)->create([
			'name' => 'Test User',
			'email' => 'Test_User_Email@testdomain.test',
			'password' => 'Test User Password',
			'role' => 'claimer',
			'company' => $company->id
		]);
		
		$this->actingAs($user)
			 ->withSession(['user' => $user]);
		
		$hc = new HomeController();
		$response = $hc->index();
		
		// This should be changed to comparing with view manually made here
		$this->assertInstanceof('Illuminate\View\View',$response);
    }
	
	// public function testViewHasAllClaimsData()
    // {
		// $company = factory(Company::class)->create(['name' => 'Test Company']);
		// $user = factory(User::class)->create([
			// 'name' => 'Test User',
			// 'email' => 'Test_User_Email@testdomain.test',
			// 'password' => 'Test User Password',
			// 'role' => 'claimer',
			// 'company' => $company->id
		// ]);
		
		// $response = $this->actingAs($user)
						 // ->withSession(['user' => 'user'])
						 // ->get('/home');
		
		// $hc = new HomeController();
		// $response = $hc->index();
		// $response = $this->createTestResponse($response)
						 // ->assertViewHas('allClaim');
		// $response = $response->call('GET','/home');
		
		// $this->assertArrayHasKey('allClaim',$response->getOriginalContent());
		// $this->assertEquals($response->getOriginalContent(),'This');
    // }
}
