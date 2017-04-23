<?php

namespace Tests\Unit;

use Tests\TestCase;
use Laracast\Integrated\Extension\Laravel;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HotelTest extends TestCase
{
    /**
     * Test halaman
     *
     * @return void
     */
    public function testPage()
    {
      $this->visit('/home/order/hotel')
          ->see('Booking Hotel');
    }

    /**
    *
    * Cek kembalian API list hotel
    *
    */
    public function testHotelList() {
      $this->visit('/home/order/hotel')
          ->type('jakarta','city')
          ->type(date('Y-m-d'),"in")
          ->type((new \DateTime('tomorrow'))->format('Y-m-d'),'out')
          ->select('1','room')
          ->select('1','adult')
          ->select('0','child')
          ->press('Cari Hotel')
          ->seejson();
    }

    /**
    *
    * Cek kembalian API detail hotel
    *
    */
    public function testHotelDetail() {
      $this->visit('/home/order/hotel')
          ->type('jakarta','city')
          ->type(date('Y-m-d'),"in")
          ->type((new DateTime('tomorrow'))->format('Y-m-d'),'out')
          ->select('1','room')
          ->select('1','adult')
          ->select('0','child')
          ->press('Cari Hotel')
          ->press('detail')
          ->seejson();
    }


    /**
    *
    * Cek book hotel, masuk database atau tidak
    *
    */
    public function testHotelBook() {
      $this->visit('/home/order/hotel')
          ->type('jakarta','city')
          ->type(date('Y-m-d'),"in")
          ->type((new DateTime('tomorrow'))->format('Y-m-d'),'out')
          ->select('1','room')
          ->select('1','adult')
          ->select('0','child')
          ->press('Cari Hotel')
          ->press('detail')
          ->press('book')
          ->seeInDatabase('users', ['email' => 'sergiturbadenas@gmail.com',
                                    'name'  => 'Sergi Tur Badenas', ]);
    }

    /**
    *
    * Cek tanggal checkout < tanggal checkin
    *
    */
    public function testDateDiff()  {

        $this->visit('/home/order/hotel')
            ->type('jakarta','city')
            ->type("2017-01-04","in")
            ->type("2017-01-03",'out')
            ->select('1','room')
            ->select('1','adult')
            ->select('0','child')
            ->press('Cari Hotel')
            ->seejson();
    }

    /**
    *
    * Cek tanggal checkin < tanggal hari ini
    *
    */
    public function testDateMinus() {

        $this->visit('/home/order/hotel')
            ->type('jakarta','city')
            ->type(date('Y-m-d',strtotime("-1 days")),"in")
            ->type(date('Y-m-d'),'out')
            ->select('1','room')
            ->select('1','adult')
            ->select('0','child')
            ->press('Cari Hotel')
            ->seejson();
    }
}
