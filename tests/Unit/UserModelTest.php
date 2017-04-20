<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserModelTest extends TestCase
{
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
