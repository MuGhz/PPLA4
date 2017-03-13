<?php

use Illuminate\Database\Seeder;

class CompanyTableSeeder extends Seeder
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
        $limit = 5;
        for($i = 0; $i < $limit; $i++)  {
          \App\Company::create([
            'name' => $faker->company
          ]);
        }
    }
}
