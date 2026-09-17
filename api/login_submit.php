<?php
require "../includes/functions.php";
require "../includes/database_connect.php";

if (!csrf_valid(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
    echo "Invalid request. Please try again.";
    exit;
}

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if ($email === '' || $password === '') {
    echo "Login failed! Invalid email or password.";
    exit;
}

$sql = "SELECT id, full_name, email, password FROM users WHERE email = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (!$result) {
    echo "Something went wrong!";
    exit;
}

$row = mysqli_fetch_assoc($result);
if (!$row || !password_verify($password, $row['password'])) {
    echo "Login failed! Invalid email or password.";
    exit;
}

/* Rotate the session id on login to prevent session fixation. */
session_regenerate_id(true);

$_SESSION['user_id'] = $row['id'];
$_SESSION['full_name'] = $row['full_name'];
$_SESSION['email'] = $row['email'];

header("location: ../index.php");
mysqli_close($conn);
