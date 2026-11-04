<?php

require_once __DIR__ . '/../Controller.php';
require_once __DIR__ . '/../../Services/Database.php';
require_once __DIR__ . '/../../Services/Auth.php';

class AdminUserController extends Controller
{
    public function index(): void
    {
        Auth::requireAdmin();

        $search = trim($_GET['q'] ?? '');
        $role = trim($_GET['role'] ?? '');

        $sql = "SELECT u.*, p.country, p.phone, p.daily_goal_minutes 
                FROM users u 
                LEFT JOIN user_profiles p ON u.id = p.user_id 
                WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (u.name LIKE :search OR u.email LIKE :search)";
            $params['search'] = "%{$search}%";
        }

        if (!empty($role)) {
            $sql .= " AND u.role = :role";
            $params['role'] = $role;
        }

        $sql .= " ORDER BY u.created_at DESC";
        $users = Database::fetchAll($sql, $params);

        $this->view('admin/users/index', [
            'title' => 'User Management — Bayan AI Admin',
            'users' => $users,
            'search' => $search,
            'role' => $role
        ]);
    }

    public function toggleStatus(): void
    {
        Auth::requireAdmin();
        $this->validateCsrf();

        $id = (int) ($_POST['user_id'] ?? 0);
        $status = $_POST['status'] ?? 'active';

        if ($id === Auth::id()) {
            $this->json(['success' => false, 'error' => 'You cannot suspend your own admin account.'], 400);
        }

        Database::update('users', ['status' => $status], 'id = :id', ['id' => $id]);
        $this->json(['success' => true]);
    }
}
