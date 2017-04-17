<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\Claim;
use App\Http\Controllers\ApproverController;

class ApproverTest extends TestCase
{
    use DatabaseTransactions;

    private $ac;
    private $approver;
    private $claimer;
    private $finance;
    private $claim;
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

    public function testApprover()
    {
      $this->ac = new ApproverController();
      $this->claimer = $this->makeUser('claimer1','claimerr1@email.com','company1','claimer');
      $this->approver = $this->makeUser('approver1','approver1@email.com','company1','approver');
      $this->finance = $this->makeUser('finance1','finance1@email.com','company1','finance');
      $idClaimer = $this->claimer->id;
      $idApprover = $this->approver->id;
      $idFinance = $this->finance->id;
      $this->claim = $this->makeClaim('1',$idClaimer,$idApprover,$idFinance,'1');
      $id=$this->claim->id;
      $this->approveTest($id);
      $this->rejectTest($id);
      $this->showDetailTest($id);
    }
    public function approveTest($idClaim)
    {
        $this->actingAs($this->approver)
             ->withSession(['user' => $this->approver]);
        $response = $this->ac->approve($idClaim);
        $response->assertRedirect('/home/approver/received');
        $status = $this->claim->claim_status;
        $approve = 2;
        $this->assertEquals($status,$approve);
    }
    public function rejectTest($idClaim)
    {
       $this->actingAs($this->approver)
           ->withSession(['user' => $this->approver]);
        $response = $this->ac->approve($idClaim);
        $response->assertRedirect('/home/approver/received');
        $status = $this->claim->claim_status;
        $reject = 6;
        $this->assertEquals($status,$reject);
    }
    public function testShowReceived()
    {
        $this->actingAs($this->user)
             ->withSession(['user' => $this->user]);
        $response=$this->ac->showReceived();
        $this->assertInstanceOf('\Illuminate\View\View', $response);
    }
    public function testShowApproved()
    {
       $this->actingAs($this->approver)
           ->withSession(['user' => $this->approver]);
        $response=$this->ac->showApproved();
        $this->assertInstanceOf('\Illuminate\View\View', $response);
    }
    public function testShowRejected()
    {
       $this->actingAs($this->approver)
           ->withSession(['user' => $this->approver]);
        $response=$this->ac->showRejected();
        $this->assertInstanceOf('\Illuminate\View\View', $response);
    }
    public function showDetailTest($idClaim)
    {
       $this->actingAs($this->approver)
           ->withSession(['user' => $this->approver]);
        $response = $this->ac->show($idClaim);
        $this->assertInstanceOf('\Illuminate\View\View', $response);
    }
}
