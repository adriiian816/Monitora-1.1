<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'opd_id',
    'action',
    'ip_address',
    'user_agent',
    'device_type',
    'platform',
    'browser',
    'city',
    'region',
    'country',
    'location',
    'latitude',
    'longitude',
    'logged_at',
])]
class LoginLog extends Model
{
    use HasFactory, MassPrunable;

    /**
     * Get the prunable model query for records older than 30 days.
     */
    public function prunable(): Builder
    {
        return static::where('logged_at', '<', now()->subDays(30));
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'logged_at' => 'datetime',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    /**
     * User associated with the log entry.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * OPD associated with the log entry.
     */
    public function opd(): BelongsTo
    {
        return $this->belongsTo(Opd::class);
    }

    /**
     * Human readable action label.
     */
    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'login' => 'Masuk (Login)',
            'logout' => 'Keluar (Logout)',
            default => ucfirst((string) $this->action),
        };
    }

    /**
     * Badge CSS classes based on action.
     */
    public function getActionBadgeClassesAttribute(): string
    {
        return match ($this->action) {
            'login' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'logout' => 'bg-amber-100 text-amber-800 border-amber-200',
            default => 'bg-slate-100 text-slate-800 border-slate-200',
        };
    }

    /**
     * Badge CSS classes based on device type.
     */
    public function getDeviceBadgeClassesAttribute(): string
    {
        return match ($this->device_type) {
            'mobile' => 'bg-sky-100 text-sky-800 border-sky-200',
            'tablet' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
            default => 'bg-blue-100 text-blue-800 border-blue-200',
        };
    }

    /**
     * Formatted string of platform and browser.
     */
    public function getDeviceInfoAttribute(): string
    {
        $platform = $this->platform ?: 'Unknown OS';
        $browser = $this->browser ?: 'Browser';

        return "{$platform} • {$browser}";
    }

    /**
     * Scope to filter logs by OPD ID.
     */
    public function scopeForOpd(Builder $query, int|string $opdId): Builder
    {
        return $query->where('opd_id', $opdId);
    }

    /**
     * Scope to filter logs by action.
     */
    public function scopeAction(Builder $query, string $action): Builder
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to filter logs by device type.
     */
    public function scopeDeviceType(Builder $query, string $deviceType): Builder
    {
        return $query->where('device_type', $deviceType);
    }

    /**
     * Scope to search logs by keyword across user, opd, ip, and location.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $term = trim($term);

        return $query->where(function (Builder $q) use ($term) {
            $q->where('ip_address', 'like', "%{$term}%")
                ->orWhere('location', 'like', "%{$term}%")
                ->orWhere('city', 'like', "%{$term}%")
                ->orWhere('region', 'like', "%{$term}%")
                ->orWhere('platform', 'like', "%{$term}%")
                ->orWhere('browser', 'like', "%{$term}%")
                ->orWhereHas('user', function (Builder $userQuery) use ($term) {
                    $userQuery->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                })
                ->orWhereHas('opd', function (Builder $opdQuery) use ($term) {
                    $opdQuery->where('nama_opd', 'like', "%{$term}%")
                        ->orWhere('kode_opd', 'like', "%{$term}%");
                });
        });
    }
}
