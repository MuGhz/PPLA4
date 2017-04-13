<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ApproverClaimListTest extends TestCase
{
    /**
     * Test halaman approver
     *
     * @return void
     */
    public function testPage()
    {
        $this->visit('/home/approver/received')
            ->see('Approve List');

    }
    /**
    *Test halaman detail
    *@return void
    */
    public function testPageDetail()
    {
      
    }
}
