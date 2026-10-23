<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'no_hp', 'password', 'role', 'opd_id', 'bidang'])]
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
    /**
     * Get the penugasan (internal assignments) received by this Bidang Ekop member.
     */
    public function penugasanDiterima(): BelongsToMany
    {
        return $this->belongsToMany(Penugasan::class, 'penugasan_penerima', 'user_id', 'penugasan_id')
            ->using(PenugasanPenerima::class)
            ->withPivot([
                'id',
                'status_penugasan',
                'status_konfirmasi',
                'catatan_konfirmasi',
                'dikonfirmasi_pada',
                'status_laporan',
                'file_laporan',
                'file_laporan_name',
                'file_laporan_size',
                'isi_laporan',
                'link_dokumen',
                'dilaporkan_pada',
            ])
            ->withTimestamps();
    }

    /**
     * Get the penugasan (internal assignments) created/given by this admin.
     */
    public function penugasanDiberi(): HasMany
    {
        return $this->hasMany(Penugasan::class, 'pemberi_id');
    }

    /**
     * Check if user is a Bidang Ekop member (staf internal Bappeda).
     */
    public function isBidangEkop(): bool
    {
        return $this->bidang === 'ekop';
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
     * Check if user is an anggota bidang.
     */
    public function isAnggotaBidang(): bool
    {
        return $this->role === 'anggota_bidang';
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
            'super_admin' => 'Super Admin',
            'admin' => 'PIC Bappeda',
            'anggota_bidang' => 'Anggota Bidang',
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
            'super_admin' => 'bg-purple-50 text-purple-700 border-purple-200/90',
            'admin' => 'bg-teal-50 text-teal-700 border-teal-200/90',
            'anggota_bidang' => 'bg-indigo-50 text-indigo-700 border-indigo-200/90',
            'opd' => 'bg-emerald-50 text-emerald-700 border-emerald-200/90',
            default => 'bg-slate-50 text-slate-700 border-slate-200/90',
        };
    }
}
