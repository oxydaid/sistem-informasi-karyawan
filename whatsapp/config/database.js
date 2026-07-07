import mysql from 'mysql2/promise';
import dotenv from 'dotenv';
import path from 'path';
import fs from 'fs';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Load env variables
if (fs.existsSync(path.join(__dirname, '../.env'))) {
    dotenv.config({ path: path.join(__dirname, '../.env') });
} else {
    dotenv.config({ path: path.join(__dirname, '../../.env') });
}

let db = null;

export async function initDb() {
    if (db) return db;
    try {
        db = await mysql.createConnection({
            host: process.env.DB_HOST || '127.0.0.1',
            port: process.env.DB_PORT || 3306,
            database: process.env.DB_DATABASE || 'manajemen_karyawan',
            user: process.env.DB_USERNAME || 'root',
            password: process.env.DB_PASSWORD || ''
        });
        console.log('Database connected successfully in WhatsApp Gateway');
        return db;
    } catch (err) {
        console.error('Database connection failed in WhatsApp Gateway:', err.message);
        db = null;
        throw err;
    }
}

export async function getSecretKey() {
    try {
        const connection = await initDb();
        const [rows] = await connection.query('SELECT whatsapp_gateway_secret FROM app_settings LIMIT 1');
        if (rows && rows.length > 0 && rows[0].whatsapp_gateway_secret) {
            return rows[0].whatsapp_gateway_secret;
        }
    } catch (err) {
        console.error('Failed to query app_settings for secret key:', err.message);
        db = null;
    }
    return process.env.WA_GATEWAY_SECRET || 'wa-gateway';
}
