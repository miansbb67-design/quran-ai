<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
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
            'password' => 'hashed',
        ];
    }

    /* ── Role Helpers ──────────────────────────────── */

    public function isOwner(): bool
    {
        return false;
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'owner']);
    }

    public function isStudent(): bool
    {
        return in_array($this->role, ['user', 'student']);
    }

    public function isUser(): bool
    {
        return $this->isStudent();
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /* ── Relationships ─────────────────────────────── */

    public function preferences()
    {
        return $this->hasOne(UserPreference::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    public function readingProgress()
    {
        return $this->hasOne(ReadingProgress::class);
    }

    public function learningGoals()
    {
        return $this->hasMany(LearningGoal::class);
    }

    public function practiceSessions()
    {
        return $this->hasMany(PracticeSession::class);
    }

    public function memorizationProgress()
    {
        return $this->hasMany(MemorizationProgress::class);
    }

    public function revisionSchedules()
    {
        return $this->hasMany(RevisionSchedule::class);
    }

    public function consents()
    {
        return $this->hasMany(UserConsent::class);
    }
}
