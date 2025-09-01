<?php
include __DIR__ . "/config/db.php";

// Get user_id from URL
if (!isset($_GET['user_id'])) {
    die("User ID not specified.");
}
$user_id = intval($_GET['user_id']);

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone    = trim($_POST['phone']);
    $email    = trim($_POST['email']);
    $linkedin = trim($_POST['linkedin']);
    $github   = trim($_POST['github']);

    // Validation: optional, can add more
    if (empty($phone) && empty($email) && empty($linkedin) && empty($github)) {
        $errors[] = "Please provide at least one contact detail.";
    }

    // Insert into contacts table
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO contacts (user_id, phone, email, linkedin, github) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $phone, $email, $linkedin, $github);

        if ($stmt->execute()) {
            header("Location: index.php?user_id=$user_id"); // Redirect to add_skills.php
            exit;
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Contact</title>
<link rel="stylesheet" href="assets/css/register.css">
<style>
.container { max-width: 600px; margin: 50px auto; }
input[type="text"], input[type="email"] { width: 100%; margin-bottom: 15px; padding: 10px; border-radius: 5px; border: 1px solid #ccc; }
button { padding: 10px 20px; border-radius: 5px; border: none; background: #333; color: #fff; cursor: pointer; }
button:hover { background: #555; }
.errors { color: red; }
.success { color: green; }
</style>
</head>
<body>
<div class="container">
    <h2>Add your contact info</h2>

    <?php
    if (!empty($errors)) {
        echo '<div class="errors"><ul>';
        foreach ($errors as $err) { echo "<li>$err</li>"; }
        echo '</ul></div>';
    }
    if ($success) { echo "<div class='success'>$success</div>"; }
    ?>

    <form method="post">
        <label>Phone</label>
        <input type="text" name="phone" placeholder="e.g., +8801XXXXXXXXX">

        <label>Email</label>
        <input type="email" name="email" placeholder="your@email.com">

        <label>LinkedIn</label>
        <input type="text" name="linkedin" placeholder="LinkedIn profile URL">

        <label>GitHub</label>
        <input type="text" name="github" placeholder="GitHub profile URL">

        <button type="submit">Complete</button>
    </form>
</div>
</body>
</html>
