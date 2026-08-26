<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::where('email', 'zeelotwebgrp@gmail.com')->first();

        if ($user) {
            $user->forceFill(['role' => 'admin'])->save();

            return;
        }

        User::create([
            'name' => 'Zeelot Web',
            'email' => 'zeelotwebgrp@gmail.com',
            'role' => 'admin',
            'password' => Str::random(32),
            'email_verified_at' => now(),
        ]);
    }
}
