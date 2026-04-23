<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class ActivityLogService
{
    public function log(?int $actorId, Model $subject, string $event, string $description, array $properties = []): void
    {
        ActivityLog::create([
            'actor_id' => $actorId,
            'subject_type' => $subject::class,
            'subject_id' => $subject->getKey(),
            'event' => $event,
            'description' => $description,
            'properties' => $properties,
        ]);
    }
}
