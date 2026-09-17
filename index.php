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
            <div class="hero-eyebrow">Trusted by 10,000+ students</div>
            <h2>Make yourself at home</h2>
            <p>Find safe, affordable and fully furnished PGs near your college.</p>
            <form class="search-bar search-card" role="form" method="get" action="property_list.php">
                <div class="input-group">
                    <input type="text" class="form-control" name="city" placeholder="Search your city, e.g. Mumbai" aria-label="Search your city" />
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-search" aria-label="Search">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
            <div class="hero-quick-cities">
                <a href="property_list.php?city=Delhi">Delhi</a>
                <a href="property_list.php?city=Mumbai">Mumbai</a>
                <a href="property_list.php?city=Bengaluru">Bengaluru</a>
                <a href="property_list.php?city=Hyderabad">Hyderabad</a>
            </div>
        </div>
    </div>

    <div class="page-container">
        <div class="stats-strip">
            <div class="stat">
                <div class="stat-number">4</div>
                <div class="stat-label">Cities</div>
            </div>
            <div class="stat">
                <div class="stat-number">10+</div>
                <div class="stat-label">Verified PGs</div>
            </div>
            <div class="stat">
                <div class="stat-number">10k+</div>
                <div class="stat-label">Students housed</div>
            </div>
            <div class="stat">
                <div class="stat-number">4.7★</div>
                <div class="stat-label">Average rating</div>
            </div>
        </div>

        <h1 class="home-title">Happiness per square foot</h1>
        <p class="home-subtitle">Pick a city and find a PG that feels like home.</p>
        <div class="city-tiles row">
            <div class="city-tile col-6 col-md-4">
                <a href="property_list.php?city=Delhi">
                    <div class="city-tile-image">
                        <img src="img/delhi.png" alt="Delhi" />
                    </div>
                    <p>PG in Delhi<span class="city-count">4 options</span></p>
                </a>
            </div>
            <div class="city-tile col-6 col-md-4">
                <a href="property_list.php?city=Mumbai">
                    <div class="city-tile-image">
                        <img src="img/mumbai.png" alt="Mumbai" />
                    </div>
                    <p>PG in Mumbai<span class="city-count">2 options</span></p>
                </a>
            </div>
            <div class="city-tile col-6 col-md-4">
                <a href="property_list.php?city=Bengaluru">
                    <div class="city-tile-image">
                        <img src="img/bangalore.png" alt="Bengaluru" />
                    </div>
                    <p>PG in Bengaluru<span class="city-count">3 options</span></p>
                </a>
            </div>
            <div class="city-tile col-6 col-md-4">
                <a href="property_list.php?city=Hyderabad">
                    <div class="city-tile-image">
                        <img src="img/hyderabad.png" alt="Hyderabad" />
                    </div>
                    <p>PG in Hyderabad<span class="city-count">1 option</span></p>
                </a>
            </div>
        </div>
    </div>

    <div class="home-value-band">
        <div class="page-container">
            <h3>Why students choose PG Life</h3>
            <div class="row">
                <div class="col-md-4">
                    <div class="value-step">
                        <div class="value-icon"><i class="fas fa-shield-alt"></i></div>
                        <h5>Verified properties</h5>
                        <p>Every PG is reviewed for safety, cleanliness and food quality before it's listed.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="value-step">
                        <div class="value-icon"><i class="fas fa-tags"></i></div>
                        <h5>Honest pricing</h5>
                        <p>Transparent rent with no hidden charges, so you can budget with confidence.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="value-step">
                        <div class="value-icon"><i class="fas fa-heart"></i></div>
                        <h5>Shortlist in one tap</h5>
                        <p>Save the PGs you like and compare them later from your dashboard.</p>
                    </div>
                </div>
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