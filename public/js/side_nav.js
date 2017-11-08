$(document).on("click", "#menu-toggle", function () {
    "use strict";
    $('.side-nav').toggleClass('active');
});

$(function () {
    "use strict";
    $('[data-toggle="tooltip"]').tooltip();
})