<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        $user = \App\Models\User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('Hello@123!'),
        ]);

        if ($user)
        {
            $token = $user->createToken('Api Auth Token');
            $user->token = $token->plainTextToken;
            $user->save();
        }
    }
}
