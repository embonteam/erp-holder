<?php

namespace Modules\Audit\Models;

use App\Core\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends BaseModel
{
    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForSubject(Builder $query, string $subjectType, int|string $subjectId): Builder
    {
        return $query
            ->where('subject_type', $subjectType)
            ->where('subject_id', $subjectId);
    }

    public function subjectLabel(): string
    {
        if ($this->subject_type === null) {
            return '-';
        }

        return class_basename($this->subject_type).' #'.$this->subject_id;
    }
}
