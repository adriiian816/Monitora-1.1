<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'kode_opd',
    'nama_opd',
    'nama_pic',
    'whatsapp_pic',
    'email_pic',
    'alamat',
    'is_active',
])]
class Opd extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get users belonging to this OPD.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the assignments (tugas_opd) for this OPD.
     */
    public function tugasOpd(): HasMany
    {
        return $this->hasMany(TugasOpd::class);
    }

    /**
     * Get tasks assigned to this OPD.
     */
    public function tugas(): BelongsToMany
    {
        return $this->belongsToMany(Tugas::class, 'tugas_opd')
            ->withPivot(['id', 'status'])
            ->withTimestamps();
    }

    /**
     * Get the PIC users assigned to handle this OPD.
     */
    public function pics(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'pic_opd', 'opd_id', 'user_id')->withTimestamps();
    }

    /**
     * Scope query to only include active OPDs.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
