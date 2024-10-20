<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create(
            [

                'name' => 'admin',
                'userid'=>"admin",
                'email' => "admin@pedrollobd.com",
                'email_verified_at' => now(),
                'role' => 'admin',
                'status' => "active",
                'password' => Hash::make('admin'),
                'remember_token' => Str::random(10)
            ]
        );

    }
}
