<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Http\Controllers\ClaimController;
use App\User;
use App\Claim;
use App\Company;

class DeleteClaimTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }
	
	public function makeClaimer($company)
	{
		$claimer = factory(User::class)->create([
			'company' => $company->id,
			'role' => 'claimer'
		]);
		return $claimer;
	}
	
	public function makeClaim($claimer,$status)
	{
		$approver = User::approver($claimer);
		$finance = User::finance($claimer);
		$claim = factory(Claim::class)->create([
			'claimer_id' => $claimer->id,
			'claim_status' => $status,
			'approver_id' => $approver->id,
			'finance_id' => $finance->id
		]);
		return $claim;
	}
	
	public function testDeleteSameUserStatus1()
	{
		$company = Company::find(1);
		$claimer = $this->makeClaimer($company);
		$claim = $this->makeClaim($claimer,1);

		$response = $this->actingAs($claimer)->get('/home/claim/delete/'.$claim->id);

		$this->assertDatabaseMissing('claims',['id' => $claim->id]);
	}
	
	public function testDeleteSameUserStatusNot1()
	{
		$company = Company::find(1);
		$claimer = $this->makeClaimer($company);
		$claim = $this->makeClaim($claimer,rand(2,5));

		$response = $this->actingAs($claimer)->get('/home/claim/delete/'.$claim->id);

		$this->assertDatabaseHas('claims',['id' => $claim->id]);
	}
	
	public function testDeleteDifferentUser()
	{
		$company = Company::find(1);
		$claimer1 = $this->makeClaimer($company);
		$claimer2 = $this->makeClaimer($company);
		$claim = $this->makeClaim($claimer1,rand(1,5));

		$response = $this->actingAs($claimer2)->get('/home/claim/delete/'.$claim->id);

		$this->assertDatabaseHas('claims',['id' => $claim->id]);
	}
}
