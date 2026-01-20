<?php
// assets/php/contact_submit.php
require_once __DIR__ . '/db_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

// Helper function to trim and sanitize basic text
function clean_input($key)
{
    return isset($_POST[$key]) ? trim($_POST[$key]) : '';
}

// Get form data
$page       = clean_input('page');
$name       = clean_input('name');
$email      = clean_input('email');
$phone      = clean_input('phone');
$service    = clean_input('service');
$department = clean_input('department'); // New
$subject    = clean_input('subject');
$company    = clean_input('company');
$timeline   = clean_input('timeline');
$property   = clean_input('property');   // Custom for Real Estate
$message    = clean_input('message');

if ($property) {
    $message = "Property/Details: " . $property . "\n\n" . $message;
}

// Basic server-side validation
$errors = [];

if ($name === '') {
    $errors[] = "Name is required.";
}
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Valid email is required.";
}
// Service is not required for Team page (which uses department)
if ($page === 'services' && $service === '') {
    $errors[] = "Please select a service.";
}
if ($message === '') {
    $errors[] = "Message is required.";
}

if (!empty($errors)) {
    echo "<h2>There were some problems with your submission:</h2>";
    echo "<ul>";
    foreach ($errors as $error) {
        echo "<li>" . htmlspecialchars($error) . "</li>";
    }
    echo "</ul>";
    echo "<p><a href=\"javascript:history.back()\">Go back</a></p>";
    exit;
}

// Extra info (optional)
$ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;

// Insert into database
try {
    $stmt = $pdo->prepare("
        INSERT INTO contact_messages 
            (page, name, email, phone, service, department, subject, company, budget, timeline, message, ip_address, user_agent)
        VALUES 
            (:page, :name, :email, :phone, :service, :department, :subject, :company, :budget, :timeline, :message, :ip_address, :user_agent)
    ");

    $stmt->execute([
        ':page'       => $page,
        ':name'       => $name,
        ':email'      => $email,
        ':phone'      => $phone,
        ':service'    => $service,
        ':department' => $department,
        ':subject'    => $subject,
        ':company'    => $company,
        ':budget'     => $budget,
        ':timeline'   => $timeline,
        ':message'    => $message,
        ':ip_address' => $ip_address,
        ':user_agent' => $user_agent,
    ]);

    // Send Email to Admin
    $to = "admin@leomarketing.com";
    $email_subject = "New Contact from $page: " . ($subject ?: $name);
    $email_body = "You have received a new message from $name.\n\n" .
        "Page: $page\n" .
        "Service: $service\n" .
        "Department: $department\n" .
        "Email: $email\n" .
        "Phone: $phone\n" .
        "Company: $company\n" .
        "Budget: $budget\n" .
        "Timeline: $timeline\n\n" .
        "Message:\n$message";
    $headers = "From: noreply@leomarketing.com";

    // Uncomment line below if you have a mail server set up
    // mail($to, $email_subject, $email_body, $headers);

    // ⭐ Redirect user based on page
    if ($page === 'team') {
        header("Location: ../../team.html?success=1");
    } elseif ($page === 'services' || strpos($page, 'service-') === 0) {
        // Redirect back to the specific service page if possible, or generic services
        $redirect_page = ($page === 'services') ? 'services.html' : $page . '.html';
        // Safety check if file exists, otherwise default to contact
        if (file_exists("../../" . $redirect_page)) {
            header("Location: ../../" . $redirect_page . "?success=1");
        } else {
            header("Location: ../../services.html?success=1");
        }
    } else {
        header("Location: ../../contact.html?success=1");
    }
    exit;
} catch (PDOException $e) {
    // Log error in real projects
    echo "<h2>Something went wrong while saving your message.</h2>";
    echo "<p>Please try again later.</p>";
    // For debugging ONLY (remove in production):
    // echo "<pre>" . $e->getMessage() . "</pre>";
    exit;
}
