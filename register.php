<?php
include __DIR__ . "/config/db.php";
$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Get form inputs
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $intro    = trim($_POST['intro']);
    $bio      = trim($_POST['bio']);
    $education= trim($_POST['education']);

    // 2. Validate inputs
    if (empty($username) || empty($password)) {
        $errors[] = "Username and password are required.";
    }

    // 3. Handle profile picture upload
    $profilePicPath = null;
    if (isset($_FILES['pro_pic']) && $_FILES['pro_pic']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['pro_pic']['tmp_name'];
        $ext = pathinfo($_FILES['pro_pic']['name'], PATHINFO_EXTENSION);
        $uniqueName = uniqid('pro_', true) . "." . $ext;
        $uploadDir = __DIR__ . "/assets/images/pro_pics/";
        $destination = $uploadDir . $uniqueName;

        if (!move_uploaded_file($tmpName, $destination)) {
            $errors[] = "Failed to upload profile picture.";
        } else {
            $profilePicPath = "assets/images/pro_pics/" . $uniqueName;
        }
    }

    // 4. If no errors, save to database
    if (empty($errors)) {
        // Hash password securely
        $pw_hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("INSERT INTO users (username, pw_hash, pro_pic, intro, bio, education) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $username, $pw_hash, $profilePicPath, $intro, $bio, $education);

        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;  // Get the newly inserted user's ID
            header("Location: add_skills.php?user_id=$user_id"); // Redirect to add_skills.php
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
    <title>Admin Register</title>
    <link rel="stylesheet" href="assets/css/register.css">
</head>
<body>
<div class="container">
    <h2>Admin Registration</h2>

    <?php
    if (!empty($errors)) {
        echo '<div class="errors"><ul>';
        foreach ($errors as $err) {
            echo "<li>$err</li>";
        }
        echo '</ul></div>';
    }
    if ($success) {
        echo "<div class='success'>$success</div>";
    }
    ?>

    <form method="post" enctype="multipart/form-data">
        <label>Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <label>Profile Picture</label>
        <input type="file" name="pro_pic" accept="image/*">

        <label>Intro</label>
        <input type="text" name="intro" placeholder="Hi, I'm Asique">

        <label>Bio</label>
        <textarea name="bio" rows="5" placeholder="Tell about yourself"></textarea>

        <label>Education</label>
        <input type="text" name="education" placeholder="e.g., Khulna University of Engineering and Technology">

        <div class="buttons">
            <button class="cancelbtn" type="button" onclick="window.location.href='index.php'">Cancel</button>
            <button class="regbutton" type="submit">Next</button>
        </div>

        
    </form>
</div>
</body>
</html>
