<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Url;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class UrlController extends Controller
{
    public function index()
    {
        return view('url.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        $originalUrl = $request->input('url');
        $shortUrl = $this->generateShortUrl();

        // Check if the URL already exists
        $existingUrl = Url::where('original_url', $originalUrl)->first();

        if ($existingUrl) {
            $shortUrl = $existingUrl->short_url;
        } else {

            // Checking URL API
            $safeBrowsingResponse = $this->checkSafeBrowsing($originalUrl);
            if (!empty($safeBrowsingResponse)) {
                return response()->json(['error' => 'The URL is flagged as unsafe.']);
            }

            Url::create(['original_url' => $originalUrl, 'short_url' => $shortUrl]);
        }

        return response()->json(['shortUrl' => url($shortUrl)]);
    }

    private function generateShortUrl()
    {
        $randomString = Str::random(6);
        $hash = preg_replace('/[^a-zA-Z0-9]/', '', $randomString);
        return 'shortly/' . $hash;
    }

    private function checkSafeBrowsing($url)
    {
        $apiKey = env('SAFE_BROWSRING_API_KEY');
        $clientId = env('SAFE_BROWSRING_CLIENT_ID');
        $clientVersion = env('SAFE_BROWSRING_CLIENT_VERSION');
        
        $endpoint = "https://safebrowsing.googleapis.com/v4/threatMatches:find?key=$apiKey";

        $response = Http::post($endpoint, [
            'client' => [
                'clientId' => $clientId,
                'clientVersion' => $clientVersion,
            ],
            'threatInfo' => [
                'threatTypes' => ['MALWARE', 'PHISHING'],
                'platformTypes' => ['ANY_PLATFORM'],
                'threatEntryTypes' => ['URL'],
                'threatEntries' => [['url' => $url]],
            ],
        ], [
            'key' => $apiKey,
        ]);

        return json_decode($response->body());
    }

    public function redirect($hash)
    {
        $url = Url::where('short_url', 'shortly/' . $hash)->firstOrFail();
        return redirect($url->original_url);
    }
}
