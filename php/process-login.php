<?php
// php/process-login.php
// ini_set('display_errors', 1); error_reporting(E_ALL); // Dev only

session_start(); // Start session for storing login state and messages

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db_connect.php';

// --- Security & Request Method Check ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = 'Invalid request method.';
    header('Location: ../login.php');
    exit;
}

// --- Honeypot Check ---
if (!empty($_POST['honeypot'])) {
    // Bot detected, fail silently or log
    $_SESSION['error_message'] = 'Invalid login attempt.'; // Generic error
    header('Location: ../login.php');
    exit;
}

// --- Get Data ---
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? ''; // Don't trim password

// --- Store email in session in case of error ---
$_SESSION['form_data'] = ['email' => $email];

// --- Server-Side Validation ---
$errors = [];
if (empty($email)) {
    $errors[] = 'Email is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format.';
}
if (empty($password)) {
    $errors[] = 'Password is required.';
}

if (!empty($errors)) {
    $_SESSION['error_message'] = implode(' ', $errors);
    header('Location: ../login.php');
    exit;
}

// --- Check Credentials ---
$pdo = getDbConnection();
if (!$pdo) {
    $_SESSION['error_message'] = 'Database connection error.';
    header('Location: ../login.php');
    exit;
}

try {
    $sql = "SELECT user_id, display_name, password_hash FROM users WHERE email = :email LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        // --- Login Successful ---

        // Regenerate session ID for security (prevents session fixation)
        session_regenerate_id(true);

        // Store user info in session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['display_name'] = $user['display_name']; // Store display name too

        // Clear form data from session on success
        unset($_SESSION['form_data']);

        // Redirect to the account page (or intended destination if stored)
        header('Location: ../account.php');
        exit;

    } else {
        // --- Login Failed ---
        $_SESSION['error_message'] = 'Invalid email or password.';
        header('Location: ../login.php');
        exit;
    }

} catch (PDOException $e) {
    error_log("Database Error during login: " . $e->getMessage());
    $_SESSION['error_message'] = 'An internal error occurred during login. Please try again later.';
    header('Location: ../login.php');
    exit;
}
?>