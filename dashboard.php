<?php
session_start();
include __DIR__ . "/config/db.php";

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// ------------------------
// Fetch existing data
// ------------------------

// Users
$stmt = $conn->prepare("SELECT username, pro_pic, intro, bio, education FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Skills
$stmt = $conn->prepare("SELECT * FROM skills WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$skills = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Experiences
$stmt = $conn->prepare("SELECT * FROM experiences WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$experiences = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Projects
$stmt = $conn->prepare("SELECT * FROM projects WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$projects = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Contact
$stmt = $conn->prepare("SELECT * FROM contacts WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$contact = $stmt->get_result()->fetch_assoc();
$stmt->close();

// ------------------------
// File upload helper
// ------------------------
function uploadFile($fileInput, $uploadDir = 'uploads/') {
    if (isset($_FILES[$fileInput]) && $_FILES[$fileInput]['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES[$fileInput]['tmp_name'];
        $filename = uniqid() . "_" . basename($_FILES[$fileInput]['name']);
        $targetPath = $uploadDir . $filename;
        if (!file_exists($uploadDir)) mkdir($uploadDir, 0755, true);
        if (move_uploaded_file($tmpName, $targetPath)) {
            return $targetPath;
        }
    }
    return null;
}

// ------------------------
// 1️⃣ Update User Profile
// ------------------------
if (isset($_POST['update_user'])) {
    $username = $_POST['username'] ?? '';
    $intro    = $_POST['intro'] ?? '';
    $bio      = $_POST['bio'] ?? '';
    $education= $_POST['education'] ?? '';

    if (!empty($_POST['password'])) {
        $pw_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET pw_hash=? WHERE id=?");
        $stmt->bind_param("si", $pw_hash, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    $pro_pic_path = uploadFile('pro_pic');
    if ($pro_pic_path) {
        $stmt = $conn->prepare("UPDATE users SET pro_pic=? WHERE id=?");
        $stmt->bind_param("si", $pro_pic_path, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    $stmt = $conn->prepare("UPDATE users SET username=?, intro=?, bio=?, education=? WHERE id=?");
    $stmt->bind_param("ssssi", $username, $intro, $bio, $education, $user_id);
    $stmt->execute();
    $stmt->close();
}

// ------------------------
// 2️⃣ Skills CRUD
// ------------------------
if (isset($_POST['add_skill'])) {
    $skill_name = $_POST['skill_name'] ?? '';
    $skill_pic_path = uploadFile('skill_pic', 'uploads/skills/');
    if ($skill_name && $skill_pic_path) {
        $stmt = $conn->prepare("INSERT INTO skills (user_id, skill_name, skill_pic) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $skill_name, $skill_pic_path);
        $stmt->execute();
        $stmt->close();
    }
}

if (isset($_POST['update_skill'])) {
    $skill_id = $_POST['skill_id'];
    $skill_name = $_POST['skill_name'] ?? '';
    $skill_pic_path = uploadFile('skill_pic', 'uploads/skills/');
    if ($skill_pic_path) {
        $stmt = $conn->prepare("UPDATE skills SET skill_name=?, skill_pic=? WHERE id=? AND user_id=?");
        $stmt->bind_param("ssii", $skill_name, $skill_pic_path, $skill_id, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE skills SET skill_name=? WHERE id=? AND user_id=?");
        $stmt->bind_param("sii", $skill_name, $skill_id, $user_id);
    }
    $stmt->execute();
    $stmt->close();
}

if (isset($_POST['delete_skill'])) {
    $skill_id = $_POST['skill_id'];
    $stmt = $conn->prepare("DELETE FROM skills WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $skill_id, $user_id);
    $stmt->execute();
    $stmt->close();
}

// ------------------------
// 3️⃣ Experiences CRUD (with exp_pic)
// ------------------------
if (isset($_POST['add_experience'])) {
    $category = $_POST['category'] ?? '';
    $name     = $_POST['name'] ?? '';
    $time     = $_POST['time'] ?? '';
    $details  = $_POST['details'] ?? '';
    $exp_pic  = uploadFile('exp_pic', 'uploads/experiences/');

    $stmt = $conn->prepare("INSERT INTO experiences (user_id, category, name, time, details, exp_pic) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $user_id, $category, $name, $time, $details, $exp_pic);
    $stmt->execute();
    $stmt->close();
}

if (isset($_POST['update_experience'])) {
    $exp_id = $_POST['exp_id'];
    $category = $_POST['category'] ?? '';
    $name     = $_POST['name'] ?? '';
    $time     = $_POST['time'] ?? '';
    $details  = $_POST['details'] ?? '';
    $exp_pic  = uploadFile('exp_pic', 'uploads/experiences/');

    if ($exp_pic) {
        $stmt = $conn->prepare("UPDATE experiences SET category=?, name=?, time=?, details=?, exp_pic=? WHERE id=? AND user_id=?");
        $stmt->bind_param("ssssssi", $category, $name, $time, $details, $exp_pic, $exp_id, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE experiences SET category=?, title=?, time=?, details=? WHERE id=? AND user_id=?");
        $stmt->bind_param("ssssii", $category, $name, $time, $details, $exp_id, $user_id);
    }
    $stmt->execute();
    $stmt->close();
}

if (isset($_POST['delete_experience'])) {
    $exp_id = $_POST['exp_id'];
    $stmt = $conn->prepare("DELETE FROM experiences WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $exp_id, $user_id);
    $stmt->execute();
    $stmt->close();
}

// ------------------------
// 4️⃣ Projects CRUD (with proj_pic)
// ------------------------
if (isset($_POST['add_project'])) {
    $category = $_POST['category'] ?? '';
    $name     = $_POST['name'] ?? '';
    $link     = $_POST['link'] ?? '';
    $github   = $_POST['github'] ?? '';
    $description = $_POST['description'] ?? '';
    $time     = $_POST['time'] ?? '';
    $proj_pic = uploadFile('proj_pic', 'uploads/projects/');

    $stmt = $conn->prepare("INSERT INTO projects (user_id, category, title, link, github, description, time, proj_pic) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssss", $user_id, $category, $name, $link, $github, $description, $time, $proj_pic);
    $stmt->execute();
    $stmt->close();
}

if (isset($_POST['update_project'])) {
    $proj_id = $_POST['proj_id'];
    $category = $_POST['category'] ?? '';
    $name     = $_POST['name'] ?? '';
    $link     = $_POST['link'] ?? '';
    $github   = $_POST['github'] ?? '';
    $description = $_POST['description'] ?? '';
    $time     = $_POST['time'] ?? '';
    $proj_pic = uploadFile('proj_pic', 'uploads/projects/');

    if ($proj_pic) {
        $stmt = $conn->prepare("UPDATE projects SET category=?, title=?, link=?, github=?, description=?, time=?, proj_pic=? WHERE id=? AND user_id=?");
        $stmt->bind_param("ssssssssi", $category, $name, $link, $github, $description, $time, $proj_pic, $proj_id, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE projects SET category=?, name=?, link=?, github=?, description=?, time=? WHERE id=? AND user_id=?");
        $stmt->bind_param("ssssssii", $category, $name, $link, $github, $description, $time, $proj_id, $user_id);
    }
    $stmt->execute();
    $stmt->close();
}

if (isset($_POST['delete_project'])) {
    $proj_id = $_POST['proj_id'];
    $stmt = $conn->prepare("DELETE FROM projects WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $proj_id, $user_id);
    $stmt->execute();
    $stmt->close();
}

// ------------------------
// 5️⃣ Contact info
// ------------------------
if (isset($_POST['update_contact'])) {
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $linkedin = $_POST['linkedin'] ?? '';
    $github = $_POST['github'] ?? '';

    $stmt = $conn->prepare("UPDATE contacts SET phone=?, email=?, linkedin=?, github=? WHERE user_id=?");
    $stmt->bind_param("ssssi", $phone, $email, $linkedin, $github, $user_id);
    $stmt->execute();
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Portfolio</title>
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/dashboard.css">

</head>
<body>

<div class="container">
    <h1>Edit Your Portfolio</h1>

    <!-- Profile Section -->
    <div class="section">
        <h2>Profile Info</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter new password">
            </div>
            <div class="form-group">
                <label>Profile Picture</label>
                <?php if(!empty($user['pro_pic'])): ?>
                    <img src="<?= htmlspecialchars($user['pro_pic']) ?>" alt="Profile" width="80"><br>
                <?php endif; ?>
                <input type="file" name="pro_pic">
            </div>
            <div class="form-group">
                <label>Intro</label>
                <input type="text" name="intro" value="<?= htmlspecialchars($user['intro']) ?>">
            </div>
            <div class="form-group">
                <label>Bio</label>
                <textarea name="bio"><?= htmlspecialchars($user['bio']) ?></textarea>
            </div>
            <div class="form-group">
                <label>Education</label>
                <input type="text" name="education" value="<?= htmlspecialchars($user['education']) ?>">
            </div>
            <button type="submit" name="update_user">Save Profile</button>
        </form>
    </div>

    <!-- Skills Section -->
    <!-- Skills Section -->
<div class="section">
    <h2>Skills</h2>
    <div class="flex-list">
        <?php foreach($skills as $skill): ?>
        <div class="item">
            <?php if(!empty($skill['skill_pic'])): ?>
                <img src="<?= htmlspecialchars($skill['skill_pic']) ?>" alt="<?= htmlspecialchars($skill['skill_name']) ?>" width="50"><br>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="skill_id" value="<?= $skill['id'] ?>">
                <input type="text" name="skill_name" value="<?= htmlspecialchars($skill['skill_name']) ?>">
                <input type="file" name="skill_pic">
                <button name="update_skill" type="submit">Update</button>
                <button name="delete_skill" type="submit" style="background:red;">Delete</button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
    <form method="POST" enctype="multipart/form-data" style="margin-top:10px;">
        <input type="text" name="skill_name" placeholder="New skill name">
        <input type="file" name="skill_pic">
        <button type="submit" name="add_skill" class="add-new">Add New Skill</button>
    </form>
</div>

<!-- Experiences Section -->
<div class="section">
    <h2>Experiences</h2>
    <div class="flex-list">
        <?php foreach($experiences as $exp): ?>
        <div class="item">
            <?php if(!empty($exp['exp_pic'])): ?>
                <img src="<?= htmlspecialchars($exp['exp_pic']) ?>" alt="<?= htmlspecialchars($exp['title']) ?>" width="80"><br>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="exp_id" value="<?= $exp['id'] ?>">
                <input type="text" name="category" value="<?= htmlspecialchars($exp['category']) ?>" placeholder="Category">
                <input type="text" name="name" value="<?= htmlspecialchars($exp['title']) ?>" placeholder="Name">
                <input type="text" name="time" value="<?= htmlspecialchars($exp['time']) ?>" placeholder="Time">
                <input type="text" name="details" value="<?= htmlspecialchars($exp['details']) ?>" placeholder="Details">
                <input type="file" name="exp_pic">
                <button name="update_experience" type="submit">Update</button>
                <button name="delete_experience" type="submit" style="background:red;">Delete</button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
    <form method="POST" enctype="multipart/form-data" style="margin-top:10px;">
        <input type="text" name="category" placeholder="Category">
        <input type="text" name="name" placeholder="Name">
        <input type="text" name="time" placeholder="Time">
        <input type="text" name="details" placeholder="Details">
        <input type="file" name="exp_pic">
        <button type="submit" name="add_experience" class="add-new">Add New Experience</button>
    </form>
</div>

<!-- Projects Section -->
<div class="section">
    <h2>Projects</h2>
    <div class="flex-list">
        <?php foreach($projects as $proj): ?>
        <div class="item">
            <?php if(!empty($proj['proj_pic'])): ?>
                <img src="<?= htmlspecialchars($proj['proj_pic']) ?>" alt="<?= htmlspecialchars($proj['title']) ?>" width="80"><br>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="proj_id" value="<?= $proj['id'] ?>">
                <input type="text" name="category" value="<?= htmlspecialchars($proj['category']) ?>" placeholder="Category">
                <input type="text" name="name" value="<?= htmlspecialchars($proj['title']) ?>" placeholder="Name">
                <input type="text" name="link" value="<?= htmlspecialchars($proj['link']) ?>" placeholder="Link">
                <input type="text" name="github" value="<?= htmlspecialchars($proj['github']) ?>" placeholder="Github">
                <input type="text" name="description" value="<?= htmlspecialchars($proj['description']) ?>" placeholder="Description">
                <input type="text" name="time" value="<?= htmlspecialchars($proj['time']) ?>" placeholder="Time">
                <input type="file" name="proj_pic">
                <button name="update_project" type="submit">Update</button>
                <button name="delete_project" type="submit" style="background:red;">Delete</button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
    <form method="POST" enctype="multipart/form-data" style="margin-top:10px;">
        <input type="text" name="category" placeholder="Category">
        <input type="text" name="name" placeholder="Name">
        <input type="text" name="link" placeholder="Link">
        <input type="text" name="github" placeholder="Github">
        <input type="text" name="description" placeholder="Description">
        <input type="text" name="time" placeholder="Time">
        <input type="file" name="proj_pic">
        <button type="submit" name="add_project" class="add-new">Add New Project</button>
    </form>
</div>


    <!-- Contacts Section -->
    <div class="section">
        <h2>Contacts</h2>
        <form method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="text" name="email" value="<?= htmlspecialchars($contact['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($contact['phone'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>LinkedIn</label>
                <input type="text" name="linkedin" value="<?= htmlspecialchars($contact['linkedin'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>GitHub</label>
                <input type="text" name="github" value="<?= htmlspecialchars($contact['github'] ?? '') ?>">
            </div>
            <button type="submit" name="update_contact">Save Contacts</button>
        </form>
    </div>

    <div><a href="index.php">Return to Home</a></div>
    

</div>
</body>
</html>