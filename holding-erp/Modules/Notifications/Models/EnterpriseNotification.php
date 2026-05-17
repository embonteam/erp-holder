<?php

namespace Modules\Notifications\Models;

use App\Core\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnterpriseNotification extends BaseModel
{
    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'read_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
