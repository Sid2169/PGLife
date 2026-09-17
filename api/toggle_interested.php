<?php
require "../includes/functions.php";
require "../includes/database_connect.php";

header("Content-Type: application/json");

$response = array("success" => false);

if (!csrf_valid(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
    $response["message"] = "Something went wrong!";
    echo json_encode($response);
    return;
}

if (!isset($_SESSION["user_id"])) {
    $response["message"] = "Please login to mark your interest.";
    echo json_encode($response);
    return;
}

$user_id = (int) $_SESSION['user_id'];
$property_id = isset($_POST['property_id']) ? $_POST['property_id'] : '';

if (!is_numeric($property_id)) {
    $response["message"] = "Invalid property.";
    echo json_encode($response);
    return;
}
$property_id = (int) $property_id;

$sql = "SELECT user_id FROM interested_users_properties WHERE user_id = ? AND property_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $user_id, $property_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
$exists = mysqli_stmt_num_rows($stmt) > 0;
mysqli_stmt_close($stmt);

if (!$exists) {
    $sql = "INSERT INTO interested_users_properties (user_id, property_id) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $property_id);
    $result = mysqli_stmt_execute($stmt);
    if (!$result) {
        $response["message"] = "Something went wrong!";
        echo json_encode($response);
        return;
    }
    $response["success"] = true;
    $response["interested"] = true;
    $response["message"] = "Added to your shortlist.";
} else {
    $sql = "DELETE FROM interested_users_properties WHERE user_id = ? AND property_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $property_id);
    $result = mysqli_stmt_execute($stmt);
    if (!$result) {
        $response["message"] = "Something went wrong!";
        echo json_encode($response);
        return;
    }
    $response["success"] = true;
    $response["interested"] = false;
    $response["message"] = "Removed from your shortlist.";
}

echo json_encode($response);
mysqli_close($conn);
