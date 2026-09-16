<?php
require("includes/database_connect.php");

$property_id = $_GET["property_id"];
if (empty($property_id)) {
    header("Location: index.php");
    return;
}

$sql = "SELECT *, 
            (SELECT COUNT(*) FROM interested_users_properties iup 
             WHERE iup.property_id = p.id) AS total_interested
        FROM properties p
        WHERE p.id = $property_id";
$result = mysqli_query($conn, $sql);
if (!$result) {
    echo "Something went wrong!";
    return;
}
$property = mysqli_fetch_assoc($result);
if (!$property) {
    echo "Property not found!";
    return;
}

$sql_images = "SELECT * FROM property_images WHERE property_id = $property_id";
$result_images = mysqli_query($conn, $sql_images);
if (!$result_images) {
    echo "Something went wrong!";
    return;
}
$images = mysqli_fetch_all($result_images, MYSQLI_ASSOC);

$sql_amenities = "SELECT * FROM amenities WHERE property_id = $property_id";
$result_amenities = mysqli_query($conn, $sql_amenities);
if (!$result_amenities) {
    echo "Something went wrong!";
    return;
}
$amenities = [];
while ($row = mysqli_fetch_assoc($result_amenities)) {
    $amenities[] = $row["amenity"];
}

$sql_testimonials = "SELECT * FROM testimonials WHERE property_id = $property_id";
$result_testimonials = mysqli_query($conn, $sql_testimonials);
if (!$result_testimonials) {
    echo "Something went wrong!";
    return;
}
$testimonials = mysqli_fetch_all($result_testimonials, MYSQLI_ASSOC);

function amenity_icon($amenity) {
    $amenity = strtolower($amenity);
    if (strpos($amenity, "power backup") !== false || strpos($amenity, "power") !== false) {
        return "powerbackup.svg";
    } elseif (strpos($amenity, "lift") !== false) {
        return "lift.svg";
    } elseif (strpos($amenity, "parking") !== false) {
        return "parking.svg";
    } elseif (strpos($amenity, "cctv") !== false || strpos($amenity, "security") !== false) {
        return "cctv.svg";
    } elseif (strpos($amenity, "fire") !== false) {
        return "fireext.svg";
    } elseif (strpos($amenity, "wifi") !== false || strpos($amenity, "wi-fi") !== false) {
        return "wifi.svg";
    } elseif (strpos($amenity, "tv") !== false) {
        return "tv.svg";
    } elseif (strpos($amenity, "water purifier") !== false || strpos($amenity, "ro ") !== false) {
        return "rowater.svg";
    } elseif (strpos($amenity, "water") !== false) {
        return "rowater.svg";
    } elseif (strpos($amenity, "dining") !== false) {
        return "dining.svg";
    } elseif (strpos($amenity, "washing") !== false) {
        return "washingmachine.svg";
    } elseif (strpos($amenity, "bed") !== false || strpos($amenity, "mattress") !== false) {
        return "bed.svg";
    } elseif (strpos($amenity, "air conditioner") !== false || strpos($amenity, "a/c") !== false || $amenity == "ac") {
        return "ac.svg";
    } elseif (strpos($amenity, "geyser") !== false || strpos($amenity, "water heater") !== false) {
        return "geyser.svg";
    }
    return "";
}

