(function($) {

    $(document).ready(function() {


        "use strict";

        $(".video-link").each(function() {

            if ($(this).hasClass('other')) {
                return true;
            }

            // $(this).find('a').hide();

            var iframe_url;
            if ($(this).hasClass('youtube')) {
                $(this).css('background-image', 'url(https://i.ytimg.com/vi/' + $(this).attr('data-videoId') + '/0.jpg)');
                iframe_url = 'https://www.youtube.com/embed/' + $(this).attr('data-videoId') + '?autoplay=1&autohide=1';
                if ($(this).data('params')) iframe_url+='&'+$(this).data('params');

            } else if ($(this).hasClass('vimeo')) {
                var that = this;
                $.getJSON('https://vimeo.com/api/oembed.json?url=https%3A//vimeo.com/' + $(that).attr('data-videoId'), function(data) {
                    iframe_url = 'https://player.vimeo.com/video/' + $(that).attr('data-videoId') + '?autoplay=1';
                    $(that).css('background-image', 'url(' + data.thumbnail_url + ')');
                    if ($(that).data('params')) iframe_url+='&'+$(that).data('params');

                    //Slick Slider refresh, da Ajax Call nach Initialisation eintrifft
                    $('.slick-slider').slick('refresh');
                });
            }

            $(document).on('click', '[data-videoId="' + $(this).attr('data-videoId') + '"]', function() {
                var iframe = $('<iframe/>', {'frameborder': '0', 'src': iframe_url, 'width': $(this).width(), 'height': $(this).innerHeight(), 'webkitallowfullscreen':'', 'mozallowfullscreen':'', 'allowfullscreen' : '' });
                $(this).replaceWith(iframe);
            });


        });




    });


})(jQuery);
