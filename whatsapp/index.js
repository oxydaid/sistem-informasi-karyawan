import express from 'express';
import makeWASocket, { useMultiFileAuthState, DisconnectReason, fetchLatestBaileysVersion, delay } from '@whiskeysockets/baileys';
import pino from 'pino';
import path from 'path';
import fs from 'fs';
import mysql from 'mysql2/promise';
import qrcode from 'qrcode';
import { fileURLToPath } from 'url';
import dotenv from 'dotenv';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Load environment variables
if (fs.existsSync(path.join(__dirname, '.env'))) {
    dotenv.config({ path: path.join(__dirname, '.env') });
} else {
    dotenv.config({ path: path.join(__dirname, '../.env') });
}

const app = express();
app.use(express.json());

const PORT = process.env.WA_GATEWAY_PORT || 6969;
const HOST = process.env.WA_GATEWAY_HOST || '0.0.0.0';
const SESSION_DIR = path.join(__dirname, 'session_auth');

let sock = null;
let connectionState = 'disconnected'; // 'disconnected', 'connecting', 'connected', 'qrcode'
let latestQr = null;
let loggedInUser = null;
let db = null;

// Initialize Database Connection
async function initDb() {
    try {
        db = await mysql.createConnection({
            host: process.env.DB_HOST || '127.0.0.1',
            port: process.env.DB_PORT || 3306,
            database: process.env.DB_DATABASE || 'manajemen_karyawan',
            user: process.env.DB_USERNAME || 'root',
            password: process.env.DB_PASSWORD || ''
        });
        console.log('Database connected successfully in WhatsApp Gateway');
    } catch (err) {
        console.error('Database connection failed in WhatsApp Gateway:', err.message);
    }
}

// Fetch Secret Key dynamically from Database with Env Fallback
async function getSecretKey() {
    if (!db) {
        try {
            await initDb();
        } catch (e) {}
    }
    
    if (db) {
        try {
            const [rows] = await db.query('SELECT whatsapp_gateway_secret FROM app_settings LIMIT 1');
            if (rows && rows.length > 0 && rows[0].whatsapp_gateway_secret) {
                return rows[0].whatsapp_gateway_secret;
            }
        } catch (err) {
            console.error('Failed to query app_settings for secret key:', err.message);
            // Reconnect if connection was lost
            db = null;
        }
    }
    return process.env.WA_GATEWAY_SECRET || 'wa-gateway';
}

// Authentication Middleware
async function authenticate(req, res, next) {
    const authHeader = req.headers['x-gateway-secret'];
    const secretKey = await getSecretKey();

    if (!secretKey) {
        return res.status(401).json({ status: false, message: 'Unauthorized. Gateway secret key is not configured.' });
    }

    if (authHeader !== secretKey) {
        return res.status(401).json({ status: false, message: 'Unauthorized. Invalid gateway secret key.' });
    }

    next();
}

// Initialize WhatsApp Socket
async function connectToWhatsApp() {
    const { state, saveCreds } = await useMultiFileAuthState(SESSION_DIR);

    let version = [2, 3000, 1015970094]; // safe fallback version
    try {
        const { version: latestVersion, isLatest } = await fetchLatestBaileysVersion();
        version = latestVersion;
        console.log(`Using WhatsApp version v${version.join('.')}, isLatest: ${isLatest}`);
    } catch (err) {
        console.error('Failed to fetch latest WhatsApp version, using fallback:', err.message);
    }

    sock = makeWASocket({
        auth: state,
        version,
        logger: pino({ level: 'silent' })
    });

    sock.ev.on('creds.update', saveCreds);

    sock.ev.on('connection.update', async (update) => {
        const { connection, lastDisconnect, qr } = update;

        if (qr) {
            connectionState = 'qrcode';
            try {
                latestQr = await qrcode.toDataURL(qr);
            } catch (err) {
                console.error('Failed to generate QR data URL:', err.message);
            }
        }

        if (connection === 'connecting') {
            connectionState = 'connecting';
            console.log('Connecting to WhatsApp...');
        }

        if (connection === 'open') {
            connectionState = 'connected';
            latestQr = null;
            loggedInUser = sock.user;
            console.log('WhatsApp connection is fully active!', sock.user);
        }

        if (connection === 'close') {
            connectionState = 'disconnected';
            loggedInUser = null;
            const shouldReconnect = lastDisconnect?.error?.output?.statusCode !== DisconnectReason.loggedOut;
            console.log('Connection closed. Error details:', lastDisconnect?.error);
            console.log('Reconnecting:', shouldReconnect);
            
            if (shouldReconnect) {
                setTimeout(connectToWhatsApp, 5000);
            } else {
                console.log('Logged out of WhatsApp. Clearing session dir...');
                if (fs.existsSync(SESSION_DIR)) {
                    fs.rmSync(SESSION_DIR, { recursive: true, force: true });
                }
                setTimeout(connectToWhatsApp, 5000);
            }
        }
    });
}

// Helper to determine media type and options from URL/Mime
function getMediaPayload(fileUrl, caption, originalMimetype = null, originalFilename = null) {
    // Clean query parameters from URL to check extension
    const cleanUrl = fileUrl.split('?')[0].toLowerCase();
    const ext = cleanUrl.split('.').pop();

    const imageExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    const videoExts = ['mp4', 'mov', 'avi', '3gp', 'm4v'];

    let type = 'document';
    let mimetype = originalMimetype || 'application/octet-stream';
    let fileName = originalFilename || cleanUrl.split('/').pop() || 'file';

    if (imageExts.includes(ext) || (originalMimetype && originalMimetype.startsWith('image/'))) {
        type = 'image';
        mimetype = originalMimetype || `image/${ext === 'jpg' ? 'jpeg' : ext}`;
    } else if (videoExts.includes(ext) || (originalMimetype && originalMimetype.startsWith('video/'))) {
        type = 'video';
        mimetype = originalMimetype || `video/${ext === 'mp4' ? 'mp4' : ext}`;
    }

    const payload = {};
    payload[type] = { url: fileUrl };
    
    if (caption && (type === 'image' || type === 'video')) {
        payload.caption = caption;
    }

    if (type === 'document') {
        payload.mimetype = mimetype;
        payload.fileName = fileName;
        if (caption) {
            payload.caption = caption; // Baileys supports caption in documents in newer versions
        }
    }

    return payload;
}

