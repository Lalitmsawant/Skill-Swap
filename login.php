<?php
session_start();
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  include 'php/db.php';
  $email = trim($_POST['email']);
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($userId, $hashedPassword);
  if ($stmt->num_rows === 1) {
    $stmt->fetch();
    if (password_verify($password, $hashedPassword)) {
      $_SESSION['user_id'] = $userId;
      header("Location: home.php");
      exit;
    } else {
      $error = "Invalid password.";
    }
  } else {
    $error = "No user found.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
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
      <?php if (isset($_GET['success'])) echo "<div style='color:green;'>Registration successful. Please log in.</div>"; ?>

      <label>Email</label>
      <input type="email" name="email" required>

      <label>Password</label>
      <input type="password" name="password" required>

      <button class="btn">Login</button>
      <p class="forgot">Don't have an account? <a href="register.php">Register here</a></p>
      <p class="forgot">
   <a href="#" onclick="handleForgotPassword()">Forgot your password?</a>
</p>

    </form>
  </main>
  <script>
function handleForgotPassword() {
  const emailField = document.querySelector('input[name="email"]');
  const email = emailField.value.trim();

  if (email === "") {
    alert("Please enter your email first.");
  } else {
    window.location.href = "forgot-password.php?email=" + encodeURIComponent(email);
  }
}
</script>

</body>

</html>
