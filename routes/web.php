<?php

require_once __DIR__ . '/../app/Services/Router.php';

// Public Landing & Static Pages
Router::get('/', 'LandingController@index');

// Authentication Routes
Router::get('/login', 'AuthController@showLogin');
Router::post('/login', 'AuthController@handleLogin');
Router::get('/register', 'AuthController@showRegister');
Router::post('/register', 'AuthController@handleRegister');
Router::any('/logout', 'AuthController@logout');

// Quran Reader Routes
Router::get('/quran', 'QuranController@index');
Router::get('/quran/{id}', 'QuranController@show');
Router::get('/api/surah/{id}', 'QuranController@apiSurahAyahs');
Router::post('/api/quran/bookmark', 'QuranController@toggleBookmark');

// Tilawat & Recitations
Router::get('/recitations', 'RecitationController@index');

// AI Quran Teacher & Recitation Evaluator
Router::get('/ai-teacher', 'AITeacherController@index');
Router::post('/api/ai-teacher/evaluate', 'AITeacherController@evaluateRecitation');

// Hifz & Memorization Tracker
Router::get('/hifz', 'HifzController@index');
Router::post('/hifz/store', 'HifzController@store');
Router::post('/api/hifz/update-status', 'HifzController@updateStatus');

// Student Dashboard
Router::get('/dashboard', 'DashboardController@index');

// Admin Panel Routes
Router::get('/admin', 'AdminDashboardController@index');
Router::get('/admin/users', 'AdminUserController@index');
Router::post('/admin/users/status', 'AdminUserController@toggleStatus');
Router::get('/admin/seo', 'AdminSeoController@index');
Router::post('/admin/seo/update', 'AdminSeoController@update');
Router::post('/admin/seo/robots', 'AdminSeoController@updateRobots');
Router::get('/admin/analytics', 'AdminAnalyticsController@index');
