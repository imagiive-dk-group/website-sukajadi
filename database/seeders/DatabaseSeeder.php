<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@desawisatasukajadi.com')],
            [
                'name' => env('ADMIN_NAME', 'Super Admin'),
                'password' => env('ADMIN_PASSWORD', 'ubah-password-ini'),
                'role' => User::ROLE_SUPER_ADMIN,
                'is_active' => true,
            ],
        );

        $this->call([
            SettingSeeder::class,
            TourismSeeder::class,
            CommunitySeeder::class,
            PartnerSeeder::class,
        ]);
    }
}
