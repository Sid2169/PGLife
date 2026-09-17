<?php
require "includes/functions.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home | PG Life</title>

    <?php include "includes/head_links.php"; ?>
    <link href="css/home.css" rel="stylesheet" />
</head>

<body>
    <?php include "includes/header.php"; ?>

    <div class="hero-image">
        <div id="home-carousel" class="carousel slide carousel-fade" data-ride="carousel" data-interval="3000" data-pause="false">
            <div class="carousel-inner">
                <div class="carousel-item hero-bg-1 active"></div>
                <div class="carousel-item hero-bg-2"></div>
                <div class="carousel-item hero-bg-3"></div>
            </div>
        </div>
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h2>Make yourself at home</h2>
            <p>Find safe, affordable and fully furnished PGs near your college.</p>
            <form class="search-bar" role="form" method="get" action="property_list.php">
                <div class="input-group">
                    <input type="text" class="form-control" name="city" placeholder="Search your city, e.g. Mumbai" />
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-search" aria-label="Search">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="page-container">
        <h1 class="home-title">Happiness per square foot</h1>
        <div class="city-tiles row">
            <div class="city-tile col-6 col-md-4">
                <a href="property_list.php?city=Delhi">
                    <div class="city-tile-image">
                        <img src="img/delhi.png" alt="Delhi" />
                    </div>
                    <p>PG in Delhi</p>
                </a>
            </div>
            <div class="city-tile col-6 col-md-4">
                <a href="property_list.php?city=Mumbai">
                    <div class="city-tile-image">
                        <img src="img/mumbai.png" alt="Mumbai" />
                    </div>
                    <p>PG in Mumbai</p>
                </a>
            </div>
            <div class="city-tile col-6 col-md-4">
                <a href="property_list.php?city=Bengaluru">
                    <div class="city-tile-image">
                        <img src="img/bangalore.png" alt="Bengaluru" />
                    </div>
                    <p>PG in Bengaluru</p>
                </a>
            </div>
            <div class="city-tile col-6 col-md-4">
                <a href="property_list.php?city=Hyderabad">
                    <div class="city-tile-image">
                        <img src="img/hyderabad.png" alt="Hyderabad" />
                    </div>
                    <p>PG in Hyderabad</p>
                </a>
            </div>
        </div>
    </div>

    <?php
    include "includes/signup_modal.php";
    include "includes/login_modal.php";
    include "includes/footer.php";
    ?>
</body>

</html>