<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappGatewayService
{
    protected ?string $apiUrl;

    protected ?string $secretKey;

    public function __construct()
    {
        $settings = AppSetting::first();

        $this->apiUrl = env('WA_GATEWAY_URL', 'http://localhost:6969');
        $this->secretKey = $settings->whatsapp_gateway_secret ?? '';
    }

    /**
     * Format any raw phone number input to a standard JID or LID.
     * Replaces 08/8 with 62 and strips space/dash characters.
     */
    public function formatNumber(string $number): string
    {
        // Remove spaces, dashes, parentheses, plus signs
        $clean = preg_replace('/[^\d@a-zA-Z\.]/', '', $number);

        // If it already contains JID/LID suffix, return it directly
        if (str_contains($clean, '@')) {
            return $clean;
        }

        // Format leading prefix
        if (str_starts_with($clean, '0')) {
            $clean = '62'.substr($clean, 1);
        } elseif (str_starts_with($clean, '8')) {
            $clean = '62'.$clean;
        }

        // Default standard user suffix
        return $clean.'@s.whatsapp.net';
    }

    /**
     * Check connection and login status of the local WhatsApp Gateway.
     */
    public function getStatus(): array
    {
        if (! $this->apiUrl) {
            return ['status' => false, 'connection' => 'disconnected', 'message' => 'API URL not configured.'];
        }

        try {
            $response = Http::withHeaders($this->getHeaders())
                ->get($this->apiUrl.'/status');

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'status' => false,
                'connection' => 'disconnected',
                'message' => 'Failed to retrieve gateway status. Code: '.$response->status(),
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'connection' => 'disconnected',
                'message' => 'Gateway server is unreachable.',
            ];
        }
    }

    /**
     * Send a single plain text message or media message.
     *
     * @param  string  $number  Recipient number
     * @param  string  $message  Message body (or caption)
     * @param  string|null  $fileUrl  Optional media URL (image/video/doc)
     * @param  string|null  $mimetype  Optional mimetype
     * @param  string|null  $filename  Optional filename
     */
    public function sendMessage(string $number, string $message, ?string $fileUrl = null, ?string $mimetype = null, ?string $filename = null): array
    {
        if (! $this->apiUrl) {
            return ['status' => false, 'message' => 'WhatsApp API URL is not configured.'];
        }

        $formattedNumber = $this->formatNumber($number);

        try {
            $payload = [
                'number' => $formattedNumber,
            ];

            if ($fileUrl) {
                $payload['file'] = $fileUrl;
                $payload['caption'] = $message;
                if ($mimetype) {
                    $payload['mimetype'] = $mimetype;
                }
                if ($filename) {
                    $payload['filename'] = $filename;
                }
            } else {
                $payload['message'] = $message;
            }

            $response = Http::withHeaders($this->getHeaders())
                ->post($this->apiUrl.'/send-message', $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('WhatsApp Gateway sendMessage failed', [
                'status' => $response->status(),
                'response' => $response->body(),
                'number' => $formattedNumber,
            ]);

            return [
                'status' => false,
                'message' => $response->json('message') ?? 'Failed to send WhatsApp message.',
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp Gateway sendMessage exception', [
                'message' => $e->getMessage(),
                'number' => $formattedNumber,
            ]);

            return [
                'status' => false,
                'message' => 'An error occurred while connecting to the WhatsApp gateway.',
            ];
        }
    }

    /**
     * Broadcast a message to multiple numbers with a delay interval.
     *
     * @param  array  $numbers  Array of recipient numbers
     * @param  string  $message  Message text (or caption)
     * @param  string|null  $fileUrl  Optional media URL
     * @param  int  $delay  Interval delay in seconds between sends
     */
    public function sendBroadcast(array $numbers, string $message, ?string $fileUrl = null, int $delay = 5): array
    {
        if (! $this->apiUrl) {
            return ['status' => false, 'message' => 'WhatsApp API URL is not configured.'];
        }

        // Format all recipient numbers
        $formattedNumbers = array_map([$this, 'formatNumber'], $numbers);

        try {
            $payload = [
                'numbers' => $formattedNumbers,
                'delay' => $delay,
            ];

            if ($fileUrl) {
                $payload['file'] = $fileUrl;
                $payload['caption'] = $message;
            } else {
                $payload['message'] = $message;
            }

            $response = Http::withHeaders($this->getHeaders())
                ->post($this->apiUrl.'/broadcast', $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('WhatsApp Gateway broadcast failed', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return [
                'status' => false,
                'message' => $response->json('message') ?? 'Failed to execute WhatsApp broadcast.',
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp Gateway broadcast exception', [
                'message' => $e->getMessage(),
            ]);

            return [
                'status' => false,
                'message' => 'An error occurred while connecting to the WhatsApp gateway.',
            ];
        }
    }

    /**
     * Log out and disconnect the WhatsApp Gateway session.
     */
    public function logout(): array
    {
        if (! $this->apiUrl) {
            return ['status' => false, 'message' => 'API URL not configured.'];
        }

        try {
            $response = Http::withHeaders($this->getHeaders())
                ->post($this->apiUrl.'/logout');

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'status' => false,
                'message' => $response->json('message') ?? 'Failed to logout session.',
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Gateway server is unreachable.',
            ];
        }
    }

    /**
     * Get request headers containing the secret authorization token.
     */
    protected function getHeaders(): array
    {
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        if ($this->secretKey) {
            $headers['X-Gateway-Secret'] = $this->secretKey;
        }

        return $headers;
    }
}
