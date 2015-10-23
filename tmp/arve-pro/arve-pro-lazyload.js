(function ($) {
	'use strict';
	/*global screenfull */

	var lazyload = function() {

		$('.arve-wrapper[data-arve-mode="lazyload"], ' +
		'.arve-wrapper[data-arve-mode="lazyload-fullscreen"], ' +
		'.arve-wrapper[data-arve-mode="lazyload-fixed"]').each(function () {

			var wrap  = $(this),
			mode      = wrap.attr('data-arve-mode'),
			iframe    = wrap.find('iframe'),
			btn_start = wrap.find('.arve-btn-start');
			//var id        = wrap.attr('id').replace('video-', '')

			if ( !screenfull.enabled && mode === 'lazyload-fullscreen' ) {
				mode = 'lazyload';
			}

			btn_start.on( 'click', function() {

				if( mode === 'lazyload-fixed' ) {

					if (screenfull.enabled) {
						screenfull.request();
					}

					$( 'html' ).addClass( 'arve-html' );
					//$( 'body' ).prepend( '<div class="arve-spacer"></div>' )

					//iframe.wrap('<div class="arve-inner arve-iframe-wrap"></div>');

					wrap.addClass('arve-wrapper-fill');

				} else if( mode === 'lazyload-fullscreen' ) {

					if (screenfull.enabled) {
						screenfull.request(iframe[0]);
					}
				}

				if ( wrap.is('[data-arve-grow="1"]') ) {
					wrap.css('max-width', 'none');
				}

				btn_start.addClass('arve-hidden');
				iframe.removeClass('arve-hidden');

				iframe.attr('src', iframe.attr('data-src'));

				// document.location.hash = 'video-' + id
			});

			wrap.find('.arve-btn-close').on('click', function() {

				if( mode === 'lazyload-fixed' ) {

					$( '.arve-spacer' ).remove();

					if (screenfull.enabled) {
						screenfull.exit();
					}

					//iframe.unwrap()

					wrap.css('height', '');
					wrap.css('width', '');

					wrap.removeClass('arve-wrapper-fill');
				}
			});
		});
	};

	lazyload();

	window.addEventListener( "hashchange", lazyload );

	if (screenfull.enabled) {

		document.addEventListener(screenfull.raw.fullscreenchange, function () {

			if (!screenfull.isFullscreen && $('.arve-wrapper-fill').length ) {
				$('.arve-wrapper-fill .arve-btn-close').trigger('click');
			}
		});
	}

/*	$(document).ready(function() {

		var hash = window.location.hash

		console.log(hash.substring(0, 6))

		if( hash.substring(0, 6) === '#arve-' ) {
			wrap = hash.replace('#arve-', '#arve-wrapper-')

			console.log(el)
			$(el).find('.arve-iframe-btn').trigger( "click" )
			//$(document.getElementById(el)).trigger( "click" )
		}
	})*/

}(jQuery));
