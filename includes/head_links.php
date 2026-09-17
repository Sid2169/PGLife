<script>
    (function () {
        /* Apply the theme before CSS paints to avoid a flash: use the stored
           choice if present, otherwise follow the OS setting. */
        var theme = null;
        try {
            theme = localStorage.getItem("pglife-theme");
        } catch (e) {}
        if (theme !== "light" && theme !== "dark") {
            theme = window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches
                ? "dark"
                : "light";
        }
        document.documentElement.setAttribute("data-theme", theme);
    })();
</script>
<link href="css/bootstrap.min.css" rel="stylesheet" />
<link href="https://use.fontawesome.com/releases/v5.11.2/css/all.css" rel="stylesheet" />
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Open+Sans:ital,wght@0,300;0,400;0,600;0,700;0,800;1,300;1,400;1,600;1,700;1,800&display=swap" rel="stylesheet" />
<link href="css/common.css" rel="stylesheet" />
<link href="css/theme.css" rel="stylesheet" />
<meta name="csrf-token" content="<?= e(csrf_token()) ?>">