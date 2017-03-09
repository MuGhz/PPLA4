<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        \App\User::create([
          'name' => 'admin',
          'username' => 'admin',
          'password' => bcrypt('admin'),
          'role' => '0',
        ]);
        \App\User::create([
          'name' => 'claimer',
          'username' => 'claimer',
          'password' => bcrypt('claimer'),
          'role' => '1'
        ]);
        \App\User::create([
          'name' => 'approver',
          'username' => 'approver',
          'password' => bcrypt('approver'),
          'role' => '2',
        ]);
        \App\User::create([
          'name' => 'finance',
          'username' => 'finance',
          'password' => bcrypt('finance'),
          'role' => '3',
        ]);
    }
}
