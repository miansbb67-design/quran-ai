@php
    $seoTitle = $seoTitle ?? \App\Models\SiteSetting::getValue('seo_meta_title', 'Bayan AI Quran Teacher - Recite, Memorize & Learn Tajweed');
    $seoDescription = $seoDescription ?? \App\Models\SiteSetting::getValue('seo_meta_description', 'Interactive AI-powered Quran recitation analysis, instant tajweed error detection, authentic Sahih Hadith library, and Hifz memorization tracker.');
    $seoKeywords = $seoKeywords ?? \App\Models\SiteSetting::getValue('seo_meta_keywords', 'quran ai, learn quran, tajweed voice, holy quran, surah recitation, hifz tracker, sahih hadith');
    $seoCanonical = $seoCanonical ?? \App\Models\SiteSetting::getValue('seo_canonical_url', url()->current());
    $seoAuthor = \App\Models\SiteSetting::getValue('seo_author', 'Bayan Islamic AI Academy');
    $seoRobots = \App\Models\SiteSetting::getValue('seo_robots_directive', 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1');

    // Social Graph
    $seoOgTitle = $seoOgTitle ?? \App\Models\SiteSetting::getValue('seo_og_title', $seoTitle);
    $seoOgDesc = $seoOgDesc ?? \App\Models\SiteSetting::getValue('seo_og_description', $seoDescription);
    $seoOgImage = $seoOgImage ?? \App\Models\SiteSetting::getValue('seo_og_image', url('/images/quran-ai-og-banner.jpg'));
    $seoOgType = $seoOgType ?? \App\Models\SiteSetting::getValue('seo_og_type', 'website');

    // Twitter Card
    $seoTwitterCard = \App\Models\SiteSetting::getValue('seo_twitter_card', 'summary_large_image');
    $seoTwitterHandle = \App\Models\SiteSetting::getValue('seo_twitter_handle', '@QuranAIOfficial');

    // Verification Codes
    $googleVerification = \App\Models\SiteSetting::getValue('google_site_verification', '');
    $bingVerification = \App\Models\SiteSetting::getValue('bing_site_verification', '');
    $yandexVerification = \App\Models\SiteSetting::getValue('yandex_site_verification', '');

    // Schema Type
    $schemaType = \App\Models\SiteSetting::getValue('seo_schema_type', 'EducationalOrganization');
    $enableSearchbox = \App\Models\SiteSetting::getValue('seo_enable_sitelinks_searchbox', true);
@endphp

<!-- Primary SEO Meta Directives -->
<meta name="title" content="{{ $seoTitle }}">
<meta name="description" content="{{ $seoDescription }}">
@if(!empty($seoKeywords))
<meta name="keywords" content="{{ $seoKeywords }}">
@endif
<meta name="author" content="{{ $seoAuthor }}">
<meta name="robots" content="{{ $seoRobots }}">
<link rel="canonical" href="{{ $seoCanonical }}">

<!-- Search Engine Webmaster Verification -->
@if(!empty($googleVerification))
<meta name="google-site-verification" content="{{ $googleVerification }}">
@endif
@if(!empty($bingVerification))
<meta name="msvalidate.01" content="{{ $bingVerification }}">
@endif
@if(!empty($yandexVerification))
<meta name="yandex-verification" content="{{ $yandexVerification }}">
@endif

<!-- Open Graph / Facebook / WhatsApp -->
<meta property="og:type" content="{{ $seoOgType }}">
<meta property="og:url" content="{{ $seoCanonical }}">
<meta property="og:site_name" content="Bayan AI Quran">
<meta property="og:title" content="{{ $seoOgTitle }}">
<meta property="og:description" content="{{ $seoOgDesc }}">
@if(!empty($seoOgImage))
<meta property="og:image" content="{{ $seoOgImage }}">
<meta property="og:image:alt" content="{{ $seoOgTitle }}">
@endif

<!-- Twitter Cards -->
<meta name="twitter:card" content="{{ $seoTwitterCard }}">
<meta name="twitter:url" content="{{ $seoCanonical }}">
<meta name="twitter:title" content="{{ $seoOgTitle }}">
<meta name="twitter:description" content="{{ $seoOgDesc }}">
@if(!empty($seoTwitterHandle))
<meta name="twitter:site" content="{{ $seoTwitterHandle }}">
<meta name="twitter:creator" content="{{ $seoTwitterHandle }}">
@endif
@if(!empty($seoOgImage))
<meta name="twitter:image" content="{{ $seoOgImage }}">
@endif

<!-- Structured Data (JSON-LD Schema.org) -->
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "{{ $schemaType }}",
  "name": "Bayan AI Quran Academy",
  "url": "{{ url('/') }}",
  "description": "{{ $seoDescription }}",
  "potentialAction": {
    "@@type": "SearchAction",
    "target": "{{ url('/quran') }}?search={search_term_string}",
    "query-input": "required name=search_term_string"
  }
}
</script>
