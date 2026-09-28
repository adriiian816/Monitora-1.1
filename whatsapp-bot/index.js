import makeWASocket, {
    DisconnectReason,
    useMultiFileAuthState,
    fetchLatestBaileysVersion
} from '@whiskeysockets/baileys';
import pino from 'pino';
import qrcode from 'qrcode-terminal';
import express from 'express';
import cors from 'cors';
import path from 'path';
import fs from 'fs';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const PORT = process.env.PORT || 3000;
const LARAVEL_WEBHOOK = process.env.LARAVEL_WEBHOOK_URL || 'http://127.0.0.1:8000/api/whatsapp/webhook';
const AUTH_DIR = path.join(__dirname, 'auth_session');

// Konfigurasi Jeda Pengiriman Anti-Spam / Anti-Banned (30 - 60 Detik secara acak)
const MIN_DELAY_SEC = parseInt(process.env.WA_MIN_DELAY || '30', 10);
const MAX_DELAY_SEC = parseInt(process.env.WA_MAX_DELAY || '60', 10);

// Filter / sembunyikan log debug internal libsignal (Closing session, Opening session, dsb.)
const originalConsoleInfo = console.info;
console.info = function (...args) {
    if (typeof args[0] === 'string' && (
        args[0].includes('Closing session:') ||
        args[0].includes('Opening session:') ||
        args[0].includes('Removing old closed session:') ||
        args[0].includes('Migrating session to:')
    )) {
        return;
    }
    originalConsoleInfo.apply(console, args);
};

const originalConsoleWarn = console.warn;
console.warn = function (...args) {
    if (typeof args[0] === 'string' && (
        args[0].includes('Session already') ||
        args[0].includes('Decrypted message with closed session') ||
        args[0].includes('Closing open session in favor')
    )) {
        return;
    }
    originalConsoleWarn.apply(console, args);
};

let sock = null;
let isConnected = false;

// Antrean Pengiriman Pesan Keluar (Outbound Queue)
const messageQueue = [];
let isProcessingQueue = false;

/**
 * Utility: Delay asynchronous execution
 */
const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

/**
 * Utility: Hitung jeda acak dalam rentang detik yang ditentukan (default 30-60 detik)
 */
function getRandomDelay(min = MIN_DELAY_SEC, max = MAX_DELAY_SEC) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

/**
 * Bersihkan format nomor telepon dan sesuaikan format internasional WhatsApp (62xxx)
 */
function formatPhoneToJid(phone) {
    let clean = String(phone).replace(/[^0-9]/g, '');
    if (clean.startsWith('0')) {
        clean = '62' + clean.slice(1);
    }
    const jid = `${clean}@s.whatsapp.net`;
    return { clean, jid };
}

/**
 * Verifikasi apakah nomor telepon terdaftar / aktif di WhatsApp sebelum pengiriman
 */
async function verifyNumberOnWhatsApp(phone) {
    if (!sock || !isConnected) {
        throw new Error('Bot WhatsApp belum terhubung. Silakan scan QR code di terminal terlebih dahulu.');
    }

    const { clean, jid } = formatPhoneToJid(phone);

    try {
        const results = await sock.onWhatsApp(jid);
        const contact = results && results.length > 0 ? results[0] : null;

        return {
            exists: !!(contact && contact.exists),
            clean,
            jid: contact?.jid || jid,
        };
    } catch (err) {
        console.error(`⚠️ [VERIFY ERROR] Gagal memeriksa nomor ${clean} via Baileys:`, err.message);
        throw err;
    }
}

/**
 * Worker Pemroses Antrean Pengiriman Pesan dengan Jeda Acak (30 - 60 Detik)
 * Mencegah pemblokiran akun (anti-banned & rate-limit avoidance)
 */
