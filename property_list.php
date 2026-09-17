<?php
require "includes/functions.php";
require "includes/database_connect.php";

$city_name = isset($_GET["city"]) ? $_GET["city"] : "";

$sql_cities = "SELECT id, name FROM cities ORDER BY id ASC";
$result_cities = mysqli_query($conn, $sql_cities);
$cities = array();
while ($row = mysqli_fetch_assoc($result_cities)) {
    $cities[] = $row;
}

$initial_city = "";
foreach ($cities as $c) {
    if (strcasecmp($c["name"], $city_name) == 0) {
        $initial_city = $c["name"];
        break;
    }
}
if ($initial_city == "") {
    $initial_city = count($cities) > 0 ? $cities[0]["name"] : "";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Best PG's in <?php echo e($initial_city); ?> | PG Life</title>

    <?php include "includes/head_links.php"; ?>
    <link href="css/property_list.css" rel="stylesheet" />
</head>

<body>
    <?php include "includes/header.php"; ?>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb py-2">
            <li class="breadcrumb-item">
                <a href="index.php">Home</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page" id="city-breadcrumb">
                <?php echo e($initial_city); ?>
            </li>
        </ol>
    </nav>

    <main id="main-content" class="page-container" tabindex="-1">
        <div class="page-heading">
            <span class="eyebrow">Find your next home</span>
            <h1>PGs in <?php echo e($initial_city); ?></h1>
        </div>
        <div id="property-list-app"></div>
    </main>

    <?php
    include "includes/signup_modal.php";
    include "includes/login_modal.php";
    include "includes/footer.php";
    ?>

    <script type="text/javascript">
        window.PG_LIFE = {
            city: <?php echo json_encode($initial_city); ?>,
            apiUrl: "api/filter_properties.php"
        };
    </script>

    <script src="js/vendor/react.production.min.js"></script>
    <script src="js/vendor/react-dom.production.min.js"></script>
    <script src="js/vendor/babel.min.js"></script>
    <script type="text/babel" src="js/property_list_app.jsx"></script>
</body>

</html>
