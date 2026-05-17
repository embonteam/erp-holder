<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Audit\Models\Role;
use Modules\Holding\Models\Holding;

class BootstrapOwnerSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $ownerRole = Role::query()->where('code', 'owner')->firstOrFail();
        $holding = Holding::query()->where('code', 'ICON-HOLDING')->firstOrFail();

        User::query()->updateOrCreate(
            ['email' => env('BOOTSTRAP_OWNER_EMAIL', 'owner@holding.test')],
            [
                'role_id' => $ownerRole->id,
                'holding_id' => $holding->id,
                'name' => 'Owner Holding',
                'password' => Hash::make(env('BOOTSTRAP_OWNER_PASSWORD', 'password123456')),
                'is_active' => true,
            ],
        );
    }
}
