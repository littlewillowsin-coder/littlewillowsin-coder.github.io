<?php
/**
 * Little Willows Academy - Contact Form Email Handler
 * 
 * This script processes contact form submissions and sends emails
 * to the school administration.
 */

// Configuration
$to_email = "info@littlewillows.in"; // Primary recipient
$cc_email = ""; // Optional CC email
$subject_prefix = "[Little Willows] "; // Email subject prefix

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    http_response_code(405);
    die("Method not allowed");
}

// Rate limiting (simple implementation)
session_start();
$rate_limit_time = 60; // seconds
$current_time = time();

if (isset($_SESSION['last_submission'])) {
    $time_diff = $current_time - $_SESSION['last_submission'];
    if ($time_diff < $rate_limit_time) {
        http_response_code(429);
        die("Please wait before submitting again.");
    }
}

// Sanitize and validate input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Get and validate form data
$name = isset($_POST['name']) ? sanitize_input($_POST['name']) : '';
$email = isset($_POST['email']) ? sanitize_input($_POST['email']) : '';
$phone = isset($_POST['phone']) ? sanitize_input($_POST['phone']) : '';
$child_name = isset($_POST['child_name']) ? sanitize_input($_POST['child_name']) : 'Not provided';
$child_age = isset($_POST['child_age']) ? sanitize_input($_POST['child_age']) : 'Not provided';
$program = isset($_POST['program']) ? sanitize_input($_POST['program']) : 'Not specified';
$inquiry_type = isset($_POST['inquiry_type']) ? sanitize_input($_POST['inquiry_type']) : 'General inquiry';
$message = isset($_POST['message']) ? sanitize_input($_POST['message']) : '';
$consent = isset($_POST['consent']) ? true : false;

// Validation
$errors = [];

if (empty($name)) {
    $errors[] = "Name is required";
}

if (empty($email) || !validate_email($email)) {
    $errors[] = "Valid email is required";
}

if (empty($phone)) {
    $errors[] = "Phone number is required";
}

if (empty($message)) {
    $errors[] = "Message is required";
}

if (!$consent) {
    $errors[] = "Consent is required";
}

// If validation fails, return error
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// Prepare email content
$email_subject = $subject_prefix . "New Contact Form Submission - " . $inquiry_type;

$email_body = "==============================================\n";
$email_body .= "LITTLE WILLOWS ACADEMY - NEW INQUIRY\n";
$email_body .= "==============================================\n\n";

$email_body .= "PARENT/GUARDIAN INFORMATION:\n";
$email_body .= "----------------------------\n";
$email_body .= "Name: " . $name . "\n";
$email_body .= "Email: " . $email . "\n";
$email_body .= "Phone: " . $phone . "\n\n";

$email_body .= "CHILD INFORMATION:\n";
$email_body .= "----------------------------\n";
$email_body .= "Child's Name: " . $child_name . "\n";
$email_body .= "Child's Age: " . $child_age . "\n";
$email_body .= "Program of Interest: " . $program . "\n\n";

$email_body .= "INQUIRY DETAILS:\n";
$email_body .= "----------------------------\n";
$email_body .= "Inquiry Type: " . $inquiry_type . "\n\n";

$email_body .= "MESSAGE:\n";
$email_body .= "----------------------------\n";
$email_body .= $message . "\n\n";

$email_body .= "==============================================\n";
$email_body .= "Submitted on: " . date('d-m-Y H:i:s') . "\n";
$email_body .= "IP Address: " . $_SERVER['REMOTE_ADDR'] . "\n";
$email_body .= "==============================================\n";

// Email headers
$headers = "From: " . $email . "\r\n";
$headers .= "Reply-To: " . $email . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

if (!empty($cc_email)) {
    $headers .= "\r\nCc: " . $cc_email;
}

// Send email
$mail_sent = mail($to_email, $email_subject, $email_body, $headers);

if ($mail_sent) {
    // Update rate limiting
    $_SESSION['last_submission'] = $current_time;
    
    // Send auto-reply to user
    $auto_reply_subject = "Thank you for contacting Little Willows Academy";
    $auto_reply_body = "Dear " . $name . ",\n\n";
    $auto_reply_body .= "Thank you for your inquiry about Little Willows Academy!\n\n";
    $auto_reply_body .= "We have received your message and will respond within 24 hours. ";
    $auto_reply_body .= "If your inquiry is urgent, please feel free to call us at 082816 22127.\n\n";
    $auto_reply_body .= "Here's a summary of your inquiry:\n";
    $auto_reply_body .= "- Inquiry Type: " . $inquiry_type . "\n";
    $auto_reply_body .= "- Program Interest: " . $program . "\n\n";
    $auto_reply_body .= "We look forward to welcoming you to our campus!\n\n";
    $auto_reply_body .= "Warm regards,\n";
    $auto_reply_body .= "Little Willows Academy Team\n\n";
    $auto_reply_body .= "---\n";
    $auto_reply_body .= "Little Willows Academy\n";
    $auto_reply_body .= "Vengalloor-Mangattukavala Bypass\n";
    $auto_reply_body .= "Thodupuzha, Kerala 685605\n";
    $auto_reply_body .= "Phone: 082816 22127\n";
    $auto_reply_body .= "Email: info@littlewillows.in\n";
    $auto_reply_body .= "Website: www.littlewillows.in\n";
    
    $auto_reply_headers = "From: info@littlewillows.in\r\n";
    $auto_reply_headers .= "Reply-To: info@littlewillows.in\r\n";
    $auto_reply_headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    mail($email, $auto_reply_subject, $auto_reply_body, $auto_reply_headers);
    
    // Return success response
    http_response_code(200);
    
    // Redirect to thank you page or show success message
    if (isset($_SERVER['HTTP_REFERER'])) {
        header("Location: " . $_SERVER['HTTP_REFERER'] . "?success=1");
    } else {
        echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Thank You - Little Willows Academy</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            text-align: center;
            background: #FFF9F0;
        }
        .success-box {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        h1 { color: #2c5e4a; }
        .checkmark {
            font-size: 64px;
            color: #A8D8B9;
            margin-bottom: 20px;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 30px;
            background: #A8D8B9;
            color: white;
            text-decoration: none;
            border-radius: 25px;
        }
    </style>
</head>
<body>
    <div class='success-box'>
        <div class='checkmark'>✓</div>
        <h1>Thank You!</h1>
        <p>Your message has been sent successfully.</p>
        <p>We'll get back to you within 24 hours at:<br><strong>" . htmlspecialchars($email) . "</strong></p>
        <a href='index.html'>Return to Home</a>
    </div>
</body>
</html>";
    }
} else {
    // Email sending failed
    http_response_code(500);
    echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Error - Little Willows Academy</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            text-align: center;
            background: #FFF9F0;
        }
        .error-box {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        h1 { color: #d32f2f; }
        .error-icon {
            font-size: 64px;
            color: #d32f2f;
            margin-bottom: 20px;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 30px;
            background: #2c5e4a;
            color: white;
            text-decoration: none;
            border-radius: 25px;
        }
    </style>
</head>
<body>
    <div class='error-box'>
        <div class='error-icon'>✗</div>
        <h1>Oops! Something went wrong</h1>
        <p>We couldn't send your message at this time.</p>
        <p>Please try again or contact us directly:</p>
        <p><strong>Phone:</strong> 082816 22127<br>
        <strong>Email:</strong> info@littlewillows.in</p>
        <a href='contact.html'>Try Again</a>
    </div>
</body>
</html>";
}
?>
