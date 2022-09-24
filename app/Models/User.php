<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail, FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'timezone',
        'ifttt_key',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'ifttt_key' => 'encrypted',
    ];

    /**
     * Interact with the user's timezone.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function timezone(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ?? config('app.timezone'),
            set: fn ($value) => $value == config('app.timezone') ? null : $value,
        );
    }

    /**
     * Route notifications for the IFTTT channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForIfttt($notification)
    {
        $event = $notification->notificationJob->event;
        $key = $this->ifttt_key;

        return "https://maker.ifttt.com/trigger/{$event}/with/key/{$key}";
    }

    /**
     * Get the notification jobs for the user.
     */
    public function notificationJobs()
    {
        return $this->hasMany(NotificationJob::class);
    }

    /**
     * Check whether the user is allowed to access Filament.
     */
    public function canAccessFilament(): bool
    {
        return true;
    }
}
