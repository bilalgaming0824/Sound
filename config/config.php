<?php
// Database configuration — adjust for your local XAMPP/WAMP setup
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sound_entertainment');

// Site configuration
define('SITE_NAME', 'SOUND');
define('SITE_TAGLINE', 'Music & Video Entertainment');
define('BASE_URL', '/Sound-main'); // change to your local folder name under htdocs/www

// Error reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
