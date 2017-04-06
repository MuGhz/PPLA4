<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class HotelTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testHotelPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/home/order/hotel')
                    ->assertSee('Booking Hotel');
        });
    }

    /**
    *
    * Cek kembalian API list hotel
    * @return void
    */
    public function testSearchHotel() {

      $this->browse(function(Browser $browser)  {
        $browser->visit('/home/order/hotel')
            ->type('jakarta','city')
            ->type(date('Y-m-d'),"in")
            ->type((new DateTime('tomorrow'))->format('Y-m-d'),'out')
            ->select('1','room')
            ->select('1','adult')
            ->select('0','child')
            ->press('Cari Hotel')
            ->seejson();
      });
    }
    /**
    *
    * Cek kembalian API detail hotel
    *
    */
    public function testHotelDetail() {

      $this->browse(function(Browser $browser)  {
        $browser->visit('/home/order/hotel')
            ->type('jakarta','city')
            ->type(date('Y-m-d'),"in")
            ->type((new DateTime('tomorrow'))->format('Y-m-d'),'out')
            ->select('1','room')
            ->select('1','adult')
            ->select('0','child')
            ->press('Cari Hotel')
            ->press('detail')
            ->seejson();
      });
    }


    /**
    *
    * Cek book hotel, masuk database atau tidak
    *
    */
    public function testHotelBook() {

      $this->browse(function (Browser $browser) {
        $browser->visit('/home/order/hotel')
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
      });

    }

    /**
    *
    * Cek tanggal checkout < tanggal checkin
    *
    */
    public function testDateDiff()  {

      $this->browse(function (Browser $browser) {
        $browser->visit('/home/order/hotel')
            ->type('jakarta','city')
            ->type("2017-01-04","in")
            ->type("2017-01-03",'out')
            ->select('1','room')
            ->select('1','adult')
            ->select('0','child')
            ->press('Cari Hotel')
            ->seejson();
      });
    }

    /**
    *
    * Cek tanggal checkin < tanggal hari ini
    *
    */
    public function testDateMinus() {

      $this->browse(function (Browser $browser) {
        $browser->visit('/home/order/hotel')
            ->type('jakarta','city')
            ->type(date('Y-m-d',strtotime("-1 days")),"in")
            ->type(date('Y-m-d'),'out')
            ->select('1','room')
            ->select('1','adult')
            ->select('0','child')
            ->press('Cari Hotel')
            ->seejson();
      });
    }
}
