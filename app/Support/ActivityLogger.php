<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ActivityLogger
{
    /** @param array<string, mixed> $properties */
    public static function record(?User $actor, Model $subject, string $event, string $description, array $properties = []): void
    {
        $subject->activityLogs()->create([
            'actor_id' => $actor?->id,
            'event' => $event,
            'description' => $description,
            'properties' => $properties ?: null,
        ]);
    }
}
