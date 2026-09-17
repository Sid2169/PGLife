/* app.js
 * Shared AJAX behaviour for PG Life.
 * - Toggles "interested" state without a page reload.
 * - Shows a loading overlay during AJAX calls.
 * - Opens the login modal when a guest tries an action that needs login.
 */

/* Footer: keep the copyright year current. */
$(function () {
    var $year = $("#copyright-year");
    if ($year.length) {
        $year.text(new Date().getFullYear());
    }
});

/* Loading overlay helpers */
function showLoading() {
    $("#loading").addClass("loading");
}

function hideLoading() {
    $("#loading").removeClass("loading");
}

/* Read the per-session CSRF token emitted in the page <head>. */
function csrfToken() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute("content") : "";
}

/* Rebuild the star icons for a numeric rating (0-5). */
function ratingStars(rating) {
    var html = "";
    for (var i = 0; i < 5; i++) {
        if (rating >= i + 0.8) {
            html += '<i class="fas fa-star"></i>';
        } else if (rating >= i + 0.3) {
            html += '<i class="fas fa-star-half-alt"></i>';
        } else {
            html += '<i class="far fa-star"></i>';
        }
    }
    return html;
}

/* Format a rent value like "8,500". */
function formatRent(rent) {
    return Number(rent).toLocaleString("en-IN");
}

/* ---------- Theme toggle ---------- */

/* Reflect the active theme on the navbar toggle (moon = switch to dark). */
function updateThemeToggle() {
    var btn = document.getElementById("theme-toggle");
    if (!btn) {
        return;
    }
    var isDark = document.documentElement.getAttribute("data-theme") === "dark";
    var icon = btn.querySelector("i");
    if (icon) {
        icon.className = isDark ? "fas fa-sun" : "fas fa-moon";
    }
    btn.setAttribute("aria-pressed", isDark ? "true" : "false");
}

function applyTheme(theme, persist) {
    document.documentElement.setAttribute("data-theme", theme);
    if (persist) {
        try {
            localStorage.setItem("pglife-theme", theme);
        } catch (e) {}
    }
    updateThemeToggle();
}

function initThemeToggle() {
    var btn = document.getElementById("theme-toggle");
    if (!btn) {
        return;
    }

    btn.addEventListener("click", function () {
        var isDark = document.documentElement.getAttribute("data-theme") === "dark";
        applyTheme(isDark ? "light" : "dark", true);
    });

    /* Until the user chooses explicitly, keep following the OS setting live. */
    var stored = null;
    try {
        stored = localStorage.getItem("pglife-theme");
    } catch (e) {}
    if (!stored && window.matchMedia) {
        var mq = window.matchMedia("(prefers-color-scheme: dark)");
        var onSystemChange = function (e) {
            applyTheme(e.matches ? "dark" : "light", false);
        };
        if (mq.addEventListener) {
            mq.addEventListener("change", onSystemChange);
        } else if (mq.addListener) {
            mq.addListener(onSystemChange);
        }
    }

    updateThemeToggle();
}

/* Keep automatic photos optional for keyboard users and reduced-motion users. */
function initCarouselAccessibility() {
    var motion = window.matchMedia("(prefers-reduced-motion: reduce)");
    $(".carousel-pause").each(function () {
        var button = this;
        var $carousel = $(button.getAttribute("data-carousel"));
        var paused = false;

        function setPaused(value) {
            paused = value;
            $carousel.carousel(paused ? "pause" : "cycle");
            if (paused) {
                /* Bootstrap 4 schedules a restart after touch gestures. */
                clearTimeout($carousel.data("bs.carousel").touchTimeout);
            }
            button.setAttribute("aria-pressed", paused ? "true" : "false");
        }

        button.addEventListener("click", function () { setPaused(!paused); });
        /* WAI-ARIA's carousel pattern requires explicit resume after focus. */
        $carousel.on("focusin", function () { setPaused(true); });
        $carousel.on("touchend pointerup", function () {
            if (paused) { setPaused(true); }
        });
        $carousel.on("click", "[data-slide], [data-slide-to]", function () {
            /* Bootstrap may restart cycling when selecting the active photo. */
            if (paused) { setTimeout(function () { setPaused(true); }, 0); }
        });
        if (motion.matches) { setPaused(true); }
        var onMotionChange = function (event) {
            if (event.matches) { setPaused(true); }
        };
        if (motion.addEventListener) {
            motion.addEventListener("change", onMotionChange);
        } else if (motion.addListener) {
            motion.addListener(onMotionChange);
        }
    });
}

/* Bootstrap starts carousels on load; apply motion preferences afterwards. */
$(window).on("load", initCarouselAccessibility);

$(document).ready(function () {
    initThemeToggle();
    /* Toggle interest on any interested button (delegated, so it also
       works for cards rendered by the React property list). */
    $(document).on("click", ".interested-btn", function (e) {
        e.preventDefault();
        var $icon = $(this);
        var propertyId = $icon.attr("property_id");

        if (!propertyId) {
            propertyId = $icon.attr("data-property-id");
        }
        if (!propertyId) {
            return;
        }

        showLoading();
        $.post(
            "api/toggle_interested.php",
            { property_id: propertyId, csrf_token: csrfToken() },
            function (data) {
                hideLoading();
                if (data.success) {
                    $icon.attr("aria-pressed", data.interested ? "true" : "false");
                    /* On the dashboard, remove the card when un-interested. */
                    if ($icon.closest("#interested-list").length > 0 && !data.interested) {
                        $icon.closest(".property-card").fadeOut(function () {
                            $(this).remove();
                        });
                    } else {
                        /* Otherwise swap the outline/filled heart. */
                        if (data.interested) {
                            $icon.removeClass("far").addClass("fas");
                        } else {
                            $icon.removeClass("fas").addClass("far");
                        }

                        /* Bump the interested count shown next to the heart. */
                        var $count = $icon.closest(".interested-container").find(".interested-user-count");
                        if ($count.length > 0) {
                            var current = parseInt($count.text(), 10) || 0;
                            $count.text(data.interested ? current + 1 : Math.max(0, current - 1));
                        }
                    }
                } else {
                    if (data.message && data.message.indexOf("login") > -1) {
                        if ($("#login-modal").length > 0) {
                            $("#login-modal").modal("show");
                        } else {
                            alert(data.message);
                        }
                    } else {
                        alert(data.message || "Something went wrong!");
                    }
                }
            },
            "json"
        );
    });
});
