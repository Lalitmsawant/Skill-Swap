<?php
session_start();
include 'php/db.php';

$message = "";
$prefill = $_GET['email'] ?? '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = trim($_POST["email"]);

  $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
    $_SESSION['reset_email'] = $email;
    header("Location: reset-password.php");
    exit;
  } else {
    $message = "No account found with that email.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <header class="header">
    <h1 class="logo">Skill Swap Platform</h1>
    <a href="login.php" class="home-link">Back to Login</a>
  </header>
  <main class="login-container">
    <form method="POST" class="login-form">
      <?php if (!empty($message)) echo "<div style='color:red;'>$message</div>"; ?>

      <label for="email">Enter your registered email</label>
      <input type="email" name="email" id="email" value="<?= htmlspecialchars($prefill) ?>" required>

      <button class="btn">Continue</button>
    </form>
  </main>
</body>
</html>
