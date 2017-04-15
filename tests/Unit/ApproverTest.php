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

    $user = User::where('role','=','approver')->first();
    $claim = Claim::where('approver_id', '=', $this->user->id)->where('claim_status', '=', '1')->first();
    $ac = new ApproverController();

    public function testApprover()
    {
      $id=$this->claim->id;
      $this->approveTest($id);
      $this->rejectTest($id);
      $this->showDetailTest($id);
    }
    public function approveTest($idClaim)
    {
        $this->actingAs($this->user)
             ->withSession(['user' => $this->user]);
        $response = $this->ac->approve($idClaim);
        $response->assertRedirect('/home/approver/received');
        $status = $this->claim->claim_status;
        $approve = 2;
        $this->assertEquals($status,$approve);
    }
    public function rejectTest($idClaim)
    {
        $this->actingAs($this->user)
             ->withSession(['user' => $this->user]);
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
        $this->actingAs($this->user)
             ->withSession(['user' => $this->user]);
        $response=$this->ac->showApproved();
        $this->assertInstanceOf('\Illuminate\View\View', $response);
    }
    public function testShowRejected()
    {
        $this->actingAs($this->user)
             ->withSession(['user' => $this->user]);
        $response=$this->ac->showRejected();
        $this->assertInstanceOf('\Illuminate\View\View', $response);
    }
    public function showDetailTest($idClaim)
    {
        $this->actingAs($this->user)
             ->withSession(['user' => $this->user]);
        $response = $this->ac->show($idClaim);
        $this->assertInstanceOf('\Illuminate\View\View', $response);
    }
}
