import { whatsappService } from '../services/WhatsAppService.js';
import { delay } from '@whiskeysockets/baileys';

export class GatewayController {
    static async getStatus(req, res) {
        return res.json({
            status: true,
            connection: whatsappService.connectionState,
            qr: whatsappService.latestQr,
            user: whatsappService.sock ? whatsappService.sock.user : null
        });
    }

    static async connect(req, res) {
        if (whatsappService.connectionState === 'connected') {
            return res.json({ status: true, message: 'WhatsApp is already connected.' });
        }

        console.log('Connect request received. Initializing pairing session...');
        whatsappService.latestQr = null;
        whatsappService.connectionState = 'connecting';

        // Initialize connection
        whatsappService.connect();

        return res.json({ status: true, message: 'Pairing session initialized. Please retrieve the QR code.' });
    }

    static async logout(req, res) {
        try {
            await whatsappService.logout();
            return res.json({ status: true, message: 'Session logged out and cleared.' });
        } catch (err) {
            console.error('Error logging out:', err.message);
            return res.status(500).json({ status: false, message: 'Failed to log out: ' + err.message });
        }
    }

    static async reset(req, res) {
        try {
            await whatsappService.reset();
            return res.json({ status: true, message: 'WhatsApp session has been force-reset and cleared.' });
        } catch (err) {
            console.error('Error resetting session:', err.message);
            return res.status(500).json({ status: false, message: 'Failed to reset session: ' + err.message });
        }
    }

    static async getQrPage(req, res) {
        if (whatsappService.connectionState === 'connected') {
            return res.send('<h1>WhatsApp is already connected!</h1>');
        }
        if (!whatsappService.latestQr) {
            return res.send('<h1>QR code is generating or loading. Please refresh in a moment...</h1>');
        }

        return res.send(`
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; font-family: sans-serif; background: #f8fafc;">
                <div style="background: white; padding: 2rem; border-radius: 1.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.05); text-align: center;">
                    <h2 style="color: #0f172a; margin-bottom: 0.5rem;">Pair WhatsApp Gateway</h2>
                    <p style="color: #64748b; font-size: 0.875rem; margin-bottom: 1.5rem;">Scan this QR code using WhatsApp Link Devices</p>
                    <img src="${whatsappService.latestQr}" style="width: 250px; height: 250px; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 0.5rem;" />
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
    }

    static async sendMessage(req, res) {
        const { number, message, file, caption, mimetype, filename } = req.body;

        if (!number) {
            return res.status(400).json({ status: false, message: 'Recipient number is required.' });
        }

        if (whatsappService.connectionState !== 'connected') {
            return res.status(503).json({ status: false, message: 'WhatsApp Gateway is not connected.' });
        }

        try {
            const response = await whatsappService.sendMessage(number, message, file, mimetype, filename, caption);
            return res.json({ status: true, message: 'Message sent successfully.', messageId: response.key.id });
        } catch (err) {
            console.error('Error sending message:', err.message);
            return res.status(500).json({ status: false, message: 'Failed to send message: ' + err.message });
        }
    }

    static async broadcast(req, res) {
        const { numbers, message, file, caption, delay: delaySec = 5, mimetype, filename } = req.body;

        if (!numbers || (!Array.isArray(numbers) && typeof numbers !== 'string')) {
            return res.status(400).json({ status: false, message: 'Numbers must be an array or comma-separated string.' });
        }

        if (whatsappService.connectionState !== 'connected') {
            return res.status(503).json({ status: false, message: 'WhatsApp Gateway is not connected.' });
        }

        const numberList = Array.isArray(numbers) 
            ? numbers 
            : numbers.split(',').map(n => n.trim()).filter(Boolean);

        const results = [];

        try {
            for (let i = 0; i < numberList.length; i++) {
                const number = numberList[i];
                try {
                    const response = await whatsappService.sendMessage(number, message, file, mimetype, filename, caption);
                    results.push({ number, status: true, message: 'Message sent', messageId: response.key.id });
                } catch (err) {
                    results.push({ number, status: false, error: err.message });
                }

                if (i < numberList.length - 1) {
                    await delay(delaySec * 1000);
                }
            }

            return res.json({ status: true, results });
        } catch (err) {
            return res.status(500).json({ status: false, message: 'Broadcast processing encountered an error: ' + err.message });
        }
    }
}
