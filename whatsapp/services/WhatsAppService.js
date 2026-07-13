import makeWASocket, { useMultiFileAuthState, DisconnectReason, fetchLatestBaileysVersion } from '@whiskeysockets/baileys';
import pino from 'pino';
import path from 'path';
import fs from 'fs';
import qrcode from 'qrcode';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const SESSION_DIR = path.join(__dirname, '../session_auth');

class WhatsAppService {
    constructor() {
        this.sock = null;
        this.connectionState = 'disconnected'; // 'disconnected', 'connecting', 'connected', 'qrcode'
        this.latestQr = null;
        this.loggedInUser = null;
        this.sessionDir = SESSION_DIR;
    }

    hasSession() {
        return fs.existsSync(path.join(this.sessionDir, 'creds.json'));
    }

    async connect() {
        // Clean up any existing connection listeners
        if (this.sock) {
            try {
                this.sock.ev.removeAllListeners();
                this.sock.end();
            } catch (e) {}
            this.sock = null;
        }

        const { state, saveCreds } = await useMultiFileAuthState(this.sessionDir);

        let version = [2, 3000, 1015970094];
        try {
            const { version: latestVersion, isLatest } = await fetchLatestBaileysVersion();
            version = latestVersion;
            console.log(`Using WhatsApp version v${version.join('.')}, isLatest: ${isLatest}`);
        } catch (err) {
            console.error('Failed to fetch latest WhatsApp version, using fallback:', err.message);
        }

        this.sock = makeWASocket({
            auth: state,
            version,
            logger: pino({ level: 'silent' })
        });

        this.sock.ev.on('creds.update', saveCreds);

        this.sock.ev.on('connection.update', async (update) => {
            const { connection, lastDisconnect, qr } = update;

            if (qr) {
                this.connectionState = 'qrcode';
                try {
                    this.latestQr = await qrcode.toDataURL(qr);
                } catch (err) {
                    console.error('Failed to generate QR data URL:', err.message);
                }
            }

            if (connection === 'connecting') {
                this.connectionState = 'connecting';
                console.log('Connecting to WhatsApp...');
            }

            if (connection === 'open') {
                this.connectionState = 'connected';
                this.latestQr = null;
                this.loggedInUser = this.sock.user;
                console.log('WhatsApp connection is fully active for user:', this.sock.user.id || this.sock.user.name || this.sock.user);
            }

            if (connection === 'close') {
                const statusCode = lastDisconnect?.error?.output?.statusCode;
                const shouldReconnect = statusCode !== DisconnectReason.loggedOut;
                console.log('Connection closed. Status code:', statusCode, 'Reason:', lastDisconnect?.error?.message || lastDisconnect?.error || 'Unknown');

                this.loggedInUser = null;
                const wasConnected = this.connectionState === 'connected';

                if (!wasConnected) {
                    console.log('Pairing session closed or expired.');
                    this.connectionState = 'disconnected';
                    this.latestQr = null;
                    if (this.sock) {
                        try {
                            this.sock.ev.removeAllListeners();
                        } catch (e) {}
                        this.sock = null;
                    }
                } else {
                    if (shouldReconnect) {
                        console.log('Reconnecting to active session in 5 seconds...');
                        this.connectionState = 'connecting';
                        setTimeout(() => this.connect(), 5000);
                    } else {
                        console.log('Logged out of WhatsApp. Clearing session dir...');
                        this.connectionState = 'disconnected';
                        this.latestQr = null;
                        if (fs.existsSync(this.sessionDir)) {
                            fs.rmSync(this.sessionDir, { recursive: true, force: true });
                        }
                        if (this.sock) {
                            try {
                                this.sock.ev.removeAllListeners();
                            } catch (e) {}
                            this.sock = null;
                        }
                    }
                }
            }
        });
    }

    async logout() {
        if (this.sock) {
            try {
                this.sock.ev.removeAllListeners();
                await this.sock.logout();
            } catch (e) {
                if (this.sock) {
                    this.sock.end();
                }
            }
            this.sock = null;
        }

        if (fs.existsSync(this.sessionDir)) {
            fs.rmSync(this.sessionDir, { recursive: true, force: true });
        }

        this.connectionState = 'disconnected';
        this.latestQr = null;
        this.loggedInUser = null;
    }

    async reset() {
        if (this.sock) {
            try {
                this.sock.ev.removeAllListeners();
                this.sock.end();
            } catch (e) {}
            this.sock = null;
        }

        if (fs.existsSync(this.sessionDir)) {
            try {
                fs.rmSync(this.sessionDir, { recursive: true, force: true });
            } catch (e) {
                console.error('Failed to remove session directory:', e.message);
            }
        }

        this.connectionState = 'disconnected';
        this.latestQr = null;
        this.loggedInUser = null;
        console.log('WhatsApp connection has been force reset.');
    }

    async sendMessage(number, message, fileUrl = null, mimetype = null, filename = null, caption = null) {
        if (this.connectionState !== 'connected' || !this.sock) {
            throw new Error('WhatsApp Gateway is not connected.');
        }

        if (fileUrl) {
            const mediaPayload = this.getMediaPayload(fileUrl, caption || message, mimetype, filename);
            return await this.sock.sendMessage(number, mediaPayload);
        } else {
            if (!message) {
                throw new Error('Message body is required when sending text.');
            }
            return await this.sock.sendMessage(number, { text: message });
        }
    }

    getMediaPayload(fileUrl, caption, originalMimetype = null, originalFilename = null) {
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
                payload.caption = caption;
            }
        }

        return payload;
    }
}

// Singleton pattern so the Express routes and socket service share the exact same state
export const whatsappService = new WhatsAppService();
