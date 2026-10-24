@extends('layouts.admin')

@section('title', 'SEO & Search Engine Intelligence - Bayan Admin')
@section('page_title', 'SEO & Search Engine Console')

@section('content')
<div class="container-fluid p-0">

    <!-- Top Executive Header Banner -->
    <div class="card card-glass border-0 p-4 mb-4 shadow-sm rounded-4">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="d-inline-flex align-items-center justify-content-center bg-gold text-dark rounded-4 p-3 shadow-sm" style="width: 54px; height: 54px;">
                    <i class="bi bi-search-heart fs-3"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <h5 class="fw-bold text-dark mb-0">Search Engine Optimization & Indexing Hub</h5>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">
                            <i class="bi bi-lightning-charge-fill me-1"></i> Live SEO Active
                        </span>
                    </div>
                    <p class="text-muted small mb-0">
                        Manage Google & Bing meta tags, social share previews, XML sitemaps, robots.txt directives, and Schema.org structured data.
                    </p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ $sitemapUrl }}" target="_blank" class="btn btn-outline-emerald rounded-pill px-3 shadow-2xs" title="View live XML sitemap">
                    <i class="bi bi-diagram-3-fill text-emerald me-1"></i> Sitemap ({{ $estimatedSitemapUrls }} URLs)
                </a>
                <a href="{{ $robotsUrl }}" target="_blank" class="btn btn-outline-secondary rounded-pill px-3 shadow-2xs" title="View robots.txt">
                    <i class="bi bi-robot me-1"></i> robots.txt
                </a>
                <form method="POST" action="{{ route('admin.seo.ping-sitemap') }}" class="d-inline mb-0">
                    @csrf
                    <button type="submit" class="btn btn-emerald rounded-pill px-3 shadow-sm" title="Notify Google & Bing of sitemap updates">
                        <i class="bi bi-broadcast me-1"></i> Ping Search Engines
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- SEO Health Score & SERP Live Preview Row -->
    <div class="row g-4 mb-4">
        <!-- SEO Health Audit Card -->
        <div class="col-lg-5">
            <div class="card card-glass border-0 p-4 shadow-sm rounded-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-speedometer text-emerald me-2"></i>SEO Readiness Score</h5>
                    <span class="badge {{ $score >= 80 ? 'bg-success' : ($score >= 60 ? 'bg-warning text-dark' : 'bg-danger') }} px-3 py-2 rounded-pill fs-6">
                        {{ $score }}/100
                    </span>
                </div>

                <div class="progress mb-3" style="height: 10px;">
                    <div class="progress-bar {{ $score >= 80 ? 'bg-success' : ($score >= 60 ? 'bg-warning' : 'bg-danger') }}" style="width: {{ $score }}%;"></div>
                </div>

                <div class="list-group list-group-flush small">
                    @foreach($auditChecks as $check)
                        <div class="list-group-item px-0 py-2 d-flex align-items-center justify-content-between border-0">
                            <div class="d-flex align-items-center gap-2">
                                @if($check['status'] === 'pass')
                                    <i class="bi bi-check-circle-fill text-success fs-6"></i>
                                @elseif($check['status'] === 'warning')
                                    <i class="bi bi-exclamation-triangle-fill text-warning fs-6"></i>
                                @elseif($check['status'] === 'fail')
                                    <i class="bi bi-x-circle-fill text-danger fs-6"></i>
                                @else
                                    <i class="bi bi-info-circle-fill text-info fs-6"></i>
                                @endif
                                <span class="fw-semibold text-dark">{{ $check['name'] }}</span>
                            </div>
                            <span class="text-muted text-end">{{ $check['text'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Google SERP Live Snippet Simulator -->
        <div class="col-lg-7">
            <div class="card card-glass border-0 p-4 shadow-sm rounded-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="bi bi-google text-danger me-2"></i>Google SERP Snippet Preview
                    </h5>
                    <span class="badge bg-light text-muted border">Live Preview</span>
                </div>

                <div class="p-3 bg-white rounded-3 border shadow-2xs mb-3">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <div class="rounded-circle bg-emerald text-white d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 0.75rem;">
                            <i class="bi bi-book"></i>
                        </div>
                        <div>
                            <div class="text-dark small fw-semibold lh-1">Bayan AI Quran</div>
                            <small class="text-muted" style="font-size: 0.72rem;" id="serpUrlPreview">{{ $seoSettings['seo_canonical_url'] }}</small>
                        </div>
                    </div>
                    <h5 class="mb-1 text-primary cursor-pointer fw-medium" id="serpTitlePreview" style="font-size: 1.15rem; color: #1a0dab !important; line-height: 1.3;">
                        {{ $seoSettings['seo_meta_title'] }}
                    </h5>
                    <p class="mb-0 text-secondary small" id="serpDescPreview" style="font-size: 0.85rem; color: #4d5156 !important; line-height: 1.4;">
                        {{ $seoSettings['seo_meta_description'] }}
                    </p>
                </div>

                <div class="row g-2 text-muted small border-top pt-3 mt-auto">
                    <div class="col-6">
                        <span>Title Length: </span>
                        <strong id="serpTitleCount" class="text-dark">{{ Str::length($seoSettings['seo_meta_title']) }}</strong>/60 chars
                    </div>
                    <div class="col-6 text-end">
                        <span>Description Length: </span>
                        <strong id="serpDescCount" class="text-dark">{{ Str::length($seoSettings['seo_meta_description']) }}</strong>/160 chars
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main SEO Settings Tabs & Form -->
    <div class="card card-glass border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom p-0 rounded-top-4">
            <ul class="nav nav-tabs border-0 px-3 pt-2" id="seoTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-semibold text-emerald" id="general-tab" data-bs-toggle="tab" data-bs-target="#generalTab" type="button" role="tab">
                        <i class="bi bi-card-heading me-1"></i> Core Metadata
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold text-dark" id="social-tab" data-bs-toggle="tab" data-bs-target="#socialTab" type="button" role="tab">
                        <i class="bi bi-share me-1"></i> Social & Open Graph
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold text-dark" id="webmaster-tab" data-bs-toggle="tab" data-bs-target="#webmasterTab" type="button" role="tab">
                        <i class="bi bi-shield-check me-1"></i> Search Console & Verification
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold text-dark" id="robots-tab" data-bs-toggle="tab" data-bs-target="#robotsTab" type="button" role="tab">
                        <i class="bi bi-robot me-1"></i> robots.txt Editor
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold text-dark" id="sitemap-tab" data-bs-toggle="tab" data-bs-target="#sitemapTab" type="button" role="tab">
                        <i class="bi bi-diagram-3 me-1"></i> Sitemap & Schema
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.seo.update') }}" id="seoForm">
                @csrf

                <div class="tab-content" id="seoTabsContent">
                    <!-- Tab 1: Core Metadata -->
                    <div class="tab-pane fade show active" id="generalTab" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-bold text-dark">
                                    Meta Title <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="seo_meta_title" id="inputMetaTitle" class="form-control" value="{{ $seoSettings['seo_meta_title'] }}" required maxlength="120">
                                <div class="d-flex justify-content-between mt-1">
                                    <small class="text-muted">Recommended: 50-60 characters for optimal display in Google SERPs.</small>
                                    <small class="text-muted"><span id="titleCounter">0</span> / 60</small>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold text-dark">
                                    Meta Description <span class="text-danger">*</span>
                                </label>
                                <textarea name="seo_meta_description" id="inputMetaDesc" class="form-control" rows="3" required maxlength="350">{{ $seoSettings['seo_meta_description'] }}</textarea>
                                <div class="d-flex justify-content-between mt-1">
                                    <small class="text-muted">Recommended: 130-160 characters. Provide a compelling call-to-action.</small>
                                    <small class="text-muted"><span id="descCounter">0</span> / 160</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark">Meta Keywords</label>
                                <input type="text" name="seo_meta_keywords" class="form-control" value="{{ $seoSettings['seo_meta_keywords'] }}" placeholder="Comma separated, e.g. quran, tajweed, ai teacher">
                                <small class="text-muted">Keywords are used by secondary search engines and content categorizers.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark">Canonical Base URL</label>
                                <input type="url" name="seo_canonical_url" id="inputCanonicalUrl" class="form-control" value="{{ $seoSettings['seo_canonical_url'] }}" placeholder="https://xyz.careernexaa.com">
                                <small class="text-muted">Prevents duplicate content penalties if accessible via multiple hostnames.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark">Author / Publisher Tag</label>
                                <input type="text" name="seo_author" class="form-control" value="{{ $seoSettings['seo_author'] }}" placeholder="Bayan Islamic AI Academy">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark">Robots Directives Header</label>
                                <input type="text" name="seo_robots_directive" class="form-control" value="{{ $seoSettings['seo_robots_directive'] }}" placeholder="index, follow, max-snippet:-1">
                                <small class="text-muted">Default: <code>index, follow, max-snippet:-1, max-image-preview:large</code></small>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Social & Open Graph -->
                    <div class="tab-pane fade" id="socialTab" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="alert alert-info py-2 small mb-3 rounded-3 border-0 bg-info bg-opacity-10 text-dark">
                                    <i class="bi bi-info-circle me-1 text-info"></i> Open Graph & Twitter Card tags control how your platform links look when shared on WhatsApp, Facebook, LinkedIn, X (Twitter), and Telegram.
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-dark">Open Graph Title</label>
                                    <input type="text" name="seo_og_title" class="form-control" value="{{ $seoSettings['seo_og_title'] }}" placeholder="Leave blank to use meta title">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-dark">Open Graph Description</label>
                                    <textarea name="seo_og_description" class="form-control" rows="2" placeholder="Leave blank to use meta description">{{ $seoSettings['seo_og_description'] }}</textarea>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-dark">OG Content Type</label>
                                        <select name="seo_og_type" class="form-select">
                                            <option value="website" {{ $seoSettings['seo_og_type'] === 'website' ? 'selected' : '' }}>website</option>
                                            <option value="article" {{ $seoSettings['seo_og_type'] === 'article' ? 'selected' : '' }}>article</option>
                                            <option value="book" {{ $seoSettings['seo_og_type'] === 'book' ? 'selected' : '' }}>book (Islamic Content)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-dark">Twitter Card Format</label>
                                        <select name="seo_twitter_card" class="form-select">
                                            <option value="summary_large_image" {{ $seoSettings['seo_twitter_card'] === 'summary_large_image' ? 'selected' : '' }}>summary_large_image (Recommended)</option>
                                            <option value="summary" {{ $seoSettings['seo_twitter_card'] === 'summary' ? 'selected' : '' }}>summary (Small thumbnail)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <label class="form-label small fw-bold text-dark">Social Share Image URL (OG Image)</label>
                                    <input type="text" name="seo_og_image" class="form-control" value="{{ $seoSettings['seo_og_image'] }}" placeholder="https://.../images/og-share-banner.jpg">
                                    <small class="text-muted">Recommended size: 1200 x 630 pixels (JPG/PNG).</small>
                                </div>

                                <div class="mt-3">
                                    <label class="form-label small fw-bold text-dark">Twitter/X Profile Handle</label>
                                    <input type="text" name="seo_twitter_handle" class="form-control" value="{{ $seoSettings['seo_twitter_handle'] }}" placeholder="@QuranAIOfficial">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-dark mb-2">WhatsApp / Social Preview</label>
                                <div class="card rounded-3 border p-0 shadow-2xs overflow-hidden">
                                    <div class="bg-emerald d-flex align-items-center justify-content-center text-white p-4" style="min-height: 120px;">
                                        <div class="text-center">
                                            <i class="bi bi-book-half fs-1 text-gold"></i>
                                            <div class="fw-bold small mt-1">BAYAN AI QURAN</div>
                                        </div>
                                    </div>
                                    <div class="p-3 bg-light">
                                        <small class="text-muted d-block text-uppercase" style="font-size: 0.68rem;">{{ parse_url(url('/'), PHP_URL_HOST) }}</small>
                                        <strong class="text-dark d-block small mt-1 text-truncate">{{ $seoSettings['seo_og_title'] }}</strong>
                                        <small class="text-muted d-block mt-1 text-truncate">{{ $seoSettings['seo_og_description'] }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 3: Search Console & Verification -->
                    <div class="tab-pane fade" id="webmasterTab" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="alert alert-warning py-2 small mb-3 rounded-3 border-0 bg-warning bg-opacity-10 text-dark">
                                    <i class="bi bi-shield-lock me-1 text-warning"></i> Add your verification token here to verify site ownership in Google Search Console, Bing Webmaster Tools, and Yandex.
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 border h-100">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="bi bi-google fs-4 text-danger"></i>
                                        <label class="form-label small fw-bold text-dark mb-0">Google Search Console Verification</label>
                                    </div>
                                    <input type="text" name="google_site_verification" class="form-control" value="{{ $seoSettings['google_site_verification'] }}" placeholder="e.g. google-site-verification token or code">
                                    <small class="text-muted d-block mt-2">
                                        Found in <strong>Search Console -> Settings -> Ownership Verification -> HTML tag</strong>.
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 border h-100">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="bi bi-microsoft fs-4 text-primary"></i>
                                        <label class="form-label small fw-bold text-dark mb-0">Bing Webmaster Tools Verification</label>
                                    </div>
                                    <input type="text" name="bing_site_verification" class="form-control" value="{{ $seoSettings['bing_site_verification'] }}" placeholder="e.g. msvalidate.01 token">
                                    <small class="text-muted d-block mt-2">
                                        Found in <strong>Bing Webmaster Tools -> Profile -> Ownership Verification</strong>.
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 border">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="bi bi-globe fs-4 text-secondary"></i>
                                        <label class="form-label small fw-bold text-dark mb-0">Yandex Webmaster Verification</label>
                                    </div>
                                    <input type="text" name="yandex_site_verification" class="form-control" value="{{ $seoSettings['yandex_site_verification'] }}" placeholder="e.g. yandex-verification code">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 5: Sitemap & Schema.org -->
                    <div class="tab-pane fade" id="sitemapTab" role="tabpanel">
                        <div class="row g-4">
                            <!-- Sitemap Overview -->
                            <div class="col-lg-6">
                                <div class="p-4 bg-light rounded-4 border h-100">
                                    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                                        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-diagram-3-fill text-emerald me-2"></i>Dynamic XML Sitemap</h6>
                                        <span class="badge bg-emerald text-white rounded-pill px-3 py-1">Auto-Generated</span>
                                    </div>
                                    <p class="text-muted small">
                                        Your XML Sitemap is automatically generated and updated in real-time according to <a href="https://www.sitemaps.org" target="_blank">sitemaps.org</a> standards.
                                    </p>

                                    <div class="list-group list-group-flush small mb-3">
                                        <div class="list-group-item bg-transparent px-0 py-2 d-flex justify-content-between">
                                            <span>Sitemap Location:</span>
                                            <a href="{{ $sitemapUrl }}" target="_blank" class="fw-bold text-emerald text-truncate" style="max-width: 250px;">{{ $sitemapUrl }}</a>
                                        </div>
                                        <div class="list-group-item bg-transparent px-0 py-2 d-flex justify-content-between">
                                            <span>Quranic Surah Pages:</span>
                                            <strong class="text-dark">{{ $totalSurahs }} Surahs</strong>
                                        </div>
                                        <div class="list-group-item bg-transparent px-0 py-2 d-flex justify-content-between">
                                            <span>Sahih Hadiths Collection:</span>
                                            <strong class="text-dark">{{ $totalHadiths }} Hadiths</strong>
                                        </div>
                                        <div class="list-group-item bg-transparent px-0 py-2 d-flex justify-content-between">
                                            <span>Total Indexable URLs:</span>
                                            <strong class="text-emerald">{{ $estimatedSitemapUrls }}+ URLs</strong>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <a href="{{ $sitemapUrl }}" target="_blank" class="btn btn-sm btn-outline-emerald rounded-pill px-3">
                                            <i class="bi bi-box-arrow-up-right me-1"></i> Open XML Sitemap
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Schema.org JSON-LD Structured Data -->
                            <div class="col-lg-6">
                                <div class="p-4 bg-light rounded-4 border h-100">
                                    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                                        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-code-square text-info me-2"></i>Schema.org JSON-LD Structured Data</h6>
                                        <span class="badge bg-info-subtle text-info rounded-pill px-2">Rich Snippets</span>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-dark">Primary Entity Type</label>
                                        <select name="seo_schema_type" class="form-select">
                                            <option value="EducationalOrganization" {{ $seoSettings['seo_schema_type'] === 'EducationalOrganization' ? 'selected' : '' }}>EducationalOrganization (Islamic Academy)</option>
                                            <option value="WebSite" {{ $seoSettings['seo_schema_type'] === 'WebSite' ? 'selected' : '' }}>WebSite</option>
                                            <option value="Organization" {{ $seoSettings['seo_schema_type'] === 'Organization' ? 'selected' : '' }}>Organization</option>
                                        </select>
                                    </div>

                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="seo_enable_sitelinks_searchbox" value="1" id="sitelinksSwitch" {{ $seoSettings['seo_enable_sitelinks_searchbox'] ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-semibold text-dark" for="sitelinksSwitch">
                                            Enable Sitelinks Searchbox Schema (Google Search Action)
                                        </label>
                                    </div>

                                    <div class="p-2 bg-dark rounded-3 text-white small font-monospace" style="font-size: 0.75rem; max-height: 140px; overflow-y: auto;">
                                        &lt;script type="application/ld+json"&gt;<br>
                                        {<br>
                                        &nbsp;&nbsp;"@context": "https://schema.org",<br>
                                        &nbsp;&nbsp;"@type": "{{ $seoSettings['seo_schema_type'] }}",<br>
                                        &nbsp;&nbsp;"name": "Bayan AI Quran Academy",<br>
                                        &nbsp;&nbsp;"url": "{{ url('/') }}"<br>
                                        }<br>
                                        &lt;/script&gt;
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Save Action Button -->
                <div class="border-top pt-4 mt-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <small class="text-muted"><i class="bi bi-clock-history me-1"></i> Changes publish instantly across all web pages and search engine crawlers.</small>
                    <button type="submit" class="btn btn-emerald rounded-pill px-5 fw-bold shadow-sm">
                        <i class="bi bi-check2-circle me-1"></i> Save & Publish SEO Settings
                    </button>
                </div>
            </form>

            <!-- Tab 4: Robots.txt Live Editor (Independent Form) -->
            <div class="tab-pane fade" id="robotsTab" role="tabpanel" style="display: none;">
                <!-- Managed via JS tab switcher -->
            </div>
        </div>
    </div>

    <!-- Robots.txt Dedicated Interactive Editor Card -->
    <div class="card card-glass border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom p-3 d-flex align-items-center justify-content-between flex-wrap gap-2 rounded-top-4">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-robot fs-4 text-emerald"></i>
                <div>
                    <h5 class="fw-bold text-dark mb-0">Interactive robots.txt File Editor</h5>
                    <small class="text-muted">Directly updates <code>public/robots.txt</code> on server</small>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" id="presetStandard">
                    Standard Preset
                </button>
                <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" id="presetBlockAi">
                    Block AI Scrapers
                </button>
                <a href="{{ $robotsUrl }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i class="bi bi-box-arrow-up-right me-1"></i> View Live robots.txt
                </a>
            </div>
        </div>

        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.seo.robots.update') }}">
                @csrf
                <div class="mb-3">
                    <textarea name="robots_content" id="robotsEditor" class="form-control font-monospace bg-light" rows="8" style="font-size: 0.9rem; line-height: 1.5;" required>{{ $robotsContent }}</textarea>
                </div>
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <small class="text-muted"><i class="bi bi-info-circle me-1"></i> Crawlers like Googlebot and Bingbot read this file every 24-48 hours.</small>
                    <button type="submit" class="btn btn-emerald rounded-pill px-4 fw-semibold shadow-sm">
                        <i class="bi bi-save me-1"></i> Save robots.txt
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Live SERP Preview Bindings
    const inputTitle = document.getElementById('inputMetaTitle');
    const inputDesc = document.getElementById('inputMetaDesc');
    const inputCanonical = document.getElementById('inputCanonicalUrl');

    const serpTitle = document.getElementById('serpTitlePreview');
    const serpDesc = document.getElementById('serpDescPreview');
    const serpUrl = document.getElementById('serpUrlPreview');

    const titleCount = document.getElementById('titleCounter');
    const descCount = document.getElementById('descCounter');
    const serpTitleCount = document.getElementById('serpTitleCount');
    const serpDescCount = document.getElementById('serpDescCount');

    function updateCounts() {
        if (inputTitle && serpTitle) {
            const val = inputTitle.value || 'Bayan AI Quran Teacher';
            serpTitle.textContent = val;
            titleCount.textContent = val.length;
            serpTitleCount.textContent = val.length;
            serpTitleCount.className = (val.length >= 30 && val.length <= 65) ? 'text-success fw-bold' : 'text-warning fw-bold';
        }

        if (inputDesc && serpDesc) {
            const val = inputDesc.value || 'AI-powered Quran recitation analysis and Hifz tracker.';
            serpDesc.textContent = val;
            descCount.textContent = val.length;
            serpDescCount.textContent = val.length;
            serpDescCount.className = (val.length >= 120 && val.length <= 170) ? 'text-success fw-bold' : 'text-warning fw-bold';
        }

        if (inputCanonical && serpUrl) {
            serpUrl.textContent = inputCanonical.value || window.location.origin;
        }
    }

    if (inputTitle) inputTitle.addEventListener('input', updateCounts);
    if (inputDesc) inputDesc.addEventListener('input', updateCounts);
    if (inputCanonical) inputCanonical.addEventListener('input', updateCounts);
    updateCounts();

    // Robots.txt Presets
    const robotsEditor = document.getElementById('robotsEditor');
    const sitemapFullUrl = "{{ $sitemapUrl }}";

    const presetStandardBtn = document.getElementById('presetStandard');
    if (presetStandardBtn && robotsEditor) {
        presetStandardBtn.addEventListener('click', function() {
            robotsEditor.value = "User-agent: *\nDisallow: /admin/\nDisallow: /profile/\n\nSitemap: " + sitemapFullUrl;
        });
    }

    const presetBlockAiBtn = document.getElementById('presetBlockAi');
    if (presetBlockAiBtn && robotsEditor) {
        presetBlockAiBtn.addEventListener('click', function() {
            robotsEditor.value = "User-agent: *\nDisallow: /admin/\nDisallow: /profile/\n\n# Block Known AI Training Scrapers\nUser-agent: GPTBot\nDisallow: /\n\nUser-agent: CCBot\nDisallow: /\n\nUser-agent: Google-Extended\nDisallow: /\n\nSitemap: " + sitemapFullUrl;
        });
    }
});
</script>
@endpush
