<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{

 protected $model = User::class;

 /**
  * Define the model's default state.
  *
  * @return array
  */
 public function definition()
 {
  return [
   'nama' => $this->faker->name(),
   'email' => $this->faker->unique()->safeEmail(),
   'role' => 'user',
   'password' => 'password',
   'status' => 'aktif',
   'last_login' => now(),
  ];
 }

 /**
  * Indicate that the model's email address should be unverified.
  *
  * @return \Illuminate\Database\Eloquent\Factories\Factory
  */
 public function unverified()
 {
  return $this->state(function (array $attributes) {
   return [
    'email_verified_at' => null,
   ];
  });
 }
}
