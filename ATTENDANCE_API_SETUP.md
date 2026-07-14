# eBilling — Attendance Export API

Integrasi read-only: ambil data absensi harian dari eBilling ke aplikasi lain.  
Disarankan lewat **backend app kamu** (bukan langsung dari browser).

| | |
|--|--|
| Base URL | `https://ebilling.sky.net.id` |
| Endpoint | `GET /api/attendances/export` |
| Auth | Header token (lihat di bawah) |
| Timezone | `Asia/Jakarta` |
| Login eBilling | Tidak perlu |

---

## Konfigurasi di app kamu

```env
EBILLING_BASE_URL=https://ebilling.sky.net.id
EBILLING_ATTENDANCE_TOKEN=3f62d8ea43ed690178d8ecaa832909e6e3b73f0e9c4eebc8
```

Token hanya di environment server. Jangan di frontend, mobile client, atau repository publik.

---

## Request

```http
GET /api/attendances/export?date=2026-07-12
X-Attendance-Token: 3f62d8ea43ed690178d8ecaa832909e6e3b73f0e9c4eebc8
```

Header lain yang diterima:

- `Authorization: Bearer <token>`
- `X-Attendance-Export-Token: <token>`

Token di query string (`?token=`) **ditolak**.

### Parameter

| Nama | Default | Keterangan |
|------|---------|------------|
| `date` | hari ini (WIB) | Satu hari, format `Y-m-d` |
| `from` / `to` | — | Rentang tanggal |
| `page` | `1` | Halaman |
| `per_page` | `200` | Maksimal `500` |

Jika `from` atau `to` diisi, rentang dipakai; jika tidak, pakai `date`.

---

## Contoh

### cURL

```bash
curl -sS \
  -H "X-Attendance-Token: $EBILLING_ATTENDANCE_TOKEN" \
  "$EBILLING_BASE_URL/api/attendances/export?date=2026-07-12"
```

### Node (proxy di backend)

```js
const base = process.env.EBILLING_BASE_URL;
const token = process.env.EBILLING_ATTENDANCE_TOKEN;

async function getAttendance(date) {
  const url = new URL('/api/attendances/export', base);
  if (date) url.searchParams.set('date', date);

  const res = await fetch(url, {
    headers: { 'X-Attendance-Token': token },
  });
  if (!res.ok) throw new Error(`eBilling ${res.status}: ${await res.text()}`);
  return res.json();
}
```

### PHP

```php
function fetchAttendance(?string $date = null): array
{
    $base  = rtrim(getenv('EBILLING_BASE_URL') ?: 'https://ebilling.sky.net.id', '/');
    $token = getenv('EBILLING_ATTENDANCE_TOKEN') ?: '';
    $qs    = $date ? '?date=' . urlencode($date) : '';

    $ch = curl_init($base . '/api/attendances/export' . $qs);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTPHEADER => [
            "X-Attendance-Token: $token",
            'Accept: application/json',
        ],
    ]);
    $body = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code !== 200) {
        throw new RuntimeException("eBilling HTTP $code: $body");
    }
    return json_decode($body, true, 512, JSON_THROW_ON_ERROR);
}
```

### Python

```python
import os, requests

BASE = os.environ.get("EBILLING_BASE_URL", "https://ebilling.sky.net.id")
TOKEN = os.environ["EBILLING_ATTENDANCE_TOKEN"]

def get_attendance(date=None):
    params = {"date": date} if date else {}
    r = requests.get(
        f"{BASE}/api/attendances/export",
        params=params,
        headers={"X-Attendance-Token": TOKEN},
        timeout=30,
    )
    r.raise_for_status()
    return r.json()
```

---

## Response `200`

```json
{
  "date": "2026-07-12",
  "timezone": "Asia/Jakarta",
  "from": "2026-07-12T00:00:00+07:00",
  "to": "2026-07-12T23:59:59+07:00",
  "page": 1,
  "per_page": 200,
  "total": 14,
  "count": 14,
  "unique_senders": 14,
  "attendances": [
    {
      "id": 29,
      "sender_name": "Agus Setiadi",
      "caption": "Masuk kantor",
      "photo_path": "ABSENSI/JULI 2026/12 JULI/Agus_Setiadi_004637.jpg",
      "photo_url": "https://drive.sky.net.id/files/ABSENSI/JULI%202026/12%20JULI/Agus_Setiadi_004637.jpg",
      "checked_in_at": "2026-07-12T07:46:37+07:00",
      "message_id": "ACBF8BB00EE458264D089FD723E475AD"
    }
  ]
}
```

| Field | Isi |
|-------|-----|
| `total` | Semua baris di window tanggal |
| `count` | Baris di halaman ini |
| `unique_senders` | Jumlah nama unik |
| `checked_in_at` | Waktu absen (WIB, ISO-8601) |
| `photo_url` | URL foto di drive |
| `message_id` | ID pesan WA (unik) |

Nomor WA / JID internal tidak dikirim. Field kosong dihilangkan dari object.

---

## Error

| HTTP | Arti |
|------|------|
| `401` | Token salah, hilang, atau dikirim di query |
| `403` | HTTPS wajib / IP tidak diizinkan |
| `422` | Format parameter invalid |
| `429` | Rate limit (60/menit) atau terlalu banyak gagal auth |
| `503` | Token belum dikonfigurasi di server eBilling |

---

## Catatan integrasi

1. Frontend / mobile panggil **API app kamu**; app kamu yang call eBilling.
2. Filter tanggal mengikuti **WIB**, bukan UTC server app.
3. `total: 0` berarti belum ada record di eBilling untuk hari itu (bukan error API).
4. Rate limit: 60 request per menit per IP.

---

## Token

```
3f62d8ea43ed690178d8ecaa832909e6e3b73f0e9c4eebc8
```

Token shared internal. Jangan diganti tanpa koordinasi tim eBilling — integrasi lain akan `401`.

---

## Smoke test

```bash
curl -sS -w "\nHTTP %{http_code}\n" \
  -H "X-Attendance-Token: $EBILLING_ATTENDANCE_TOKEN" \
  "$EBILLING_BASE_URL/api/attendances/export?per_page=1"
```

Harapan: `HTTP 200` dan body JSON berisi array `attendances`.

---

Kontak teknis: tim eBilling / infra Skynet.
