<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB, Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();

        DB::table('users')->insert([
            'first_name' => 'Mr.',
            'last_name' => 'Admin',
            'email' => 'admin@gmail.com',
            'type' => 'admin',
            'password' => Hash::make('123456'),
        ]);
    }
}
