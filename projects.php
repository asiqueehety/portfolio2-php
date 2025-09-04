<?php
include __DIR__ . '/includes/navbar.php';
include __DIR__ . "/config/db.php";

$user_id = $_SESSION['user_id']?? 1; // fallback if not logged in


// Fetch projects from DB
$sql = "SELECT * FROM projects WHERE user_id = ? ORDER BY category, id";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$projects = [];
while ($row = $result->fetch_assoc()) {
    $projects[$row['category']][] = $row; // group by category
}
$stmt->close();

// Fallback projects if DB is empty
if (empty($projects)) {
    $projects = [
        "Web Development" => [
            [
                "title" => "Portfolio Website builder template",
                "time" => "2025",
                "description" => "Built a responsive personal portfolio builder website using HTML, CSS, JS, and PHP, MySQL.",
                "link" => "https://example.com/portfolio",
                "github" => "https://github.com/asiqueehety/portfolio2-php",
                "proj_pic" => null
            ],
            [
                "title" => "FoLo - Found, Lost & co.",
                "time" => "2025",
                "description" => "Created a full-stack found & lost platform application using Next.js, React and MongoDB.",
                "link" => "https://foundlost.vercel.app",
                "github" => "https://github.com/asiqueehety/folo.git",
                "proj_pic" => "assets/images/projects/folo.png"
            ],
            [
                "title" => "Derel'd - Online debating platform.",
                "time" => "2025",
                "description" => "Created a full-stack debating platform application using React, Node.js, Express.js, Javascript, EJS, Vanilla CSS and PostgreSQL.",
                "link" => "https://foundlost.vercel.app",
                "github" => "https://github.com/asiqueehety/dereld.git",
                "proj_pic" => "assets/images/projects/dereld.png"
            ],
            [
                "title" => "Personal portfolio website",
                "time" => "2025",
                "description" => "Built a responsive personal portfolio builder website using Nextjs, React, TailwindCSS, JS, and MongoDB.",
                "link" => "https://asiqueehety.vercel.app",
                "github" => "https://github.com/asiqueehety/asique-port.git",
                "proj_pic" => "assets/images/projects/asiqueport.png"
            ],
        ],
        "App Development" => [
            [
                "title" => "CookTwah - Recipe Finder App",
                "time" => "2024",
                "description" => "Android app built in Java to find recipes based on ingredients and preferences.",
                "link" => "#",
                "github" => "https://github.com/asiqueehety/cooktwah.git",
                "proj_pic" => "assets/images/projects/cooktwah.jpg"
            ]
        ]
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Projects - Portfolio</title>
<link rel="stylesheet" href="assets/css/style.css">
<script src="assets/js/script.js"></script>
<style>
body { font-family: 'font2', sans-serif; }
.container { max-width: 1200px; margin: 30px auto; padding: 0 30px;padding-left: 8vw; }
.container h1{ background-color: #2a1eaca6; border-radius: 10px; padding: 5px; color: white; }
.category-title { display: inline-block; font-size: 28px; font-weight: bold; margin: 40px 0 20px 0; color: #333; border-radius: 5px 5px 4px 4px; background-color: #d7c1ab86; padding: 5px 15px; }
.projects-row { display: flex; flex-wrap: wrap; gap: 20px; }
.proj-box { flex: 1 1 calc(33.33% - 20px); background: #f8f8f8; padding: 20px; border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); transition: transform 0.2s; }
.proj-box:hover { transform: translateY(-5px); }
.proj-title { font-size: 20px; font-weight: bold; margin-bottom: 5px; }
.proj-time { font-size: 14px; color: #666; margin-bottom: 10px; }
.proj-desc { font-size: 16px; color: #333; margin-bottom: 10px; }
.proj-pic { width: 100%; max-height: 150px; object-fit: cover; border-radius: 10px; margin-bottom: 10px; }
.proj-links a { display: inline-block; margin-right: 10px; color: #fff; background: #333; padding: 5px 10px; border-radius: 5px; text-decoration: none; font-size: 14px; }
.proj-links a:hover { background: #555; }
/* Responsive */
@media (max-width: 992px) { .proj-box { flex: 1 1 calc(50% - 20px); } }
@media (max-width: 600px) { .proj-box { flex: 1 1 100%; } .container h1{ margin-left:15px; } }
</style>
</head>
<body>
<div class="container">
    <h1>Projects</h1>

    <?php foreach ($projects as $category => $items) : ?>
        <div class="category">
            <div class="category-title"><?php echo htmlspecialchars($category); ?></div>
            <div class="projects-row">
                <?php foreach ($items as $proj) : ?>
                    <div class="proj-box">
                        <?php if (!empty($proj['proj_pic'])): ?>
                            <img src="<?php echo htmlspecialchars($proj['proj_pic']); ?>" alt="Project" class="proj-pic">
                        <?php endif; ?>
                        <div class="proj-title"><?php echo htmlspecialchars($proj['title']); ?></div>
                        <div class="proj-time"><?php echo htmlspecialchars($proj['time']); ?></div>
                        <div class="proj-desc"><?php echo nl2br(htmlspecialchars($proj['description'])); ?></div>
                        <div class="proj-links">
                            <?php if (!empty($proj['link'])): ?>
                                <a href="<?php echo htmlspecialchars($proj['link']); ?>" target="_blank">Live</a>
                            <?php endif; ?>
                            <?php if (!empty($proj['github'])): ?>
                                <a href="<?php echo htmlspecialchars($proj['github']); ?>" target="_blank">GitHub</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
