var BOLDGRID = BOLDGRID || {};
BOLDGRID.ThemeBuilder = BOLDGRID.ThemeBuilder || {};


(function ( $ ) {
	var self;
	
	BOLDGRID.ThemeBuilder = {
			
		init : function () {
			self.onload();
		},
		onload : function () {
			console.log('fff');
			$( document ).on( 'load', self.requestThemes );
		},
		requestThemes : function () {
			
			var template = wp.template( 'boldgrid-theme-builder-theme' ),
				renderedTemplate = template();
			
			console.log( renderedTemplate );
		}
	};
	
	self = BOLDGRID.ThemeBuilder;
	BOLDGRID.ThemeBuilder.init();
	
})( jQuery );