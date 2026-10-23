<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'admin_id',
    'judul',
    'deskripsi',
    'file_path',
    'file_original_name',
    'file_size',
    'format_pengumpulan',
    'deadline',
    'tahun_anggaran',
    'status',
])]
class Tugas extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tugas';

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'deadline' => 'datetime',
        ];
    }

    /**
     * Admin who created this task.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Task assignments per OPD.
     */
    public function tugasOpd(): HasMany
    {
        return $this->hasMany(TugasOpd::class, 'tugas_id');
    }

    /**
     * OPDs assigned to this task.
     */
    public function opds(): BelongsToMany
    {
        return $this->belongsToMany(Opd::class, 'tugas_opd', 'tugas_id', 'opd_id')
            ->withPivot(['id', 'status'])
            ->withTimestamps();
    }

    /**
     * WhatsApp logs associated with this task.
     */
    public function whatsappLogs(): HasMany
    {
        return $this->hasMany(WhatsappLog::class, 'tugas_id');
    }

    /**
     * Check if deadline has passed.
     */
    public function isPastDeadline(): bool
    {
        return Carbon::now()->greaterThan($this->deadline);
    }

    /**
     * Human readable format label.
     */
    public function getFormatLabelAttribute(): string
    {
        return match ($this->format_pengumpulan) {
            'digital' => 'Digital (Upload File)',
            'fisik' => 'Fisik (Manual)',
            'hybrid' => 'Hybrid (Digital & Fisik)',
            default => ucfirst($this->format_pengumpulan),
        };
    }

    /**
     * Human readable remaining time in Indonesian.
     */
    public function getSisaWaktuLabelAttribute(): string
    {
        if ($this->isPastDeadline()) {
            return 'Batas Waktu Telah Berakhir';
        }

        $now = Carbon::now();
        $diff = $now->diff($this->deadline);
        $days = (int) $diff->days;
        $hours = (int) $diff->h;
        $minutes = (int) $diff->i;

        if ($days > 0) {
            return "Sisa {$days} hari lagi";
        }

        if ($hours > 0) {
            return "Sisa {$hours} jam lagi";
        }

        if ($minutes > 0) {
            return "Sisa {$minutes} menit lagi";
        }

        return 'Sisa kurang dari 1 menit';
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

    /**
     * Check if task has an attachment file.
     */
    public function hasAttachment(): bool
    {
        return ! empty($this->file_path);
    }
}
