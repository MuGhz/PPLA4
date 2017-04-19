<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;

class LoginPageTest extends DuskTestCase
{
	// Self-made, complementing AcachaAdminLTELaravelTest
    public function testLoginAccess()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->clickLink('Start')
					->assertPathIs('/login');
        });
    }
	
	public function testLoginEmptyField()
	{
		$this->browse(function (Browser $browser) {
			$browser->visit('/login')
					->press('Sign In')
					->assertSee('The email field is required')
					->assertSee('The password field is required');
		});
	}
	
	public function testLoginEmailOnly()
	{
		$this->browse(function (Browser $browser) {
			$user = User::find(1);
			$browser->visit('/login')
					->type('email',$user->email)
					->press('Sign In')
					->assertSee('The password field is required');
		});
	}
	
	public function testLoginPasswordOnly()
	{
		$this->browse(function (Browser $browser) {
			$user = User::find(1);
			$browser->visit('/login')
					->type('password',$user->name)
					->press('Sign In')
					->assertSee('The email field is required');
		});
	}
	
	public function testLoginUnregistered()
	{
		$this->browse(function (Browser $browser) {
			$browser->visit('/login')
					->type('email','nonexistentuser@fakedomain.moc')
					->type('password','unimpressivepassword')
					->press('Sign In')
					->assertSee('These credentials do not match our records');
		});
	}
	
	public function testLoginWrongPassword()
	{
		$this->browse(function (Browser $browser) {
			$user = User::find(1);
			$browser->visit('/login')
					->type('email',$user->email)
					->type('password','AnythingThatCannotPossiblyBeCorrectPassword')
					->press('Sign In')
					->assertSee('These credentials do not match our records');
		});
	} 
	
	public function testLoginValid()
	{
		$this->browse(function (Browser $browser) {
			$user = User::find(1);
			$browser->visit('/login')
					->type('email',$user->email)
					->type('password',$user->name)
					->press('Sign In')
					->assertPathIs('/home');
		});
	} 
}
