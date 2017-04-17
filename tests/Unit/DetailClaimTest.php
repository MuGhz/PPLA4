<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Http\Controllers\ClaimController;
use App\Company;
use App\Claim;
use App\User;

class DetailClaimTest extends TestCase
{
    use DatabaseTransactions;
	
	private $testData;
	
	public function setUp()
	{
		parent::setUp();
		$this->generateTestData();
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
	
	private function makeClaim($claimType, $claimerId, $approverId, $financeId, $claimStatus)
	{
		return factory(Claim::class)->create([
			'claim_type' => $claimType,
			'claimer_id' => $claimerId,
			'approver_id' => $approverId,
			'finance_id' => $financeId,
			'claim_status' => $claimStatus
		]);
	}
	
	private function generateTestData()
	{
		$company = $this->makeCompany('Test Company');
		$claimer = $this->makeUser('Claimer', 'Claimer@Company.test', $company->id, 'claimer');
		$approver = $this->makeUser('Approver', 'Approver@Company.test', $company->id, 'approver');
		$finance = $this->makeUser('Finance', 'Finance@Company.test', $company->id, 'finance');
		$claim1 = $this->makeClaim(1, $claimer->id, $approver->id, $finance->id, 1);
		$claim2 = $this->makeClaim(1, $claimer->id, $approver->id, $finance->id, 1);
		$claim3 = $this->makeClaim(1, $claimer->id, $approver->id, $finance->id, 1);
		
		$this->testData = array(
			'company' => $company,
			'claimer' => $claimer,
			'approver' => $approver,
			'finance' => $finance,
			'claim' => array($claim1, $claim2, $claim3)
		);
	}
	
	public function testReturnedCorrectView()
	{
		$testData = $this->testData;
		$claim = $testData['claim'][0];
		
		$this->actingAs($testData['claimer']);
		
		$cc = new ClaimController();
		
		$response = $cc->show($claim->id)
					   ->getName();
		
		$this->assertEquals('claim.viewclaim', $response);
	}
	
	public function testReturnedViewHasData()
	{
		$testData = $this->testData;
		$claim = $testData['claim'][0];
		
		$this->actingAs($testData['claimer']);
		
		$cc = new ClaimController();
		
		$response = $cc->show($claim->id)
					   ->getData();
		
		$this->assertArrayHasKey('detailClaim',$response);
	}
	
	public function testReturnedDataIsCorrect()
	{
		$testData = $this->testData;
		$claim = $testData['claim'][0];
		
		$this->actingAs($testData['claimer']);
		
		$cc = new ClaimController();
		
		$response = $cc->show($claim->id)
					   ->getData();
		
		$this->assertEquals($response['detailClaim'][0]->id,$claim->id);
	}
}
