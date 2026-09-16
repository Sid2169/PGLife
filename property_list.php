<?php
require("includes/database_connect.php");

$city = $_GET["city"];
if (empty($city)) {
    $city = "Mumbai";
}

$sql_city_id = "SELECT * FROM cities WHERE name='$city'";
$result_city_id = mysqli_query($conn, $sql_city_id);
if (!$result_city_id) {
    echo "Something went wrong!";
    return;
}
$row = mysqli_fetch_assoc($result_city_id);
$city_id = $row["id"];

$sql = "SELECT *, (SELECT COUNT(*) 
            FROM interested_users_properties iup 
            WHERE iup.property_id = p.id) AS total_interested
        FROM properties p
        WHERE p.city_id = $city_id";
$result = mysqli_query($conn, $sql);
if (!$result) {
    echo "Something went wrong!";
    return;
}

$sql_images = "SELECT * FROM property_images";
$result_images = mysqli_query($conn, $sql_images);
if (!$result_images) {
    echo "Something went wrong!";
    return;
}
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
                <?php echo $city; ?>
            </li>
        </ol>
    </nav>

    <div class="page-container">
        <div class="filter-bar row justify-content-around">
            <div class="col-auto" data-toggle="modal" data-target="#filter-modal">
                <img src="img/filter.png" alt="filter" />
                <span>Filter</span>
            </div>
            <div class="col-auto">
                <img src="img/desc.png" alt="sort-desc" />
                <span>Highest rent first</span>
            </div>
            <div class="col-auto">
                <img src="img/asc.png" alt="sort-asc" />
                <span>Lowest rent first</span>
            </div>
        </div>

        <?php
        while ($property = mysqli_fetch_assoc($result)) {
            if (isset($images[$property["id"]])) {
                $first_image = $images[$property["id"]][0];
            } else {
                $first_image = "";
            }
            $rating = (($property["rating_clean"] + $property["rating_food"] + $property["rating_safety"]) / 3);
            $rating = round($rating * 2) / 2;
            $stars = rating_to_stars($rating);
            ?>
            <div class="property-card row">
                <div class="image-container col-md-4">
                    <img src="<?php echo "img/properties/" . $property["id"] . "/" . $first_image; ?>" />
                </div>
                <div class="content-container col-md-8">
                    <div class="row no-gutters justify-content-between">
                        <div class="star-container" title="<?php echo $rating; ?>">
                            <?php echo $stars; ?>
                        </div>
                        <div class="interested-container">
                            <i class="far fa-heart"></i>
                            <div class="interested-text"><?php echo $property["total_interested"]; ?> interested</div>
                        </div>
                    </div>
                    <div class="detail-container">
                        <div class="property-name"><?php echo $property["name"]; ?></div>
                        <div class="property-address"><?php echo $property["address"]; ?></div>
                        <div class="property-gender">
                            <?php
                            $gender = $property["gender"];
                            if ($gender == "male") {
                                $gender_img = "male";
                            } elseif ($gender == "female") {
                                $gender_img = "female";
                            } else {
                                $gender_img = "unisex";
                            }
                            ?>
                            <img src="img/<?php echo $gender_img; ?>.png" />
                        </div>
                    </div>
                    <div class="row no-gutters">
                        <div class="rent-container col-6">
                            <div class="rent">Rs <?php echo number_format($property["rent"]); ?>/-</div>
                            <div class="rent-unit">per month</div>
                        </div>
                        <div class="button-container col-6">
                            <a href="property_detail.php?property_id=<?php echo $property["id"]; ?>" class="btn btn-primary">View</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <div class="modal fade" id="filter-modal" tabindex="-1" role="dialog" aria-labelledby="filter-heading" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="filter-heading">Filters</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <h5>Gender</h5>
                    <hr />
                    <div>
                        <button class="btn btn-outline-dark btn-active">
                            No Filter
                        </button>
                        <button class="btn btn-outline-dark">
                            <i class="fas fa-venus-mars"></i>Unisex
                        </button>
                        <button class="btn btn-outline-dark">
                            <i class="fas fa-mars"></i>Male
                        </button>
                        <button class="btn btn-outline-dark">
                            <i class="fas fa-venus"></i>Female
                        </button>
                    </div>
                </div>

                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-success">Okay</button>
                </div>
            </div>
        </div>
    </div>

    <?php include("includes/signup_modal.php"); ?>
    <?php include("includes/login_modal.php"); ?>

    <?php include("includes/footer.php"); ?>
</body>

</html>