// --- Endpoints ---

// Get gateway connection and pairing status
app.get('/status', async (req, res) => {
    res.json({
        status: true,
        connection: connectionState,
        qr: latestQr,
        user: sock ? sock.user : null
    });
});

// Disconnect and clear the current session
app.post('/logout', authenticate, async (req, res) => {
    try {
        if (sock) {
            try {
                await sock.logout();
            } catch (e) {
                sock.end();
            }
        }

        // Ensure session directory is cleared
        if (fs.existsSync(SESSION_DIR)) {
            fs.rmSync(SESSION_DIR, { recursive: true, force: true });
        }

        connectionState = 'disconnected';
        latestQr = null;
        loggedInUser = null;

        // Reconnect to initialize a clean socket (which will emit a fresh QR)
        connectToWhatsApp();

        res.json({ status: true, message: 'Session logged out and cleared.' });
    } catch (err) {
        console.error('Error logging out:', err.message);
        res.status(500).json({ status: false, message: 'Failed to log out: ' + err.message });
    }
});

// Render QR code raw page for scanning
app.get('/qr', (req, res) => {
    if (connectionState === 'connected') {
        return res.send('<h1>WhatsApp is already connected!</h1>');
    }
    if (!latestQr) {
        return res.send('<h1>QR code is generating or loading. Please refresh in a moment...</h1>');
    }

    res.send(`
        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; font-family: sans-serif; background: #f8fafc;">
            <div style="background: white; padding: 2rem; border-radius: 1.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.05); text-align: center;">
                <h2 style="color: #0f172a; margin-bottom: 0.5rem;">Pair WhatsApp Gateway</h2>
                <p style="color: #64748b; font-size: 0.875rem; margin-bottom: 1.5rem;">Scan this QR code using WhatsApp Link Devices</p>
                <img src="${latestQr}" style="width: 250px; height: 250px; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 0.5rem;" />
                <p style="color: #94a3b8; font-size: 0.75rem; margin-top: 1.5rem;">Halaman ini akan diperbarui otomatis saat tersambung.</p>
            </div>
            <script>
                setInterval(async () => {
                    const res = await fetch('/status');
                    const data = await res.json();
                    if (data.connection === 'connected') {
                        window.location.reload();
                    }
                }, 2000);
            </script>
        </div>
    `);
});

// Send single text or media message
app.post('/send-message', authenticate, async (req, res) => {
    const { number, message, file, caption, mimetype, filename } = req.body;

    if (!number) {
        return res.status(400).json({ status: false, message: 'Recipient number is required.' });
    }

    if (connectionState !== 'connected') {
        return res.status(503).json({ status: false, message: 'WhatsApp Gateway is not connected.' });
    }

    try {
        let response;
        if (file) {
            // Send Media
            const mediaPayload = getMediaPayload(file, caption || message, mimetype, filename);
            response = await sock.sendMessage(number, mediaPayload);
        } else {
            // Send Plain Text
            if (!message) {
                return res.status(400).json({ status: false, message: 'Message body is required when sending text.' });
            }
            response = await sock.sendMessage(number, { text: message });
        }

        res.json({ status: true, message: 'Message sent successfully.', messageId: response.key.id });
    } catch (err) {
        console.error('Error sending message:', err.message);
        res.status(500).json({ status: false, message: 'Failed to send message: ' + err.message });
    }
});

// Broadcast messages to multiple numbers with delay
app.post('/broadcast', authenticate, async (req, res) => {
    const { numbers, message, file, caption, delay: delaySec = 5, mimetype, filename } = req.body;

    if (!numbers || (!Array.isArray(numbers) && typeof numbers !== 'string')) {
        return res.status(400).json({ status: false, message: 'Numbers must be an array or comma-separated string.' });
    }

    if (connectionState !== 'connected') {
        return res.status(503).json({ status: false, message: 'WhatsApp Gateway is not connected.' });
    }

    const numberList = Array.isArray(numbers) 
        ? numbers 
        : numbers.split(',').map(n => n.trim()).filter(Boolean);

    const results = [];

    // Run the broadcast loop
    for (let i = 0; i < numberList.length; i++) {
        const number = numberList[i];
        try {
            let response;
            if (file) {
                const mediaPayload = getMediaPayload(file, caption || message, mimetype, filename);
                response = await sock.sendMessage(number, mediaPayload);
            } else {
                response = await sock.sendMessage(number, { text: message });
            }

            results.push({ number, status: true, message: 'Message sent', messageId: response.key.id });
        } catch (err) {
            results.push({ number, status: false, error: err.message });
        }

        // Wait for the configured delay interval (except after the last message)
        if (i < numberList.length - 1) {
            await delay(delaySec * 1000);
        }
    }

    res.json({ status: true, results });
});

// Start Node Server & DB Connection
app.listen(PORT, HOST, async () => {
    console.log(`WhatsApp Gateway Service running on http://${HOST}:${PORT}`);
    await initDb();
    await connectToWhatsApp();
});
