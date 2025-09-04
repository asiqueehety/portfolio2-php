<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        header("Location: contact.php?error=missing_fields");
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: contact.php?error=invalid_email");
        exit();
    }
    
    session_start();
    $user_id = $_SESSION['user_id'] ?? 1; // fallback if not logged in
    include __DIR__ . "/config/db.php";
    if (isset($conn) && $conn instanceof mysqli) {
        $sql = "SELECT email
                FROM contacts 
                WHERE user_id = ? 
                ORDER BY id DESC 
                LIMIT 1";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $user_id);
            if ($stmt->execute()) {
                $res = $stmt->get_result();
                if ($row = $res->fetch_assoc()) {
                    // Merge DB row over fallbacks (so any NULL becomes fallback)
                    $to = $row["email"];
                }
            }
            $stmt->close();
        }
    }
    $email_subject = "Contact Form: " . $subject;
    $email_body = "Name: $name\n";
    $email_body .= "Email: $email\n";
    $email_body .= "Subject: $subject\n\n";
    $email_body .= "Message:\n$message";
    
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    
    // Send email
    if (mail($to, $email_subject, $email_body, $headers)) {
        header("Location: contact.php?success=1");
    } else {
        header("Location: contact.php?error=send_failed");
    }
    exit();
} else {
    header("Location: contact.php");
    exit();
}
?>