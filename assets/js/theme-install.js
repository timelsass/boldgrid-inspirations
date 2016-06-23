( function ( $ ) {
	"use strict";
	$( function () {
		postInspirationInstall();
	} );
	var postInspirationInstall = function () {
		var $p = $( 'a[title="Enable theme for this site"]' ).closest( 'p' );

		if ( $p.length ) {
			//Add text about the users boldgrid theme install
			var source   = $( '#inspiration-post-theme-install' ).html();
			if ( source ) {
				var template = Handlebars.compile( source );
				$( template() ).insertBefore( $p );
			}
		}
	};
} )( jQuery );
