<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'role' => 'admin',
        'company' => rand(1,10),
        'remember_token' => str_random(10)
    ];
});

$factory->define(App\Company::class, function(Faker\Generator $faker){
    return ['name' => $faker->company];
});
$factory->define(App\Claim::class,function(Faker\Generator $faker){
  return[
    'claim_type' => rand(1,2),
    'claim_data_id' => str_random(6),
    'claimer_id' => 1,
    'approver_id' => 2,
    'finance_id' => 3,
    'claim_status' => '1',
  ];
});
