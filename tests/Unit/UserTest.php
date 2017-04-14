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

	public function generateTestData()
	{
		// Test companies
		$company1 = factory(Company::class)->create([
			'name' => 'Company 1'
		]);
		$company2 = factory(Company::class)->create([
			'name' => 'Company 2'
		]);
		// Test admins
		$admin1 = factory(User::class)->create([
			'name' => 'Admin 1',
			'company' => $company1->id,
			'role' => 'admin'
		]);
		$admin2 = factory(User::class)->create([
			'name' => 'Admin 2',
			'company' => $company2->id,
			'role' => 'admin'
		]);
		// Test claimers
		$claimer11 = factory(User::class)->create([
			'name' => 'Claimer 1-1',
			'company' => $company1->id,
			'role' => 'claimer'
		]);
		$claimer12 = factory(User::class)->create([
			'name' => 'Claimer 1-2',
			'company' => $company1->id,
			'role' => 'claimer'
		]);
		$claimer21 = factory(User::class)->create([
			'name' => 'Claimer 2-1',
			'company' => $company2->id,
			'role' => 'claimer'
		]);
		$claimer22 = factory(User::class)->create([
			'name' => 'Claimer 2-2',
			'company' => $company2->id,
			'role' => 'claimer'
		]);
		// Test approvers
		$approver1 = factory(User::class)->create([
			'name' => 'Approver 1',
			'company' => $company1->id,
			'role' => 'approver'
		]);
		$approver2 = factory(User::class)->create([
			'name' => 'Approver 2',
			'company' => $company2->id,
			'role' => 'approver'
		]);
		// Test finances
		$finance1 = factory(User::class)->create([
			'name' => 'Finance 1',
			'company' => $company1->id,
			'role' => 'finance'
		]);
		$finance2 = factory(User::class)->create([
			'name' => 'Finance 2',
			'company' => $company2->id,
			'role' => 'finance'
		]);
		
		$id1 = $company1->id;
		$id2 = $company2->id;
		return array(
			'key' => array($id1, $id2),
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
		$testData = $this->generateTestData();
		// $queryResult = User::claimer()->get();
		// $claimers = array_merge($testData['claimer'][$testData['key'][0]],$testData['claimer'][$testData['key'][1]]);
		// $this->assertEquals($queryResult,$queryResult->intersect($claimers));
	}
	
	public function testScopeApprover()
	{
		$testData = $this->generateTestData();
		$testClaimer = $testData['claimer'][$testData['key'][0]][0];
		$queryResult = User::approver($testClaimer)->get();
	}
}
