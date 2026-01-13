<?php

namespace App\Models;

use App\Models\Activity;
use App\Models\ActivityComment;
use App\Models\FriendRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'birth_date',
        'gender',
        'height_cm',
        'weight_kg',
        'avatar_url',
        'bio',
        'locale',
        'timezone',
        'role',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'birth_date' => 'date',
            'password' => 'hashed',
            'role' => 'string',
        ];
    }

    public function getAvatarUrlAttribute(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        if (Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }

        $baseUrl = rtrim(config('app.url'), '/');

        return $baseUrl.'/api/avatars/'.$this->getKey();
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function friendRequestsSent(): HasMany
    {
        return $this->hasMany(FriendRequest::class, 'sender_id');
    }

    public function friendRequestsReceived(): HasMany
    {
        return $this->hasMany(FriendRequest::class, 'receiver_id');
    }

    public function likedActivities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class, 'activity_likes');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ActivityComment::class);
    }
}
