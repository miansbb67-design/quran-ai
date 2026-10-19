<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\SitePage;
use App\Models\Announcement;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContentController extends Controller
{
    /**
     * Display all content management items.
     */
    public function index()
    {
        $faqs = Faq::orderBy('sort_order')->orderBy('id')->get();
        $pages = SitePage::all();
        $announcements = Announcement::latest()->get();

        return view('admin.content.index', compact('faqs', 'pages', 'announcements'));
    }

    /* ── FAQs ────────────────────────────────────────── */

    public function storeFaq(Request $request)
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $faq = Faq::create($validated);

        AdminActivityLog::logActivity(
            $request->user()->id,
            'create_faq',
            'Faq',
            $faq->id,
            ['question' => $validated['question']]
        );

        return back()->with('success', 'FAQ created successfully.');
    }

    public function updateFaq(Request $request, Faq $faq)
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->has('is_active');
        $faq->update($validated);

        return back()->with('success', 'FAQ updated successfully.');
    }

    public function destroyFaq(Request $request, Faq $faq)
    {
        $faq->delete();
        return back()->with('success', 'FAQ deleted successfully.');
    }

    /* ── Site Pages ──────────────────────────────────── */

    public function updatePage(Request $request, SitePage $page)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->has('is_active');
        $page->update($validated);

        AdminActivityLog::logActivity(
            $request->user()->id,
            'update_page',
            'SitePage',
            $page->id,
            ['slug' => $page->slug]
        );

        return back()->with('success', "Page '{$page->title}' updated successfully.");
    }

    /* ── Announcements ───────────────────────────────── */

    public function storeAnnouncement(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'type' => ['required', 'in:info,warning,success'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->has('is_active');

        $announcement = Announcement::create($validated);

        AdminActivityLog::logActivity(
            $request->user()->id,
            'create_announcement',
            'Announcement',
            $announcement->id,
            ['title' => $validated['title']]
        );

        return back()->with('success', 'Announcement published successfully.');
    }

    public function updateAnnouncement(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'type' => ['required', 'in:info,warning,success,danger'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->has('is_active');
        $announcement->update($validated);

        return back()->with('success', 'Announcement updated successfully.');
    }

    public function destroyAnnouncement(Request $request, Announcement $announcement)
    {
        $announcement->delete();
        return back()->with('success', 'Announcement removed successfully.');
    }
}
