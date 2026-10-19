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
        if ($surahNumber < 1 || $surahNumber > 114) {
            abort(404, 'Surah not found.');
        }

        $user = $request->user();
        $prefs = UserPreference::firstOrCreate(
            ['user_id' => $user->id],
            ['preferred_reciter' => 'ar.alafasy', 'preferred_translation' => 'en.asad']
        );

        // Get Arabic text
        $surah = $this->quranApi->getSurah($surahNumber);

        // Get translation
        $translation = null;
        if ($prefs->show_translation) {
            $translation = $this->quranApi->getSurahWithTranslation($surahNumber, $prefs->preferred_translation);
        }

        // Get audio edition
        $audio = $this->quranApi->getSurahAudio($surahNumber, $prefs->preferred_reciter);

        if (!$surah) {
            return back()->with('error', 'Unable to load Surah data. Please try again.');
        }

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
        $progress->last_surah_number = $surahNumber;
        $progress->updateStreak();

        return view('customer.quran.show', compact('surah', 'translation', 'audio', 'surahs', 'bookmarks', 'prefs', 'surahNumber'));
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
}
