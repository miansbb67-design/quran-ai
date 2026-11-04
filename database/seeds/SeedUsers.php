<?php

require_once __DIR__ . '/../../app/Services/Env.php';
require_once __DIR__ . '/../../app/Services/Database.php';

Env::load(__DIR__ . '/../../.env');

echo "--- Seeding Database ---" . PHP_EOL;

// Admin User
$adminEmail = 'admin@bayan.test';
$existing = Database::fetch('SELECT id FROM users WHERE email = :email', ['email' => $adminEmail]);
if (!$existing) {
    $hash = password_hash('Admin@123456', PASSWORD_BCRYPT);
    $adminId = Database::insert('users', [
        'name' => 'Bayan Super Admin',
        'email' => $adminEmail,
        'password' => $hash,
        'role' => 'admin',
        'status' => 'active',
        'email_verified_at' => date('Y-m-d H:i:s')
    ]);
    Database::insert('user_profiles', [
        'user_id' => $adminId,
        'preferred_qari_id' => 1,
        'daily_goal_minutes' => 30,
        'daily_ayah_target' => 10,
        'arabic_font_size' => 28
    ]);
    echo "[✓] Admin created: {$adminEmail} (Pass: Admin@123456)" . PHP_EOL;
} else {
    echo "[i] Admin exists: {$adminEmail}" . PHP_EOL;
}

// Student User
$studentEmail = 'student@bayan.test';
$existingStudent = Database::fetch('SELECT id FROM users WHERE email = :email', ['email' => $studentEmail]);
if (!$existingStudent) {
    $hash = password_hash('Student@123456', PASSWORD_BCRYPT);
    $studentId = Database::insert('users', [
        'name' => 'Talib-e-Ilm (Student)',
        'email' => $studentEmail,
        'password' => $hash,
        'role' => 'student',
        'status' => 'active',
        'email_verified_at' => date('Y-m-d H:i:s')
    ]);
    Database::insert('user_profiles', [
        'user_id' => $studentId,
        'preferred_qari_id' => 1,
        'daily_goal_minutes' => 15,
        'daily_ayah_target' => 5,
        'arabic_font_size' => 28
    ]);
    echo "[✓] Student created: {$studentEmail} (Pass: Student@123456)" . PHP_EOL;
} else {
    echo "[i] Student exists: {$studentEmail}" . PHP_EOL;
}

echo "--- Seeding Completed Successfully ---" . PHP_EOL;
