<?php

namespace App\Core\Activity;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoginLogger
{
    /**
     * @param array<string, mixed> $metadata
     */
    public function log(User $user, string $event, Request $request, array $metadata = []): void
    {
        DB::table('login_logs')->insert([
            'user_id' => $user->id,
            'event' => $event,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'occurred_at' => now(),
            'metadata' => json_encode($metadata),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
