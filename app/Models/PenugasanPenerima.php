<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PenugasanPenerima extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'penugasan_penerima';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'penugasan_id',
        'user_id',
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
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'dikonfirmasi_pada' => 'datetime',
            'dilaporkan_pada' => 'datetime',
        ];
    }

    /**
     * The penugasan this pivot belongs to.
     */
    public function penugasan(): BelongsTo
    {
        return $this->belongsTo(Penugasan::class, 'penugasan_id');
    }

    /**
     * The user this pivot belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Formatted file size of the submitted report.
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if (! $this->file_laporan_size) {
            return '-';
        }

        if ($this->file_laporan_size < 1024 * 1024) {
            return round($this->file_laporan_size / 1024, 1).' KB';
        }

        return round($this->file_laporan_size / (1024 * 1024), 2).' MB';
    }

    /**
     * Check if assignment is confirmed.
     */
    public function isConfirmed(): bool
    {
        return $this->status_konfirmasi === 'dikonfirmasi';
    }

    /**
     * Check if assignment was declined / rejected.
     */
    public function isRejected(): bool
    {
        return $this->status_konfirmasi === 'ditolak';
    }

    /**
     * Check if assignment is pending confirmation.
     */
    public function isPendingConfirmation(): bool
    {
        return empty($this->status_konfirmasi) || $this->status_konfirmasi === 'menunggu_konfirmasi';
    }

    /**
     * Check if report has been submitted.
     */
    public function isReportSubmitted(): bool
    {
        return $this->status_laporan === 'sudah_dilaporkan';
    }

    /**
     * Human-readable label for status konfirmasi.
     */
    public function getStatusKonfirmasiLabelAttribute(): string
    {
        return match ($this->status_konfirmasi) {
            'dikonfirmasi' => 'Dikonfirmasi',
            'ditolak' => 'Berhalangan / Ditolak',
            default => 'Menunggu Konfirmasi',
        };
    }
}
