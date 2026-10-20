<?php

namespace App\Services\Quran;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\ApiUsageLog;

class QuranApiService
{
    protected string $baseUrl = 'https://api.alquran.cloud/v1';

    /**
     * Get list of all 114 Surahs.
     */
    public function getSurahs(): ?array
    {
        return $this->cachedRequest('quran_surahs', '/surah', 86400);
    }

    /**
     * Get a specific Surah with Arabic text.
     */
    public function getSurah(int $surahNumber, string $edition = 'quran-uthmani'): ?array
    {
        $cacheKey = "surah_{$surahNumber}_{$edition}";
        return $this->cachedRequest($cacheKey, "/surah/{$surahNumber}/{$edition}", 2592000);
    }

    /**
     * Get Surah with multiple editions (Arabic text + translation) in a single API call.
     */
    public function getSurahWithEditions(int $surahNumber, array $editions = ['quran-uthmani', 'en.asad']): ?array
    {
        $editionStr = implode(',', $editions);
        $cacheKey = "surah_multi_{$surahNumber}_" . md5($editionStr);
        return $this->cachedRequest($cacheKey, "/surah/{$surahNumber}/editions/{$editionStr}", 2592000);
    }

    /**
     * Get a specific Ayah.
     */
    public function getAyah(int $surahNumber, int $ayahNumber, string $edition = 'quran-uthmani'): ?array
    {
        $reference = "{$surahNumber}:{$ayahNumber}";
        $cacheKey = "ayah_{$reference}_{$edition}";
        return $this->cachedRequest($cacheKey, "/ayah/{$reference}/{$edition}", 86400);
    }

    /**
     * Get Surah with translation.
     */
    public function getSurahWithTranslation(int $surahNumber, string $translation = 'en.asad'): ?array
    {
        $cacheKey = "surah_{$surahNumber}_translation_{$translation}";
        return $this->cachedRequest($cacheKey, "/surah/{$surahNumber}/{$translation}", 86400);
    }

    /**
     * Get multiple editions for an Ayah (Arabic + translation).
     */
    public function getAyahEditions(int $surahNumber, int $ayahNumber, array $editions = ['quran-uthmani', 'en.asad']): ?array
    {
        $reference = "{$surahNumber}:{$ayahNumber}";
        $editionStr = implode(',', $editions);
        $cacheKey = "ayah_editions_{$reference}_{$editionStr}";
        return $this->cachedRequest($cacheKey, "/ayah/{$reference}/editions/{$editionStr}", 86400);
    }

    /**
     * Get audio recitation URL for an Ayah.
     */
    public function getAyahAudio(int $surahNumber, int $ayahNumber, string $reciter = 'ar.alafasy'): ?array
    {
        $reference = "{$surahNumber}:{$ayahNumber}";
        $cacheKey = "ayah_audio_{$reference}_{$reciter}";
        return $this->cachedRequest($cacheKey, "/ayah/{$reference}/{$reciter}", 86400);
    }

    /**
     * Get full Surah audio edition.
     */
    public function getSurahAudio(int $surahNumber, string $reciter = 'ar.alafasy'): ?array
    {
        $cacheKey = "surah_audio_{$surahNumber}_{$reciter}";
        return $this->cachedRequest($cacheKey, "/surah/{$surahNumber}/{$reciter}", 86400);
    }

    /**
     * Get available editions (reciters, translations).
     */
    public function getEditions(string $format = 'audio', string $language = 'ar'): ?array
    {
        $cacheKey = "editions_{$format}_{$language}";
        return $this->cachedRequest($cacheKey, "/edition?format={$format}&language={$language}", 86400);
    }

    /**
     * Get word-level data with verified word audio for an Ayah.
     */
    public function getAyahWords(int $surahNumber, int $ayahNumber): array
    {
        $cacheKey = "ayah_words_{$surahNumber}_{$ayahNumber}";
        return Cache::remember($cacheKey, 86400, function () use ($surahNumber, $ayahNumber) {
            try {
                $response = Http::timeout(6)->get("https://api.quran.com/api/v4/verses/by_key/{$surahNumber}:{$ayahNumber}", [
                    'words' => 'true',
                    'word_fields' => 'text_uthmani,audio_url',
                ]);

                if ($response->successful()) {
                    $json = $response->json();
                    $rawWords = $json['verse']['words'] ?? [];
                    $words = [];
                    foreach ($rawWords as $w) {
                        if (($w['char_type_name'] ?? '') === 'end') {
                            continue;
                        }
                        $audio = !empty($w['audio_url']) ? 'https://audio.qurancdn.com/' . ltrim($w['audio_url'], '/') : null;
                        $words[] = [
                            'position' => $w['position'] ?? (count($words) + 1),
                            'text_uthmani' => $w['text_uthmani'] ?? ($w['text'] ?? ''),
                            'audio_url' => $audio,
                            'translation' => $w['translation']['text'] ?? '',
                            'transliteration' => $w['transliteration']['text'] ?? '',
                        ];
                    }
                    if (!empty($words)) {
                        return $words;
                    }
                }
            } catch (\Exception $e) {
                // Fallback to text parsing
            }

            // Fallback: tokenize verse text from AlQuran Cloud
            $ayah = $this->getAyah($surahNumber, $ayahNumber);
            $text = $ayah['text'] ?? '';
            $rawTokens = array_filter(explode(' ', trim($text)));
            $fallback = [];
            $pos = 1;
            foreach ($rawTokens as $tok) {
                $fallback[] = [
                    'position' => $pos++,
                    'text_uthmani' => $tok,
                    'audio_url' => null,
                    'translation' => '',
                    'transliteration' => '',
                ];
            }
            return $fallback;
        });
    }

    /**
     * Search the Quran.
     */
    public function search(string $keyword, string $edition = 'en.asad'): ?array
    {
        $cacheKey = "search_" . md5("{$keyword}_{$edition}");
        return $this->cachedRequest($cacheKey, "/search/{$keyword}/all/{$edition}", 3600);
    }

    /**
     * Make a cached API request.
     */
    protected function cachedRequest(string $cacheKey, string $endpoint, int $ttl): ?array
    {
        return Cache::remember($cacheKey, $ttl, function () use ($endpoint) {
            return $this->makeRequest($endpoint);
        });
    }

    /**
     * Make an HTTP request to the Quran API.
     */
    protected function makeRequest(string $endpoint): ?array
    {
        $startTime = microtime(true);
        $url = $this->baseUrl . $endpoint;

        try {
            $response = Http::timeout(45)
                ->retry(2, 500)
                ->get($url);

            $responseTime = (int) ((microtime(true) - $startTime) * 1000);

            // Log the API call
            ApiUsageLog::create([
                'user_id' => auth()->id(),
                'service' => 'alquran_cloud',
                'endpoint' => $endpoint,
                'response_code' => $response->status(),
                'response_time_ms' => $responseTime,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['code']) && $data['code'] === 200) {
                    return $data['data'];
                }
            }

            return null;
        } catch (\Exception $e) {
            $responseTime = (int) ((microtime(true) - $startTime) * 1000);

            ApiUsageLog::create([
                'user_id' => auth()->id(),
                'service' => 'alquran_cloud',
                'endpoint' => $endpoint,
                'response_code' => 0,
                'response_time_ms' => $responseTime,
                'error_message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Check if the API is reachable.
     */
    public function healthCheck(): bool
    {
        try {
            $response = Http::timeout(5)->get($this->baseUrl . '/surah');
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}
