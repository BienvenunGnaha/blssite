<?php

use Faker\Generator as Faker;
use Illuminate\Support\Str;

use function GuzzleHttp\json_encode;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\User::class, function (Faker $faker) {
    return [
        'firstname' => $faker->firstname,
        'plan_id' => 3,
        'ref_id' => '271',
        'lastname' => $faker->lastname,
        'accept_term' => 'on',
        'is_test' => true,
        'temp' => false,
        'email' => $faker->unique()->safeEmail,
        'username' => $faker->unique()->userName,
        'mobile' => $faker->unique()->phoneNumber,
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'address' => [
            'address' => $faker->streetAddress,
            'city' => $faker->city,
            'state' => $faker->state,
            'zip' => $faker->postcode,
            'country' => $faker->country,
        ],
        'remember_token' => Str::random(10),
        'created_at' => \Carbon\Carbon::now(),
        'updated_at' => \Carbon\Carbon::now(),
    ];
});
