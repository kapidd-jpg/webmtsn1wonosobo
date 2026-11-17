<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'gemini' => [
        'key' => env('GEMINI_API_KEY'),
        // Flash-lite lebih ringan dan jauh lebih jarang kena 503 "high demand".
        'model' => env('GEMINI_MODEL', 'gemini-3.5-flash-lite'),
        'fallback_models' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('GEMINI_FALLBACK_MODELS', 'gemini-3.1-flash-lite,gemini-2.5-flash-lite'))
        ))),
        'max_output_tokens' => (int) env('GEMINI_MAX_OUTPUT_TOKENS', 2048),
        // Opsional. Model 3.x memakai thinking_level (minimal/low/...), bukan thinkingBudget.
        // Kosongkan untuk memakai default model.
        'thinking_level' => env('GEMINI_THINKING_LEVEL'),
    ],

    /*
    | Penyimpanan gambar unggahan. Bila token diisi (otomatis oleh Vercel
    | saat Blob store terhubung), gambar disimpan ke Vercel Blob. Bila
    | kosong, gambar disimpan ke penyimpanan lokal.
    */
    'vercel_blob' => [
        'token' => env('BLOB_READ_WRITE_TOKEN'),
        'store_id' => env('BLOB_STORE_ID'),
        'access' => env('BLOB_ACCESS', 'public'),
    ],

];
