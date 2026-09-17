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

    <main id="main-content" tabindex="-1">
    <section class="hero-image" aria-labelledby="home-heading">
        <div id="home-carousel" class="carousel slide carousel-fade" data-ride="carousel" data-interval="3000" data-pause="false" aria-hidden="true">
            <div class="carousel-inner">
                <div class="carousel-item hero-bg-1 active"></div>
                <div class="carousel-item hero-bg-2"></div>
                <div class="carousel-item hero-bg-3"></div>
            </div>
        </div>
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <span class="eyebrow">A place to belong</span>
            <h1 id="home-heading">Make yourself<br>at home</h1>
            <p>Find safe, affordable and fully furnished PGs near your college.</p>
            <form class="search-bar" role="search" method="get" action="property_list.php">
                <label for="city-search">Where would you like to live?</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="city-search" name="city" placeholder="Search your city, e.g. Mumbai" />
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-search" aria-label="Search">
                            <i class="fas fa-search" aria-hidden="true"></i><span>Search</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <button type="button" class="carousel-pause" data-carousel="#home-carousel" aria-controls="home-carousel" aria-pressed="false">Pause photos</button>
    </section>

    <section class="page-container home-cities" aria-labelledby="cities-heading">
        <span class="eyebrow">Explore our cities</span>
        <h2 class="home-title" id="cities-heading">Happiness per square foot</h2>
        <div class="city-tiles">
            <div class="city-tile">
                <a href="property_list.php?city=Delhi">
                    <div class="city-tile-image">
                        <img src="img/delhi.png" alt="" />
                    </div>
                    <p>PG in Delhi</p>
                </a>
            </div>
            <div class="city-tile">
                <a href="property_list.php?city=Mumbai">
                    <div class="city-tile-image">
                        <img src="img/mumbai.png" alt="" />
                    </div>
                    <p>PG in Mumbai</p>
                </a>
            </div>
            <div class="city-tile">
                <a href="property_list.php?city=Bengaluru">
                    <div class="city-tile-image">
                        <img src="img/bangalore.png" alt="" />
                    </div>
                    <p>PG in Bengaluru</p>
                </a>
            </div>
            <div class="city-tile">
                <a href="property_list.php?city=Hyderabad">
                    <div class="city-tile-image">
                        <img src="img/hyderabad.png" alt="" />
                    </div>
                    <p>PG in Hyderabad</p>
                </a>
            </div>
        </div>
    </section>
    </main>

    <?php
    include "includes/signup_modal.php";
    include "includes/login_modal.php";
    include "includes/footer.php";
    ?>
</body>

</html>
