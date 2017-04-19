<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Http\Controllers\ClaimController;
use App\Company;
use App\Claim;
use App\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
		$claimer1 = $this->makeUser('Claimer 1', 'Claimer1@Company.test', $company->id, 'claimer');
		$claimer2 = $this->makeUser('Claimer 2', 'Claimer2@Company.test', $company->id, 'claimer');
		$approver = $this->makeUser('Approver', 'Approver@Company.test', $company->id, 'approver');
		$finance = $this->makeUser('Finance', 'Finance@Company.test', $company->id, 'finance');
		$claim1 = $this->makeClaim(1, $claimer1->id, $approver->id, $finance->id, 1);
		$claim2 = $this->makeClaim(1, $claimer1->id, $approver->id, $finance->id, 1);
		
		$this->testData = [
			'company' => $company,
			'claimer' => [$claimer1, $claimer2],
			'approver' => $approver,
			'finance' => $finance,
			'claim' => [$claim1, $claim2],
		];
	}
	
	public function testReturnedCorrectView()
	{
		$testData = $this->testData;
		$claim = $testData['claim'][0];
		$claimer = $testData['claimer'][0];
		
		$this->actingAs($claimer);
		
		$cc = new ClaimController();
		
		$response = $cc->show($claim->id)
					   ->getName();
		
		$this->assertEquals('claim.viewclaim', $response);
	}
	
	public function testReturnedViewHasData()
	{
		$testData = $this->testData;
		$claim = $testData['claim'][0];
		$claimer = $testData['claimer'][0];
		
		$this->actingAs($claimer);
		
		$cc = new ClaimController();
		
		$response = $cc->show($claim->id)
					   ->getData();
		
		$this->assertArrayHasKey('detailClaim',$response);
	}
	
	public function testReturnedDataIsCorrect()
	{
		$testData = $this->testData;
		$claim = $testData['claim'][0];
		$claimer = $testData['claimer'][0];
		
		$this->actingAs($claimer);
		
		$cc = new ClaimController();
		
		$response = $cc->show($claim->id)
					   ->getData();
		
		$this->assertEquals($response['detailClaim'][0]->id,$claim->id);
	}
	
	public function testNonexistentClaimAccess()
	{
		$testData = $this->testData;
		$toDeleteClaim = $testData['claim'][1];
		$toDeleteClaimId = $toDeleteClaim->id;
		$claimer = $testData['claimer'][0];
		
		$this->actingAs($claimer);
		$toDeleteClaim->delete();
		
		$cc = new ClaimController();
		
		try {
			$cc->show($toDeleteClaimId);
		}
		catch (HttpException $he) {
			$this->assertEquals(404,$he->getStatusCode());
		}
	}
	
	public function testUnauthorizedClaimerAccess()
	{
		$testData = $this->testData;
		$claim = $testData['claim'][0];
		$unauthorizedClaimer = $testData['claimer'][1];
		
		$this->actingAs($unauthorizedClaimer);
		
		$cc = new ClaimController();
		
		try {
			$cc->show($claim->id);
		}
		catch (HttpException $he) {
			$this->assertEquals(403,$he->getStatusCode());
		}
	}
}
