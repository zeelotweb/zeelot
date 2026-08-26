<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * The super admin role itself is enforced by User::booted() for whichever
     * account matches config('app.super_admin_email') — this seeder only
     * needs to make sure that account exists.
     */
    public function run(): void
    {
        $email = config('app.super_admin_email');

        if (User::where('email', $email)->exists()) {
            return;
        }

        User::create([
            'name' => 'Zeelot Web',
            'email' => $email,
            'password' => Str::random(32),
            'email_verified_at' => now(),
        ]);
    }
}
