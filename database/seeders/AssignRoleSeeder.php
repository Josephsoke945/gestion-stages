<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AssignRoleSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        if ($user) {
            $user->assignRole('stagiaire');
        }
    }
} 