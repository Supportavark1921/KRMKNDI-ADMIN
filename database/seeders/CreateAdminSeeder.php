<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CreateAdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'admin@krmkndi.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('Admin@1234'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        $user->syncRoles(['admin']);

        $this->command->info('Admin ready — email: admin@krmkndi.com  password: Admin@1234');
    }
}
