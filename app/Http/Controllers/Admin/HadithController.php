<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hadith;
use Illuminate\Http\Request;

class HadithController extends Controller
{
    /**
     * Display a listing of hadiths with filters and search.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $category = $request->query('category');

        $query = Hadith::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('arabic_text', 'like', "%{$search}%")
                  ->orWhere('english_translation', 'like', "%{$search}%")
                  ->orWhere('urdu_translation', 'like', "%{$search}%")
                  ->orWhere('source_book', 'like', "%{$search}%")
                  ->orWhere('narrator', 'like', "%{$search}%");
            });
        }

        if (!empty($category) && $category !== 'all') {
            $query->where('category', $category);
        }

        $hadiths = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();
        $categories = Hadith::select('category')->distinct()->pluck('category');

        $stats = [
            'total' => Hadith::count(),
            'categories' => $categories->count(),
            'books' => Hadith::select('source_book')->distinct()->count(),
        ];

        return view('admin.hadiths.index', compact('hadiths', 'categories', 'stats', 'search', 'category'));
    }

    /**
     * Store a newly created hadith.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'book' => ['required', 'string', 'max:150'],
            'hadith_number' => ['nullable', 'string', 'max:50'],
            'chapter' => ['nullable', 'string', 'max:255'],
            'narrator' => ['nullable', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'arabic' => ['required', 'string'],
            'english' => ['required', 'string'],
            'urdu' => ['required', 'string'],
        ]);

        Hadith::create([
            'source_book' => $validated['book'],
            'chapter' => $validated['chapter'] ?? null,
            'narrator' => $validated['narrator'] ?? null,
            'category' => $validated['category'],
            'arabic_text' => $validated['arabic'],
            'english_translation' => $validated['english'],
            'urdu_translation' => $validated['urdu'],
            'grade' => 'Sahih',
            'is_featured' => false,
        ]);

        return back()->with('success', 'New Hadith successfully added to database.');
    }

    /**
     * Update an existing hadith.
     */
    public function update(Request $request, Hadith $hadith)
    {
        $validated = $request->validate([
            'book' => ['required', 'string', 'max:150'],
            'hadith_number' => ['nullable', 'string', 'max:50'],
            'chapter' => ['nullable', 'string', 'max:255'],
            'narrator' => ['nullable', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'arabic' => ['required', 'string'],
            'english' => ['required', 'string'],
            'urdu' => ['required', 'string'],
        ]);

        $hadith->update([
            'source_book' => $validated['book'],
            'chapter' => $validated['chapter'] ?? null,
            'narrator' => $validated['narrator'] ?? null,
            'category' => $validated['category'],
            'arabic_text' => $validated['arabic'],
            'english_translation' => $validated['english'],
            'urdu_translation' => $validated['urdu'],
        ]);

        return back()->with('success', 'Hadith updated successfully.');
    }

    /**
     * Remove the specified hadith.
     */
    public function destroy(Hadith $hadith)
    {
        $hadith->delete();

        return back()->with('success', 'Hadith removed successfully.');
    }
}
