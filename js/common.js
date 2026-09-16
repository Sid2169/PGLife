/* common.js
 * Shared behaviour for all PG Life pages.
 * The login/signup/logout flows are now handled server-side
 * via PHP sessions. This file handles minor UI helpers.
 */
$(document).ready(function () {
    /* Loading animation on page transition */
    $(window).on("beforeunload", function () {
        $("#loading").addClass("loading");
    });
});
