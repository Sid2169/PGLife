<?php
require("includes/database_connect.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    return;
}

$user_id = $_SESSION["user_id"];

$user_query = "SELECT * FROM users WHERE id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

$properties_query = "SELECT *, 
    (SELECT COUNT(*) FROM interested_users_properties iup 
     WHERE iup.property_id = p.id) AS total_interested
FROM properties p
INNER JOIN interested_users_properties iup ON p.id = iup.property_id
WHERE iup.user_id = $user_id";
$properties_result = mysqli_query($conn, $properties_query);

$sql_images = "SELECT * FROM property_images";
$result_images = mysqli_query($conn, $sql_images);
$images = [];
while ($row = mysqli_fetch_assoc($result_images)) {
    $images[$row["property_id"]][] = $row["image"];
}

function rating_to_stars($rating) {
    $output = "";
    $num = (int)$rating;
    $frac = $rating - $num;
    for ($i = 0; $i < 5; $i++) {
        if ($i < $num) {
            $output .= '<i class="fas fa-star"></i>';
        } elseif ($i == $num && $frac >= 0.5) {
            $output .= '<i class="fas fa-star-half-alt"></i>';
        } else {
            $output .= '<i class="far fa-star"></i>';
        }
    }
    return $output;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("includes/head_links.php"); ?>
</head>

<body>
    <?php include("includes/header.php"); ?>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb py-2">
            <li class="breadcrumb-item">
                <a href="index.php">Home</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                Dashboard
            </li>
        </ol>
    </nav>

    <div class="page-container">
        <div class="user-info-card">
            <div class="user-info-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="user-info-details">
                <div class="user-info-title">Welcome back!</div>
                <div class="user-info-name"><?php echo $user["full_name"]; ?></div>
                <div class="user-info-subtitle">The people you meet matter. So does your room.</div>
            </div>
        </div>

        <h1>Your Favourite PGs</h1>
        <p class="dashboard-subtitle">PGs you have marked as interested. Visit them and start your new journey!</p>

        <?php
        while ($property = mysqli_fetch_assoc($properties_result)) {
            if (isset($images[$property["id"]])) {
                $first_image = $images[$property["id"]][0];
            } else {
                $first_image = "";
            }
            $rating = round(($property["rating_clean"] + $property["rating_food"] + $property["rating_safety"]) / 3, 1);
            $stars = rating_to_stars($rating);

            if ($property["gender"] == "male") {
                $gender_img = "male";
            } elseif ($property["gender"] == "female") {
                $gender_img = "female";
            } else {
                $gender_img = "unisex";
            }
            ?>
            <div class="property-card row">
                <div class="image-container col-md-4">
                    <img src="img/properties/<?php echo $property["id"]; ?>/<?php echo $first_image; ?>" />
                </div>
                <div class="content-container col-md-8">
                    <div class="row no-gutters justify-content-between">
                        <div class="star-container" title="<?php echo $rating; ?>">
                            <?php echo $stars; ?>
                        </div>
                        <div class="interested-container" title="Interested">
                            <i class="fas fa-heart"></i>
                            <div class="interested-text">Interested</div>
                        </div>
                    </div>
                    <div class="detail-container">
                        <div class="property-name">
                            <a href="property_detail.php?property_id=<?php echo $property["id"]; ?>"><?php echo $property["name"]; ?></a>
                        </div>
                        <div class="property-address"><?php echo $property["address"]; ?></div>
                        <div class="property-gender">
                            <img src="img/<?php echo $gender_img; ?>.png" />
                        </div>
                    </div>
                    <div class="row no-gutters">
                        <div class="rent-container col-6">
                            <div class="rent">Rs <?php echo number_format($property["rent"]); ?>/-</div>
                            <div class="rent-unit">per month</div>
                        </div>
                        <div class="button-container col-6">
                            <a href="property_detail.php?property_id=<?php echo $property["id"]; ?>" class="btn btn-danger">Remove</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <?php include("includes/signup_modal.php"); ?>
    <?php include("includes/login_modal.php"); ?>

    <?php include("includes/footer.php"); ?>
</body>

</html>