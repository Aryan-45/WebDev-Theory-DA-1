<?php
// php/update-account.php
ini_set('display_errors', 1); // Remove for production
error_reporting(E_ALL);     // Remove for production

session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db_connect.php';

header('Content-Type: application/json');

// --- Security Checks ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Authentication required.']);
    exit;
}
$user_id = $_SESSION['user_id'];

// --- Get and Validate Data ---
$first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
$last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
$display_name = isset($_POST['display_name']) ? trim($_POST['display_name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password_current = isset($_POST['password_current']) ? $_POST['password_current'] : ''; // Don't trim passwords
$password_new = isset($_POST['password_new']) ? $_POST['password_new'] : '';
$password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';

$errors = [];
if (empty($first_name)) $errors[] = 'First name is required.';
if (empty($last_name)) $errors[] = 'Last name is required.';
if (empty($display_name)) $errors[] = 'Display name is required.';
if (empty($email)) {
    $errors[] = 'Email is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format.';
}

// --- Password Change Validation ---
$change_password = false;
$new_password_hash = null;
if (!empty($password_current) || !empty($password_new) || !empty($password_confirm)) {
    // If any password field is filled, treat it as an attempt to change
    if (empty($password_current)) {
        $errors[] = 'Current password is required to change your password.';
    }
    if (empty($password_new)) {
        $errors[] = 'New password is required.';
    } elseif (strlen($password_new) < 8) { // Example minimum length
         $errors[] = 'New password must be at least 8 characters long.';
    }
    if ($password_new !== $password_confirm) {
        $errors[] = 'New password and confirmation password do not match.';
    }

    if (empty($errors)) { // Only proceed if basic password validation passes
        $change_password = true;
    }
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

// --- Database Interaction ---
$pdo = getDbConnection();
if (!$pdo) {
    echo json_encode(['success' => false, 'message' => 'Database connection error.']);
    exit;
}

try {
    // If changing password, verify the current one first
    if ($change_password) {
        $sql_check = "SELECT password_hash FROM users WHERE user_id = :user_id"; // Adjust table/column names
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt_check->execute();
        $user = $stmt_check->fetch();

        if (!$user || !password_verify($password_current, $user['password_hash'])) {
            echo json_encode(['success' => false, 'message' => 'Incorrect current password.']);
            exit;
        }
        // Current password is correct, hash the new one
        $new_password_hash = password_hash($password_new, PASSWORD_DEFAULT);
    }

    // --- Prepare Update Statement ---
    $sql_update = "UPDATE users SET
                       first_name = :first_name,
                       last_name = :last_name,
                       display_name = :display_name,
                       email = :email";
    $params = [
        ':first_name' => $first_name,
        ':last_name' => $last_name,
        ':display_name' => $display_name,
        ':email' => $email,
        ':user_id' => $user_id
    ];

    if ($change_password && $new_password_hash) {
        $sql_update .= ", password_hash = :password_hash"; // Add password update part
        $params[':password_hash'] = $new_password_hash;
    }

    $sql_update .= " WHERE user_id = :user_id";

    $stmt_update = $pdo->prepare($sql_update);
    $stmt_update->execute($params);

     // Update session variables if needed (e.g., display name)
     $_SESSION['display_name'] = $display_name; // Example

    echo json_encode(['success' => true, 'message' => 'Account details updated successfully.']);

} catch (PDOException $e) {
    // Check for unique constraint violation (e.g., email already exists)
    if ($e->getCode() == 23000) { // Integrity constraint violation code
         echo json_encode(['success' => false, 'message' => 'The email address is already registered to another account.']);
    } else {
        error_log("Database Error updating account for user {$user_id}: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred while updating account details.']);
    }
}

exit;
?>