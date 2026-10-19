@extends('layouts.main')

@section('title', 'Bayan AI Quran Teacher - Recite, Learn & Memorize with Intelligent Voice Feedback')

@section('content')
<!-- Hero Section -->
<section class="py-5 bg-sand position-relative overflow-hidden">
    <div class="container py-lg-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-emerald-light border border-emerald-border mb-3">
                    <span class="badge bg-gold-grad text-white rounded-pill px-2 py-1"><i class="bi bi-stars"></i> Intelligent Tajweed</span>
                    <span class="small fw-semibold text-emerald">Empowering Your Daily Recitation Journey</span>
                </div>

                <h1 class="display-4 fw-extrabold text-emerald mb-3 lh-sm">
                    Master Quran Recitation with <span class="text-gold">AI Voice Feedback</span>
                </h1>

                <p class="lead text-secondary mb-4">
                    Read the Holy Quran in authentic Uthmani script, listen to celebrated reciters, practice your recitation with real-time word-by-word voice recognition, and memorize effortlessly through spaced repetition.
                </p>

                <div class="d-flex flex-wrap gap-3 mb-4">
                    <a href="{{ route('register') }}" class="btn btn-emerald btn-lg rounded-pill px-4 py-3 shadow-sm d-inline-flex align-items-center gap-2">
                        <i class="bi bi-mic-fill fs-5 text-gold"></i>
                        <span>Start Practicing Free</span>
                    </a>
                    <a href="#how-it-works" class="btn btn-outline-emerald btn-lg rounded-pill px-4 py-3">
                        <i class="bi bi-play-circle me-1"></i> See How It Works
                    </a>
                </div>

                <!-- Trust indicators -->
                <div class="row g-3 pt-3 border-top">
                    <div class="col-4">
                        <div class="fw-bold fs-4 text-emerald">114</div>
                        <small class="text-muted">Complete Surahs</small>
                    </div>
                    <div class="col-4">
                        <div class="fw-bold fs-4 text-emerald">4+</div>
                        <small class="text-muted">Celebrated Qaris</small>
                    </div>
                    <div class="col-4">
                        <div class="fw-bold fs-4 text-emerald">100%</div>
                        <small class="text-muted">Privacy Focused</small>
                    </div>
                </div>
            </div>

            <!-- Hero Interactive Preview Card -->
            <div class="col-lg-5">
                <div class="card card-glass border-0 shadow-lg p-4 position-relative">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge badge-emerald">Surah Al-Fatihah : 1</span>
                            <span class="text-muted small">The Opening</span>
                        </div>
                        <div class="voice-wave">
                            <span></span><span></span><span></span><span></span><span></span>
                        </div>
                    </div>

                    <!-- Calligraphy Verse Display -->
                    <div class="bg-light rounded-4 p-4 text-center mb-3 border">
                        <div class="font-arabic fs-1 text-emerald mb-2">
                            بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ
                        </div>
                        <small class="text-muted fst-italic">"In the name of Allah, the Entirely Merciful, the Especially Merciful."</small>
                    </div>

                    <!-- AI Analysis Visualizer Sample -->
                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small fw-semibold text-secondary"><i class="bi bi-cpu text-emerald me-1"></i> AI Word-by-Word Alignment</span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle">98% Match</span>
                        </div>
                        <div class="p-3 bg-white rounded-3 border text-end">
                            <span class="word-feedback word-matched">بِسْمِ</span>
                            <span class="word-feedback word-matched">اللَّهِ</span>
                            <span class="word-feedback word-matched">الرَّحْمَٰنِ</span>
                            <span class="word-feedback word-matched">الرَّحِيمِ</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-emerald-light border border-emerald-border">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-success text-white p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="bi bi-check-lg"></i>
                            </div>
                            <small class="fw-semibold text-emerald">Clear Makhraj & Correct Sequence</small>
                        </div>
                        <span class="text-gold fw-bold small">A+</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Grid -->