async function processQueue() {
    if (isProcessingQueue) return;
    isProcessingQueue = true;

    console.log(`\n🚦 [ANTREAN DIMULAI] Memproses antrean pengiriman pesan (Total: ${messageQueue.length})...`);

    while (messageQueue.length > 0) {
        if (!sock || !isConnected) {
            console.log('⚠️ [ANTREAN DIJEDA] Koneksi WhatsApp terputus. Antrean ditangguhkan sampai bot terhubung kembali.');
            break;
        }

        const currentItem = messageQueue[0];
        const delaySec = getRandomDelay(MIN_DELAY_SEC, MAX_DELAY_SEC);

        console.log(`\n------------------------------------------------------`);
        console.log(`⏳ [ANTI-SPAM DELAY] Menerapkan jeda acak ${delaySec} detik sebelum mengirim ke ${currentItem.cleanPhone}...`);
        console.log(`📋 Sisa antrean setelah ini: ${messageQueue.length - 1} pesan.`);
        console.log(`------------------------------------------------------`);

        await sleep(delaySec * 1000);

        // Periksa kembali koneksi setelah jeda waktu selesai
        if (!sock || !isConnected) {
            console.log('⚠️ [ANTREAN DIJEDA] Bot terputus selama masa jeda. Menunda pengiriman.');
            break;
        }

        try {
            // Simulasi indikator sedang mengetik (composing) selama 2 detik agar menyerupai aktivitas manusia
            try {
                await sock.sendPresenceUpdate('composing', currentItem.targetJid);
                await sleep(2000);
                await sock.sendPresenceUpdate('paused', currentItem.targetJid);
            } catch (_) { }

            await sock.sendMessage(currentItem.targetJid, { text: currentItem.message });
            console.log(`✅ [OUTBOUND SUKSES] Pesan terkirim ke ${currentItem.cleanPhone} (Jeda: ${delaySec}s, ID: ${currentItem.id})`);

            // Keluarkan pesan yang telah berhasil dikirim dari antrean
            messageQueue.shift();
        } catch (error) {
            console.error(`❌ [OUTBOUND GAGAL] Gagal mengirim pesan ke ${currentItem.cleanPhone}:`, error.message);
            // Tetap keluarkan dari antrean agar tidak menghambat pesan berikutnya
            messageQueue.shift();
        }
    }

    isProcessingQueue = false;
    console.log(`🏁 [ANTREAN SELESAI] Seluruh pesan dalam antrean telah selesai diproses.\n`);
}

// Setup Express API Server
const app = express();
app.use(cors());
app.use(express.json());

app.get('/', (req, res) => {
    res.json({
        service: 'MONITORA WhatsApp Bot (Bappeda Maluku Utara)',
        status: isConnected ? 'connected' : 'waiting_for_connection',
        webhook_url: LARAVEL_WEBHOOK,
        anti_spam: {
            min_delay_seconds: MIN_DELAY_SEC,
            max_delay_seconds: MAX_DELAY_SEC,
            queue_length: messageQueue.length,
            is_processing: isProcessingQueue,
        },
        uptime_seconds: Math.floor(process.uptime()),
    });
});

app.get('/status', (req, res) => {
    res.json({
        connected: isConnected,
        status: isConnected ? 'connected' : 'disconnected',
        queue_length: messageQueue.length,
        is_processing: isProcessingQueue,
        min_delay_seconds: MIN_DELAY_SEC,
        max_delay_seconds: MAX_DELAY_SEC,
    });
});

/**
 * Cek Ketersediaan Nomor WhatsApp (Verification Endpoint)
 */
app.post('/check-number', async (req, res) => {
    const { phone } = req.body;

    if (!phone) {
        return res.status(400).json({
            status: 'error',
            message: 'Nomor telepon (phone) wajib diisi.',
        });
    }

    if (!sock || !isConnected) {
        return res.status(503).json({
            status: 'error',
            message: 'WhatsApp bot belum terhubung. Silakan scan QR code di terminal terlebih dahulu.',
        });
    }

    try {
        const check = await verifyNumberOnWhatsApp(phone);
        return res.json({
            status: 'success',
            target: check.clean,
            registered: check.exists,
            jid: check.jid,
            message: check.exists
                ? `Nomor ${check.clean} terdaftar aktif di WhatsApp.`
                : `Nomor ${check.clean} tidak terdaftar di WhatsApp.`,
        });
    } catch (error) {
        return res.status(500).json({
            status: 'error',
            message: error.message || 'Gagal memverifikasi nomor WhatsApp.',
        });
    }
});

/**
 * Status Antrean Pesan (Queue Status Endpoint)
 */
app.get('/queue-status', (req, res) => {
    res.json({
        total_in_queue: messageQueue.length,
        is_processing: isProcessingQueue,
        min_delay_seconds: MIN_DELAY_SEC,
        max_delay_seconds: MAX_DELAY_SEC,
        items: messageQueue.map((item, idx) => ({
            position: idx + 1,
            id: item.id,
            phone: item.cleanPhone,
            queued_at: item.queuedAt,
        })),
    });
});

