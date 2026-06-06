<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Domain;
use App\Models\CheckLog;
use App\Models\CheckSetting;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'admin@admin.com',
            'password' => '1',
            'telegram_chat_id' => '620475914',
        ]);

        $users = User::all();

        foreach ($users as $user) {
            $domains = Domain::factory(10)->create(['user_id' => $user->id]);
            $settings = CheckSetting::factory(5)->create(['user_id' => $user->id]);

            foreach ($domains as $domain) {
                foreach ($settings as $setting) {
                    CheckLog::factory()->create([
                        'domain_id' => $domain->id,
                        'check_setting_id' => $setting->id,
                    ]);
                }
            }
        }
    }
}
