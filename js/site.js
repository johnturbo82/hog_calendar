$(document).ready(function () {

    // More Button toggles description
    $('.more-button').on('click', function () {
        $(this).parent().parent().find('.description-container').toggle();
    });
    $('.close').on('click', function () {
        $(this).parent().parent().parent().toggle();
    });
    $('.button-close').on('click', function () {
        $(this).parent().parent().parent().toggle();
    });

});

const isIOSWebApp =
    ('standalone' in window.navigator) && window.navigator.standalone ||
    window.matchMedia('(display-mode: standalone)').matches;

if (isIOSWebApp) {
    document.documentElement.classList.add('is-webapp');
}