<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Hadith;
use App\Services\HadithService;
use Illuminate\Http\Request;

class HadithController extends Controller
{
    protected HadithService $hadithService;

    public function __construct(HadithService $hadithService)
    {
        $this->hadithService = $hadithService;
    }

    /**
     * Display the Hadith collection page with daily featured hadith and search/filter.
     */
    public function index(Request $request)
    {
        $dailyHadith = $this->hadithService->getDailyHadith();
        $categories = HadithService::getCategories();

        $selectedCategory = $request->query('category', 'all');
        $search = trim($request->query('search', ''));

        $query = Hadith::query();

        if ($selectedCategory !== 'all' && array_key_exists($selectedCategory, $categories)) {
            $query->where('category', $selectedCategory);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('arabic_text', 'LIKE', "%{$search}%")
                  ->orWhere('english_translation', 'LIKE', "%{$search}%")
                  ->orWhere('urdu_translation', 'LIKE', "%{$search}%")
                  ->orWhere('narrator', 'LIKE', "%{$search}%")
                  ->orWhere('source_book', 'LIKE', "%{$search}%")
                  ->orWhere('chapter', 'LIKE', "%{$search}%");
            });
        }

        $hadiths = $query->orderBy('id')->paginate(12)->withQueryString();
        $totalCount = Hadith::count();

        return view('customer.hadith.index', compact(
            'dailyHadith',
            'hadiths',
            'categories',
            'selectedCategory',
            'search',
            'totalCount'
        ));
    }

    /**
     * Display a specific Hadith detail.
     */
    public function show(int $id)
    {
        $hadith = Hadith::findOrFail($id);
        $relatedHadiths = Hadith::where('category', $hadith->category)
            ->where('id', '!=', $hadith->id)
            ->take(3)
            ->get();

        return view('customer.hadith.show', compact('hadith', 'relatedHadiths'));
    }
}
