<?php
session_start();
require_once "config/db.php";

// Initialize variables
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id, pw_hash FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $pw_hash);
            $stmt->fetch();

            if (password_verify($password, $pw_hash)) {
                // Login success: set session
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $username;

                header("Location: index.php");
                exit;
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "Username not found.";
        }
        $stmt->close();
    } else {
        $error = "Please fill all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login</title>
<link rel="stylesheet" href="assets/css/style.css">
<style>
  body { font-family: 'font2', sans-serif; background: #f4f6f8; margin: 0; }
  .login-container { max-width: 400px; margin: 80px auto; padding: 30px; background: #fff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
  .login-container h2 { text-align: center; margin-bottom: 20px; color: #333; }
  .login-container input 
  { 
    width: 100%; 
    padding: 10px; 
    margin: 10px 0; 
    border-radius: 5px; 
    border: 1px solid #ccc; 
    box-sizing: border-box;
}
  .btn-group { display: flex; justify-content: space-between; align-items: center; }
  .btn { padding: 12px 20px; border: none; border-radius: 6px; color: #fff; font-weight: 600; cursor: pointer; }
  .btn-login { background: #1d4ed8; } /* blue */
  .btn-login:hover { background: #2563eb; }
  .btn-cancel { background: #dc2626; } /* red */
  .btn-cancel:hover { background: #ef4444; }
  .center-link { text-align: center; margin: 15px 0; }
  .center-link a { text-decoration: none; color: #1d4ed8; font-weight: 600; }
  .center-link a:hover { text-decoration: underline; }
  .error { color: red; text-align: center; margin-bottom: 15px; }
</style>
</head>
<body>

<div class="login-container">
  <h2>Admin login</h2>

  <?php if(!empty($error)): ?>
    <p class="error"><?php echo $error; ?></p>
  <?php endif; ?>

  <form method="post" action="login.php">
    <label>Username</label>
    <input type="text" name="username" required>
    <label>Password</label>
    <input type="password" name="password" required>

    <div class="btn-group">
      <button type="button" class="btn btn-cancel" onclick="window.location.href='index.php'">Cancel</button>
      <button type="submit" class="btn btn-login">Login</button>
    </div>

    <div class="center-link">
      <a href="register.php">Create Account</a>
    </div>
  </form>
</div>

</body>
</html>
