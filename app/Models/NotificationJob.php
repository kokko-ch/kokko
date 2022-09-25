<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property User $user
 */
class NotificationJob extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'minute',
        'hour',
        'day',
        'month',
        'weekday',
        'timezone',
        'event',
        'title',
        'content',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'content' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Interact with the notification job's timezone.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, string>
     */
    public function timezone(): Attribute
    {
        $user = $this->user ?? auth()->user();

        return Attribute::make(
            get: fn ($value) => $value ?? $user->timezone,
            set: fn ($value) => $value == optional($user)->timezone ? null : $value,
        );
    }

    /**
     * Get the user that owns the notification job.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, NotificationJob>
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