function amenity_section($amenity) {
    $amenity = strtolower($amenity);
    if (strpos($amenity, "power backup") !== false || strpos($amenity, "power") !== false ||
        strpos($amenity, "lift") !== false || strpos($amenity, "parking") !== false ||
        strpos($amenity, "cctv") !== false || strpos($amenity, "security") !== false ||
        strpos($amenity, "fire") !== false) {
        return "Building";
    } elseif (strpos($amenity, "wifi") !== false || strpos($amenity, "wi-fi") !== false ||
        strpos($amenity, "tv") !== false || strpos($amenity, "water") !== false ||
        strpos($amenity, "dining") !== false || strpos($amenity, "washing") !== false ||
        strpos($amenity, "fridge") !== false || strpos($amenity, "sofa") !== false) {
        return "Common Area";
    } elseif (strpos($amenity, "bed") !== false || strpos($amenity, "mattress") !== false ||
        strpos($amenity, "air conditioner") !== false || strpos($amenity, "a/c") !== false ||
        $amenity == "ac" || strpos($amenity, "study table") !== false ||
        strpos($amenity, "wardrobe") !== false) {
        return "Bedroom";
    } elseif (strpos($amenity, "geyser") !== false || strpos($amenity, "water heater") !== false) {
        return "Washroom";
    }
    return "Others";
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

$gender = $property["gender"];
if ($gender == "male") {
    $gender_img = "male";
} elseif ($gender == "female") {
    $gender_img = "female";
} else {
    $gender_img = "unisex";
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
                <?php echo $property["name"]; ?>
            </li>
        </ol>
    </nav>

    <div id="property-images" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            <?php
            $i = 0;
            foreach ($images as $image) {
                if ($i == 0) {
                    echo '<li data-target="#property-images" data-slide-to="' . $i . '" class="active"></li>';
                } else {
                    echo '<li data-target="#property-images" data-slide-to="' . $i . '" class=""></li>';
                }
                $i++;
            }
            ?>
        </ol>
        <div class="carousel-inner">
            <?php
            $i = 0;
            foreach ($images as $image) {
                if ($i == 0) {
                    echo '<div class="carousel-item active">';
                } else {
                    echo '<div class="carousel-item">';
                }
                echo '<img class="d-block w-100" src="img/properties/' . $property["id"] . '/' . $image["image"] . '" alt="slide">';
                echo '</div>';
                $i++;
            }
            ?>
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
            <div class="star-container" title="<?php echo round(($property["rating_clean"] + $property["rating_food"] + $property["rating_safety"]) / 3, 1); ?>">
                <?php echo rating_to_stars(round(($property["rating_clean"] + $property["rating_food"] + $property["rating_safety"]) / 3)); ?>
            </div>
            <div class="interested-container">
                <i class="is-interested-image far fa-heart"></i>
                <div class="interested-text">
                    <span class="interested-user-count"><?php echo $property["total_interested"]; ?></span> interested
                </div>
            </div>
        </div>
        <div class="detail-container">
            <div class="property-name"><?php echo $property["name"]; ?></div>
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
                    $section_amenities = [];
                    foreach ($amenities as $amenity) {
                        if (amenity_section($amenity) == $section) {
                            $section_amenities[] = $amenity;
                        }
                    }
                    if (count($section_amenities) == 0) {
                        continue;
                    }
                    ?>
                    <div class="col-md-auto">
                        <h5><?php echo $section; ?></h5>
                        <?php foreach ($section_amenities as $amenity) { ?>
                            <div class="amenity-container">
                                <?php if (amenity_icon($amenity) != "") { ?>
                                    <img src="img/amenities/<?php echo amenity_icon($amenity); ?>">
                                <?php } ?>
                                <span><?php echo $amenity; ?></span>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="property-about page-container">
        <h1>About the Property</h1>
        <p><?php echo $property["description"]; ?></p>
    </div>

    <div class="property-rating">
        <div class="page-container">
            <h1>Property Rating</h1>
            <div class="row align-items-center justify-content-between">
                <div class="col-md-6">
                    <div class="rating-criteria row">
                        <div class="col-6">
                            <i class="rating-criteria-icon fas fa-broom"></i>
                            <span class="rating-criteria-text">Cleanliness</span>
                        </div>
                        <div class="rating-criteria-star-container col-6" title="<?php echo $property["rating_clean"]; ?>">
                            <?php echo rating_to_stars($property["rating_clean"]); ?>
                        </div>
                    </div>

                    <div class="rating-criteria row">
                        <div class="col-6">
                            <i class="rating-criteria-icon fas fa-utensils"></i>
                            <span class="rating-criteria-text">Food Quality</span>
                        </div>
                        <div class="rating-criteria-star-container col-6" title="<?php echo $property["rating_food"]; ?>">
                            <?php echo rating_to_stars($property["rating_food"]); ?>
                        </div>
                    </div>

                    <div class="rating-criteria row">
                        <div class="col-6">
                            <i class="rating-criteria-icon fa fa-lock"></i>
                            <span class="rating-criteria-text">Safety</span>
                        </div>
                        <div class="rating-criteria-star-container col-6" title="<?php echo $property["rating_safety"]; ?>">
                            <?php echo rating_to_stars($property["rating_safety"]); ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="rating-circle">
                        <div class="total-rating"><?php echo round(($property["rating_clean"] + $property["rating_food"] + $property["rating_safety"]) / 3, 1); ?></div>
                        <div class="rating-circle-star-container">
                            <?php echo rating_to_stars(round(($property["rating_clean"] + $property["rating_food"] + $property["rating_safety"]) / 3)); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="property-testimonials page-container">
        <h1>What people say</h1>
        <?php
        foreach ($testimonials as $testimonial) {
            ?>
            <div class="testimonial-block">
                <div class="testimonial-image-container">
                    <img class="testimonial-img" src="img/man.png">
                </div>
                <div class="testimonial-text">
                    <i class="fa fa-quote-left" aria-hidden="true"></i>
                    <p><?php echo $testimonial["content"]; ?></p>
                </div>
                <div class="testimonial-name">- <?php echo $testimonial["user_name"]; ?></div>
            </div>
        <?php } ?>
    </div>

    <?php include("includes/signup_modal.php"); ?>
    <?php include("includes/login_modal.php"); ?>

    <?php include("includes/footer.php"); ?>
</body>

</html>