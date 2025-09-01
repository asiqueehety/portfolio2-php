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
    $category    = trim($_POST['category']);
    $title       = trim($_POST['title']);
    $link        = trim($_POST['link']);
    $github      = trim($_POST['github']);
    $description = trim($_POST['description']);
    $time        = trim($_POST['time']);

    // Optional: handle project image upload safely
    $projPicPath = null;
    if (isset($_FILES['proj_pic']) && $_FILES['proj_pic']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['proj_pic']['tmp_name'];
        $originalName = $_FILES['proj_pic']['name'];
        $fileSize = $_FILES['proj_pic']['size'];

        // Allowed extensions
        $allowedExt = ['jpg','jpeg','png','gif','webp'];
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt)) {
            $errors[] = "Invalid image format. Allowed: " . implode(", ", $allowedExt);
        } elseif ($fileSize > 2 * 1024 * 1024) { // 2MB limit
            $errors[] = "Image size exceeds 2MB limit.";
        } else {
            // Create upload folder if not exists
            $uploadDir = __DIR__ . "/assets/images/projects/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $uniqueName = uniqid('proj_', true) . "." . $ext;
            $destination = $uploadDir . $uniqueName;

            if (!move_uploaded_file($tmpName, $destination)) {
                $errors[] = "Failed to upload project image.";
            } else {
                $projPicPath = "assets/images/projects/" . $uniqueName;
            }
        }
    }


    // Validation
    if (empty($category) || empty($title)) {
        $errors[] = "Category and Title are required.";
    }

    // Insert into projects table
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO projects (user_id, category, title, link, github, description, time, proj_pic) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssss", $user_id, $category, $title, $link, $github, $description, $time , $projPicPath);

        if ($stmt->execute()) {
            $success = "Project added successfully!";
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
<title>Add Project</title>
<link rel="stylesheet" href="assets/css/register.css">
<style>
.container { max-width: 600px; margin: 50px auto; }
input[type="text"], input[type="file"], textarea { width: 100%; margin-bottom: 15px; padding: 10px; border-radius: 5px; border: 1px solid #ccc; }
button { padding: 10px 20px; border-radius: 5px; border: none; background: #333; color: #fff; cursor: pointer; }
button:hover { background: #555; }
.errors { color: red; }
.success { color: green; }
</style>
</head>
<body>
<div class="container">
    <h2>Add your projects</h2>

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
        <input type="text" name="category" placeholder="e.g., Web Development, App Development" required>

        <label>Title</label>
        <input type="text" name="title" placeholder="Project Title" required>

        <label>Live Link</label>
        <input type="text" name="link" placeholder="https://yourproject.com">

        <label>GitHub Link</label>
        <input type="text" name="github" placeholder="https://github.com/username/repo">

        <label>Description</label>
        <textarea name="description" rows="5" placeholder="Short project description..."></textarea>

        <label>Time (duration)</label>
        <input type="text" name="time" placeholder="e.g., 2 months">

        <label>Project Image (optional)</label>
        <input type="file" name="proj_pic" accept="image/*">

        <button type="submit">Add Project</button>
    </form>

    <p>You can keep adding projects one by one.</p>
    <a href="add_contacts.php?user_id=<?php echo $user_id; ?>"><button>Next</button></a>
</div>
</body>
</html>
