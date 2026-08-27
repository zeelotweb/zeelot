<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

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
        'google_id',
        'can_issue_discounts',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
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
            'can_issue_discounts' => 'boolean',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Admin-tier and above (admin or super_admin).
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin'], true);
    }

    /**
     * Has any backend access at all (staff and above).
     */
    public function isStaff(): bool
    {
        return in_array($this->role, ['staff', 'admin', 'super_admin'], true);
    }

    /**
     * Super admin can always issue discount codes; admins need the flag.
     */
    public function canIssueDiscounts(): bool
    {
        return $this->isSuperAdmin() || ($this->isAdmin() && $this->can_issue_discounts);
    }

    /**
     * Whether this user is allowed to change $target's role.
     */
    public function canManageRoleOf(User $target): bool
    {
        if ($target->isSuperAdmin()) {
            return false;
        }

        if ($this->isSuperAdmin()) {
            return in_array($target->role, ['admin', 'staff', 'customer'], true);
        }

        if ($this->isAdmin()) {
            return in_array($target->role, ['staff', 'customer'], true);
        }

        return false;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Project, $this>
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    protected static function booted(): void
    {
        static::saving(function (User $user) {
            if ($user->email === config('app.super_admin_email')) {
                $user->role = 'super_admin';
            }
        });
    }
}
