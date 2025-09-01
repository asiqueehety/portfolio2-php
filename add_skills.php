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
    $skill_name = trim($_POST['skill_name']);

    // Handle skill image upload
    $skillPicPath = null;
    if (isset($_FILES['skill_pic']) && $_FILES['skill_pic']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['skill_pic']['tmp_name'];
        $ext = pathinfo($_FILES['skill_pic']['name'], PATHINFO_EXTENSION);
        $uniqueName = uniqid('skill_', true) . "." . $ext;
        $uploadDir = __DIR__ . "/assets/images/pro_pics/"; // same folder as profile pics
        $destination = $uploadDir . $uniqueName;

        if (!move_uploaded_file($tmpName, $destination)) {
            $errors[] = "Failed to upload skill picture.";
        } else {
            $skillPicPath = "assets/images/pro_pics/" . $uniqueName;
        }
    }

    if (empty($skill_name)) {
        $errors[] = "Skill name cannot be empty.";
    }

    // Insert into database
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO skills (user_id, skill_name, skill_pic) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $skill_name, $skillPicPath);

        if ($stmt->execute()) {
            $success = "Skill added successfully!";
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
<title>Add Skills</title>
<link rel="stylesheet" href="assets/css/register.css">
<style>
.container { max-width: 500px; margin: 50px auto; }
input[type="text"], input[type="file"] { width: 100%; margin-bottom: 15px; padding: 10px; border-radius: 5px; border: 1px solid #ccc; }
button { padding: 10px 20px; border-radius: 5px; border: none; background: #333; color: #fff; cursor: pointer; }
button:hover { background: #555; }
.errors { color: red; }
.success { color: green; }
</style>
</head>
<body>
<div class="container">
    <h2>Add your skills</h2>

    <?php
    if (!empty($errors)) {
        echo '<div class="errors"><ul>';
        foreach ($errors as $err) { echo "<li>$err</li>"; }
        echo '</ul></div>';
    }
    if ($success) { echo "<div class='success'>$success</div>"; }
    ?>

    <form method="post" enctype="multipart/form-data">
        <label>Skill Name</label>
        <input type="text" name="skill_name" placeholder="e.g., React" required>

        <label>Skill Image</label>
        <input type="file" name="skill_pic" accept="image/png">

        <button type="submit">Add Skill</button>
    </form>

    <p>You can keep adding skills one by one.</p>
    <a href="add_exp.php?user_id=<?php echo $user_id; ?>">
        <button type="button">Next</button>
    </a>

</div>
</body>
</html>
