var BOLDGRID = BOLDGRID || {};
BOLDGRID.ThemeBuilder = BOLDGRID.ThemeBuilder || {};


(function ( $ ) {
	var self;
	
	BOLDGRID.ThemeBuilder = {
			
		$themes : null,
		themeTemplate : null,
			
		init : function () {
			$( document ).ready( self.onload );
		},
		onload : function () {
			self.$themes = $('.themes');
			self.themeTemplate = wp.template( 'boldgrid-theme-builder-theme' );
			self.requestThemes();
		},
		requestThemes : function () {
			
			$.ajax( {
			    type: 'post',
			    dataType : 'json',
				url : ajaxurl,
				data : {
					'count': 15,
					'action': 'boldgrid_random_theme'
				}
			} ).success( function ( response ) {
				
				if ( ! response.results ) {
					alert('failure');
				}
				
				$.each( response.results.sites, function () {
					self.$themes.append( self.themeTemplate( this ) );
				} );
			} );
		},
		
	};
	
	self = BOLDGRID.ThemeBuilder;
	BOLDGRID.ThemeBuilder.init();
	
})( jQuery );