/**
 * Outbound endpoint: Laravel sends messages to this endpoint
 * 1. Verifikasi ketersediaan nomor di WhatsApp
 * 2. Masukkan ke antrean dengan jeda acak 30 - 60 detik
 */
app.post('/send-message', async (req, res) => {
    const { phone, message } = req.body;

    if (!phone || !message) {
        return res.status(400).json({
            status: 'error',
            message: 'Nomor telepon (phone) dan isi pesan (message) wajib diisi.',
        });
    }

    if (!sock || !isConnected) {
        return res.status(503).json({
            status: 'error',
            message: 'WhatsApp bot belum terhubung. Silakan scan QR code pada terminal terlebih dahulu.',
        });
    }

    const { clean, jid } = formatPhoneToJid(phone);

    try {
        console.log(`🔍 [VERIFIKASI NOMOR] Memeriksa ketersediaan nomor ${clean} di WhatsApp...`);
        const check = await verifyNumberOnWhatsApp(clean);

        if (!check.exists) {
            console.warn(`⚠️ [VERIFIKASI GAGAL] Nomor ${clean} TIDAK terdaftar di WhatsApp. Pengiriman dibatalkan.`);
            return res.status(422).json({
                status: 'error',
                code: 'NUMBER_NOT_REGISTERED',
                target: clean,
                message: `Nomor ${clean} tidak terdaftar di WhatsApp. Pengiriman dibatalkan untuk menghindari pembatasan / banned WhatsApp.`,
            });
        }

        console.log(`✓ [VERIFIKASI BERHASIL] Nomor ${clean} terdaftar aktif di WhatsApp.`);

        const queueItem = {
            id: 'MSG-' + Date.now() + '-' + Math.floor(Math.random() * 1000),
            cleanPhone: clean,
            targetJid: check.jid,
            message: message,
            queuedAt: new Date().toISOString(),
        };

        messageQueue.push(queueItem);
        const queuePos = messageQueue.length;

        console.log(`📥 [ANTREAN DITAMBAHKAN] Pesan untuk ${clean} masuk antrean (Posisi: #${queuePos}).`);

        // Picu pemrosesan antrean di latar belakang
        processQueue().catch((err) => {
            console.error('❌ [QUEUE PROCESSOR ERROR]:', err);
        });

        return res.json({
            status: 'success',
            code: 'MESSAGE_QUEUED',
            target: clean,
            queue_id: queueItem.id,
            queue_position: queuePos,
            min_delay_sec: MIN_DELAY_SEC,
            max_delay_sec: MAX_DELAY_SEC,
            message: `Nomor ${clean} terverifikasi aktif di WhatsApp. Pesan berhasil dimasukkan ke antrean dengan jeda acak ${MIN_DELAY_SEC}-${MAX_DELAY_SEC} detik.`,
        });
    } catch (error) {
        console.error('❌ [OUTBOUND ERROR]:', error);
        return res.status(500).json({
            status: 'error',
            message: error.message || 'Gagal memproses pengiriman pesan WhatsApp.',
        });
    }
});

