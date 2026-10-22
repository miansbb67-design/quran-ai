@extends('layouts.admin')

@section('title', 'Manage Students & Users - Bayan Admin')
@section('page_title', 'User & Student Management')

@section('content')
<div class="container-fluid p-0">

    <!-- Filters & Search Toolbar -->
    <div class="card card-glass border-0 p-3 mb-4 shadow-sm">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search by name or email..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-md-3 col-6">
                <select name="role" class="form-select">
                    <option value="">All Roles</option>
                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Student</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                </select>
            </div>

            <div class="col-md-2 col-6">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-emerald w-100 rounded-pill">Filter</button>
                @if(request()->hasAny(['search', 'role', 'status']))
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light rounded-circle" title="Clear filters">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="card card-glass border-0 p-4 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="small text-muted">
                        <th>#</th>
                        <th>User</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Sessions</th>
                        <th>Bookmarks</th>
                        <th>Memorized</th>
                        <th>Joined</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="text-muted small">{{ $user->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-emerald-light text-emerald d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; font-size: 0.85rem;">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <strong class="d-block text-dark small">{{ $user->name }}</strong>
                                        <small class="text-muted">{{ $user->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($user->isAdmin())
                                    <span class="badge bg-warning text-dark px-2 py-1">
                                        <i class="bi bi-shield-lock-fill me-1"></i> Admin
                                    </span>
                                @else
                                    <span class="badge bg-light text-dark border px-2 py-1">
                                        <i class="bi bi-mortarboard-fill text-emerald me-1"></i> Student
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $user->isActive() ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td class="small">{{ $user->practice_sessions_count }}</td>
                            <td class="small">{{ $user->bookmarks_count }}</td>
                            <td class="small">{{ $user->memorization_progress_count }}</td>
                            <td class="text-muted small">{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-light border" title="View details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-light border edit-user-btn" 
                                            data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}"
                                            data-email="{{ $user->email }}"
                                            data-role="{{ $user->role }}"
                                            data-status="{{ $user->status }}"
                                            title="Edit user">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                No users matched your search criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-dark">Edit User Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Name</label>
                        <input type="text" name="name" id="editUserName" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Email</label>
                        <input type="email" name="email" id="editUserEmail" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Role</label>
                        <select name="role" id="editUserRole" class="form-select" required>
                            <option value="user">Student</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Account Status</label>
                        <select name="status" id="editUserStatus" class="form-select" required>
                            <option value="active">Active</option>
                            <option value="suspended">Suspended</option>
                        </select>
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

@push('scripts')
<script>
    $(document).ready(function() {
        var modal = new bootstrap.Modal(document.getElementById('editUserModal'));

        $('.edit-user-btn').on('click', function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var email = $(this).data('email');
            var role = $(this).data('role');
            var status = $(this).data('status');

            $('#editUserName').val(name);
            $('#editUserEmail').val(email);
            $('#editUserRole').val(role);
            $('#editUserStatus').val(status);

            $('#editUserForm').attr('action', '/admin/users/' + id);
            modal.show();
        });
    });
</script>
@endpush
@endsection
