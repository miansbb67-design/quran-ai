@extends('layouts.admin')

@section('title', 'Hadith Collection Manager - Bayan Admin')
@section('page_title', 'Hadith Collection Management')

@section('content')
<div class="container-fluid p-0">

    <!-- Header & Action Bar -->
    <div class="card card-glass border-0 p-4 mb-4 shadow-sm rounded-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <h4 class="fw-bold text-dark mb-1">
                    <i class="bi bi-quote text-emerald me-2"></i>Hadith Collection Manager (نظام إدارة الأحاديث النبوية)
                </h4>
                <p class="text-muted small mb-0">
                    Add, edit, and organize authentic Prophetic traditions shown on the student dashboard and public Hadith library.
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('hadith.index') }}" target="_blank" class="btn btn-outline-emerald rounded-pill px-3">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Public Hadith Hub
                </a>
                <button type="button" class="btn btn-emerald rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#createHadithModal">
                    <i class="bi bi-plus-circle me-1"></i> Add New Hadith
                </button>
            </div>
        </div>

        <!-- Metric Badges -->
        <div class="row g-3 mt-2 pt-3 border-top">
            <div class="col-sm-4">
                <div class="p-3 bg-light rounded-3 d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block">Total Authentic Hadiths</small>
                        <h4 class="fw-bold text-emerald mb-0">{{ number_format($stats['total']) }}</h4>
                    </div>
                    <div class="rounded-circle bg-emerald-light text-emerald p-2" style="width: 38px; height: 38px; display: grid; place-items: center;">
                        <i class="bi bi-book-half fs-5"></i>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="p-3 bg-light rounded-3 d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block">Active Categories</small>
                        <h4 class="fw-bold text-dark mb-0">{{ $stats['categories'] }}</h4>
                    </div>
                    <div class="rounded-circle bg-warning-subtle text-warning p-2" style="width: 38px; height: 38px; display: grid; place-items: center;">
                        <i class="bi bi-tags-fill fs-5 text-gold"></i>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="p-3 bg-light rounded-3 d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block">Hadith Books Referenced</small>
                        <h4 class="fw-bold text-info mb-0">{{ $stats['books'] }}</h4>
                    </div>
                    <div class="rounded-circle bg-info-subtle text-info p-2" style="width: 38px; height: 38px; display: grid; place-items: center;">
                        <i class="bi bi-journal-bookmark-fill fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card card-glass border-0 p-3 mb-4 shadow-sm rounded-4">
        <form method="GET" action="{{ route('admin.hadiths.index') }}" class="row g-2 align-items-center">
            <div class="col-md-6 col-lg-7">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search by text, book (Bukhari, Muslim), narrator, or translation..." value="{{ $search }}">
                </div>
            </div>
            <div class="col-md-4 col-lg-3">
                <select name="category" class="form-select" onchange="this.form.submit()">
                    <option value="all">All Categories (تمام موضوعات)</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 col-lg-2 d-flex gap-2">
                <button type="submit" class="btn btn-emerald w-100 rounded-pill">Filter</button>
                @if($search || ($category && $category !== 'all'))
                    <a href="{{ route('admin.hadiths.index') }}" class="btn btn-light border rounded-pill" title="Reset filters">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Hadiths Table -->
    <div class="card card-glass border-0 p-4 shadow-sm rounded-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="small text-muted">
                        <th style="width: 5%;">#</th>
                        <th style="width: 35%;">Arabic Text (المتن الشريف)</th>
                        <th style="width: 30%;">English & Urdu</th>
                        <th style="width: 18%;">Reference & Category</th>
                        <th style="width: 12%; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hadiths as $hadith)
                        <tr>
                            <td class="text-muted small fw-bold">{{ $hadith->id }}</td>
                            <td>
                                <div class="font-arabic fw-semibold text-dark fs-5 text-end mb-1" style="line-height: 1.8;">
                                    {{ Str::limit($hadith->arabic, 140) }}
                                </div>
                                @if($hadith->narrator)
                                    <small class="text-muted d-block"><i class="bi bi-person me-1"></i>{{ $hadith->narrator }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="small text-secondary mb-1">
                                    {{ Str::limit($hadith->english, 90) }}
                                </div>
                                <div class="font-arabic-clean small text-success text-end" dir="rtl">
                                    {{ Str::limit($hadith->urdu, 90) }}
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-emerald-light text-emerald fw-semibold mb-1 d-inline-block">{{ $hadith->category }}</span>
                                <div class="small text-muted fw-semibold">
                                    <i class="bi bi-book me-1"></i>{{ $hadith->book }}
                                    @if($hadith->hadith_number)
                                        #{{ $hadith->hadith_number }}
                                    @endif
                                </div>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-light border rounded-pill px-2 me-1" 
                                    onclick='editHadith({!! json_encode($hadith) !!})' 
                                    title="Edit Hadith">
                                    <i class="bi bi-pencil-square text-emerald"></i>
                                </button>
                                <form method="POST" action="{{ route('admin.hadiths.destroy', $hadith->id) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this Hadith?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border rounded-pill px-2 text-danger" title="Delete Hadith">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-journal-x fs-1 d-block mb-2 text-secondary"></i>
                                No Hadiths matched your search or category filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $hadiths->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- Modal: Add Hadith -->
<div class="modal fade" id="createHadithModal" tabindex="-1" aria-labelledby="createHadithModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-emerald text-white border-0 py-3 rounded-top-4">
                <h5 class="modal-title fw-bold" id="createHadithModalLabel"><i class="bi bi-plus-circle me-2 text-gold"></i>Add Authentic Hadith</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.hadiths.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Hadith Book (Source)</label>
                            <input type="text" name="book" class="form-control" placeholder="e.g. Sahih al-Bukhari, Sahih Muslim, Jami at-Tirmidhi" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Hadith #</label>
                            <input type="text" name="hadith_number" class="form-control" placeholder="e.g. 5027">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Category</label>
                            <select name="category" class="form-select" required>
                                <option value="Quran Virtues">Quran Virtues</option>
                                <option value="Seeking Knowledge">Seeking Knowledge</option>
                                <option value="Good Character">Good Character</option>
                                <option value="Prayer & Dhikr">Prayer & Dhikr</option>
                                <option value="Patience & Trust">Patience & Trust</option>
                                <option value="Kindness & Charity">Kindness & Charity</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Narrator (الراوي)</label>
                            <input type="text" name="narrator" class="form-control" placeholder="e.g. Uthman ibn Affan (RA)">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Chapter / Topic</label>
                            <input type="text" name="chapter" class="form-control" placeholder="e.g. Virtues of the Quran">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Arabic Text (نص الحديث الشريف)</label>
                        <textarea name="arabic" class="form-control font-arabic fs-5" rows="3" dir="rtl" placeholder="خَيْرُكُمْ مَنْ تَعَلَّمَ الْقُرْآنَ وَعَلَّمَهُ" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">English Translation</label>
                        <textarea name="english" class="form-control" rows="3" placeholder="The best among you are those who learn the Quran and teach it." required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Urdu Translation (اردو ترجمہ)</label>
                        <textarea name="urdu" class="form-control font-arabic-clean" rows="3" dir="rtl" placeholder="تم میں سب سے بہتر وہ شخص ہے جو قرآن سیکھے اور سکھائے۔" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-emerald rounded-pill px-4 fw-semibold">Save Hadith</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Edit Hadith -->
<div class="modal fade" id="editHadithModal" tabindex="-1" aria-labelledby="editHadithModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-emerald text-white border-0 py-3 rounded-top-4">
                <h5 class="modal-title fw-bold" id="editHadithModalLabel"><i class="bi bi-pencil-square me-2 text-gold"></i>Edit Hadith</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editHadithForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Hadith Book</label>
                            <input type="text" name="book" id="edit_book" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Hadith #</label>
                            <input type="text" name="hadith_number" id="edit_hadith_number" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Category</label>
                            <select name="category" id="edit_category" class="form-select" required>
                                <option value="Quran Virtues">Quran Virtues</option>
                                <option value="Seeking Knowledge">Seeking Knowledge</option>
                                <option value="Good Character">Good Character</option>
                                <option value="Prayer & Dhikr">Prayer & Dhikr</option>
                                <option value="Patience & Trust">Patience & Trust</option>
                                <option value="Kindness & Charity">Kindness & Charity</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Narrator</label>
                            <input type="text" name="narrator" id="edit_narrator" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Chapter</label>
                            <input type="text" name="chapter" id="edit_chapter" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Arabic Text</label>
                        <textarea name="arabic" id="edit_arabic" class="form-control font-arabic fs-5" rows="3" dir="rtl" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">English Translation</label>
                        <textarea name="english" id="edit_english" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Urdu Translation</label>
                        <textarea name="urdu" id="edit_urdu" class="form-control font-arabic-clean" rows="3" dir="rtl" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-emerald rounded-pill px-4 fw-semibold">Update Hadith</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editHadith(data) {
    document.getElementById('editHadithForm').action = '/admin/hadiths/' + data.id;
    document.getElementById('edit_book').value = data.source_book || data.book || '';
    document.getElementById('edit_hadith_number').value = data.hadith_number || '';
    document.getElementById('edit_category').value = data.category || 'Quran Virtues';
    document.getElementById('edit_narrator').value = data.narrator || '';
    document.getElementById('edit_chapter').value = data.chapter || '';
    document.getElementById('edit_arabic').value = data.arabic_text || data.arabic || '';
    document.getElementById('edit_english').value = data.english_translation || data.english || '';
    document.getElementById('edit_urdu').value = data.urdu_translation || data.urdu || '';

    new bootstrap.Modal(document.getElementById('editHadithModal')).show();
}
</script>
@endpush