<section id="features" class="py-5 bg-white border-top border-bottom">
    <div class="container py-lg-4">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="badge badge-emerald mb-2">Platform Capabilities</span>
            <h2 class="fw-bold text-emerald mb-3">Designed for Devoted Quran Seekers</h2>
            <p class="text-secondary">Combining the dignity of traditional Quranic science with modern assistive voice intelligence.</p>
        </div>

        <div class="row g-4">
            <!-- Feature 1 -->
            <div class="col-md-6 col-lg-3">
                <div class="card card-glass h-100 p-4 border-0">
                    <div class="rounded-4 bg-emerald-light text-emerald p-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 54px; height: 54px;">
                        <i class="bi bi-mic fs-3"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">AI Recitation Coach</h5>
                    <p class="text-secondary small mb-0">Speak directly into your microphone. The AI aligns your spoken Arabic with the verse word-by-word and identifies areas to refine.</p>
                </div>
            </div>

            <!-- Feature 2 -->
            <div class="col-md-6 col-lg-3">
                <div class="card card-glass h-100 p-4 border-0">
                    <div class="rounded-4 bg-emerald-light text-emerald p-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 54px; height: 54px;">
                        <i class="bi bi-journal-text fs-3"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Authentic Uthmani Text</h5>
                    <p class="text-secondary small mb-0">Crystal-clear typography formatted with standard Medina Mushaf styling, accompanied by reputable translations and transliterations.</p>
                </div>
            </div>

            <!-- Feature 3 -->
            <div class="col-md-6 col-lg-3">
                <div class="card card-glass h-100 p-4 border-0">
                    <div class="rounded-4 bg-emerald-light text-emerald p-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 54px; height: 54px;">
                        <i class="bi bi-clock-history fs-3"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Hifz Spaced Repetition</h5>
                    <p class="text-secondary small mb-0">Memorize systematically. Our cognitive algorithm schedules automatic revision intervals so you retain every Surah for life.</p>
                </div>
            </div>

            <!-- Feature 4 -->
            <div class="col-md-6 col-lg-3">
                <div class="card card-glass h-100 p-4 border-0">
                    <div class="rounded-4 bg-emerald-light text-emerald p-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 54px; height: 54px;">
                        <i class="bi bi-fire fs-3 text-danger"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Streaks & Milestones</h5>
                    <p class="text-secondary small mb-0">Track your daily reading consistency, ayahs completed, and practice duration with beautiful interactive analytics.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section id="how-it-works" class="py-5 bg-sand">
    <div class="container py-lg-4">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="badge badge-gold mb-2">Simple 3-Step Journey</span>
            <h2 class="fw-bold text-emerald mb-3">How to Practice Your Recitation</h2>
            <p class="text-secondary">Improve your pronunciation in three intuitive steps right inside your web browser.</p>
        </div>

        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="p-4 bg-white rounded-4 shadow-sm h-100">
                    <div class="rounded-circle bg-emerald-grad text-white mx-auto d-flex align-items-center justify-content-center fw-bold fs-4 mb-3" style="width: 60px; height: 60px;">1</div>
                    <h5 class="fw-bold mb-2">Select Surah & Ayah</h5>
                    <p class="text-secondary small mb-0">Choose any Surah from the 114 chapters and listen to correct recitation by renowned Qaris to familiarize your ear.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="p-4 bg-white rounded-4 shadow-sm h-100">
                    <div class="rounded-circle bg-emerald-grad text-white mx-auto d-flex align-items-center justify-content-center fw-bold fs-4 mb-3" style="width: 60px; height: 60px;">2</div>
                    <h5 class="fw-bold mb-2">Recite with Your Microphone</h5>
                    <p class="text-secondary small mb-0">Press record and recite the selected verse. The browser captures your voice securely with zero permanent audio storage.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="p-4 bg-white rounded-4 shadow-sm h-100">
                    <div class="rounded-circle bg-gold-grad text-white mx-auto d-flex align-items-center justify-content-center fw-bold fs-4 mb-3" style="width: 60px; height: 60px;">3</div>
                    <h5 class="fw-bold mb-2">Receive Instant AI Feedback</h5>
                    <p class="text-secondary small mb-0">Inspect color-coded word matches, view your confidence percentage, and practice repeated attempts until perfection.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Hadith Quote Banner -->
<section class="py-5 bg-dark-grad text-white position-relative">
    <div class="container py-3 text-center">
        <div class="font-arabic fs-2 text-gold mb-3">
            خَيْرُكُمْ مَنْ تَعَلَّمَ الْقُرْآنَ وَعَلَّمَهُ
        </div>
        <blockquote class="blockquote mb-2 fs-5">
            "The best among you are those who learn the Quran and teach it."
        </blockquote>
        <figcaption class="blockquote-footer text-white-50">
            Sahih al-Bukhari (5027)
        </figcaption>
    </div>
</section>

<!-- FAQs Section -->
<section id="faq" class="py-5 bg-white">
    <div class="container py-lg-4">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="badge badge-emerald mb-2">Common Inquiries</span>
            <h2 class="fw-bold text-emerald mb-3">Frequently Asked Questions</h2>
            <p class="text-secondary">Everything you need to know about our AI engine, privacy, and learning methodologies.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion accordion-flush shadow-sm rounded-4 border overflow-hidden" id="faqAccordion">
                    @forelse($faqs as $index => $faq)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $faq->id }}">
                                <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }} fw-semibold text-emerald" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $faq->id }}">
                                    {{ $faq->question }}
                                </button>
                            </h2>
                            <div id="collapse{{ $faq->id }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-secondary lh-lg">
                                    {{ $faq->answer }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">
                            FAQs will be available shortly.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Banner -->
<section class="py-5 bg-sand">
    <div class="container">
        <div class="hero-banner text-center p-5 shadow-lg">
            <h2 class="display-6 fw-bold text-white mb-3">Begin Your Quran Learning Today</h2>
            <p class="lead text-white-50 mb-4 max-w-600 mx-auto">
                Join our supportive platform to read, memorize, and perfect your recitation with intelligent voice assistance.
            </p>
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('register') }}" class="btn btn-gold btn-lg rounded-pill px-5 shadow-sm">
                    Create Free Account
                </a>
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg rounded-pill px-4">
                    Sign In
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
