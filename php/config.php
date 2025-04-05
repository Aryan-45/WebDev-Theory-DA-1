<?php
// php/config.php

// --- Database Credentials ---
// !! IMPORTANT: Replace with your actual database details !!
// !! Consider using environment variables or a more secure config method !!
define('DB_HOST', 'localhost');         // Usually 'localhost'
define('DB_NAME', 'artisan_collective_db'); // Your database name
define('DB_USER', 'root');      // Your database username
define('DB_PASS', '');      // Your database password
define('DB_CHARSET', 'utf8mb4');

// --- File Upload Configuration ---
// !! IMPORTANT: Make sure this path exists and is writable by the web server !!
// !! Ideally, place this OUTSIDE the public web root for security !!
// Example assuming it's one level above the 'php' directory
define('UPLOAD_PATH', __DIR__ . '/../uploads/'); // Adjust path as needed
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2 MB (consistent with JS)
define('ALLOWED_MIME_TYPES', ['image/jpeg', 'image/png']);

// --- Email Configuration ---
define('ADMIN_EMAIL', 'admin@yourdomain.com'); // Email to send contact/application forms to
define('EMAIL_FROM', 'noreply@yourdomain.com'); // 'From' address for emails sent by the site

// --- Site Configuration ---
define('SITE_URL', 'http://localhost/your-project-root'); // Your base site URL (for redirects etc.)

// You might add other constants here (API keys, etc.)

?>