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
    $this ->be(User::where('role','=','approver')->first());
    $claim = Claim::where('approver_id','=',$this->id)->where('claim_status','=','1')->first();
    $ac = new ApproverController();

    public function approveTest($idClaim)
    {
      $response = $ac->approve($idClaim);
      $response -> assertRedirect('/home/approver/received');

    }
    public function rejectTest($idClaim)
    {
      $response = $ac->approve($idClaim);
      $response -> assertRedirect('/home/approver/received');
    }
    public function showReceivedTest()
    {

    }
    public function showApprovedTest()
    {

    }
    public function showRejectedTest()
    {

    }
    public function showDetailTest($idClaim)
    {

    }
}
