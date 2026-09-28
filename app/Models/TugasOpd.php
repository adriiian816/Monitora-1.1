<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'tugas_id',
    'opd_id',
    'status',
])]
class TugasOpd extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tugas_opd';

    /**
     * The parent task.
     */
    public function tugas(): BelongsTo
    {
        return $this->belongsTo(Tugas::class, 'tugas_id');
    }

    /**
     * The assigned OPD.
     */
    public function opd(): BelongsTo
    {
        return $this->belongsTo(Opd::class, 'opd_id');
    }

    /**
     * Submission record for this assignment.
     */
    public function submission(): HasOne
    {
        return $this->hasOne(Submission::class, 'tugas_opd_id');
    }

    /**
     * Status human readable label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'belum_dikumpulkan' => 'Belum Dikumpulkan',
            'sudah_dikumpulkan' => 'Menunggu Verifikasi',
            'diterima' => 'Diterima',
            'perlu_revisi' => 'Perlu Revisi',
            'terlambat' => 'Terlambat',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }
}
