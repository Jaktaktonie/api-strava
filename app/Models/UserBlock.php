<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class UserBlock extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'blocker_id',
        'blocked_id',
    ];

    public function blocker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'blocker_id');
    }

    public function blocked(): BelongsTo
    {
        return $this->belongsTo(User::class, 'blocked_id');
    }

    /**
     * Lista ID użytkowników, z którymi podany user ma relację blokady (obie strony).
     *
     * @return Collection<int, int>
     */
    public static function relatedIds(int $userId): Collection
    {
        return self::query()
            ->where('blocker_id', $userId)
            ->orWhere('blocked_id', $userId)
            ->get()
            ->flatMap(fn (UserBlock $block) => [$block->blocker_id === $userId ? $block->blocked_id : $block->blocker_id])
            ->unique()
            ->values();
    }

    public static function existsBetween(int $userA, int $userB): bool
    {
        return self::query()
            ->where(function ($q) use ($userA, $userB) {
                $q->where('blocker_id', $userA)->where('blocked_id', $userB);
            })
            ->orWhere(function ($q) use ($userA, $userB) {
                $q->where('blocker_id', $userB)->where('blocked_id', $userA);
            })
            ->exists();
    }
}
