<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\SiteSetting;
use App\Models\Announcement;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $faqs = Faq::active()->get();
        $announcements = Announcement::active()->latest()->take(3)->get();

        return view('landing.index', compact('faqs', 'announcements'));
    }

    public function page(string $slug)
    {
        $page = \App\Models\SitePage::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return view('landing.page', compact('page'));
    }
}
