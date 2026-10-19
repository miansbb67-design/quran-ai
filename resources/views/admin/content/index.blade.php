@extends('layouts.admin')

@section('title', 'Content & FAQ Management - Bayan Admin')
@section('page_title', 'Content & Information Management')

@section('content')
<div class="container-fluid p-0">

    <!-- Content Navigation Tabs -->
    <ul class="nav nav-pills mb-4 gap-2 bg-white p-2 rounded-4 shadow-sm border" id="contentTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active rounded-pill px-4" id="faqs-tab" data-bs-toggle="pill" data-bs-target="#faqsTab" type="button">
                <i class="bi bi-question-circle me-1"></i> FAQs ({{ $faqs->count() }})
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link rounded-pill px-4" id="pages-tab" data-bs-toggle="pill" data-bs-target="#pagesTab" type="button">
                <i class="bi bi-file-earmark-richtext me-1"></i> Site Pages ({{ $pages->count() }})
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link rounded-pill px-4" id="announcements-tab" data-bs-toggle="pill" data-bs-target="#announcementsTab" type="button">
                <i class="bi bi-megaphone me-1"></i> Announcements ({{ $announcements->count() }})
            </button>
        </li>
    </ul>

    <div class="tab-content" id="contentTabsContent">

        <!-- TAB 1: FAQs -->
        <div class="tab-pane fade show active" id="faqsTab">
            <div class="card card-glass border-0 p-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Frequently Asked Questions</h5>
                        <small class="text-muted">Display on the public landing page</small>
                    </div>
                    <button type="button" class="btn btn-emerald rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createFaqModal">
                        <i class="bi bi-plus-lg me-1"></i> Add New FAQ
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="small text-muted">
                                <th>Order</th>
                                <th>Question</th>
                                <th>Answer Preview</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($faqs as $faq)
                                <tr>
                                    <td><span class="badge bg-light text-dark border">{{ $faq->sort_order }}</span></td>
                                    <td><strong class="text-dark small">{{ $faq->question }}</strong></td>
                                    <td><small class="text-secondary">{{ Str::limit($faq->answer, 80) }}</small></td>
                                    <td>
                                        <span class="badge {{ $faq->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $faq->is_active ? 'Published' : 'Hidden' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-light border edit-faq-btn"
                                                    data-id="{{ $faq->id }}"
                                                    data-question="{{ $faq->question }}"
                                                    data-answer="{{ $faq->answer }}"
                                                    data-order="{{ $faq->sort_order }}"
                                                    data-active="{{ $faq->is_active ? '1' : '0' }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form method="POST" action="{{ route('admin.content.faqs.destroy', $faq->id) }}" onsubmit="return confirm('Delete this FAQ?');" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-light border text-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No FAQs created yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 2: Site Pages -->
        <div class="tab-pane fade" id="pagesTab">
            <div class="card card-glass border-0 p-4 shadow-sm">
                <div class="mb-4">
                    <h5 class="fw-bold text-dark mb-0">Static Institutional Pages</h5>
                    <small class="text-muted">Edit legal, mission, and policy content</small>
                </div>

                <div class="row g-3">
                    @foreach($pages as $page)
                        <div class="col-md-4">
                            <div class="card card-glass border p-4 h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge badge-emerald text-uppercase">{{ $page->slug }}</span>
                                    <span class="badge {{ $page->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $page->is_active ? 'Active' : 'Draft' }}</span>
                                </div>
                                <h5 class="fw-bold text-dark mb-2">{{ $page->title }}</h5>
                                <p class="text-secondary small mb-3 flex-grow-1">
                                    {{ Str::limit(strip_tags($page->content), 100) }}
                                </p>
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <a href="{{ route('page.show', $page->slug) }}" target="_blank" class="small text-emerald text-decoration-none">
                                        <i class="bi bi-box-arrow-up-right me-1"></i> View Live
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-emerald rounded-pill px-3 edit-page-btn"
                                            data-id="{{ $page->id }}"
                                            data-title="{{ $page->title }}"
                                            data-content="{{ htmlspecialchars($page->content) }}"
                                            data-active="{{ $page->is_active ? '1' : '0' }}">
                                        Edit Content
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- TAB 3: Announcements -->
        <div class="tab-pane fade" id="announcementsTab">
            <div class="card card-glass border-0 p-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Platform Announcements</h5>
                        <small class="text-muted">Display top banner ticker and dashboard alerts</small>
                    </div>
                    <button type="button" class="btn btn-emerald rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createAnnouncementModal">
                        <i class="bi bi-plus-lg me-1"></i> New Announcement
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="small text-muted">
                                <th>Title</th>
                                <th>Content</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Dates</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($announcements as $ann)
                                <tr>
                                    <td><strong class="text-dark small">{{ $ann->title }}</strong></td>
                                    <td><small class="text-secondary">{{ Str::limit($ann->content, 60) }}</small></td>
                                    <td>
                                        <span class="badge bg-{{ $ann->type === 'warning' ? 'warning text-dark' : 'info' }}">
                                            {{ ucfirst($ann->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $ann->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $ann->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="small text-muted">
                                        {{ $ann->starts_at ? $ann->starts_at->format('M d') : 'Now' }} - {{ $ann->ends_at ? $ann->ends_at->format('M d, Y') : 'Indefinite' }}
                                    </td>
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('admin.content.announcements.destroy', $ann->id) }}" onsubmit="return confirm('Delete announcement?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border text-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No announcements found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Create FAQ -->
<div class="modal fade" id="createFaqModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-dark">Create New FAQ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.content.faqs.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Question</label>
                        <input type="text" name="question" class="form-control" required placeholder="e.g. How does voice analysis work?">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Answer</label>
                        <textarea name="answer" class="form-control" rows="4" required placeholder="Detailed response for students..."></textarea>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="0">
                        </div>
                        <div class="col-6 d-flex align-items-center pt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="faqActiveSwitch" checked>
                                <label class="form-check-label small" for="faqActiveSwitch">Published</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-emerald rounded-pill px-4">Create FAQ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Edit FAQ -->
<div class="modal fade" id="editFaqModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-dark">Edit FAQ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editFaqForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Question</label>
                        <input type="text" name="question" id="editFaqQuestion" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Answer</label>
                        <textarea name="answer" id="editFaqAnswer" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Sort Order</label>
                            <input type="number" name="sort_order" id="editFaqOrder" class="form-control">
                        </div>
                        <div class="col-6 d-flex align-items-center pt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="editFaqActive">
                                <label class="form-check-label small" for="editFaqActive">Published</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-emerald rounded-pill px-4">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Edit Static Page -->
<div class="modal fade" id="editPageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-dark" id="editPageModalTitle">Edit Static Page</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editPageForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Page Title</label>
                        <input type="text" name="title" id="editPageTitle" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Content (HTML formatted)</label>
                        <textarea name="content" id="editPageContent" class="form-control font-monospace small" rows="10" required></textarea>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="editPageActive">
                        <label class="form-check-label small" for="editPageActive">Active / Published</label>
                    </div>
                </div>
                <div class="modal-footer border-top p-3">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-emerald rounded-pill px-4">Update Page</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Create Announcement -->
<div class="modal fade" id="createAnnouncementModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-dark">Publish New Announcement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.content.announcements.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Announcement Title</label>
                        <input type="text" name="title" class="form-control" required placeholder="e.g. Ramadan Recitation Challenge Announced!">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Announcement Text</label>
                        <textarea name="content" class="form-control" rows="3" required placeholder="Brief message to show across student dashboards..."></textarea>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Alert Style</label>
                            <select name="type" class="form-select">
                                <option value="info">Info (Blue)</option>
                                <option value="warning">Important (Amber)</option>
                                <option value="success">Celebration (Green)</option>
                            </select>
                        </div>
                        <div class="col-6 d-flex align-items-center pt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="annActiveSwitch" checked>
                                <label class="form-check-label small" for="annActiveSwitch">Live on Site</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-emerald rounded-pill px-4">Broadcast</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        var faqModal = new bootstrap.Modal(document.getElementById('editFaqModal'));
        var pageModal = new bootstrap.Modal(document.getElementById('editPageModal'));

        // Edit FAQ
        $('.edit-faq-btn').on('click', function() {
            var id = $(this).data('id');
            $('#editFaqQuestion').val($(this).data('question'));
            $('#editFaqAnswer').val($(this).data('answer'));
            $('#editFaqOrder').val($(this).data('order'));
            $('#editFaqActive').prop('checked', $(this).data('active') == '1');
            $('#editFaqForm').attr('action', '/admin/content/faqs/' + id);
            faqModal.show();
        });

        // Edit Page
        $('.edit-page-btn').on('click', function() {
            var id = $(this).data('id');
            $('#editPageTitle').val($(this).data('title'));
            $('#editPageContent').val($(this).data('content'));
            $('#editPageActive').prop('checked', $(this).data('active') == '1');
            $('#editPageForm').attr('action', '/admin/content/pages/' + id);
            pageModal.show();
        });
    });
</script>
@endpush
@endsection
