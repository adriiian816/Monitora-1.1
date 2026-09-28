<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'tugas_opd_id',
    'file_path',
    'file_original_name',
    'file_size',
    'konfirmasi_fisik',
    'nama_pengantar',
    'nomor_surat',
    'tanggal_penyerahan_fisik',
    'catatan_opd',
    'catatan_admin',
    'status_verifikasi',
    'verified_at',
    'verified_by',
])]
class Submission extends Model
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
            'konfirmasi_fisik' => 'boolean',
            'tanggal_penyerahan_fisik' => 'date',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * The assignment this submission belongs to.
     */
    public function tugasOpd(): BelongsTo
    {
        return $this->belongsTo(TugasOpd::class, 'tugas_opd_id');
    }

    /**
     * Admin who verified this submission.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Formatted file size (KB / MB).
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if (! $this->file_size) {
            return '-';
        }

        if ($this->file_size < 1024 * 1024) {
            return round($this->file_size / 1024, 1).' KB';
        }

        return round($this->file_size / (1024 * 1024), 2).' MB';
    }
}
