<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable([
    'pemberi_id',
    'judul',
    'deskripsi',
    'tanggal_kegiatan',
    'batas_laporan',
    'catatan',
])]
class Penugasan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'penugasan';

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_kegiatan' => 'date',
            'batas_laporan' => 'date',
        ];
    }

    /**
     * The admin/PIC who created this assignment.
     */
    public function pemberi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pemberi_id');
    }

    /**
     * The Bidang Ekop members who received this assignment.
     */
    public function penerima(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'penugasan_penerima', 'penugasan_id', 'user_id')
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
     * Check if the tanggal kegiatan has passed.
     */
    public function isKegiatanPassed(): bool
    {
        return Carbon::today()->greaterThan($this->tanggal_kegiatan);
    }

    /**
     * Check if the batas laporan deadline has passed.
     */
    public function isBatasLaporanPassed(): bool
    {
        if (! $this->batas_laporan) {
            return false;
        }

        return Carbon::today()->greaterThan($this->batas_laporan);
    }

    /**
     * Pivot status penugasan human-readable label.
     */
    public static function statusPenugasanLabel(string $status): string
    {
        return match ($status) {
            'belum_dikerjakan' => 'Belum Dikerjakan',
            'sedang_dikerjakan' => 'Sedang Dikerjakan',
            'selesai' => 'Selesai',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    /**
     * Pivot status laporan human-readable label.
     */
    public static function statusLaporanLabel(string $status): string
    {
        return match ($status) {
            'belum_dilaporkan' => 'Belum Dilaporkan',
            'sudah_dilaporkan' => 'Sudah Dilaporkan',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    /**
     * Pivot status konfirmasi human-readable label.
     */
    public static function statusKonfirmasiLabel(string $status): string
    {
        return match ($status) {
            'dikonfirmasi' => 'Dikonfirmasi',
            'ditolak' => 'Berhalangan / Ditolak',
            default => 'Menunggu Konfirmasi',
        };
    }
}
