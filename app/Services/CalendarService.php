<?php

namespace App\Services;

use App\Models\Opd;
use App\Models\Tugas;

class CalendarService
{
    /**
     * Generate iCalendar (.ics) content for a task.
     */
    public function generateIcs(Tugas $tugas, ?Opd $opd = null): string
    {
        $dtStamp = now()->utc()->format('Ymd\THis\Z');
        $dtEnd = $tugas->deadline->clone()->utc()->format('Ymd\THis\Z');
        // Set event start 1 hour before deadline
        $dtStart = $tugas->deadline->clone()->subHour()->utc()->format('Ymd\THis\Z');

        $uid = 'monitora-tugas-'.$tugas->id.'-'.($opd ? $opd->id : 'all').'@bappeda.malutprov.go.id';
        $summary = $this->escapeIcs('DEADLINE: '.$tugas->judul.' - BAPPEDA MALUT');

        $descriptionText = "Tugas: {$tugas->judul}\n"
            ."Format Pengumpulan: {$tugas->format_label}\n"
            ."Tahun Anggaran: {$tugas->tahun_anggaran}\n"
            .($opd ? "Instansi: {$opd->nama_opd}\n" : '')
            .($tugas->deskripsi ? "Deskripsi: {$tugas->deskripsi}\n" : '')
            .'Tautan: '.url('/opd/tugas/'.$tugas->id);

        $description = $this->escapeIcs($descriptionText);
        $location = $this->escapeIcs('BAPPEDA Provinsi Maluku Utara - Bidang Perekonomian dan Perdagangan');

        $ics = "BEGIN:VCALENDAR\r\n"
            ."VERSION:2.0\r\n"
            ."PRODID:-//MONITORA BAPPEDA MALUT//ID\r\n"
            ."CALSCALE:GREGORIAN\r\n"
            ."METHOD:PUBLISH\r\n"
            ."X-WR-CALNAME:MONITORA Bappeda Malut\r\n"
            ."X-WR-TIMEZONE:Asia/Jayapura\r\n"
            ."BEGIN:VEVENT\r\n"
            ."UID:{$uid}\r\n"
            ."DTSTAMP:{$dtStamp}\r\n"
            ."DTSTART:{$dtStart}\r\n"
            ."DTEND:{$dtEnd}\r\n"
            ."SUMMARY:{$summary}\r\n"
            ."DESCRIPTION:{$description}\r\n"
            ."LOCATION:{$location}\r\n"
            ."STATUS:CONFIRMED\r\n"
            ."BEGIN:VALARM\r\n"
            ."TRIGGER:-P1D\r\n"
            ."ACTION:DISPLAY\r\n"
            ."DESCRIPTION:Pengingat Deadline Tugas MONITORA (1 Hari Lagi)\r\n"
            ."END:VALARM\r\n"
            ."BEGIN:VALARM\r\n"
            ."TRIGGER:-PT2H\r\n"
            ."ACTION:DISPLAY\r\n"
            ."DESCRIPTION:Peringatan Deadline Tugas MONITORA (2 Jam Lagi)\r\n"
            ."END:VALARM\r\n"
            ."END:VEVENT\r\n"
            ."END:VCALENDAR\r\n";

        return $ics;
    }

    /**
     * Escape special iCalendar characters.
     */
    protected function escapeIcs(string $text): string
    {
        $text = str_replace('\\', '\\\\', $text);
        $text = str_replace(';', '\;', $text);
        $text = str_replace(',', '\,', $text);
        $text = str_replace("\r\n", '\\n', $text);
        $text = str_replace("\n", '\\n', $text);

        return $text;
    }
}
