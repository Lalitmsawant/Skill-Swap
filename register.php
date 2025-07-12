<?php
// Handle registration
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  include 'php/db.php';

  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $password = $_POST['password'];
  $confirm = $_POST['confirm_password'];

  if ($password !== $confirm) {
    $error = "Passwords do not match.";
  } else {
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
      $error = "Email already registered.";
    } else {
      $hashed = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
      $stmt->bind_param("sss", $name, $email, $hashed);
      if ($stmt->execute()) {
        header("Location: login.php?success=1");
        exit;
      } else {
        $error = "Something went wrong.";
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <header class="header">
    <h1 class="logo">Skill Swap Platform</h1>
    <a href="index.html" class="home-link">Home</a>
  </header>
  <main class="login-container">
    <form method="POST" class="login-form">
      <?php if (!empty($error)) echo "<div style='color:red;'>$error</div>"; ?>
      <label>Full Name</label>
      <input type="text" name="name" required>

      <label>Email</label>
      <input type="email" name="email" required>

      <label>Password</label>
      <input type="password" name="password" required minlength="6">

      <label>Confirm Password</label>
      <input type="password" name="confirm_password" required minlength="6">

      <button class="btn">Register</button>
      <p class="forgot">Already registered? <a href="login.php">Login</a></p>
    </form>
  </main>
</body>
</html>