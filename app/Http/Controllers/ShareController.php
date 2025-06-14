<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use League\CommonMark\CommonMarkConverter;

class ShareController extends Controller
{
    // Redirect dari /share/{token} ke /share-page?store_token={token}
    public function handleShare($token)
    {
        // Kamu bisa logika extra di sini (log, validasi, dsb) kalau mau
        return redirect('/share-page?store_token=' . $token);
    }

    // Tampilkan halaman share multi-platform, token dari URL (?store_token=)
    public function showSharePage(Request $request)
    {
        $token = $request->query('store_token', 'EXAMPLETOKEN');
        // Daftar platform dan endpoint API masing-masing
        $platforms = [
            [
                'name' => 'Red Note',
                'api' => '/proxy-api/rednote?store_token='. $token,
                'key' => 'rednote'
            ],
            [
                'name' => 'Google Review',
                'api' => '/proxy-api/google?store_token='. $token,
                'key' => 'google'
            ],
            [
                'name' => 'Facebook',
                'api' => '/proxy-api/facebook?store_token='. $token,
                'key' => 'facebook'
            ],
            [
                'name' => 'Instagram',
                'api' => '/proxy-api/instagram?store_token='. $token,
                'key' => 'instagram'
            ],
            [
                'name' => 'WhatsApp',
                'api' => '/proxy-api/whatsapp?store_token='. $token,
                'key' => 'whatsapp'
            ],
            [
                'name' => 'Home',
                'api' => '/proxy-api/home?store_token='. $token,
                'key' => 'home'
            ],
        ];
        return view('shared-page', compact('platforms', 'token'));
    }
    
    public function proxyAPI(Request $request, $platform)
    {
        $token = $request->query('store_token');
        $endpoints = [
            'rednote' => 'https://fanstagai.com/api/market/share/data/xiaohongshu',
            'google' => 'https://fanstagai.com/api/market/share/data/google_reviews',
            'facebook' => 'https://fanstagai.com/api/market/share/data/facebook_comment',
            'instagram' => 'https://fanstagai.com/api/market/share/data/instagram',
            'whatsapp' => 'https://fanstagai.com/api/market/share/data/whatsapp_community',
            'wifi' => 'https://fanstagai.com/api/market/share/data/wifi',
            'home' => 'https://fanstagai.com/api/market/share/data/home',
        ];

        if (!isset($endpoints[$platform])) {
            return response()->json(['error' => 'Invalid platform'], 400);
        }

        $response = Http::withHeaders([
            'store-token' => $token
        ])->get($endpoints[$platform]);

        return response($response->body(), $response->status())
            ->header('Content-Type', 'application/json');
    }
    
    public function renderPlatformNote($platform)
    {
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzM4NCJ9.eyJzdWIiOiJtZW1iZXIiLCJpZCI6MjA5fQ.-1yqPiWwYV_ltGSs9CHvjSrwZgU-hGL7IEAkK_sWDBx-JKsfEOOG5CVhORuuZuq6';
        
        $url = "http://127.0.0.1:8000/proxy-api/{$platform}?store_token={$token}";
        $response = Http::timeout(5)->get($url); // timeout opsional
        if (!$response->ok()) {
            abort(404, 'Platform not found');
        }

        $data = $response->json();

        $info = $data['data']['info'] ?? [];
        $title = $info['title_format'] ?? 'Untitled';
        $rawContent = $info['content'] ?? '';

        $converter = new \League\CommonMark\CommonMarkConverter();
        $htmlContent = $converter->convertToHtml($rawContent);

        return view('red-note-single', [
            'title' => $title,
            'content' => $htmlContent
        ]);
    }
    public function buildSharePage(Request $request)
    {
        $token = $request->query('store_token');

        $platforms = [
            [
                'name' => 'Red Note',
                'api' => 'xiaohongshu',
                'key' => 'rednote',
            ],
            [
                'name' => 'Google Review',
                'api' => 'google_reviews',
                'key' => 'google',
            ],
            [
                'name' => 'Facebook',
                'api' => 'facebook_comment',
                'key' => 'facebook',
            ],
            [
                'name' => 'Instagram',
                'api' => 'instagram',
                'key' => 'instagram',
            ],
            [
                'name' => 'WhatsApp',
                'api' => 'whatsapp_community',
                'key' => 'whatsapp',
            ],
            [
                'name' => 'Home',
                'api' => 'home',
                'key' => 'home',
            ],
        ];

        $results = [];

        foreach ($platforms as $p) {
            $response = Http::withHeaders([
                'store-token' => $token
            ])->get("https://fanstagai.com/api/market/share/data/" . $p['api']);

            $info = $response->json('data.info') ?? [];

            $results[] = [
                'name' => $p['name'],
                'title' => $info['title_format'] ?? 'Untitled',
                'content' => $info['content'] ?? '',
            ];
        }

        return view('shared-page', [
            'platforms' => $results
        ]);
    }

}
