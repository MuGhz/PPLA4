<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserModelTest extends TestCase
{
    use DatabaseTransactions;

    protected $company;
    protected $claimer;
    protected $approver;
    protected $finance;

    public function setUp()
    {
        parent::setUp();
        $this->company = $this->makeCompany('Test Company');
        $this->claimer = $this->makeUser('Claimer 1', 'Claimer1@Company.test', $company->id, 'claimer');
        $this->approver = $this->makeUser('Approver', 'Appover@Company.test', $company->id, 'approver');
        $this->finance = $this->makeUser('Finance', 'Finance@Company.test', $company->id, 'finance');
    }
    /**
     * A basic test example.
     *
     * @return void
     */

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

    public function testExample()
    {
        $this->assertTrue(true);
    }
}
