<?php
// php/update-address.php
ini_set('display_errors', 1); // Remove for production
error_reporting(E_ALL);     // Remove for production

// Start session BEFORE any output
session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db_connect.php'; // Include DB connection helper

header('Content-Type: application/json');

// --- Security Checks ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Check if user is logged in (adjust session variable name if needed)
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Authentication required. Please log in.']);
    exit;
}
$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

// --- Get and Validate Data ---
$address_type = isset($_POST['address_type']) ? trim($_POST['address_type']) : '';
$first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
$last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
$company = isset($_POST['company']) ? trim($_POST['company']) : ''; // Optional
$street1 = isset($_POST['street1']) ? trim($_POST['street1']) : '';
$street2 = isset($_POST['street2']) ? trim($_POST['street2']) : ''; // Optional
$city = isset($_POST['city']) ? trim($_POST['city']) : '';
$state = isset($_POST['state']) ? trim($_POST['state']) : '';
$zip = isset($_POST['zip']) ? trim($_POST['zip']) : '';
$country = isset($_POST['country']) ? trim($_POST['country']) : ''; // e.g., 'US', 'CA'

// --- Server-Side Validation ---
$errors = [];
if ($address_type !== 'billing' && $address_type !== 'shipping') {
    $errors[] = 'Invalid address type specified.';
}
if (empty($first_name)) $errors[] = 'First name is required.';
if (empty($last_name)) $errors[] = 'Last name is required.';
if (empty($street1)) $errors[] = 'Street address is required.';
if (empty($city)) $errors[] = 'Town / City is required.';
if (empty($state)) $errors[] = 'State / County is required.';
if (empty($zip)) $errors[] = 'Postcode / ZIP is required.';
if (empty($country)) $errors[] = 'Country is required.';
// Add more specific validation (length, format, country codes) as needed

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

// Using INSERT ... ON DUPLICATE KEY UPDATE is efficient for upserting
// Assumes you have a UNIQUE constraint on (user_id, address_type) in your 'user_addresses' table
// Adjust table and column names as per your database schema
$sql = "INSERT INTO user_addresses (user_id, address_type, first_name, last_name, company, street_address_1, street_address_2, city, state, postcode, country_code)
        VALUES (:user_id, :address_type, :first_name, :last_name, :company, :street1, :street2, :city, :state, :zip, :country)
        ON DUPLICATE KEY UPDATE
            first_name = VALUES(first_name),
            last_name = VALUES(last_name),
            company = VALUES(company),
            street_address_1 = VALUES(street1),
            street_address_2 = VALUES(street2),
            city = VALUES(city),
            state = VALUES(state),
            postcode = VALUES(zip),
            country_code = VALUES(country)";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':address_type', $address_type, PDO::PARAM_STR);
    $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
    $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
    $stmt->bindParam(':company', $company, PDO::PARAM_STR);
    $stmt->bindParam(':street1', $street1, PDO::PARAM_STR);
    $stmt->bindParam(':street2', $street2, PDO::PARAM_STR);
    $stmt->bindParam(':city', $city, PDO::PARAM_STR);
    $stmt->bindParam(':state', $state, PDO::PARAM_STR);
    $stmt->bindParam(':zip', $zip, PDO::PARAM_STR);
    $stmt->bindParam(':country', $country, PDO::PARAM_STR);

    $stmt->execute();

    // Optionally, you could fetch the updated data to send back, but a success message is often enough
    $updatedData = [ // Example data to send back if needed by JS
        'type' => $address_type,
        'first_name' => $first_name,
        // ... other fields
    ];

    echo json_encode(['success' => true, 'message' => ucfirst($address_type) . ' address saved successfully.', 'updatedData' => $updatedData]);

} catch (PDOException $e) {
    error_log("Database Error updating address for user {$user_id}: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while saving the address. Please try again.']);
}

exit;
?>