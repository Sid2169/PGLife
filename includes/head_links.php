<meta name="viewport" content="width=device-width, initial-scale=1">
<?php if (basename($_SERVER["PHP_SELF"]) == "index.php") { ?>
    <title>Home | PG Life</title>
<?php } elseif (basename($_SERVER["PHP_SELF"]) == "property_list.php") { ?>
    <title>Best PG's | PG Life</title>
<?php } elseif (basename($_SERVER["PHP_SELF"]) == "property_detail.php") { ?>
    <title>Property | PG Life</title>
<?php } elseif (basename($_SERVER["PHP_SELF"]) == "dashboard.php") { ?>
    <title>Dashboard | PG Life</title>
<?php } ?>

<link href="css/bootstrap.min.css" rel="stylesheet" />
<link href="https://use.fontawesome.com/releases/v5.11.2/css/all.css" rel="stylesheet" />
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;0,700;0,800;1,300;1,400;1,600;1,700;1,800&display=swap" rel="stylesheet" />
<link href="css/common.css" rel="stylesheet" />
<?php if (basename($_SERVER["PHP_SELF"]) == "index.php") { ?>
    <link href="css/home.css" rel="stylesheet" />
<?php } elseif (basename($_SERVER["PHP_SELF"]) == "property_list.php") { ?>
    <link href="css/property_list.css" rel="stylesheet" />
<?php } elseif (basename($_SERVER["PHP_SELF"]) == "property_detail.php") { ?>
    <link href="css/property_detail.css" rel="stylesheet" />
<?php } elseif (basename($_SERVER["PHP_SELF"]) == "dashboard.php") { ?>
    <link href="css/dashboard.css" rel="stylesheet" />
<?php } ?>
