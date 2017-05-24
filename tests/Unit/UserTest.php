<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\Company;

class UserTest extends TestCase
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
	
	private function generateTestData()
	{
		// Test companies
		$company1 = $this->makeCompany('Company 1');
		$company2 = $this->makeCompany('Company 2');
		// Company IDs
		$id1 = $company1->id;
		$id2 = $company2->id;
		// Test admins
		$admin1 =$this->makeUser('Admin 1','Admin@Company1.test', $id1, 'admin');
		$admin2 =$this->makeUser('Admin 2','Admin@Company2.test', $id2, 'admin');
		// Test claimers
		$claimer11 =$this->makeUser('Claimer 1-1','Claimer1@Company1.test', $id1, 'claimer');
		$claimer12 =$this->makeUser('Claimer 1-2','Claimer1@Company2.test', $id1, 'claimer');
		$claimer21 =$this->makeUser('Claimer 2-1','Claimer2@Company1.test', $id2, 'claimer');
		$claimer22 =$this->makeUser('Claimer 2-2','Claimer2@Company2.test', $id2, 'claimer');
		// Test approvers
		$approver1 =$this->makeUser('Approver 1','Approver@Company1.test', $id1, 'approver');
		$approver2 =$this->makeUser('Approver 2','Approver@Company2.test', $id2, 'approver');
		// Test finances
		$finance1 =$this->makeUser('Finance 1','Finance@Company1.test', $id1,'finance');
		$finance2 =$this->makeUser('Finance 2','Finance@Company2.test', $id2,'finance');
		
		$this->testData = array(
			'key' => array($id1, $id2),
			'admin' => array(
				$id1 => $admin1,
				$id2 => $admin2
			),
			'company' => array(
				$id1 => $company1,
				$id2 => $company2
			),
			'claimer' => array(
				$id1 => array($claimer11, $claimer12),
				$id2 => array($claimer21, $claimer22)
			),
			'approver' => array(
				$id1 => $approver1,
				$id2 => $approver2
			),
			'finance' => array(
				$id1 => $finance1,
				$id2 => $finance2
			)
		);
	}
	
	public function testScopeClaimer()
	{
		$testData = $this->testData;
		
		$testClaimers = array_merge($testData['claimer'][$testData['key'][0]],$testData['claimer'][$testData['key'][1]]);
		
		$queryResult = User::claimer()->get();
		
		// Manual assertion - All test elements contained(O(N^2))
		$allClaimersContained = true;
		foreach ($testClaimers as $claimer) {
			$claimerContained = false;
			foreach ($queryResult as $resultElement) {
				$claimerContained = $claimerContained || ($resultElement->id == $claimer->id);
			}
			$allClaimersContained = $allClaimersContained && $claimerContained;
		}
		
		$this->assertTrue($allClaimersContained);
	}
	
	public function testScopeApprover()
	{
		$testData = $this->testData;
		
		$testClaimer = $testData['claimer'][$testData['key'][0]][0];
		$testApprover  = $testData['approver'][$testData['key'][0]];
		
		$queryResult = User::approver($testClaimer);

		$this->assertEquals($queryResult->id,$testApprover->id);
	}
	
	public function testScopeFinance()
	{
		$testData = $this->testData;
		
		$testClaimer = $testData['claimer'][$testData['key'][0]][0];
		$testFinance  = $testData['finance'][$testData['key'][0]];
		
		$queryResult = User::finance($testClaimer);

		$this->assertEquals($queryResult->id,$testFinance->id);
	}
}
