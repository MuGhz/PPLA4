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
        $approver = User::approver()->get();
        $finance = User::finance()->get();
        $limit = 500;
        for($i = 0; $i < $limit; $i++)  {
          Claim::create([
            'claim_type' => $faker->numberBetween(1,2),
            'claim_data_id' => $faker->randomNumber($nbDigits = 6),
            'claimer_id' => $claimer[$faker->numberBetween(0,count($claimer)-1)]->id,
            'approver_id' => $approver[$faker->numberBetween(0,count($approver)-1)]->id,
            'finance_id' => $finance[$faker->numberBetween(0,count($finance)-1)]->id,
          ]);
        }
    }
}
