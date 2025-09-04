<?php 
include __DIR__ . '/includes/navbar.php';
include __DIR__ . "/config/db.php";
$user_id = $_SESSION['user_id']?? 1; // fallback if not logged in
// Fetch user info
$sqlUser = "SELECT username, pro_pic, intro, bio, education FROM users WHERE id = ?";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->bind_param("i", $user_id);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();
$user = $resultUser->fetch_assoc();

// Fallbacks if user data missing
$username = $user['username'] ?? "Asique Ehety";
$pro_pic  = $user['pro_pic'] ?? "assets/images/asique.jpeg";
$intro    = $user['intro'] ?? "Hi, I'm Asique";
$bio      = $user['bio'] ?? "A passionate tech enthusiast with a deep interest in full-stack web and mobile application development; building modern, efficient, and scalable solutions using up-to-date techs. My goal is to create meaningful digital experiences solving real-world problems.";
$education= $user['education'] ?? "Department of Computer Science and Engineering, Khulna University of Engineering and Technology";

// Fetch skills
$skills = [];
$sqlSkills = "SELECT skill_name, skill_pic FROM skills WHERE user_id = ?";
$stmtSkills = $conn->prepare($sqlSkills);
$stmtSkills->bind_param("i", $user_id);
$stmtSkills->execute();
$resultSkills = $stmtSkills->get_result();

if ($resultSkills && $resultSkills->num_rows > 0) {
    while ($row = $resultSkills->fetch_assoc()) {
        $skills[] = [
            'name' => $row['skill_name'],
            'pic'  => $row['skill_pic']
        ];
    }
}

// Fallback skills if database empty
if (empty($skills)) {
    $skills = [
        ['name'=>'Next.js', 'pic'=>'assets/images/nextjs.png'],
        ['name'=>'React', 'pic'=>'assets/images/react.png'],
        ['name'=>'Node.js', 'pic'=>'assets/images/nodejs.png'],
        ['name'=>'Express.js', 'pic'=>'assets/images/expressjs.png'],
        ['name'=>'Javascript', 'pic'=>'assets/images/js.png'],
        ['name'=>'Typescript', 'pic'=>'assets/images/ts.png'],
        ['name'=>'PHP', 'pic'=>'assets/images/php.png'],
        ['name'=>'Laravel', 'pic'=>'assets/images/laravel.png'],
        ['name'=>'ASP.NET', 'pic'=>'assets/images/asp_net.png'],
        ['name'=>'C#', 'pic'=>'assets/images/csharp.png'],
        ['name'=>'MongoDB', 'pic'=>'assets/images/mongodb.png'],
        ['name'=>'PostgreSQL', 'pic'=>'assets/images/postgresql.png'],
        ['name'=>'MySQL', 'pic'=>'assets/images/mysql.png'],
        ['name'=>'Android Studio', 'pic'=>'assets/images/androidstudio.png'],
        ['name'=>'Java', 'pic'=>'assets/images/java.png'],
        ['name'=>'C++', 'pic'=>'assets/images/cpp.png'],
        ['name'=>'C', 'pic'=>'assets/images/c.png'],
        ['name'=>'Python', 'pic'=>'assets/images/python.png'],
        ['name'=>'Autocad', 'pic'=>'assets/images/autocad.png'],
        ['name'=>'Solidworks', 'pic'=>'assets/images/solidworks.png'],
        ['name'=> 'Arduino','pic'=>'assets/images/arduino.png']
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo htmlspecialchars($username); ?>'s Portfolio</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="assets/js/script.js" defer></script>
</head>
<body>
  <div class="main-content">

    <!-- Row 1: Profile + Skills -->
    <section class="hero">
      <div class="profile">
        <img src="<?php echo htmlspecialchars($pro_pic); ?>" alt="<?php echo htmlspecialchars($username); ?>" class="profile-pic">
        
        <h1><?php echo htmlspecialchars($intro); ?></h1>
        <p>working with</p>

        <div class="skills-slider">
          <div class="slide-track">
            <?php foreach($skills as $skill): ?>
              <img src="<?php echo htmlspecialchars($skill['pic']); ?>" alt="<?php echo htmlspecialchars($skill['name']); ?>">
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </section>

    <!-- Row 2: Bio -->
    <section class="bio">
      <h2>About Me</h2>
      <p><?php echo htmlspecialchars($bio); ?></p>
    </section>

    <!-- Row 3: Education -->
    <section class="education">
      <h2>Education</h2>
      <p>Currently enrolled in <b><?php echo htmlspecialchars($education); ?></b></p>
    </section>

  </div>
</body>
</html>
