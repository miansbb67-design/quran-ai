<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Quran\QuranApiService;
use App\Models\Bookmark;
use App\Models\ReadingProgress;
use App\Models\UserPreference;
use Illuminate\Http\Request;

class QuranController extends Controller
{
    protected QuranApiService $quranApi;

    public function __construct(QuranApiService $quranApi)
    {
        $this->quranApi = $quranApi;
    }

    /**
     * Show the Quran reader — Surah list.
     */
    public function index(Request $request)
    {
        $surahs = $this->quranApi->getSurahs();

        if (!$surahs) {
            return view('customer.quran.index', ['surahs' => [], 'error' => 'Unable to load Quran data. Please try again later.']);
        }

        $readingProgress = ReadingProgress::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['last_surah_number' => 1, 'last_ayah_number' => 1]
        );

        return view('customer.quran.index', compact('surahs', 'readingProgress'));
    }

    /**
     * Show a specific Surah for reading.
     */
    public function show(Request $request, int $surahNumber)
    {
        @set_time_limit(180);

        if ($surahNumber < 1 || $surahNumber > 114) {
            abort(404, 'Surah not found.');
        }

        $user = $request->user();
        $prefs = UserPreference::firstOrCreate(
            ['user_id' => $user->id],
            ['preferred_reciter' => 'ar.alafasy', 'preferred_translation' => 'en.asad']
        );

        $translationEdition = $prefs->preferred_translation ?: 'en.asad';
        $defaultReciter = \App\Models\SiteSetting::get('default_reciter', 'ar.alafasy');
        $allowStudentChange = \App\Models\SiteSetting::get('allow_student_reciter_change', '1') == '1';
        $reciterEdition = ($allowStudentChange && !empty($prefs->preferred_reciter))
            ? $prefs->preferred_reciter
            : $defaultReciter;

        // Load Arabic text and translation in a single combined API call to prevent timeout on large surahs
        $multiEditions = $this->quranApi->getSurahWithEditions($surahNumber, ['quran-uthmani', $translationEdition]);

        if (is_array($multiEditions) && count($multiEditions) >= 2) {
            $surah = $multiEditions[0];
            $translation = $prefs->show_translation ? $multiEditions[1] : null;
        } else {
            // Fallback to individual cached requests if multi-edition endpoint is unreachable
            $surah = $this->quranApi->getSurah($surahNumber);
            $translation = $prefs->show_translation ? $this->quranApi->getSurahWithTranslation($surahNumber, $translationEdition) : null;
        }

        if (!$surah) {
            return back()->with('error', 'Unable to load Surah data. Please try again.');
        }

        // Build audio data directly using high-speed Islamic Network CDN URLs (0 network delay)
        $audioAyahs = [];
        if (isset($surah['ayahs']) && is_array($surah['ayahs'])) {
            foreach ($surah['ayahs'] as $a) {
                $audioAyahs[] = [
                    'number' => $a['number'],
                    'audio' => 'https://cdn.islamic.network/quran/audio/128/' . $reciterEdition . '/' . $a['number'] . '.mp3',
                ];
            }
        }
        $audio = [
            'number' => $surahNumber,
            'name' => $surah['name'] ?? '',
            'ayahs' => $audioAyahs,
        ];

        // Get surahs for navigation
        $surahs = $this->quranApi->getSurahs();

        // Get user bookmarks for this surah
        $bookmarks = Bookmark::where('user_id', $user->id)
            ->where('surah_number', $surahNumber)
            ->pluck('ayah_number')
            ->toArray();

        // Update reading progress
        $progress = ReadingProgress::firstOrCreate(
            ['user_id' => $user->id],
            ['last_surah_number' => $surahNumber, 'last_ayah_number' => 1]
        );
        $savedAyahNumber = ($progress->last_surah_number == $surahNumber && $progress->last_ayah_number > 0)
            ? (int) $progress->last_ayah_number
            : 1;

        $progress->last_surah_number = $surahNumber;
        $progress->updateStreak();

        return view('customer.quran.show', compact('surah', 'translation', 'audio', 'surahs', 'bookmarks', 'prefs', 'surahNumber', 'savedAyahNumber'));
    }

    /**
     * Get Ayah data via AJAX.
     */
    public function getAyah(Request $request, int $surahNumber, int $ayahNumber)
    {
        $prefs = UserPreference::firstOrCreate(
            ['user_id' => $request->user()->id]
        );

        $editions = $this->quranApi->getAyahEditions(
            $surahNumber,
            $ayahNumber,
            ['quran-uthmani', $prefs->preferred_translation, $prefs->preferred_reciter]
        );

        if (!$editions) {
            return response()->json(['error' => 'Unable to load Ayah data.'], 500);
        }

        return response()->json(['data' => $editions]);
    }

    /**
     * Toggle bookmark for an Ayah.
     */
    public function toggleBookmark(Request $request)
    {
        $request->validate([
            'surah_number' => 'required|integer|min:1|max:114',
            'ayah_number' => 'required|integer|min:1',
            'note' => 'nullable|string|max:255',
        ]);

        $bookmark = Bookmark::where('user_id', $request->user()->id)
            ->where('surah_number', $request->surah_number)
            ->where('ayah_number', $request->ayah_number)
            ->first();

        if ($bookmark) {
            $bookmark->delete();
            return response()->json(['bookmarked' => false, 'message' => 'Bookmark removed.']);
        }

        Bookmark::create([
            'user_id' => $request->user()->id,
            'surah_number' => $request->surah_number,
            'ayah_number' => $request->ayah_number,
            'note' => $request->note,
        ]);

        return response()->json(['bookmarked' => true, 'message' => 'Ayah bookmarked!']);
    }

    /**
     * Update reading progress via AJAX.
     */
    public function updateProgress(Request $request)
    {
        $request->validate([
            'surah_number' => 'required|integer|min:1|max:114',
            'ayah_number' => 'required|integer|min:1',
        ]);

        $progress = ReadingProgress::firstOrCreate(
            ['user_id' => $request->user()->id]
        );

        $progress->last_surah_number = $request->surah_number;
        $progress->last_ayah_number = $request->ayah_number;
        $progress->total_ayahs_read++;
        $progress->updateStreak();

        return response()->json(['success' => true]);
    }

    /**
     * Show bookmarks page.
     */
    public function bookmarks(Request $request)
    {
        $bookmarks = Bookmark::where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('customer.quran.bookmarks', compact('bookmarks'));
    }

    /**
     * Update user reading & playback preferences via AJAX.
     */
    public function updatePreferences(Request $request)
    {
        $prefs = UserPreference::firstOrCreate(['user_id' => $request->user()->id]);

        if ($request->has('auto_play_next')) {
            $prefs->auto_play_next = (bool) $request->auto_play_next;
        }

        $prefs->save();

        return response()->json([
            'success' => true,
            'auto_play_next' => (bool) $prefs->auto_play_next
        ]);
    }
}
