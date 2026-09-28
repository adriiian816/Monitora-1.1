# MONITORA WhatsApp Bot Service (Demo)

Microservice bot WhatsApp lokal untuk sistem **MONITORA (Bappeda Provinsi Maluku Utara)** menggunakan `@whiskeysockets/baileys` dan `Express`.

---

## 🚀 Persyaratan
- Node.js versi 18+ (Anda menggunakan Node.js v22)
- Koneksi internet aktif di komputer dan ponsel yang menjalankan WhatsApp

---

## 📦 Instalasi
Buka terminal dan masuk ke folder `whatsapp-bot`:

```powershell
cd whatsapp-bot
npm.cmd install
```

---

## ▶️ Menjalankan Bot
Jalankan perintah berikut:

```powershell
npm.cmd run bot
```
*(atau `node index.js`)*

---

## 📲 Langkah Scan QR Code
1. Saat bot pertama kali dijalankan, kode QR akan muncul langsung di jendela terminal.
2. Buka aplikasi WhatsApp di HP Anda.
3. Masuk ke **Pengaturan / Titik Tiga (pojok kanan atas) > Perangkat Tertaut (Linked Devices) > Tautkan Perangkat**.
4. Scan QR Code yang ada di terminal.
5. Sesi login akan otomatis tersimpan di folder `whatsapp-bot/auth_session/`, sehingga Anda tidak perlu scan ulang setiap kali restart service.

---

## 🤖 Fitur Chatbot (Inbound)
Kirim pesan dari nomor HP yang telah didaftarkan di profil Staf OPD ke nomor WhatsApp bot:
* `MENU` / `HALO` : Menampilkan menu perintah utama.
* `1` atau `TUGAS` : Melihat daftar tugas aktif yang belum dikumpulkan OPD bersangkutan beserta batas deadlinenya.
* `2` atau `STATUS` : Melihat status riwayat pengumpulan berkas dan hasil verifikasi Bappeda.
* `3` atau `DEADLINE` : Menampilkan tugas yang paling mendesak batas waktunya.
* `4` atau `INFO` : Melihat petunjuk format berkas dan tautan login web MONITORA.

---

## 📤 Fitur Pengiriman Notifikasi (Outbound)
Laravel MONITORA akan otomatis mengirim pesan notifikasi (saat tugas dibuat, broadcast pengingat, atau verifikasi berkas) melalui HTTP POST ke:
```
http://127.0.0.1:3000/send-message
```
Body request:
```json
{
  "phone": "6281234567890",
  "message": "Isi pesan notifikasi..."
}
```
