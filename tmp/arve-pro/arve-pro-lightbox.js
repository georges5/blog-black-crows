(function ($) {
	'use strict';

	$(document).on( 'lity:open', function(event) {

		$('*').not('.lity, .lity-wrap, .lity-close').filter(function() {
    		return $(this).css("position") === 'fixed';
		}).addClass('arve-hidden').attr('data-arve-hidden-fixed', 'true');
	});

	$(document).on( 'lity:ready', function(event) {

		var i = $( '.lity-content iframe' );

		i.attr( 'src', i.attr('data-src') );
		i.attr( 'data-arve-lightbox', 'open' );
	});

	$(document).on( 'lity:close', function(event) {

		$( '[data-arve-lightbox]' ).removeAttr( 'src data-arve-lightbox' );
		$( '[data-arve-hidden-fixed]' ).removeClass( 'arve-hidden' );
	});

}(jQuery));
