$(document).ready(function () {

    // More Button toggles description
    $('.more-button').on('click', function () {
        $(this).parent().parent().find('.description-container').toggle();
    });
    $('.close').on('click', function () {
        $(this).parent().parent().toggle(); 
    });
    $('.button-close').on('click', function () {
        $(this).parent().parent().parent().toggle(); 
    });

});