

 (function($) {

 	"use strict";
	$(document).ready(function() {


		jQuery('#slider1').camera({
			height: '800px',
			thumbnails: false,
			pagination: true,
			playPause: false,
			overlayer: true,
			pieDiameter: 40,
			time: 3000
			
		});

	});	

})(jQuery);