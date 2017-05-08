<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\ClaimController;
use App;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FinanceTest extends TestCase
{
	use DatabaseTransactions;
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	 //MOCK
	 public function requestMock($map)
	 {
			 $request = $this->getMockBuilder('Illuminate\Http\Request')
									 ->setMethods(array('input'))
									 ->getMock();
			 $request->expects($this->any())
									 ->method('input')
									 ->will($this->returnValueMap($map));
			 return $request;
	 }

	public function dataset()
	{
		$company      = factory(App\Company::class)->create(['name' => 'TestCompany']);
		$claimer      = factory(App\User::class)->create(['company' => $company->id, 'role' => 'claimer']);
		$approver     = factory(App\User::class)->create(['company' => $company->id, 'role' => 'approver']);
		$finance      = factory(App\User::class)->create(['company' => $company->id, 'role' => 'finance']);
		$otherFinance = factory(App\User::class)->create(['company' => $company->id, 'role' => 'finance']);
		$fc = new FinanceController();
		$map =[["alasan_reject",null,"inilah alasannya"]];
		$request = $this->requestMock($map);

		// DATA SET
		// Tes claim yang harus diapprove oleh finance ini (ditampilkan dalam showReceived)
		factory(App\Claim::class)->create([
			'claim_data_id'=>1,
			'claimer_id'=>$claimer->id,
			'approver_id'=>$approver->id,
			'finance_id'=>$finance->id,
			'claim_status'=>2
		]);

		// Tes claim dengan status baru dibuat (tidak ditampilkan di finance)
		factory(App\Claim::class)->create([
			'claim_data_id'=>2,
			'claimer_id'=>$claimer->id,
			'approver_id'=>$approver->id,
			'finance_id'=>$finance->id,
			'claim_status'=>1
		]);

		// Tes claim yang telah di-approve (ditampilkan dalam showApproved)
		factory(App\Claim::class)->create([
			'claim_data_id'=>3,
			'claimer_id'=>$claimer->id,
			'approver_id'=>$approver->id,
			'finance_id'=>$finance->id,
			'claim_status'=>4
		]);

		// Tes claim dengan status rejected (ditampilkan dalam showRejected)
		factory(App\Claim::class)->create([
			'claim_data_id'=>4,
			'claimer_id'=>$claimer->id,
			'approver_id'=>$approver->id,
			'finance_id'=>$finance->id,
			'claim_status'=>6
		]);

		// Tes claim yang harus di-approve/reject oleh finance lain (tidak ditampilkan)
		factory(App\Claim::class)->create([
			'claim_data_id'=>5,
			'claimer_id'=>$claimer->id,
			'approver_id'=>$approver->id,
			'finance_id'=>$otherFinance->id,
			'claim_status'=>2
		]);

		// Tes claim yang telah diapprove oleh finance lain (tidak ditampilkan)
		factory(App\Claim::class)->create([
			'claim_data_id'=>6,
			'claimer_id'=>$claimer->id,
			'approver_id'=>$approver->id,
			'finance_id'=>$otherFinance->id,
			'claim_status'=>4
		]);

		// Tes claim yang telah direject oleh finance lain (tidak ditampilkan)
		factory(App\Claim::class)->create([
			'claim_data_id'=>7,
			'claimer_id'=>$claimer->id,
			'approver_id'=>$approver->id,
			'finance_id'=>$otherFinance->id,
			'claim_status'=>6
		]);

		return array(
			'claimer' => $claimer,
			'approver' => $approver,
			'finance' => $finance,
			'company' => $company,
			'otherFinance' => $otherFinance,
			'fc' => $fc,
			'request' => $request
		);
	}

	public function testReceived() {
		extract($this->dataset());
		$this->actingAs($finance);
		$claims = $fc->showReceived()->getData()['allClaim'];
		$this->assertEquals(1, sizeof($claims));
		$claim = $claims[0];
		$this->assertEquals($claimer->id, $claim->claimer_id);
		$this->assertEquals($approver->id, $claim->approver_id);
		$this->assertEquals($finance->id, $claim->finance_id);
		$this->assertEquals(2, $claim->claim_status);
	}

	public function testApproved() {
		extract($this->dataset());
		$this->actingAs($finance);
		$claims = $fc->showApproved()->getData()['allClaim'];
		$this->assertEquals(1, sizeof($claims));
		$claim = $claims[0];
		$this->assertEquals($claimer->id, $claim->claimer_id);
		$this->assertEquals($approver->id, $claim->approver_id);
		$this->assertEquals($finance->id, $claim->finance_id);
		$this->assertEquals(4, $claim->claim_status);
	}

	public function testRejectedSuccess() {
		extract($this->dataset());
		$this->actingAs($finance);
		$claims = $fc->showRejected()->getData()['allClaim'];
		$this->assertEquals(1, sizeof($claims));
		$claim = $claims[0];
		$this->assertEquals($claimer->id, $claim->claimer_id);
		$this->assertEquals($approver->id, $claim->approver_id);
		$this->assertEquals($finance->id, $claim->finance_id);
		$this->assertEquals(6, $claim->claim_status);
	}


	public function testRejectedFailed() {
		extract($this->dataset());
		$this->actingAs($approver);
		$claim = factory(App\Claim::class)->create([
			'claim_data_id'=>1,
			'claimer_id'=>$claimer->id,
			'approver_id'=>$approver->id,
			'finance_id'=>$finance->id,
			'claim_status'=>2
		]);
		$cc = new ClaimController();
		$returnedStatusCode = null;
		try {
			$response = $cc->reject($request,$claim->id);
		}
		catch (HttpException $he) {
			$returnedStatusCode = $he->getStatusCode();
		}
		$this->assertEquals(403, $returnedStatusCode);
	}

  public function testRejectClaim() {
    extract($this->dataset());
		$this->actingAs($finance);
		$claims = $fc->showReceived()->getData()['allClaim'];
		$claim = $claims[0];
    $cc = new ClaimController();
    $cc->reject($request,$claim->id);
    $claims = $fc->showRejected()->getData()['allClaim'];
    $this->assertEquals(2, sizeof($claims));
  }
}
