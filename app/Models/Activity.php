<?php

namespace App\Models;

use App\Models\ActivityComment;
use App\Models\ActivityLike;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activity extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'type',
        'start_time',
        'end_time',
        'duration_seconds',
        'distance_meters',
        'avg_speed_kmh',
        'avg_pace',
        'route',
        'notes',
        'photo_url',
        'gpx_path',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'route' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(ActivityLike::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ActivityComment::class);
    }
}
