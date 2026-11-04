<?php

// 1. Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

// 2. Load Environment Variables
require_once __DIR__ . '/app/Services/Env.php';
Env::load(__DIR__ . '/.env');

// 3. Security & Session Initialization
require_once __DIR__ . '/app/Services/Security.php';
Security::startSession();

// 4. Load Database & Router
require_once __DIR__ . '/app/Services/Database.php';
require_once __DIR__ . '/app/Services/Router.php';

// 5. Load Application Routes
require_once __DIR__ . '/routes/web.php';

// 6. Dispatch Request
Router::dispatch();
