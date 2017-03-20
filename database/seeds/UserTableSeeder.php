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
        $faker = Faker\Factory::create();
        $company = \App\Company::all()->all();
        $limit = 5;

        for($j = 0; $j < 2; $j++) {
          for($i = 0; $i < $limit; $i++)  {
            \App\User::create([
              "name" => "admin$j$i",
              "name" => "admin$j$i",
              "email" => $faker->companyEmail,
              "password" => bcrypt("admin$j$i"),
              "role" => "admin",
              "company" => $company[$i]->id,
            ]);
            \App\User::create([
              "name" => "claimer$j$i",
              "name" => "claimer$j$i",
              "email" => $faker->companyEmail,
              "password" => bcrypt("claimer$j$i"),
              "role" => "claimer",
              "company" => $company[$i]->id,
            ]);
            \App\User::create([
              "name" => "approver$j$i",
              "name" => "approver$j$i",
              "email" => $faker->companyEmail,
              "password" => bcrypt("approver$j$i"),
              "role" => "approver",
              "company" => $company[$i]->id,
            ]);
            \App\User::create([
              "name" => "finance$j$i",
              "name" => "finance$j$i",
              "email" => $faker->companyEmail,
              "password" => bcrypt("finance$j$i"),
              "role" => "finance",
              "company" => $company[$i]->id,
            ]);
          }
        }
    }
}
