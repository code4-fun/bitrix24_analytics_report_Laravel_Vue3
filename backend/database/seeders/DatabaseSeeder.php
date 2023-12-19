<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void {
    User::factory()->create([
      'name' => 'John Smith',
      'email' => 'admin@domain.com',
      'password' => Hash::make('1'),
      'role' => 1
    ]);

    User::factory(1)->create();
  }
}
