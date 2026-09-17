/* app.js
 * Shared AJAX behaviour for PG Life.
 * - Toggles "interested" state without a page reload.
 * - Shows a loading overlay during AJAX calls.
 * - Opens the login modal when a guest tries an action that needs login.
 */

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

$(document).ready(function () {
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