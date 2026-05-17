<?php

namespace App\Core\Activity;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityLogger
{
    /**
     * @param array<string, mixed> $metadata
     * @param array<string, mixed>|null $oldValues
     * @param array<string, mixed>|null $newValues
     */
    public function log(
        string $event,
        ?User $user = null,
        ?Model $subject = null,
        array $metadata = [],
        ?Request $request = null,
        ?array $oldValues = null,
        ?array $newValues = null,
    ): void {
        DB::table('activity_logs')->insert([
            'user_id' => $user?->id,
            'event' => $event,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'old_values' => $oldValues === null ? null : json_encode($oldValues),
            'new_values' => $newValues === null ? null : json_encode($newValues),
            'metadata' => json_encode($metadata),
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
