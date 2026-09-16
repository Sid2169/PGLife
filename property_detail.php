<?php
session_start();
require "includes/database_connect.php";

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;
$property_id = $_GET["property_id"];

$sql_1 = "SELECT *, p.id AS property_id, p.name AS property_name, c.name AS city_name
            FROM properties p
            INNER JOIN cities c ON p.city_id = c.id
            WHERE p.id = $property_id";
$result_1 = mysqli_query($conn, $sql_1);
if (!$result_1) { echo "Something went wrong!"; return; }
$property = mysqli_fetch_assoc($result_1);
if (!$property) { echo "Something went wrong!"; return; }

$sql_2 = "SELECT * FROM testimonials WHERE property_id = $property_id";
$result_2 = mysqli_query($conn, $sql_2);
if (!$result_2) { echo "Something went wrong!"; return; }
$testimonials = mysqli_fetch_all($result_2, MYSQLI_ASSOC);

$sql_3 = "SELECT a.*
            FROM amenities a
            INNER JOIN properties_amenities pa ON a.id = pa.amenity_id
            WHERE pa.property_id = $property_id";
$result_3 = mysqli_query($conn, $sql_3);
if (!$result_3) { echo "Something went wrong!"; return; }
$amenities = mysqli_fetch_all($result_3, MYSQLI_ASSOC);

$sql_4 = "SELECT * FROM interested_users_properties WHERE property_id = $property_id";
$result_4 = mysqli_query($conn, $sql_4);
if (!$result_4) { echo "Something went wrong!"; return; }
$interested_users = mysqli_fetch_all($result_4, MYSQLI_ASSOC);
$interested_users_count = mysqli_num_rows($result_4);

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
    <title><?= $property['property_name']; ?> | PG Life</title>

    <?php include "includes/head_links.php"; ?>
    <link href="css/property_detail.css" rel="stylesheet" />
</head>

<body>
    <?php include "includes/header.php"; ?>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb py-2">
            <li class="breadcrumb-item">
                <a href="index.php">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a href="property_list.php?city=<?= $property['city_name']; ?>"><?= $property['city_name']; ?></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <?= $property['property_name']; ?>
            </li>
        </ol>
    </nav>

    <div id="property-images" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            <?php
            $property_images = glob("img/properties/" . $property['property_id'] . "/*");
            foreach ($property_images as $index => $property_image) {
            ?>
                <li data-target="#property-images" data-slide-to="<?= $index ?>" class="<?= $index == 0 ? "active" : ""; ?>"></li>
            <?php } ?>
        </ol>
        <div class="carousel-inner">
            <?php foreach ($property_images as $index => $property_image) { ?>
                <div class="carousel-item <?= $index == 0 ? "active" : ""; ?>">
                    <img class="d-block w-100" src="<?= $property_image ?>" alt="slide">
                </div>
            <?php } ?>
        </div>
        <a class="carousel-control-prev" href="#property-images" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#property-images" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

    <div class="property-summary page-container">
        <div class="row no-gutters justify-content-between">
            <?php $total_rating = round(($property['rating_clean'] + $property['rating_food'] + $property['rating_safety']) / 3, 1); ?>
            <div class="star-container" title="<?= $total_rating ?>">
                <?= rating_to_stars($total_rating) ?>
            </div>
            <div class="interested-container">
                <?php
                $is_interested = false;
                foreach ($interested_users as $iu) {
                    if ($iu['user_id'] == $user_id) { $is_interested = true; break; }
                }
                ?>
                <?php if ($is_interested): ?>
                    <i class="is-interested-image fas fa-heart"></i>
                <?php else: ?>
                    <i class="is-interested-image far fa-heart"></i>
                <?php endif; ?>
                <div class="interested-text">
                    <span class="interested-user-count"><?= $interested_users_count ?></span> interested
                </div>
            </div>
        </div>
        <div class="detail-container">
            <div class="property-name"><?= $property['property_name'] ?></div>
            <div class="property-address"><?= $property['address'] ?></div>
            <div class="property-gender">
                <?php
                if ($property['gender'] == "male") echo '<img src="img/male.png">';
                elseif ($property['gender'] == "female") echo '<img src="img/female.png">';
                else echo '<img src="img/unisex.png">';
                ?>
            </div>
        </div>
        <div class="row no-gutters">
            <div class="rent-container col-6">
                <div class="rent">₹ <?= number_format($property['rent']) ?>/-</div>
                <div class="rent-unit">per month</div>
            </div>
            <div class="button-container col-6">
                <a href="#" class="btn btn-primary">Book Now</a>
            </div>
        </div>
    </div>

    <div class="property-amenities">
        <div class="page-container">
            <h1>Amenities</h1>
            <div class="row justify-content-between">
                <?php
                $sections = ["Building", "Common Area", "Bedroom", "Washroom"];
                foreach ($sections as $section) {
                    $section_amenities = array_filter($amenities, function($a) use ($section) { return $a['type'] == $section; });
                    if (count($section_amenities) == 0) continue;
                ?>
                    <div class="col-md-auto">
                        <h5><?= $section ?></h5>
                        <?php foreach ($section_amenities as $amenity) { ?>
                            <div class="amenity-container">
                                <img src="img/amenities/<?= $amenity['icon'] ?>.svg">
                                <span><?= $amenity['name'] ?></span>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="property-about page-container">
        <h1>About the Property</h1>
        <p><?= $property['description'] ?></p>
    </div>

    <div class="property-rating">
        <div class="page-container">
            <h1>Property Rating</h1>
            <div class="row align-items-center justify-content-between">
                <div class="col-md-6">
                    <?php
                    $rating_labels = [
                        ['icon' => 'fas fa-broom', 'text' => 'Cleanliness', 'val' => $property['rating_clean']],
                        ['icon' => 'fas fa-utensils', 'text' => 'Food Quality', 'val' => $property['rating_food']],
                        ['icon' => 'fa fa-lock', 'text' => 'Safety', 'val' => $property['rating_safety']],
                    ];
                    foreach ($rating_labels as $r) {
                    ?>
                        <div class="rating-criteria row">
                            <div class="col-6">
                                <i class="rating-criteria-icon <?= $r['icon'] ?>"></i>
                                <span class="rating-criteria-text"><?= $r['text'] ?></span>
                            </div>
                            <div class="rating-criteria-star-container col-6" title="<?= $r['val'] ?>">
                                <?= rating_to_stars($r['val']) ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <div class="col-md-4">
                    <div class="rating-circle">
                        <div class="total-rating"><?= $total_rating ?></div>
                        <div class="rating-circle-star-container">
                            <?= rating_to_stars($total_rating) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="property-testimonials page-container">
        <h1>What people say</h1>
        <?php foreach ($testimonials as $testimonial) { ?>
            <div class="testimonial-block">
                <div class="testimonial-image-container">
                    <img class="testimonial-img" src="img/man.png">
                </div>
                <div class="testimonial-text">
                    <i class="fa fa-quote-left" aria-hidden="true"></i>
                    <p><?= $testimonial['content'] ?></p>
                </div>
                <div class="testimonial-name">- <?= $testimonial['user_name'] ?></div>
            </div>
        <?php } ?>
    </div>

    <?php
    include "includes/signup_modal.php";
    include "includes/login_modal.php";
    include "includes/footer.php";
    ?>
</body>

</html>