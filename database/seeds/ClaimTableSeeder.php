<?php

use Illuminate\Database\Seeder;

use App\User;
use App\Claim;
class ClaimTableSeeder extends Seeder
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
        $claimer = User::claimer()->get();
        $limit = 500;
        for($i = 0; $i < $limit; $i++)  {
          $temp = $claimer[$faker->numberBetween(0,count($claimer)-1)];
          Claim::create([
            'claim_type' => $faker->numberBetween(1,2),
            'claim_data_id' => $faker->randomNumber($nbDigits = 6),
            'claimer_id' => $temp->id,
            'approver_id' => User::approver($temp)->id,
            'finance_id' => User::finance($temp)->id,
            'order_information' => '',
			'description' => '',
          ]);
        }
    }
}
