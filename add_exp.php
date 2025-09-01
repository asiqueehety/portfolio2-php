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
    $category = trim($_POST['category']);
    $title    = trim($_POST['title']);
    $time     = trim($_POST['time']);
    $details  = trim($_POST['details']);

    // Handle experience image upload
    $expPicPath = null;
    if (isset($_FILES['exp_pic']) && $_FILES['exp_pic']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['exp_pic']['tmp_name'];
        $ext = pathinfo($_FILES['exp_pic']['name'], PATHINFO_EXTENSION);
        $uniqueName = uniqid('exp_', true) . "." . $ext;
        $uploadDir = __DIR__ . "/assets/images/experiences/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $destination = $uploadDir . $uniqueName;

        if (!move_uploaded_file($tmpName, $destination)) {
            $errors[] = "Failed to upload experience picture.";
        } else {
            $expPicPath = "assets/images/experiences/" . $uniqueName;
        }
    }

    // Validation
    if (empty($category) || empty($title) || empty($time)) {
        $errors[] = "Category, Title, and Time are required.";
    }

    // Insert into experiences table
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO experiences (user_id, category, title, time, details, exp_pic) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $user_id, $category, $title, $time, $details, $expPicPath);

        if ($stmt->execute()) {
            $success = "Experience added successfully!";
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
<title>Add Experience</title>
<link rel="stylesheet" href="assets/css/register.css">
<style>
.container { max-width: 600px; margin: 50px auto; }
input[type="text"], input[type="file"], textarea, select { width: 100%; margin-bottom: 15px; padding: 10px; border-radius: 5px; border: 1px solid #ccc; }
button { padding: 10px 20px; border-radius: 5px; border: none; background: #333; color: #fff; cursor: pointer; }
button:hover { background: #555; }
.errors { color: red; }
.success { color: green; }
</style>
</head>
<body>
<div class="container">
    <h2>Add your experiences</h2>

    <?php
    if (!empty($errors)) {
        echo '<div class="errors"><ul>';
        foreach ($errors as $err) { echo "<li>$err</li>"; }
        echo '</ul></div>';
    }
    if ($success) { echo "<div class='success'>$success</div>"; }
    ?>

    <form method="post" enctype="multipart/form-data">
        <label>Category</label>
        <input type="text" name="category" placeholder="e.g, Problem solving" required>

        <label>Title</label>
        <input type="text" name="title" placeholder="Experience Title" required>

        <label>Time (duration)</label>
        <input type="text" name="time" placeholder="e.g, 3 months" required>

        <label>Details</label>
        <textarea name="details" rows="5" placeholder="Worked with developing 5 apps"></textarea>

        <label>Experience Image</label>
        <input type="file" name="exp_pic" accept="image/*">

        <button type="submit">Add Experience</button>
    </form>

    <p>You can keep adding experiences one by one.</p>
    <a href="add_projects.php?user_id=<?php echo $user_id; ?>"><button>Next</button></a>
</div>
</body>
</html>
