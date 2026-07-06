<?php

use App\Models\AppSetting;
use App\Services\WhatsappGatewayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

test('it formats phone numbers to WhatsApp JID format correctly', function () {
    $service = new WhatsappGatewayService;

    // 08 prefix
    expect($service->formatNumber('081234567890'))->toBe('6281234567890@s.whatsapp.net');

    // 8 prefix
    expect($service->formatNumber('81234567890'))->toBe('6281234567890@s.whatsapp.net');

    // Numbers with spaces/dashes
    expect($service->formatNumber('+62 812-3456-7890'))->toBe('6281234567890@s.whatsapp.net');

    // Already formatted JID
    expect($service->formatNumber('6281234567890@s.whatsapp.net'))->toBe('6281234567890@s.whatsapp.net');
});

test('it can fetch gateway status', function () {
    Http::fake([
        'localhost:4000/status' => Http::response([
            'status' => true,
            'connection' => 'connected',
            'qr' => null,
            'user' => ['id' => '628123456789@s.whatsapp.net'],
        ], 200),
    ]);

    $service = new WhatsappGatewayService;
    $status = $service->getStatus();

    expect($status['status'])->toBeTrue()
        ->and($status['connection'])->toBe('connected')
        ->and($status['user']['id'])->toBe('628123456789@s.whatsapp.net');
});

test('it sends single text message successfully', function () {
    AppSetting::firstOrCreate([], [
        'whatsapp_gateway_secret' => 'test-secret',
    ]);

    Http::fake([
        'localhost:4000/send-message' => Http::response([
            'status' => true,
            'message' => 'Message sent successfully.',
            'messageId' => 'msg-12345',
        ], 200),
    ]);

    $service = new WhatsappGatewayService;
    $response = $service->sendMessage('08123456789', 'Hello employee');

    Http::assertSent(function ($request) {
        return $request->url() === 'http://localhost:4000/send-message'
            && $request->header('X-Gateway-Secret')[0] === 'test-secret'
            && $request['number'] === '628123456789@s.whatsapp.net'
            && $request['message'] === 'Hello employee';
    });

    expect($response['status'])->toBeTrue()
        ->and($response['messageId'])->toBe('msg-12345');
});

test('it sends media message successfully', function () {
    Http::fake([
        'localhost:4000/send-message' => Http::response([
            'status' => true,
            'message' => 'Message sent successfully.',
        ], 200),
    ]);

    $service = new WhatsappGatewayService;
    $response = $service->sendMessage(
        '08123456789',
        'Your payslip',
        'http://example.com/payslip.pdf',
        'application/pdf',
        'Payslip.pdf'
    );

    Http::assertSent(function ($request) {
        return $request->url() === 'http://localhost:4000/send-message'
            && $request['file'] === 'http://example.com/payslip.pdf'
            && $request['caption'] === 'Your payslip'
            && $request['mimetype'] === 'application/pdf'
            && $request['filename'] === 'Payslip.pdf';
    });

    expect($response['status'])->toBeTrue();
});

test('it broadcasts messages successfully', function () {
    Http::fake([
        'localhost:4000/broadcast' => Http::response([
            'status' => true,
            'results' => [
                ['number' => '628123456789@s.whatsapp.net', 'status' => true],
            ],
        ], 200),
    ]);

    $service = new WhatsappGatewayService;
    $response = $service->sendBroadcast(['08123456789'], 'Blast Message', null, 5);

    Http::assertSent(function ($request) {
        return $request->url() === 'http://localhost:4000/broadcast'
            && $request['numbers'] === ['628123456789@s.whatsapp.net']
            && $request['message'] === 'Blast Message'
            && $request['delay'] === 5;
    });

    expect($response['status'])->toBeTrue();
});
