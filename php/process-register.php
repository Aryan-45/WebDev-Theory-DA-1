<?php
// php/process-register.php
ini_set('display_errors', 1); error_reporting(E_ALL); // Dev only

session_start(); // Start session for storing messages and login state

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db_connect.php';

// --- Security & Request Method Check ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = 'Invalid request method.';
    header('Location: ../register.php'); // Redirect back
    exit;
}

// --- Honeypot Check ---
if (!empty($_POST['honeypot'])) {
    // Bot detected, redirect without error, maybe log it
    header('Location: ../register.php');
    exit;
}

// --- Get Data ---
$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$display_name = trim($_POST['display_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? ''; // Don't trim password yet
$password_confirm = $_POST['password_confirm'] ?? '';

// --- Store form data in session in case of error ---
$_SESSION['form_data'] = [
    'first_name' => $first_name,
    'last_name' => $last_name,
    'display_name' => $display_name,
    'email' => $email,
];

// --- Server-Side Validation ---
$errors = [];
if (empty($first_name)) $errors[] = 'First name is required.';
if (empty($last_name)) $errors[] = 'Last name is required.';
if (empty($display_name)) $errors[] = 'Display name is required.';
if (empty($email)) {
    $errors[] = 'Email is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format.';
}
if (empty($password)) {
     $errors[] = 'Password is required.';
} elseif (strlen($password) < 8) {
    $errors[] = 'Password must be at least 8 characters long.';
}
if ($password !== $password_confirm) {
    $errors[] = 'Passwords do not match.';
}

if (!empty($errors)) {
    $_SESSION['error_message'] = implode(' ', $errors);
    header('Location: ../register.php');
    exit;
}

// --- Check if Email Already Exists ---
$pdo = getDbConnection();
if (!$pdo) {
    $_SESSION['error_message'] = 'Database connection error.';
    header('Location: ../register.php');
    exit;
}

try {
    $sql_check = "SELECT user_id FROM users WHERE email = :email";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt_check->execute();

    if ($stmt_check->fetch()) {
        $_SESSION['error_message'] = 'An account with this email address already exists. Please <a href="login.php">log in</a>.';
        header('Location: ../register.php');
        exit;
    }

    // --- Hash Password ---
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    if ($password_hash === false) {
         $_SESSION['error_message'] = 'Failed to process password.';
         error_log("Password hashing failed for email: " . $email); // Log error
         header('Location: ../register.php');
         exit;
    }


    // --- Insert User into Database ---
    $sql_insert = "INSERT INTO users (first_name, last_name, display_name, email, password_hash, created_at, updated_at)
                   VALUES (:first_name, :last_name, :display_name, :email, :password_hash, NOW(), NOW())";
    $stmt_insert = $pdo->prepare($sql_insert);

    $stmt_insert->bindParam(':first_name', $first_name, PDO::PARAM_STR);
    $stmt_insert->bindParam(':last_name', $last_name, PDO::PARAM_STR);
    $stmt_insert->bindParam(':display_name', $display_name, PDO::PARAM_STR);
    $stmt_insert->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt_insert->bindParam(':password_hash', $password_hash, PDO::PARAM_STR);

    if ($stmt_insert->execute()) {
        // --- Registration Successful - Log User In ---
        $user_id = $pdo->lastInsertId();

        // Regenerate session ID for security after state change (login)
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user_id;
        $_SESSION['display_name'] = $display_name;
        $_SESSION['success_message'] = 'Registration successful! Welcome!'; // Optional message for destination page

        // Clear form data from session on success
        unset($_SESSION['form_data']);

        // Redirect to account page
        header('Location: ../account.php');
        exit;

    } else {
        $_SESSION['error_message'] = 'Registration failed. Please try again.';
        error_log("Registration failed for email: " . $email); // Log error
        header('Location: ../register.php');
        exit;
    }

} catch (PDOException $e) {
    error_log("Database Error during registration: " . $e->getMessage());
    $_SESSION['error_message'] = 'An internal error occurred. Please try again later.';
    header('Location: ../register.php');
    exit;
}
?>