// Baileys Connection Handler
async function connectToWhatsApp() {
    const { state, saveCreds } = await useMultiFileAuthState(AUTH_DIR);
    const { version } = await fetchLatestBaileysVersion();

    console.log(`📡 Menginisialisasi koneksi WhatsApp Web v${version.join('.')}...`);

    sock = makeWASocket({
        version,
        logger: pino({ level: 'silent' }),
        auth: state,
        printQRInTerminal: false,
    });

    sock.ev.on('connection.update', (update) => {
        const { connection, lastDisconnect, qr } = update;

        if (qr) {
            console.log('\n======================================================');
            console.log('📌 SCAN QR CODE BERIKUT DENGAN WHATSAPP DI HP ANDA:');
            console.log('======================================================\n');
            qrcode.generate(qr, { small: true });
            console.log('Tips: Buka WhatsApp > Perangkat Tertaut > Tautkan Perangkat.\n');
        }

        if (connection === 'close') {
            isConnected = false;
            const statusCode = lastDisconnect?.error?.output?.statusCode;
            const shouldReconnect = statusCode !== DisconnectReason.loggedOut;

            console.log(`⚠️ Koneksi WhatsApp terputus (Status: ${statusCode}). Reconnect: ${shouldReconnect}`);

            if (shouldReconnect) {
                setTimeout(connectToWhatsApp, 3000);
            } else {
                console.log('❌ Sesi WhatsApp telah logout atau kedaluwarsa (Status 401).');
                console.log('🔄 Membersihkan sesi lama dan menyiapkan QR Code baru...');
                try {
                    fs.rmSync(AUTH_DIR, { recursive: true, force: true });
                } catch (err) {
                    console.error('⚠️ Gagal membersihkan auth_session:', err.message);
                }
                setTimeout(connectToWhatsApp, 2000);
            }
        } else if (connection === 'open') {
            isConnected = true;
            console.log('\n🎉 ======================================================');
            console.log('✅ WHATSAPP BOT MONITORA BERHASIL TERHUBUNG!');
            console.log(`💬 Fitur Anti-Spam: Jeda acak ${MIN_DELAY_SEC} - ${MAX_DELAY_SEC} detik & Pre-Verifikasi Nomor.`);
            console.log('======================================================\n');

            // Lanjutkan pengiriman antrean jika ada pesan yang tertunda saat koneksi terputus
            if (messageQueue.length > 0 && !isProcessingQueue) {
                console.log(`🔄 [KONEKSI PULIH] Melanjutkan pengiriman ${messageQueue.length} pesan dalam antrean...`);
                processQueue().catch((err) => console.error('❌ Error resuming queue:', err));
            }
        }
    });

    sock.ev.on('creds.update', saveCreds);

    // Inbound Message Listener (Pesan Masuk dari Staf OPD)
    sock.ev.on('messages.upsert', async ({ messages, type }) => {
        if (type !== 'notify') return;

        for (const msg of messages) {
            try {
                if (!msg.message || msg.key.fromMe) continue;

                const remoteJid = msg.key.remoteJid || '';
                // Skip group chats and broadcast channels
                if (remoteJid.includes('@g.us') || remoteJid.includes('broadcast')) continue;

                const messageText =
                    msg.message.conversation ||
                    msg.message.extendedTextMessage?.text ||
                    msg.message.imageMessage?.caption ||
                    '';

                if (!messageText.trim()) continue;

                const senderPhone = remoteJid.replace('@s.whatsapp.net', '');
                console.log(`📩 [INBOUND] Pesan dari ${senderPhone}: "${messageText.trim()}"`);

                // Forward to Laravel Webhook
                const response = await fetch(LARAVEL_WEBHOOK, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        phone: senderPhone,
                        message: messageText.trim(),
                    }),
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.reply) {
                        // Simulasi waktu mengetik alami (2 detik) sebelum membalas interaktif
                        try {
                            await sock.sendPresenceUpdate('composing', remoteJid);
                            await sleep(2000);
                            await sock.sendPresenceUpdate('paused', remoteJid);
                        } catch (_) { }

                        await sock.sendMessage(remoteJid, { text: data.reply });
                        console.log(`💬 [REPLY] Balasan otomatis terkirim ke ${senderPhone}`);
                    }
                } else {
                    console.error(`⚠️ [WEBHOOK ERROR] HTTP ${response.status}:`, await response.text());
                }
            } catch (err) {
                console.error('❌ Error menangani pesan masuk:', err);
            }
        }
    });
}

// Start Server and Connect
app.listen(PORT, () => {
    console.log(`\n======================================================`);
    console.log(`🚀 MONITORA WhatsApp Bot Service aktif di port ${PORT}`);
    console.log(`🛡️ Fitur Proteksi: Jeda acak ${MIN_DELAY_SEC}-${MAX_DELAY_SEC} detik & Verifikasi Nomor Aktif`);
    console.log(`🌐 Outbound URL: http://127.0.0.1:${PORT}/send-message`);
    console.log(`🔍 Check Number: http://127.0.0.1:${PORT}/check-number`);
    console.log(`📊 Queue Status: http://127.0.0.1:${PORT}/queue-status`);
    console.log(`🔗 Webhook Laravel: ${LARAVEL_WEBHOOK}`);
    console.log(`======================================================\n`);

    connectToWhatsApp().catch((err) => {
        console.error('❌ Gagal menjalankan WhatsApp bot:', err);
    });
});
