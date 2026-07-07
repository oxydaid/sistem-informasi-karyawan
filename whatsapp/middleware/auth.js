import { getSecretKey } from '../config/database.js';

export async function authenticate(req, res, next) {
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
