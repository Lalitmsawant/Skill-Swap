<?php
session_start();
include 'php/db.php';

if (!isset($_SESSION['reset_email'])) {
  header("Location: forgot-password.php");
  exit;
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $newPassword = $_POST['new_password'];
  $confirmPassword = $_POST['confirm_password'];

  if ($newPassword !== $confirmPassword) {
    $error = "Passwords do not match.";
  } elseif (strlen($newPassword) < 6) {
    $error = "Password must be at least 6 characters.";
  } else {
    $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $hashed, $_SESSION['reset_email']);
    if ($stmt->execute()) {
      unset($_SESSION['reset_email']); // clear reset session
      header("Location: login.php?success=1");
      exit;
    } else {
      $error = "Failed to reset password. Please try again.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <header class="header">
    <h1 class="logo">Skill Swap Platform</h1>
    <a href="login.php" class="home-link">Back to Login</a>
  </header>
  <main class="login-container">
    <form method="POST" class="login-form">
      <?php if (!empty($error)) echo "<div style='color:red;'>$error</div>"; ?>

      <label for="new_password">New Password</label>
      <input type="password" name="new_password" required minlength="6">

      <label for="confirm_password">Confirm New Password</label>
      <input type="password" name="confirm_password" required minlength="6">

      <button class="btn">Reset Password</button>
    </form>
  </main>
</body>
</html>
