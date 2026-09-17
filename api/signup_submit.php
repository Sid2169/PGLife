<?php
require "../includes/functions.php";
require "../includes/database_connect.php";

function signup_fail($message)
{
    echo e($message);
    echo '<br><a href="../index.php">Click here</a> to continue.';
    exit;
}

if (!csrf_valid(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
    signup_fail("Invalid request. Please try again.");
}

$full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$college_name = isset($_POST['college_name']) ? trim($_POST['college_name']) : '';
$gender = isset($_POST['gender']) ? $_POST['gender'] : '';

if ($full_name === '' || $phone === '' || $email === '' || $password === '' || $college_name === '') {
    signup_fail("Please fill in all the fields.");
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    signup_fail("Please enter a valid email address.");
}
if (!preg_match('/^[0-9]{10}$/', $phone)) {
    signup_fail("Please enter a valid 10 digit phone number.");
}
if (!in_array($gender, array('male', 'female', 'other'), true)) {
    $gender = 'other';
}
if (strlen($password) < 6) {
    signup_fail("Password must be at least 6 characters long.");
}

$sql = "SELECT id FROM users WHERE email = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
$row_count = mysqli_stmt_num_rows($stmt);
mysqli_stmt_close($stmt);
if ($row_count != 0) {
    signup_fail("This email id is already registered with us!");
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (email, password, full_name, phone, gender, college_name) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssssss", $email, $hashed_password, $full_name, $phone, $gender, $college_name);
$result = mysqli_stmt_execute($stmt);
if (!$result) {
    signup_fail("Something went wrong!");
}

echo "Your account has been created successfully!";
?>

Click <a href="../index.php">here</a> to continue.
<?php
mysqli_close($conn);
