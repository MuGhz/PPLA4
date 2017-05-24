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
		$claim3 = $this->makeClaim(1, $claimer1->id, $approver->id, $finance->id, 3);
		
		$this->testData = [
			'company' => $company,
			'claimer' => [$claimer1, $claimer2],
			'approver' => $approver,
			'finance' => $finance,
			'claim' => [$claim1, $claim2, $claim3],
		];
	}
    
    public function makeMock($class, $constructorArgs, $methodVals)
    {
        $request = $this->getMockBuilder($class)
                        ->setConstructorArgs($constructorArgs)
                        ->setMethods(array_keys($methodVals))
                        ->getMock();
        foreach ($methodVals as $methodName => $methodCriteria) {
            $request->expects($this->any())
                    ->method($methodName)
                    ->will($this->returnValueMap($methodCriteria));
        }
        return $request;
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
		$response = null;
		
		try {
			$cc->show($toDeleteClaimId);
		}
		catch (HttpException $he) {
			$response = $he->getStatusCode();
		}
		$this->assertEquals(404,$response);
	}
	
	public function testUnauthorizedClaimerAccess()
	{
		$testData = $this->testData;
		$claim = $testData['claim'][0];
		$unauthorizedClaimer = $testData['claimer'][1];
		
		$this->actingAs($unauthorizedClaimer);
		
		$cc = new ClaimController();
		$response = null;
		
		try {
			$cc->show($claim->id);
		}
		catch (HttpException $he) {
			$response = $he->getStatusCode();
		}
		$this->assertEquals(403,$response);
	}
    
    public function testUploadProofWithFile()
    {
        $testData = $this->testData;
        $claim = $testData['claim'][2];
        $claimer = $testData['claimer'][0];
        
        $this->actingAs($claimer);
        
        $cc = new ClaimController();
        
        $fileMock = $this->makeMock(
            'Illuminate\Http\Request',
            [],
            [
                'move' => [[$this->any(), $this->any(), null]]
            ]
        );
        $request = $this->makeMock('Illuminate\Http\Request',
            [],
            [
                'hasFile' => [['proof',true]],
                'file' => [['proof',null,$fileMock]]
            ]
        );
        
        $cc->uploadProof($request, $claim->id);
        
        $this->assertDatabaseHas('claims',[
            'id' => $claim->id,
            'claim_status' => 4
        ]);
    }
    
    public function testUploadProofWithoutFile()
    {
        $testData = $this->testData;
        $claim = $testData['claim'][2];
        $claimer = $testData['claimer'][0];
        $claimOriginalStatus = $claim->claim_status;
        
        $this->actingAs($claimer);
        
        $cc = new ClaimController();
        
        $request = $this->makeMock(
            'Illuminate\Http\Request',
            [],
            [
                'hasFile' => ['proof',false]
            ]
        );
        
        $cc->uploadProof($request, $claim->id);
        
        $this->assertDatabaseHas('claims',[
            'id' => $claim->id,
            'claim_status' => $claimOriginalStatus
        ]);
    }
}
