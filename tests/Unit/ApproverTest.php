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
    private $sentClaim;
    private $approvedClaim;
    private $rejectedClaim;
	private $toDeleteClaim;
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
          $this->claimer = $this->makeUser('claimer1','claimerr1@email.com',$company->id,'claimer');
          $this->approver1 = $this->makeUser('approver1','approver1@email.com',$company->id,'approver');
          $this->approver2 = $this->makeUser('approver2','approver2@email.com',$company->id,'approver');
          $this->finance = $this->makeUser('finance1','finance1@email.com',$company->id,'finance');
          $idClaimer = $this->claimer->id;
          $idApprover = $this->approver1->id;
          $idFinance = $this->finance->id;
          $this->sentClaim = $this->makeClaim('1',$idClaimer,$idApprover,$idFinance,'1');
          $this->approvedClaim = $this->makeClaim('1',$idClaimer,$idApprover,$idFinance,'2');
          $this->rejectedClaim = $this->makeClaim('1',$idClaimer,$idApprover,$idFinance,'6');
          $this->toDeleteClaim = $this->makeClaim('1',$idClaimer,$idApprover,$idFinance,'1');
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

    public function testApprove()
    {
        $this->actingAs($this->approver1)
             ->withSession(['user' => $this->approver1]);
        $idClaim = $this->approvedClaim->id;
        $response = $this->ac->approve($idClaim);
        //$status = $this->approvedClaim->claim_status;
        $approve = 2;
        //$this->assertEquals($status,$approve);
        $this->assertDatabaseHas('claims',['id'=>$idClaim,'claim_status'=>$approve]);
    }
	
    public function testReject()
    {
       $this->actingAs($this->approver1)
           ->withSession(['user' => $this->approver1]);
        $idClaim = $this->rejectedClaim->id;
        $response = $this->ac->reject($idClaim);
        //$status = $this->rejectedClaim->claim_status;
        $reject = 6;
        $this->assertDatabaseHas('claims',['id'=>$idClaim,'claim_status'=>$reject]);
        //$this->assertEquals($status,$reject);
    }
	
	public function testApproveFail()
    {
        $this->actingAs($this->approver2)
             ->withSession(['user' => $this->approver2]);
        $idClaim = $this->approvedClaim->id;
		try {
			$response = $this->ac->approve($idClaim);
		}
		catch (HttpException $he) {
			$this->assertEquals(403,$he->getStatusCode());
		}
    }
	
	public function testRejectFail()
    {
        $this->actingAs($this->approver2)
             ->withSession(['user' => $this->approver2]);
        $idClaim = $this->approvedClaim->id;
		try {
			$response = $this->ac->reject($idClaim);
		}
		catch (HttpException $he) {
			$this->assertEquals(403,$he->getStatusCode());
		}
    }
	
    public function testShowExisting()
    {
      $this->actingAs($this->approver1)
           ->withSession(['user' => $this->approver1]);
      $id = $this->sentClaim->id;
      $response = $this->ac->show($id);
      $data = $response->getData();
      $claim = $data['detailClaim'][0];
      $this->assertEquals($claim->id, $id);
    }
	
	public function testShowNonexisting()
	{
		$this->actingAs($this->approver1)
			 ->withSession(['user' => $this->approver1]);
		$id = $this->toDeleteClaim->id;
		$this->toDeleteClaim->delete();
		try {
			$this->ac->show($id);
		}
		catch (HttpException $he) {
			$this->assertEquals(404,$he->getStatusCode());
		}
	}
	
    public function testShowReceived()
    {
        $this->actingAs($this->approver1)
             ->withSession(['user' => $this->approver1]);
        $response=$this->ac->showReceived();
        $data = $response->getData();
        $retrievedClaim = $data['claims'][0];
        $this->assertEquals($retrievedClaim->id, $this->sentClaim->id);
    }
    public function testShowApproved()
    {
        $this->actingAs($this->approver1)
           ->withSession(['user' => $this->approver1]);
        $response=$this->ac->showApproved();
        $data = $response->getData();
        $retrievedClaim = $data['claims'][0];
        $this->assertEquals($retrievedClaim->id, $this->approvedClaim->id);
    }
    public function testShowRejected()
    {
        $this->actingAs($this->approver1)
           ->withSession(['user' => $this->approver1]);
        $response=$this->ac->showRejected();
        $data = $response->getData();
        $retrievedClaim = $data['claims'][0];
        $this->assertEquals($retrievedClaim->id, $this->rejectedClaim->id);
    }
}
