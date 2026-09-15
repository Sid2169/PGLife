$(document).ready(function () {
    var loggedIn = localStorage.getItem("pgLifeLoggedIn") === "true";
    var userName = localStorage.getItem("pgLifeUserName");

    function updateNav() {
        if (loggedIn) {
            $(".logged-in").removeClass("d-none");
            $("#signup-nav").addClass("d-none");
            $("#vl-signup").addClass("d-none");
            $("#login-nav").addClass("d-none");
            $("#nav-user").text(userName || "User");
        } else {
            $(".logged-in").addClass("d-none");
            $("#signup-nav").removeClass("d-none");
            $("#vl-signup").removeClass("d-none");
            $("#login-nav").removeClass("d-none");
        }

        handleDashboardPage();
    }

    function handleDashboardPage() {
        if (window.location.pathname.indexOf("dashboard.html") > -1) {
            if (loggedIn) {
                $("#dashboard-content").removeClass("d-none");
                $("#dashboard-logged-out").addClass("d-none");
            } else {
                $("#dashboard-content").addClass("d-none");
                $("#dashboard-logged-out").removeClass("d-none");
            }
        }
    }

    function setLoggedIn(name) {
        loggedIn = true;
        userName = name || "User";
        localStorage.setItem("pgLifeLoggedIn", "true");
        localStorage.setItem("pgLifeUserName", userName);
    }

    $("#login-form").on("submit", function (e) {
        e.preventDefault();
        setLoggedIn($(this).find("input[name='email']").val());
        $("#login-modal").modal("hide");
        updateNav();
    });

    $("#signup-form").on("submit", function (e) {
        e.preventDefault();
        setLoggedIn($(this).find("input[name='full_name']").val());
        $("#signup-modal").modal("hide");
        updateNav();
    });

    $("#logout-link").on("click", function (e) {
        e.preventDefault();
        loggedIn = false;
        localStorage.removeItem("pgLifeLoggedIn");
        localStorage.removeItem("pgLifeUserName");
        updateNav();
        if (window.location.pathname.indexOf("dashboard.html") > -1) {
            window.location.href = "index.html";
        }
    });

    updateNav();
});