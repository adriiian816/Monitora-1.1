<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Opd;
use App\Models\Submission;
use App\Models\TugasOpd;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppBotController extends Controller
{
    /**
     * Handle incoming webhook message from WhatsApp bot.
     */
    public function handle(Request $request): JsonResponse
    {
        $rawSender = (string) ($request->input('phone') ?? $request->input('sender') ?? '');
        $rawMessage = (string) ($request->input('message') ?? $request->input('body') ?? '');

        if (empty($rawSender)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sender phone is required.',
            ], 422);
        }

        // Clean phone number (strip @s.whatsapp.net, +, spaces, dashes)
        $cleanPhone = preg_replace('/[^0-9]/', '', explode('@', $rawSender)[0]);

        // Build phone number variants (08xx and 628xx)
        $phoneVariants = [$cleanPhone];
        if (str_starts_with($cleanPhone, '62')) {
            $phoneVariants[] = '0'.substr($cleanPhone, 2);
        } elseif (str_starts_with($cleanPhone, '0')) {
            $phoneVariants[] = '62'.substr($cleanPhone, 1);
        }

        // Find registered user (staff/admin) or OPD PIC
        $user = User::with('opd')->whereIn('no_hp', $phoneVariants)->first();
        $opd = $user?->opd ?: Opd::whereIn('whatsapp_pic', $phoneVariants)->first();

        Log::info('WhatsApp Bot Webhook received', [
            'sender' => $cleanPhone,
            'message' => $rawMessage,
            'user_id' => $user?->id,
            'opd_id' => $opd?->id,
        ]);

        // Unknown number response
        if (! $user && ! $opd) {
            $reply = "👋 *Halo!*\n\n"
                ."Nomor WhatsApp Anda (*{$cleanPhone}*) belum terdaftar pada Sistem MONITORA Bappeda Provinsi Maluku Utara.\n\n"
                .'Silakan hubungi Admin Bappeda Maluku Utara atau perbarui nomor WhatsApp pada profil akun Anda di portal MONITORA agar dapat mengakses layanan asisten bot ini.';

            return response()->json([
                'status' => 'success',
                'is_registered' => false,
                'reply' => $reply,
            ]);
        }

        $userName = $user?->name ?? $opd?->nama_pic ?? 'Bpk/Ibu PIC';
        $opdName = $opd?->nama_opd ?? 'Instansi OPD';
        $command = trim(strtoupper($rawMessage));

        $reply = match (true) {
            in_array($command, ['1', 'TUGAS', 'CEK TUGAS', 'DAFTAR TUGAS'], true) => $this->buildTaskListReply($opd, $opdName),
            in_array($command, ['2', 'STATUS', 'CEK STATUS', 'VERIFIKASI'], true) => $this->buildStatusReply($opd, $opdName),
            in_array($command, ['3', 'DEADLINE', 'JADWAL', 'BATAS WAKTU'], true) => $this->buildDeadlineReply($opd, $opdName),
            in_array($command, ['4', 'INFO', 'BANTUAN', 'HELP', 'FORMAT'], true) => $this->buildInfoReply(),
            in_array($command, ['MENU', 'HALO', 'HI', 'HAI', 'START', 'P', ''], true) => $this->buildMenuReply($userName, $opdName),
            default => $this->buildUnknownReply($rawMessage, $userName, $opdName),
        };

        return response()->json([
            'status' => 'success',
            'is_registered' => true,
            'user' => $userName,
            'opd' => $opdName,
            'reply' => $reply,
        ]);
    }

    /**
     * Build Main Menu reply.
     */
    protected function buildMenuReply(string $userName, string $opdName): string
    {
        return "🤖 *ASISTEN BOT MONITORA BAPPEDA MALUT*\n"
            ."Halo, *{$userName}* ({$opdName})!\n\n"
            ."Silakan balas pesan ini dengan mengetik *angka* atau *kata kunci* berikut:\n\n"
            ."1️⃣ *1* atau *TUGAS*\n"
            ."   👉 Cek daftar tugas aktif & tanggungan pengumpulan berkas.\n\n"
            ."2️⃣ *2* atau *STATUS*\n"
            ."   👉 Cek riwayat pengumpulan & status verifikasi admin.\n\n"
            ."3️⃣ *3* atau *DEADLINE*\n"
            ."   👉 Cek tugas yang paling mendesak batas waktunya.\n\n"
            ."4️⃣ *4* atau *INFO*\n"
            ."   👉 Petunjuk pengumpulan berkas & link portal web.\n\n"
            .'_Ketik *MENU* kapan saja untuk memanggil kembali opsi ini._';
    }

    /**
     * Build Active Task List reply.
     */
    protected function buildTaskListReply(?Opd $opd, string $opdName): string
    {
        if (! $opd) {
            return '⚠️ Data OPD belum terhubung dengan akun Anda.';
        }

        $pendingTasks = TugasOpd::where('opd_id', $opd->id)
            ->whereIn('status', ['belum_dikumpulkan', 'perlu_revisi', 'terlambat'])
            ->with('tugas')
            ->get()
            ->sortBy(fn ($to) => $to->tugas->deadline);

        if ($pendingTasks->isEmpty()) {
            return "🎉 *Alhamdulillah, Luar Biasa!*\n\n"
                ."Tidak ada tanggungan tugas aktif untuk *{$opdName}*.\n"
                .'Semua berkas laporan telah dikumpulkan.';
        }

        $reply = "📋 *DAFTAR TUGAS AKTIF*\n"
            ."Instansi: *{$opdName}*\n"
            ."Total Tanggungan: *{$pendingTasks->count()} Tugas*\n"
            ."────────────────────────\n\n";

        $i = 1;
        foreach ($pendingTasks as $to) {
            $tugas = $to->tugas;
            $deadlineStr = $tugas->deadline->translatedFormat('d M Y, H:i').' WIT';
            $statusLabel = match ($to->status) {
                'perlu_revisi' => '⚠️ Perlu Revisi',
                'terlambat' => '❌ Terlambat',
                default => '⏳ Belum Dikumpulkan',
            };

            $reply .= "*{$i}. {$tugas->judul}*\n"
                ."   • Status: *{$statusLabel}*\n"
                ."   • Deadline: {$deadlineStr}\n"
                ."   • Format: {$tugas->format_label}\n"
                ."   • Sisa Waktu: _{$tugas->sisa_waktu_label}_\n\n";
            $i++;
        }

        $reply .= "🔗 Kumpulkan berkas Anda melalui:\n".url('/opd/tugas');

        return $reply;
    }

    /**
     * Build Verification Status reply.
     */
    protected function buildStatusReply(?Opd $opd, string $opdName): string
    {
        if (! $opd) {
            return '⚠️ Data OPD belum terhubung dengan akun Anda.';
        }

        $submissions = Submission::whereHas('tugasOpd', function ($q) use ($opd): void {
            $q->where('opd_id', $opd->id);
        })
            ->with(['tugasOpd.tugas'])
            ->latest('created_at')
            ->take(5)
            ->get();

        if ($submissions->isEmpty()) {
            return "ℹ️ *Belum ada riwayat pengumpulan berkas* untuk *{$opdName}*.\n\n"
                .'Ketik *1* untuk melihat tugas yang perlu Anda kumpulkan.';
        }

        $reply = "📑 *STATUS VERIFIKASI PENGUMPULAN TERAKHIR*\n"
            ."Instansi: *{$opdName}*\n"
            ."────────────────────────\n\n";

        foreach ($submissions as $sub) {
            $tugas = $sub->tugasOpd?->tugas;
            $tugasJudul = $tugas?->judul ?? 'Tugas';
            $tglKirim = $sub->created_at?->translatedFormat('d M Y H:i') ?? '-';

            $statusText = match ($sub->status_verifikasi) {
                'diterima' => '✅ DITERIMA',
                'perlu_revisi' => '⚠️ PERLU REVISI',
                default => '⏳ MENUNGGU VERIFIKASI',
            };

            $reply .= "📌 *{$tugasJudul}*\n"
                ."   • Status: *{$statusText}*\n"
                ."   • Dikirim: {$tglKirim} WIT\n";

            if (! empty($sub->catatan_admin)) {
                $reply .= "   • Catatan Bappeda: _{$sub->catatan_admin}_\n";
            }

            $reply .= "\n";
        }

        $reply .= '🔗 Detail lengkap: '.url('/opd/dashboard');

        return $reply;
    }

    /**
     * Build Urgent Deadline reply.
     */
    protected function buildDeadlineReply(?Opd $opd, string $opdName): string
    {
        if (! $opd) {
            return '⚠️ Data OPD belum terhubung dengan akun Anda.';
        }

        $nearest = TugasOpd::where('opd_id', $opd->id)
            ->whereIn('status', ['belum_dikumpulkan', 'perlu_revisi'])
            ->with('tugas')
            ->get()
            ->sortBy(fn ($to) => $to->tugas->deadline)
            ->first();

        if (! $nearest) {
            return "🎉 *Tidak ada tugas mendesak!*\n\n"
                ."Semua tugas untuk *{$opdName}* telah dikumpulkan tepat waktu.";
        }

        $tugas = $nearest->tugas;
        $deadlineStr = $tugas->deadline->translatedFormat('l, d F Y - H:i').' WIT';

        return "⏰ *TUGAS PALING MENDEKATI BATAS WAKTU*\n"
            ."Instansi: *{$opdName}*\n"
            ."────────────────────────\n\n"
            ."📌 *Judul:* {$tugas->judul}\n"
            ."📅 *Deadline:* {$deadlineStr}\n"
            ."⏱️ *Sisa Waktu:* *{$tugas->sisa_waktu_label}*\n"
            ."📂 *Format Berkas:* {$tugas->format_label}\n\n"
            ."Mohon segera selesaikan sebelum batas waktu berakhir:\n"
            .'🔗 '.url('/opd/tugas/'.$tugas->id);
    }

    /**
     * Build Information & Guide reply.
     */
    protected function buildInfoReply(): string
    {
        return "ℹ️ *PANDUAN & INFORMASI SISTEM MONITORA*\n"
            ."Bappeda Provinsi Maluku Utara\n"
            ."────────────────────────\n\n"
            ."📌 *Ketentuan Pengunggahan Berkas:*\n"
            ."1. Format file yang diterima: Dokumen PDF, Spreadsheet Excel (.xlsx), atau Arsip ZIP.\n"
            ."2. Ukuran maksimal berkas: *20 MB*.\n"
            ."3. Pastikan berkas telah ditandatangani atau disahkan sebelum diunggah.\n\n"
            .'🌐 *Tautan Akses Portal:* '.url('/')."\n\n"
            ."📞 *Bantuan & Dukungan:*\n"
            .'Bidang Pengendalian & Evaluasi Bappeda Provinsi Maluku Utara.';
    }

    /**
     * Build Fallback Unknown reply.
     */
    protected function buildUnknownReply(string $rawMessage, string $userName, string $opdName): string
    {
        return "Halo *{$userName}* ({$opdName}), maaf pesan *\"{$rawMessage}\"* tidak dikenali oleh asisten bot.\n\n"
            .'Silakan ketik *MENU* untuk melihat daftar opsi perintah yang tersedia.';
    }
}
