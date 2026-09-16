/* common.js
 * Shared behaviour for all PG Life pages.
 * - Reflects the login state in the navbar
 *   (Dashboard/Logout when logged in, Signup/Login otherwise).
 * - Handles login, signup and logout flows, persisted in localStorage.
 * - Gates the dashboard page behind a logged-in user.
 */
$(document).ready(function () {
    /* Restore the session (if any) from a previous visit. */
    var loggedIn = localStorage.getItem("pgLifeLoggedIn") === "true";
    var userName = localStorage.getItem("pgLifeUserName");

    /* Show/hide the correct navbar items based on the login state. */
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

    /* On the dashboard page, show the content only for a logged-in user. */
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

    /* Log the user in and persist the session. */
    function setLoggedIn(name) {
        loggedIn = true;
        userName = name || "User";
        localStorage.setItem("pgLifeLoggedIn", "true");
        localStorage.setItem("pgLifeUserName", userName);
    }

    /* Login form submission. */
    $("#login-form").on("submit", function (e) {
        e.preventDefault();
        setLoggedIn($(this).find("input[name='email']").val());
        $("#login-modal").modal("hide");
        updateNav();
    });

    /* Signup form submission (also logs the user in). */
    $("#signup-form").on("submit", function (e) {
        e.preventDefault();
        setLoggedIn($(this).find("input[name='full_name']").val());
        $("#signup-modal").modal("hide");
        updateNav();
    });

    /* Logout: clear the session and return home from the dashboard. */
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

    /* Apply the login state once the page has loaded. */
    updateNav();
});