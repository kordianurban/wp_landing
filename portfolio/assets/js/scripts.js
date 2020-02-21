jQuery(document).ready(function($) {

    /**
     * sticky header
     */
    $(window).scroll(function() {
        if ( $(window).width() > 767 ) {
            var scroll = $(window).scrollTop();

            if (scroll >= 5) {
                $('#header').addClass('compact');
            } else {
                $('#header').removeClass('compact');
            }
        }
    });

    /**
     * Scroll on anchor
     */
    $(document).on('click', 'a[href^="#"]', function (event) {
        event.preventDefault();

        if ( $.attr(this, 'href').length > 1 ) {
            $('html, body').animate({
                scrollTop: $($.attr(this, 'href')).offset().top - 50
            }, 500);
        }
    });

});
