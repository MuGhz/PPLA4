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
	use DatabaseTransactions;
	
	private $testData;
	
	private function generateTestData()
	{
		$company = $this->makeCompany('TestCompany');
		$approver = $this->makeUser('TestApprover','Approver@Company.test',$company,'approver');
		$finance = $this->makeUser('TestFinance','Finance@Company.test',$company,'finance');
		$claimer1 = $this->makeUser('TestClaimer1','Claimer1@Company.test',$company,'claimer');
		$claimer2 = $this->makeUser('TestClaimer2','Claimer2@Company.test',$company,'claimer');
		
		$this->testData = compact('company', 'approver', 'finance', 'claimer1', 'claimer2');
	}
	
	private function makeCompany($name)
	{
		return factory(User::class)->create([
			'name' => $name
		]);
	}
	
	private function makeUser($name,$email,$company,$role)
	{
		return factory(User::class)->create([
			'name' => $name,
			'email' => $email,
			'company' => $company->id,
			'role' => $role
		]);
	}
	
	public function setUp()
	{
		parent::setUp();
		$this->generateTestData();
	}
	
	public function makeClaim($claimer,$approver,$finance,$status)
	{
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
		$testData = $this->testData;
		$claimer = $testData['claimer1'];
		$approver = $testData['approver'];
		$finance = $testData['finance'];
		$claim = $this->makeClaim($claimer,$approver,$finance,1);

		$response = $this->actingAs($claimer)->get('/home/claim/delete/'.$claim->id);

		$this->assertDatabaseMissing('claims',['id' => $claim->id]);
	}
	
	public function testDeleteSameUserStatusNot1()
	{
		$testData = $this->testData;
		$claimer = $testData['claimer1'];
		$approver = $testData['approver'];
		$finance = $testData['finance'];
		$claim = $this->makeClaim($claimer,$approver,$finance,rand(2,5));

		$response = $this->actingAs($claimer)->get('/home/claim/delete/'.$claim->id);

		$this->assertDatabaseHas('claims',['id' => $claim->id]);
	}
	
	public function testDeleteDifferentUser()
	{
		$testData = $this->testData;
		$claimer1 = $testData['claimer1'];
		$claimer2 = $testData['claimer21'];
		$approver = $testData['approver'];
		$finance = $testData['finance'];
		$claim = $this->makeClaim($claimer1,$approver,$finance,rand(1,5));

		$response = $this->actingAs($claimer2)->get('/home/claim/delete/'.$claim->id);

		$this->assertDatabaseHas('claims',['id' => $claim->id]);
	}
}
