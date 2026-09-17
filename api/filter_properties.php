<?php
require "../includes/functions.php";
require "../includes/database_connect.php";

header("Content-Type: application/json");

$response = array("success" => false);

$user_id = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : NULL;
$city_name = isset($_GET["city"]) ? trim($_GET["city"]) : "";
$gender = isset($_GET["gender"]) ? $_GET["gender"] : "";
$min_rent = isset($_GET["min_rent"]) ? $_GET["min_rent"] : "";
$max_rent = isset($_GET["max_rent"]) ? $_GET["max_rent"] : "";
$sort = isset($_GET["sort"]) ? $_GET["sort"] : "";

if (!isset($_GET["city"]) || $_GET["city"] === "") {
    $result_city = mysqli_query($conn, "SELECT id, name FROM cities ORDER BY id ASC LIMIT 1");
} else {
    $stmt_city = mysqli_prepare($conn, "SELECT id, name FROM cities WHERE name = ?");
    mysqli_stmt_bind_param($stmt_city, "s", $city_name);
    mysqli_stmt_execute($stmt_city);
    $result_city = mysqli_stmt_get_result($stmt_city);
}

if (!$result_city || mysqli_num_rows($result_city) == 0) {
    $response["message"] = "Sorry! We do not have any PG listed in this city.";
    echo json_encode($response);
    return;
}
$city_row = mysqli_fetch_assoc($result_city);
$response["city"] = $city_row;
$city_id = (int) $city_row["id"];
$city_name = $city_row["name"];

$where = "p.city_id = ?";
$types = "i";
$params = array($city_id);

if ($gender === "male") {
    $where .= " AND (p.gender = 'male' OR p.gender = 'unisex')";
} elseif ($gender === "female") {
    $where .= " AND (p.gender = 'female' OR p.gender = 'unisex')";
} elseif ($gender === "unisex") {
    $where .= " AND p.gender = 'unisex'";
}

if ($min_rent !== "" && is_numeric($min_rent)) {
    $where .= " AND p.rent >= ?";
    $types .= "i";
    $params[] = (int) $min_rent;
}
if ($max_rent !== "" && is_numeric($max_rent)) {
    $where .= " AND p.rent <= ?";
    $types .= "i";
    $params[] = (int) $max_rent;
}

$order_by = "p.id ASC";
if ($sort === "rent_asc") {
    $order_by = "p.rent ASC";
} elseif ($sort === "rent_desc") {
    $order_by = "p.rent DESC";
}

$sql = "SELECT * FROM properties p WHERE $where ORDER BY $order_by";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (!$result) {
    $response["message"] = "Something went wrong!";
    echo json_encode($response);
    return;
}

$sql_interested = "SELECT iup.user_id, iup.property_id FROM interested_users_properties iup
                    INNER JOIN properties p ON iup.property_id = p.id
                    WHERE p.city_id = ?";
$stmt_interested = mysqli_prepare($conn, $sql_interested);
mysqli_stmt_bind_param($stmt_interested, "i", $city_id);
mysqli_stmt_execute($stmt_interested);
$result_interested = mysqli_stmt_get_result($stmt_interested);
if (!$result_interested) {
    $response["message"] = "Something went wrong!";
    echo json_encode($response);
    return;
}
$bookkeeping = array();
while ($row = mysqli_fetch_assoc($result_interested)) {
    $property_id = (int) $row["property_id"];
    if (!isset($bookkeeping[$property_id])) {
        $bookkeeping[$property_id] = array("count" => 0, "me" => false);
    }
    $bookkeeping[$property_id]["count"]++;
    if ($user_id != NULL && (int) $row["user_id"] === $user_id) {
        $bookkeeping[$property_id]["me"] = true;
    }
}

$properties = array();
while ($row = mysqli_fetch_assoc($result)) {
    $property_id = (int) $row["id"];
    $row["rating"] = round(($row["rating_clean"] + $row["rating_food"] + $row["rating_safety"]) / 3, 1);
    $images = glob(dirname(__DIR__) . "/img/properties/" . $property_id . "/*");
    $row["image"] = count($images) > 0
        ? "img/properties/" . $property_id . "/" . basename($images[0])
        : "";
    $interested = isset($bookkeeping[$property_id])
        ? $bookkeeping[$property_id]
        : array("count" => 0, "me" => false);
    $row["interested_count"] = $interested["count"];
    $row["is_interested"] = $interested["me"];
    $properties[] = $row;
}

$response["success"] = true;
$response["count"] = count($properties);
$response["properties"] = $properties;
echo json_encode($response);
mysqli_close($conn);
