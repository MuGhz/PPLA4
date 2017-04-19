<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\Claim;
use App\Company;
use App\Http\Controllers\ApproverController;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ApproverTest extends TestCase
{
    use DatabaseTransactions;

    private $ac;
    private $approver1;
    private $approver2;
    private $claimer;
    private $finance;
    private $sentClaim1;
    private $sentClaim2;
    private $approvedClaim1;
    private $approvedClaim2;
    private $rejectedClaim1;
    private $rejectedClaim2;
    private $company;

    public function setUp()
	{
		parent::setUp();
		$this->generateTestData();
	}

    public function generateTestData()
	{
		$this->ac = new ApproverController();
		$this->company = $this->makeCompany('company1');
		$company = $this->company;
		$this->claimer = $this->makeUser('claimer1','claimer1@email.com',$company->id,'claimer');
		$this->approver1 = $this->makeUser('approver1','approver1@email.com',$company->id,'approver');
		$this->approver2 = $this->makeUser('approver2','approver2@email.com',$company->id,'approver');
		$this->finance = $this->makeUser('finance1','finance1@email.com',$company->id,'finance');
		$idClaimer = $this->claimer->id;
		$idApprover = $this->approver1->id;
		$idFinance = $this->finance->id;
		$this->sentClaim1 = $this->makeClaim(1,$idClaimer,$idApprover,$idFinance,1);
		$this->sentClaim2 = $this->makeClaim(1,$idClaimer,$idApprover,$idFinance,1);
		$this->approvedClaim1 = $this->makeClaim(1,$idClaimer,$idApprover,$idFinance,2);
		$this->approvedClaim2 = $this->makeClaim(1,$idClaimer,$idApprover,$idFinance,2);
		$this->rejectedClaim1 = $this->makeClaim(1,$idClaimer,$idApprover,$idFinance,6);
		$this->rejectedClaim2 = $this->makeClaim(1,$idClaimer,$idApprover,$idFinance,6);
	}

    private function makeCompany($name)
	{
		return factory(Company::class)->create([
			'name' => $name
		]);
	}
	
    public function makeUser($name, $email, $company, $role)
	{
		return factory(User::class)->create([
			'name' => $name,
			'email' => $email,
			'company' => $company,
			'role' => $role,
		]);
	}
    public function makeClaim($claim_type, $claimer_id, $approver_id,$finance_id,$claim_status)
	{
		return factory(Claim::class)->create([
			'claim_type' => $claim_type,
			'claimer_id' => $claimer_id,
			'approver_id' => $approver_id,
			'finance_id' => $finance_id,
			'claim_status' => $claim_status,
		]);
	}
	
    public function testShowReceived()
    {
        $this->actingAs($this->approver1);
        $response=$this->ac->showReceived();
        $data = $response->getData();
        $retrievedClaims = $data['claims'];
		
		$expectedClaims = [$this->sentClaim1, $this->sentClaim2];
		$allClaimsHandledByApprover = true;
		$allExpectedClaimsReturned = true;
		// $sentence = "";
		foreach ($expectedClaims as $expectedClaim) {
			$claimReturned = false;
			foreach ($retrievedClaims as $retrievedClaim) {
				$claimReturned = $claimReturned || ($expectedClaim->id == $retrievedClaim->id);
			}
			$allExpectedClaimsReturned = $allExpectedClaimsReturned && $claimReturned;
		}
		foreach ($retrievedClaims as $retrievedClaim) {
			$allClaimsHandledByApprover = $allClaimsHandledByApprover && ($retrievedClaim->approver_id == $this->approver1->id);
		}
		
		$this->assertEquals(count($expectedClaims), count($retrievedClaims));
		$this->assertTrue($allClaimsHandledByApprover);
		$this->assertTrue($allExpectedClaimsReturned);
    }
    public function testShowApproved()
    {
        $this->actingAs($this->approver1);
        $response = $this->ac->showApproved();
        $data = $response->getData();
        $retrievedClaims = $data['claims'];
		
		$expectedClaims = [$this->approvedClaim1, $this->approvedClaim2];
		$allClaimsHandledByApprover = true;
		$allExpectedClaimsReturned = true;
		foreach ($expectedClaims as $expectedClaim) {
			$claimReturned = false;
			foreach ($retrievedClaims as $retrievedClaim) {
				$claimReturned = $claimReturned || ($expectedClaim->id == $retrievedClaim->id);
			}
			$allExpectedClaimsReturned = $allExpectedClaimsReturned && $claimReturned;
		}
		foreach ($retrievedClaims as $retrievedClaim) {
			$allClaimsHandledByApprover = $allClaimsHandledByApprover && ($retrievedClaim->approver_id == $this->approver1->id);
		}
		
		$this->assertEquals(count($expectedClaims), count($retrievedClaims));
		$this->assertTrue($allClaimsHandledByApprover);
		$this->assertTrue($allExpectedClaimsReturned);
    }
    public function testShowRejected()
    {
        $this->actingAs($this->approver1);
        $response = $this->ac->showRejected();
        $data = $response->getData();
        $retrievedClaims = $data['claims'];
		
		$expectedClaims = [$this->rejectedClaim1, $this->rejectedClaim2];
		$allClaimsHandledByApprover = true;
		$allExpectedClaimsReturned = true;
		foreach ($expectedClaims as $expectedClaim) {
			$claimReturned = false;
			foreach ($retrievedClaims as $retrievedClaim) {
				$claimReturned = $claimReturned || ($expectedClaim->id == $retrievedClaim->id);
			}
			$allExpectedClaimsReturned = $allExpectedClaimsReturned && $claimReturned;
		}
		foreach ($retrievedClaims as $retrievedClaim) {
			$allClaimsHandledByApprover = $allClaimsHandledByApprover && ($retrievedClaim->approver_id == $this->approver1->id);
		}
		
		$this->assertEquals(count($expectedClaims), count($retrievedClaims));
		$this->assertTrue($allClaimsHandledByApprover);
		$this->assertTrue($allExpectedClaimsReturned);
    }

    public function testApproveSuccess()
    {
        $this->actingAs($this->approver1);
        $idClaim = $this->sentClaim1->id;
        $response = $this->ac->approve($idClaim);
        //$status = $this->approvedClaim->claim_status;
        $approve = 2;
        //$this->assertEquals($status,$approve);
        $this->assertDatabaseHas('claims',['id'=>$idClaim,'claim_status'=>$approve]);
    }
	
    public function testRejectSuccess()
    {
       $this->actingAs($this->approver1);
        $idClaim = $this->sentClaim1->id;
        $response = $this->ac->reject($idClaim);
        //$status = $this->rejectedClaim->claim_status;
        $reject = 6;
        $this->assertDatabaseHas('claims',['id'=>$idClaim,'claim_status'=>$reject]);
        //$this->assertEquals($status,$reject);
    }
	
	public function testApproveFailNonsentStatus()
	{
		$this->actingAs($this->approver1);
		$nonSentClaim = $this->makeClaim(1,$this->claimer->id,$this->approver1->id,$this->finance->id,2);
		$returnedStatusCode = null;
		try {
			$this->ac->approve($nonSentClaim->id);
		}
		catch (HttpException $he) {
			$returnedStatusCode = $he->getStatusCode();
		}
		$this->assertEquals(403, $returnedStatusCode);
	}
	
	public function testRejectFailNonsentStatus()
	{
		$this->actingAs($this->approver1);
		$nonSentClaim = $this->makeClaim(1,$this->claimer->id,$this->approver1->id,$this->finance->id,2);
		$returnedStatusCode = null;
		try {
			$this->ac->reject($nonSentClaim->id);
		}
		catch (HttpException $he) {
			$returnedStatusCode = $he->getStatusCode();
		}
		$this->assertEquals(403, $returnedStatusCode);
	}
	
	public function testApproveFailUnauthorizedApprover()
    {
        $this->actingAs($this->approver2);
        $idClaim = $this->sentClaim1->id;
		$returnedStatusCode = null;
		try {
			$response = $this->ac->approve($idClaim);
		}
		catch (HttpException $he) {
			$returnedStatusCode = $he->getStatusCode();
		}
		$this->assertEquals(403, $returnedStatusCode);
    }
	
	public function testRejectFailUnauthorizedApprover()
    {
        $this->actingAs($this->approver2);
        $idClaim = $this->sentClaim1->id;
		$returnedStatusCode = null;
		try {
			$response = $this->ac->reject($idClaim);
		}
		catch (HttpException $he) {
			$returnedStatusCode = $he->getStatusCode();
		}
		$this->assertEquals(403, $returnedStatusCode);
    }
	
    public function testShowExisting()
    {
      $this->actingAs($this->approver1);
      $id = $this->sentClaim1->id;
      $response = $this->ac->show($id);
      $data = $response->getData();
      $claim = $data['detailClaim'][0];
      $this->assertEquals($claim->id, $id);
    }
	
	public function testShowNonexisting()
	{
		$toDeleteClaim = $this->makeClaim(1,$this->claimer->id,$this->approver1->id,$this->finance->id,2);
		$this->actingAs($this->approver1);
		$id = $toDeleteClaim->id;
		$toDeleteClaim->delete();
		try {
			$this->ac->show($id);
		}
		catch (HttpException $he) {
			$this->assertEquals(404,$he->getStatusCode());
		}
	}
}
