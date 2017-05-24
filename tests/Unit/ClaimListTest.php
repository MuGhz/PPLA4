<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ClaimController;
use App\Company;
use App\Claim;
use App\User;

class ClaimListTest extends TestCase
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
	
	private function makeClaim($claimType, $claimerId, $approverId, $financeId, $claimStatus)
	{
		return factory(Claim::class)->create([
			'claim_type' => $claimType,
			'claimer_id' => $claimerId,
			'approver_id' => $approverId,
			'finance_id' => $financeId,
			'claim_status' => $claimStatus
		]);
	}
	
	private function generateTestData()
	{
		$company = $this->makeCompany('Test Company');
		$claimer1 = $this->makeUser('Claimer 1', 'Claimer1@Company.test', $company->id, 'claimer');
		$claimer2 = $this->makeUser('Claimer 2', 'Claimer2@Company.test', $company->id, 'claimer');
		$approver = $this->makeUser('Approver', 'Appover@Company.test', $company->id, 'approver');
		$finance = $this->makeUser('Finance', 'Finance@Company.test', $company->id, 'finance');
		$claim11 = $this->makeClaim(1, $claimer1->id, $approver->id, $finance->id, 1);
		$claim12 = $this->makeClaim(2, $claimer1->id, $approver->id, $finance->id, 2);
		$claim13 = $this->makeClaim(2, $claimer1->id, $approver->id, $finance->id, 1);
		$claim14 = $this->makeClaim(2, $claimer1->id, $approver->id, $finance->id, 2);
		$claim21 = $this->makeClaim(3, $claimer2->id, $approver->id, $finance->id, 3);
		$claim22 = $this->makeClaim(2, $claimer2->id, $approver->id, $finance->id, 4);
		$claim23 = $this->makeClaim(2, $claimer2->id, $approver->id, $finance->id, 3);
		$claim24 = $this->makeClaim(2, $claimer2->id, $approver->id, $finance->id, 4);
		
		$this->testData = array(
			'company' => $company,
			'claimer' => array($claimer1, $claimer2),
			'approver' => $approver,
			'finance' => $finance,
			'claim' => array(
				$claimer1->id => array($claim11, $claim12, $claim13, $claim14),
				$claimer2->id => array($claim21, $claim22, $claim23, $claim24)
			)
		);
	}
	
    public function testViewHasAllClaimData()
    {
		$testData = $this->testData;
		$user = $testData['claimer'][0];
		
		$this->actingAs($user)
			 ->withSession(['user' => $user]);
		
		$hc = new HomeController();
		$response = $hc->index()
		               ->getData();
		
		$this->assertArrayHasKey('allClaim',$response);
    }
	
	public function testReturnedClaimsBelongToUser()
    {
		$testData = $this->testData;
		$user = $testData['claimer'][0];
		$allClaim = $testData['claim'][$user->id];
		
		$this->actingAs($user)
			 ->withSession(['user' => $user]);
		
		$hc = new HomeController();
		$response = $hc->index()
		               ->getData()
					   ['allClaim'];
		
		$returnedClaimsBelongToUser = $this->arrayElementsHasSpecificValueForAttribute('claimer_id', $user->id, $response);
		
		$this->assertTrue($returnedClaimsBelongToUser);
    }
	
	public function testReturnsAllUserClaims()
    {
		$testData = $this->testData;
		$user = $testData['claimer'][0];
		$allClaim = $testData['claim'][$user->id];
		
		$this->actingAs($user)
			 ->withSession(['user' => $user]);
		
		$hc = new HomeController();
		$response = $hc->index()
		               ->getData()
					   ['allClaim'];
		
		$allUserClaimsReturned = $this->arrayHasSameDataForAttribute('claimer_id',$response,$allClaim);
		
		$this->assertTrue($allUserClaimsReturned);
    }
	
	public function testSpecificClaimList()
	{
		$testData = $this->testData;
		$user = $testData['claimer'][0];
		$allClaim = $testData['claim'][$user->id];
		$claimStat = 1;
		$expectedClaims = [$allClaim[0],$allClaim[2]];
		$cc = new ClaimController();
		$response = $cc->index($claimStat)
					   ->getData()
					   ['claims'];
		
		$allExpectedClaimsReturned = $this->arrayHasSameDataForAttribute('id',$response,$expectedClaims);
	}
	
	private function arrayElementsHasSpecificValueForAttribute($attribute, $value, $array)
	{
		$allEqual = true;
		foreach ($array as $element) {
			$allEqual = $allEqual && ($element->$attribute == $value);
		}
		return $allEqual;
	}
	
	private function arrayHasSameDataForAttribute($attribute, $array1, $array2)
	{
		$allExist = true;
		foreach ($array1 as $element1) {
			$exist = false;
			foreach ($array2 as $element2) {
				$exist = $exist || ($element1->$attribute == $element2->$attribute);
			}
			$allExist = $allExist && $exist;
		}
		return $allExist && (count($array1) == count($array2));
	}
}
