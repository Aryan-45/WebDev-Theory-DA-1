<?php
// php/process-application.php
ini_set('display_errors', 1); // Remove for production
error_reporting(E_ALL);     // Remove for production

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db_connect.php';

header('Content-Type: application/json');

// --- Basic Validation & Security ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Honeypot check
if (!empty($_POST['honeypot'])) {
    echo json_encode(['success' => true, 'message' => 'Application received.']); // Silently succeed
    exit;
}

// --- Get and Sanitize Text Data ---
$first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
$last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : ''; // Optional
$business_name = isset($_POST['business_name']) ? trim($_POST['business_name']) : ''; // Optional
$website = isset($_POST['website']) ? trim($_POST['website']) : ''; // Optional
$craft_description = isset($_POST['craft_description']) ? trim($_POST['craft_description']) : '';

// --- Server-Side Validation (Text Fields) ---
$errors = [];
if (empty($first_name)) $errors[] = 'First name is required.';
if (empty($last_name)) $errors[] = 'Last name is required.';
if (empty($email)) {
    $errors[] = 'Email is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format.';
}
if (empty($craft_description)) $errors[] = 'Craft description is required.';
if (!empty($website) && !filter_var($website, FILTER_VALIDATE_URL)) {
    // Basic URL validation, might need more robust checking
    $errors[] = 'Invalid website/social media URL format.';
}
// Add more validation as needed (phone format, lengths)

// --- File Upload Validation & Processing ---
$uploaded_filenames = []; // To store successfully uploaded *new* filenames
$max_files = 5; // Align with JS

if (isset($_FILES['portfolio_images'])) {
    $portfolio_files = $_FILES['portfolio_images'];

    // Check if too many files were uploaded (PHP might truncate, but good to check count)
    if (count($portfolio_files['name']) > $max_files) {
        $errors[] = "You can upload a maximum of {$max_files} portfolio images.";
    } else {
        // Iterate through each uploaded file
        for ($i = 0; $i < count($portfolio_files['name']); $i++) {
            // Check for upload errors first
            if ($portfolio_files['error'][$i] === UPLOAD_ERR_OK) {
                $tmp_name = $portfolio_files['tmp_name'][$i];
                $file_size = $portfolio_files['size'][$i];
                $original_name = $portfolio_files['name'][$i];

                // Validate Size
                if ($file_size > MAX_FILE_SIZE) {
                    $errors[] = "File '{$original_name}' is too large (Max " . (MAX_FILE_SIZE / 1024 / 1024) . " MB).";
                    continue; // Skip this file
                }

                // Validate Type (using finfo is more reliable)
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime_type = finfo_file($finfo, $tmp_name);
                finfo_close($finfo);

                if (!in_array($mime_type, ALLOWED_MIME_TYPES)) {
                    $errors[] = "File '{$original_name}' has an invalid type ('{$mime_type}'). Only JPG/PNG allowed.";
                    continue; // Skip this file
                }

                // Generate unique filename to prevent overwrites and hide original name
                $extension = pathinfo($original_name, PATHINFO_EXTENSION);
                $safe_extension = strtolower($extension); // Ensure lowercase
                 if (!in_array($safe_extension, ['jpg', 'jpeg', 'png'])) { // Double-check extension
                    $errors[] = "File '{$original_name}' has an invalid extension.";
                    continue;
                 }
                $new_filename = uniqid('app_', true) . '.' . $safe_extension;
                $destination = UPLOAD_PATH . $new_filename;

                // Ensure upload directory exists
                 if (!is_dir(UPLOAD_PATH)) {
                     if (!mkdir(UPLOAD_PATH, 0775, true)) { // Create recursively with appropriate permissions
                         $errors[] = "Failed to create upload directory.";
                         // Break here as no files can be saved
                         break;
                     }
                 }


                // Move the uploaded file
                if (move_uploaded_file($tmp_name, $destination)) {
                    $uploaded_filenames[] = $new_filename; // Store the *new* filename
                } else {
                    $errors[] = "Failed to move uploaded file '{$original_name}'. Check permissions.";
                    // You might want to stop processing or log this failure
                }

            } elseif ($portfolio_files['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                // Handle other upload errors (UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE, etc.)
                $errors[] = "Error uploading file '{$portfolio_files['name'][$i]}'. Code: {$portfolio_files['error'][$i]}.";
            }
             // UPLOAD_ERR_NO_FILE is ignored unless you make files explicitly required server-side below
        }
    }
     // After loop, check if *any* files were required and none uploaded
     if (empty($uploaded_filenames) && empty($errors)) { // Add check if files are truly required
        $errors[] = "At least one portfolio image is required.";
     }

} else {
    // Handle case where 'portfolio_images' field wasn't sent at all
    $errors[] = "Portfolio images are missing.";
}


// --- Final Check and Database Insertion ---
if (!empty($errors)) {
    // If there were file errors, attempt to delete any files already moved for this request (optional cleanup)
    foreach ($uploaded_filenames as $filename) {
        $filepath = UPLOAD_PATH . $filename;
        if (file_exists($filepath)) {
            @unlink($filepath);
        }
    }
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

// All data is valid, proceed to save to database
$pdo = getDbConnection();
if (!$pdo) {
    // Also attempt cleanup on DB error
     foreach ($uploaded_filenames as $filename) { @unlink(UPLOAD_PATH . $filename); }
    echo json_encode(['success' => false, 'message' => 'Database connection error.']);
    exit;
}

// Store filenames as JSON string or use a related table
$portfolio_json = json_encode($uploaded_filenames);

// Adjust table and column names
$sql = "INSERT INTO artisan_applications (first_name, last_name, email, phone, business_name, website_url, craft_description, portfolio_files, submission_date)
        VALUES (:first_name, :last_name, :email, :phone, :business_name, :website, :craft_description, :portfolio_files, NOW())";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
    $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
    $stmt->bindParam(':business_name', $business_name, PDO::PARAM_STR);
    $stmt->bindParam(':website', $website, PDO::PARAM_STR);
    $stmt->bindParam(':craft_description', $craft_description, PDO::PARAM_STR);
    $stmt->bindParam(':portfolio_files', $portfolio_json, PDO::PARAM_STR);

    $stmt->execute();
    $application_id = $pdo->lastInsertId(); // Get ID if needed

    // --- Optional: Send Admin Notification Email ---
    // (Use PHPMailer for reliability)
    $notify_subject = "New Artisan Application Received - ID: {$application_id}";
    $notify_body = "A new artisan application has been submitted.\n\n" .
                   "Name: {$first_name} {$last_name}\n" .
                   "Email: {$email}\n" .
                   "Business: {$business_name}\n" .
                   "Website: {$website}\n" .
                   "Description: {$craft_description}\n\n" .
                   "Portfolio Files: " . implode(', ', $uploaded_filenames) . "\n\n" .
                   "Please review the application in the admin panel."; // Link to admin if you have one
     $notify_headers = 'From: ' . EMAIL_FROM . "\r\n";
     @mail(ADMIN_EMAIL, $notify_subject, $notify_body, $notify_headers);


    echo json_encode(['success' => true, 'message' => 'Application submitted successfully! We will review it and get back to you.']);

} catch (PDOException $e) {
    error_log("Database Error saving application: " . $e->getMessage());
    // Attempt cleanup on DB error
     foreach ($uploaded_filenames as $filename) { @unlink(UPLOAD_PATH . $filename); }
    echo json_encode(['success' => false, 'message' => 'An error occurred while submitting your application. Please try again.']);
}

exit;
?>