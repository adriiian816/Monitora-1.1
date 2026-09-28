<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'tugas_id',
    'opd_id',
    'tipe_pengingat',
    'no_tujuan',
    'isi_pesan',
    'status',
    'response_payload',
    'sent_at',
])]
class WhatsappLog extends Model
{
    use HasFactory, MassPrunable;

    /**
     * Get the prunable model query for records older than 30 days.
     */
    public function prunable(): Builder
    {
        return static::where('sent_at', '<', now()->subDays(30))
            ->orWhere('created_at', '<', now()->subDays(30));
    }

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'response_payload' => 'array',
            'sent_at' => 'datetime',
        ];
    }

    /**
     * Associated task if any.
     */
    public function tugas(): BelongsTo
    {
        return $this->belongsTo(Tugas::class, 'tugas_id');
    }

    /**
     * Associated OPD.
     */
    public function opd(): BelongsTo
    {
        return $this->belongsTo(Opd::class, 'opd_id');
    }

    /**
     * Human readable type label.
     */
    public function getTipeLabelAttribute(): string
    {
        return match ($this->tipe_pengingat) {
            'pembuatan_tugas' => 'Tugas Baru Dibuat',
            'h-14' => 'Pengingat H-14',
            'h-7' => 'Pengingat H-7',
            'h-3' => 'Pengingat H-3',
            'h-2' => 'Pengingat H-2',
            'h-1' => 'Pengingat H-1',
            'h-0' => 'Pengingat Hari-H',
            'h-0-sore' => 'Pengingat Hari-H (Sore/Mendesak)',
            'keterlambatan' => 'Peringatan Terlambat',
            'eskalasi_1' => 'Peringatan Eskalasi (+3 Hari)',
            'eskalasi_2' => 'Peringatan Kritis (+7 Hari)',
            'manual_broadcast' => 'Broadcast Manual',
            'verifikasi' => 'Hasil Verifikasi',
            'pengumpulan_tugas' => 'Pengumpulan Tugas (Ke PIC)',
            'konfirmasi_pengumpulan' => 'Konfirmasi Pengumpulan (Ke OPD)',
            default => ucfirst(str_replace('_', ' ', $this->tipe_pengingat)),
        };
    }
}
