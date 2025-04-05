<?php
// php/process-contact.php
ini_set('display_errors', 1); // Remove for production
error_reporting(E_ALL);     // Remove for production

require_once __DIR__ . '/config.php';

header('Content-Type: application/json'); // Response will be JSON

// --- Basic Validation & Security ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Honeypot check
if (!empty($_POST['honeypot'])) {
    // Likely a bot, silently succeed or log it, don't send email
    echo json_encode(['success' => true, 'message' => 'Thank you for your message!']);
    exit;
}

// --- Get and Sanitize Data ---
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$subject = isset($_POST['subject']) ? trim($_POST['subject']) : ''; // Optional
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// --- Server-Side Validation ---
$errors = [];
if (empty($name)) {
    $errors[] = 'Name is required.';
}
if (empty($email)) {
    $errors[] = 'Email is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format.';
}
if (empty($message)) {
    $errors[] = 'Message is required.';
}
// Add length checks if needed

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

// --- Sanitize for Output/Email (HTML entities) ---
$name_safe = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
$email_safe = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
$subject_safe = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
$message_safe = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
$message_nl2br = nl2br($message_safe); // Preserve line breaks in HTML email

// --- Prepare Email ---
$to = ADMIN_EMAIL;
$email_subject = "Contact Form Submission: " . ($subject_safe ?: 'No Subject'); // Use subject or default

$email_body = "<html><body>";
$email_body .= "<h2>Contact Form Submission</h2>";
$email_body .= "<p><strong>Name:</strong> {$name_safe}</p>";
$email_body .= "<p><strong>Email:</strong> {$email_safe}</p>";
if ($subject_safe) {
    $email_body .= "<p><strong>Subject:</strong> {$subject_safe}</p>";
}
$email_body .= "<hr>";
$email_body .= "<p><strong>Message:</strong></p>";
$email_body .= "<p>{$message_nl2br}</p>";
$email_body .= "</body></html>";

// Basic Headers (For HTML Email) - Improve with PHPMailer for production
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= 'From: ' . EMAIL_FROM . "\r\n";
$headers .= 'Reply-To: ' . $email_safe . "\r\n"; // Allow replying directly to the sender

// --- Send Email ---
// Use @ to suppress default warnings/errors - we handle it below
$mail_sent = @mail($to, $email_subject, $email_body, $headers);

// --- Respond to Client ---
if ($mail_sent) {
    echo json_encode(['success' => true, 'message' => 'Thank you for your message! We will get back to you soon.']);
} else {
    // Log the error server-side if possible
    error_log("Contact form mail() failed. To: $to, Subject: $email_subject");
    echo json_encode(['success' => false, 'message' => 'Sorry, there was an error sending your message. Please try again later or contact us directly.']);
}

exit;
?>