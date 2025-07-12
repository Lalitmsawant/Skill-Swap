<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // No password usually in XAMPP
$db = 'skill_swap'; // Make sure this database exists in phpMyAdmin

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
