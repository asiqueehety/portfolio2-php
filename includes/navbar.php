<?php
// includes/navbar.php

include __DIR__ . "/../config/db.php";

$user_id = 1; // or get from session

$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$username = "Asique Ehety"; // fallback
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $username = $row["username"] ?? $username;
}

$stmt->close();

?>
<nav class="sidebar">
    <div class="logo"><?php echo htmlspecialchars($username); ?></div>
    <ul class="nav-links">
        <li><a href="index.php">About me</a></li>
        <li><a href="experiences.php">Experiences</a></li>
        <li><a href="projects.php">Projects</a></li>
        <li><a href="contact.php">Contact</a></li>
    </ul>
    <div class="admin-link">
        <a href="login.php">Admin Login / Register</a>
    </div>
    <div class="menu-toggle" onclick="toggleMenu()">☰</div>
</nav>
