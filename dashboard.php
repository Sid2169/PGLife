<?php
require "includes/functions.php";
require "includes/database_connect.php";

if (!isset($_SESSION["user_id"])) {
    header("location: index.php");
    die();
}
$user_id = (int) $_SESSION['user_id'];

$sql_1 = "SELECT * FROM users WHERE id = ?";
$stmt_1 = mysqli_prepare($conn, $sql_1);
mysqli_stmt_bind_param($stmt_1, "i", $user_id);
mysqli_stmt_execute($stmt_1);
$result_1 = mysqli_stmt_get_result($stmt_1);
if (!$result_1) { echo "Something went wrong!"; return; }
$user = mysqli_fetch_assoc($result_1);
if (!$user) { echo "Something went wrong!"; return; }

$sql_2 = "SELECT p.*
            FROM interested_users_properties iup
            INNER JOIN properties p ON iup.property_id = p.id
            WHERE iup.user_id = ?";
$stmt_2 = mysqli_prepare($conn, $sql_2);
mysqli_stmt_bind_param($stmt_2, "i", $user_id);
mysqli_stmt_execute($stmt_2);
$result_2 = mysqli_stmt_get_result($stmt_2);
if (!$result_2) { echo "Something went wrong!"; return; }
$interested_properties = mysqli_fetch_all($result_2, MYSQLI_ASSOC);

function rating_to_stars($rating) {
    $output = "";
    for ($i = 0; $i < 5; $i++) {
        if ($rating >= $i + 0.8) {
            $output .= '<i class="fas fa-star"></i>';
        } elseif ($rating >= $i + 0.3) {
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | PG Life</title>

    <?php include "includes/head_links.php"; ?>
    <link href="css/dashboard.css" rel="stylesheet" />
</head>

<body>
    <?php include "includes/header.php"; ?>

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

    <main id="main-content" tabindex="-1">
    <section class="my-profile page-container" aria-labelledby="profile-heading">
        <span class="eyebrow">Your space</span>
        <h1 id="profile-heading">My Profile</h1>
        <div class="row profile-card">
            <div class="col-md-3 profile-img-container">
                <i class="fas fa-user profile-img" aria-hidden="true"></i>
            </div>
            <div class="col-md-9">
                <div class="row no-gutters justify-content-between align-items-end">
                    <div class="profile">
                        <div class="name"><?= e($user['full_name']) ?></div>
                        <div class="email"><?= e($user['email']) ?></div>
                        <div class="phone"><?= e($user['phone']) ?></div>
                        <div class="college"><?= e($user['college_name']) ?></div>
                    </div>
                    <div class="edit">
                        <div class="edit-profile">Edit Profile</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php if (count($interested_properties) > 0) { ?>
        <div class="my-interested-properties" id="interested-list">
            <div class="page-container">
                <h2>My Interested Properties</h2>

                <?php foreach ($interested_properties as $property) {
                    $property_id = (int) $property['id'];
                    $property_images = glob(__DIR__ . "/img/properties/" . $property_id . "/*");
                    $image_url = count($property_images) > 0
                        ? "img/properties/" . $property_id . "/" . basename($property_images[0])
                        : "";
                    $total_rating = round(($property['rating_clean'] + $property['rating_food'] + $property['rating_safety']) / 3, 1);
                    $stars = rating_to_stars($total_rating);
                ?>
                    <div class="property-card property-id-<?= $property_id ?> row">
                        <div class="image-container col-md-4">
                            <?php if ($image_url): ?>
                                <img src="<?= e($image_url) ?>" alt="<?= e($property['name']) ?>" />
                            <?php endif; ?>
                        </div>
                        <div class="content-container col-md-8">
                            <div class="row no-gutters justify-content-between">
                                <div class="star-container" title="<?= e($total_rating) ?>" role="img" aria-label="Rating: <?= e($total_rating) ?> out of 5">
                                    <?= $stars ?>
                                </div>
                                <div class="interested-container">
                                    <button type="button" class="is-interested-image interested-btn fas fa-heart" property_id="<?= $property_id ?>" aria-label="Shortlist <?= e($property['name']) ?>" aria-pressed="true"></button>
                                </div>
                            </div>
                            <div class="detail-container">
                                <h3 class="property-name"><?= e($property['name']) ?></h3>
                                <div class="property-address"><?= e($property['address']) ?></div>
                                <div class="property-gender">
                                    <?php
                                    if ($property['gender'] == "male") echo '<img src="img/male.png" alt=""><span>Male</span>';
                                    elseif ($property['gender'] == "female") echo '<img src="img/female.png" alt=""><span>Female</span>';
                                    else echo '<img src="img/unisex.png" alt=""><span>Unisex</span>';
                                    ?>
                                </div>
                            </div>
                            <div class="row no-gutters">
                                <div class="rent-container col-6">
                                    <div class="rent">₹ <?= number_format($property['rent']) ?>/-</div>
                                    <div class="rent-unit">per month</div>
                                </div>
                                <div class="button-container col-6">
                                    <a href="property_detail.php?property_id=<?= $property_id ?>" class="btn btn-primary" aria-label="View <?= e($property['name']) ?>">View</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
    </main>

    <?php include "includes/footer.php"; ?>
</body>

</html>
