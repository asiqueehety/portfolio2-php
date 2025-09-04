<?php
// contact.php

// Include navbar and DB
include __DIR__ . '/includes/navbar.php';
include __DIR__ . '/config/db.php';

// Set which user's contacts to show (make dynamic later if you have auth/session)

$user_id = $_SESSION['user_id']?? 1; // fallback if not logged in
// Fallback values if DB has no contacts for this user
$fallback = [
    'phone'    => '+880 1896-121096',
    'email'    => 'asique228@gmail.com',
    'linkedin' => 'https://www.linkedin.com/in/asique96',
    'github'   => 'https://github.com/username',
];

// Try to fetch from DB
$contact = $fallback;
if (isset($conn) && $conn instanceof mysqli) {
    $sql = "SELECT phone, email, linkedin, github 
            FROM contacts 
            WHERE user_id = ? 
            ORDER BY id DESC 
            LIMIT 1";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $res = $stmt->get_result();
            if ($row = $res->fetch_assoc()) {
                // Merge DB row over fallbacks (so any NULL becomes fallback)
                $contact = array_merge($fallback, array_filter($row, fn($v) => $v !== null && $v !== ''));
            }
        }
        $stmt->close();
    }
}

// Build a display array for cards
$cards = [
    ['label' => 'Email',   'key' => 'email',   'icon' => 'assets/images/icons/gmail.svg'],
    ['label' => 'Phone',   'key' => 'phone',   'icon' => 'assets/images/icons/phone.svg'],
    ['label' => 'LinkedIn','key' => 'linkedin','icon' => 'assets/images/icons/linkedin.svg'],
    ['label' => 'GitHub',  'key' => 'github',  'icon' => 'assets/images/icons/github.svg'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Contact</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="assets/css/style.css" />
<script src="assets/js/script.js"></script>
<style>
  body { font-family: 'font2', sans-serif; background: #f4f6f8; margin: 0; }
  .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
  .container h1 { background-color: #920f39ff; border-radius: 10px; padding: 8px 12px; color: #fff; }
  .contact-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px; margin-top: 30px;
  }
  .contact-card {
    background: #fff; border-radius: 14px; padding: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    transition: transform .2s ease;
  }
  .contact-card:hover { transform: translateY(-4px); }
  .card-icon {
    width: 40px;
    height: 40px;
    margin-bottom: 12px;
    display: block;
    }

  .card-title { font-weight: 700; font-size: 16px; color: #333; margin-bottom: 6px; }
  .card-value a { color: #1f2937; text-decoration: none; word-break: break-all; }
  .card-value a:hover { text-decoration: underline; }
  .contact-form {
    background: #fff; border-radius: 14px; padding: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    margin-top: 35px;
  }
  .contact-form h2 { margin: 0 0 15px; color: #333; }
  .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
  .form-group { margin-bottom: 14px; }
  label { display: block; font-weight: 600; margin-bottom: 6px; color: #374151; }
  input[type="text"], input[type="email"], textarea {
    width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: #fafafa;
  }
  textarea { min-height: 120px; resize: vertical; }
  .btn {
    display: inline-block; background: #333; color: #fff; border: none;
    padding: 10px 18px; border-radius: 8px; font-weight: 600; cursor: pointer;
  }
  .btn:hover { background: #555; }

  /* Responsive */
  @media (max-width: 1024px) {
    .contact-grid { grid-template-columns: repeat(2, 1fr); }
  }
  @media (max-width: 640px) {
    .contact-grid { grid-template-columns: 1fr; }
    .form-row { grid-template-columns: 1fr; }
    .container h1{ margin-left:15px; }
  }
</style>
</head>
<body>
  <div class="main-content">
    <div class="container">
      <h1>Contact</h1>

      <!-- Contact Info Cards -->
      <div class="contact-grid">
        <?php foreach ($cards as $c): 
          $val = $contact[$c['key']] ?? '';
          if (!$val) continue;
          // Make links clickable for URLs, mailto for email, tel for phone
          $display = htmlspecialchars($val);
          $isUrl = filter_var($val, FILTER_VALIDATE_URL);
          if ($c['key'] === 'email') {
              $display = '<a href="mailto:'.htmlspecialchars($val).'">'.htmlspecialchars($val).'</a>';
          } elseif ($c['key'] === 'phone') {
              $tel = preg_replace('/\s+/', '', $val);
              $display = '<a href="tel:'.htmlspecialchars($tel).'">'.htmlspecialchars($val).'</a>';
          } elseif ($c['key'] === 'linkedin') {
              $display = '<a href="'.htmlspecialchars($val).'" target="_blank" rel="noopener">'.htmlspecialchars($val).'</a>';
          } elseif ($c['key'] === 'github') {
              $display = '<a href="'.htmlspecialchars($val).'" target="_blank" rel="noopener">'.htmlspecialchars($val).'</a>';
          }
        ?>
          <div class="contact-card">
            <img class="card-icon" src="<?= htmlspecialchars($c['icon']); ?>" alt="<?= htmlspecialchars($c['label']); ?> icon" />
            <div class="card-title"><?= htmlspecialchars($c['label']); ?></div>
            <div class="card-value"><?= $display; ?></div>
          </div>

        <?php endforeach; ?>
      </div>

      <!-- Contact Form -->
      <div class="contact-form">
        <h2>Send a Message</h2>
        <form action="send_message.php" method="post">
          <div class="form-row">
            <div class="form-group">
              <label for="name">Your Name</label>
              <input type="text" id="name" name="name" required />
            </div>
            <div class="form-group">
              <label for="email">Your Email</label>
              <input type="email" id="email" name="email" required />
            </div>
          </div>
          <div class="form-group">
            <label for="subject">Subject</label>
            <input type="text" id="subject" name="subject" required />
          </div>
          <div class="form-group">
            <label for="message">Your Message</label>
            <textarea id="message" name="message" required></textarea>
          </div>
          <button type="submit" class="btn">Send</button>
        </form>
      </div>

    </div>
  </div>
</body>
</html>
