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
    public function setup()
    {

    }
    public function approveTest($idClaim)
    {
        $this->withoutMiddleware();
        $this->be(Claim::where('claim_status','=','1')->first());

    }
    public function rejectTest($idClaim)
    {

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
