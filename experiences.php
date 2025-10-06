<?php
include __DIR__ . '/includes/navbar.php';
include __DIR__ . "/config/db.php";


$user_id = $_SESSION['user_id']?? 1; // fallback if not logged in
// Fetch experiences from DB
$sql = "SELECT * FROM experiences WHERE user_id = ? ORDER BY category, id";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$experiences = [];
while ($row = $result->fetch_assoc()) {
    $experiences[$row['category']][] = $row; // group by category
}
$stmt->close();

// Fallback experiences if DB is empty
if (empty($experiences)) {
    $experiences = [
        "Achievements" => [
            [
                "title" => "Won 3rd prize at BNMPC Science Fest 2016",
                "time" => "2016",
                "details" => "Built a project using Arduino UNO, named 'Home Automation System' in a team of 2. We won in the project display category.",
                "exp_pic" => "assets/images/experiences/sci_pro.jpg"
            ],
            [
                "title" => "Won 1st prize at Spelling Bee Contest 2014",
                "time" => "2014",
                "details" => "Won the Interschool English Spelling Bee Contest 2014.",
                "exp_pic" => "assets/images/experiences/spellingbee.jpg"
            ]
        ],
        "Problem solving" => [
            [
                "title" => "LeetCode",
                "time" => "2024 - ongoing",
                "details" => "Solved 100+ problems",
                "exp_pic" => "assets/images/experiences/leetcode.png"
            ],
            [
                "title" => "CodeForces",
                "time" => "2023 - ongoing",
                "details" => "Solved 200+ problems",
                "exp_pic" => "assets/images/experiences/cf.jpg"
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

<title>Experiences - Portfolio</title>
<link rel="stylesheet" href="assets/css/style.css">
<script src="assets/js/script.js"></script>
<style>
body { font-family: 'font2', sans-serif; }
.container { max-width: 1200px; margin: 30px auto; padding: 0 30px; }
.container h1{ background-color: #686161ff; border-radius: 10px; padding: 5px; color: white;}
.category-title { display: inline-block; font-size: 28px; font-weight: bold; margin: 40px 0 20px 0; color: #333; border-radius: 5px 5px 4px; background-color: #d7c1ab86;}
.experiences-row { display: flex; flex-wrap: wrap; gap: 20px; }
.exp-box { flex: 1 1 calc(33.33% - 20px); background: #f8f8f8; padding: 20px; border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); transition: transform 0.2s; }
.exp-box:hover { transform: translateY(-5px); }
.exp-title { font-size: 20px; font-weight: bold; margin-bottom: 5px; }
.exp-time { font-size: 14px; color: #666; margin-bottom: 10px; }
.exp-details { font-size: 16px; color: #333; }
.exp-pic { width: 100%; max-height: 150px; object-fit: cover; border-radius: 10px; margin-bottom: 10px; }
/* Responsive */
@media (max-width: 992px) { .exp-box { flex: 1 1 calc(50% - 20px); } }
@media (max-width: 768px) { .exp-box { flex: 1 1 100%; } }
</style>
</head>
<body>
<div class="main-content">
<div class="container">
    <h1>Experiences</h1>

    <?php foreach ($experiences as $category => $items) : ?>
        <div class="category">
            <div class="category-title"><?php echo htmlspecialchars($category); ?></div>
            <div class="experiences-row">
                <?php foreach ($items as $exp) : ?>
                    <div class="exp-box">
                        <?php if (!empty($exp['exp_pic'])): ?>
                            <img src="<?php echo htmlspecialchars($exp['exp_pic']); ?>" alt="Experience" class="exp-pic">
                        <?php endif; ?>
                        <div class="exp-title"><?php echo htmlspecialchars($exp['title']); ?></div>
                        <div class="exp-time"><?php echo htmlspecialchars($exp['time']); ?></div>
                        <div class="exp-details"><?php echo nl2br(htmlspecialchars($exp['details'])); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
</div>
</body>
</html>
