<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'no_hp', 'password', 'role', 'opd_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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

    /**
     * Get the OPD associated with the user.
     */
    public function opd(): BelongsTo
    {
        return $this->belongsTo(Opd::class);
    }

    /**
     * Get the OPDs assigned to/handled by this PIC user.
     */
    public function handledOpds(): BelongsToMany
    {
        return $this->belongsToMany(Opd::class, 'pic_opd', 'user_id', 'opd_id')->withTimestamps();
    }

    /**
     * Get IDs of OPDs handled by this user.
     * Super Admin has access to all active OPDs.
     *
     * @return array<int>
     */
    public function handledOpdIds(): array
    {
        if ($this->isSuperAdmin()) {
            return Opd::pluck('id')->toArray();
        }

        if ($this->relationLoaded('handledOpds')) {
            return $this->handledOpds->pluck('id')->toArray();
        }

        return $this->handledOpds()->pluck('opds.id')->toArray();
    }

    /**
     * Determine if this user handles/manages the given OPD.
     */
    public function handlesOpd(int|string|null $opdId): bool
    {
        if (empty($opdId)) {
            return false;
        }

        if ($this->isSuperAdmin()) {
            return true;
        }

        return in_array((int) $opdId, $this->handledOpdIds(), true);
    }

    /**
     * Check if user is a super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is either a super admin or an admin.
     */
    public function isAdminOrSuperAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'admin'], true);
    }

    /**
     * Check if user is an OPD user.
     */
    public function isOpd(): bool
    {
        return $this->role === 'opd';
    }

    /**
     * Get human-readable role label.
     */
    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'super_admin' => 'Admin',
            'admin' => 'PIC',
            'opd' => 'Staf OPD',
            default => ucfirst(str_replace('_', ' ', (string) $this->role)),
        };
    }

    /**
     * Get Tailwind CSS badge classes based on role.
     */
    public function getRoleBadgeClassesAttribute(): string
    {
        return match ($this->role) {
            'super_admin' => 'bg-purple-100 text-purple-800 border-purple-200',
            'admin' => 'bg-blue-100 text-blue-800 border-blue-200',
            'opd' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            default => 'bg-slate-100 text-slate-800 border-slate-200',
        };
    }
}
