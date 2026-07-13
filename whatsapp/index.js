import express from 'express';
import dotenv from 'dotenv';
import path from 'path';
import fs from 'fs';
import { fileURLToPath } from 'url';

import { initDb } from './config/database.js';
import { authenticate } from './middleware/auth.js';
import { whatsappService } from './services/WhatsAppService.js';
import { GatewayController } from './controllers/GatewayController.js';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Load env variables
if (fs.existsSync(path.join(__dirname, '.env'))) {
    dotenv.config({ path: path.join(__dirname, '.env') });
} else {
    dotenv.config({ path: path.join(__dirname, '../.env') });
}

const app = express();
app.use(express.json());

const PORT = process.env.WA_GATEWAY_PORT || 6969;
const HOST = process.env.WA_GATEWAY_HOST || '0.0.0.0';

// --- Routes ---
app.get('/status', GatewayController.getStatus);
app.post('/connect', authenticate, GatewayController.connect);
app.post('/logout', authenticate, GatewayController.logout);
app.post('/reset', authenticate, GatewayController.reset);
app.get('/qr', GatewayController.getQrPage);
app.post('/send-message', authenticate, GatewayController.sendMessage);
app.post('/broadcast', authenticate, GatewayController.broadcast);

// Start Server & Database
app.listen(PORT, HOST, async () => {
    console.log(`WhatsApp Gateway Service running on http://${HOST}:${PORT}`);
    
    try {
        await initDb();
    } catch (e) {
        console.error('Failed to initialize database connection on startup:', e.message);
    }
    
    // Auto-connect on startup if session credentials exist
    if (whatsappService.hasSession()) {
        console.log('Session credentials found. Auto-connecting to WhatsApp...');
        whatsappService.connect();
    } else {
        console.log('No session credentials found. Gateway is idle. Click Connect in UI to pair.');
    }
});
