<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class NotificationPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create manageNotifications permission
        Permission::firstOrCreate([
            'name' => 'manageNotifications',
            'guard_name' => 'web'
        ]);
    }
}
