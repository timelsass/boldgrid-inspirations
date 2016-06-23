/**
 * This file is inteded for js that will appear on every admin page
 */

var IMHWPB = IMHWPB || {};

IMHWPB.BaseAdmin = function() {
	var self = this;

	// References to use as selectors.
	self.$wrap = jQuery( '.wrap' );
	self.$wrap_header = jQuery( 'h1', self.$wrap );

	self.strings = {
		'add_gridblock_set' : 'New From GridBlocks'
	};

	jQuery( function() {
		// Within Dashboard >> Media >> BoldGrid Connect Search, self.init is
		// not a function. Therefore, only self.init if self.init is found to be
		// a function.
		if ( typeof self.init === 'function' ) {
			self.init();
		}
	});

	/**
	 * Init.
	 *
	 * @since 1.0.10
	 */
	this.init = function() {
		/*
		 * Avoid this error: Uncaught TypeError: self.update_customizer_link is
		 * not a function.
		 *
		 * Only call self.update_customizer_link if it is a function.
		 */
		if ( 'function' === typeof ( self.update_customizer_link ) ) {
			self.update_customizer_link();
		}

		self.init_gridblock_sets();
	};

	/**
	 * Get parameter from URL
	 *
	 * @link http://www.jquerybyexample.net/2012/06/get-url-parameters-using-jquery.html
	 */
	this.GetURLParameter = function( sParam ) {
		var sPageURL = window.location.search.substring( 1 );
		var sURLVariables = sPageURL.split( '&' );
		for ( var i = 0; i < sURLVariables.length; i++ ) {
			var sParameterName = sURLVariables[i].split( '=' );
			if ( sParameterName[0] == sParam ) {
				return sParameterName[1];
			}
		}
	};

	/**
	 *
	 */
	this.show_pointer = function( element, selector ) {
		// Abort if necessary.
		if ( typeof WPHelpPointerIndex === 'undefined' ) {
			return;
		}

		// Get the pointer.
		var i = WPHelpPointerIndex[selector];
		pointer = WPHelpPointer.pointers[i];
		if ( typeof pointer == 'undefined' ) {
			return;
		}

		// If the pointer has not been dismissed, show it.
		var pointer_is_dismissed = jQuery( element )
			.attr( 'data-pointer-is-dismissed' );
		if ( 'yes' != pointer_is_dismissed ) {
			wp_help_pointer_open( i );
		}
	};

	/**
	 * Sort a column in a table.
	 *
	 * @thanks http://stackoverflow.com/questions/16588123/sorting-tables-by-columns-jquery
	 */
	this.sort_table_column = function( this_th ) {
		/**
		 * Get the th the user clicked on.
		 *
		 * For example, if you're sorting by date, it will be:
		 */
		/*
		 * <th class='sort-date sorted asc'>
		 *
		 * <a href=''>
		 *
		 * <span>Date</span>
		 *
		 * <span class="sorting-indicator"></span>
		 *
		 * </a>
		 *
		 * </th>
		 */
		var $this_th = jQuery( this_th ), sort_order;

		/**
		 * Get the current sort and define the new sort.
		 */
		if ( $this_th.hasClass( 'asc' ) ) {
			sort_order = 'desc';
			$this_th.removeClass( 'asc' ).addClass( 'desc' );
		} else {
			sort_order = 'asc';
			$this_th.removeClass( 'desc' ).addClass( 'asc' );
		}

		var $tbody = $this_th.closest( 'table' ).children( 'tbody' );
		$tbody.find( 'tr' ).sort( function( a, b ) {
			var tda = jQuery( a ).find( 'td:eq(' + $this_th.index() + ')' ).text();

			var tdb = jQuery( b ).find( 'td:eq(' + $this_th.index() + ')' ).text();

			if ( 'desc' == sort_order ) {
				return tda < tdb ? 1 : tda > tdb ? -1 : 0;
			} else {
				return tda > tdb ? 1 : tda < tdb ? -1 : 0;
			}
		}).appendTo( $tbody );
	};

	/**
	 * Init any methods needed pertaining to "Add GridBlock Set".
	 *
	 * @since 1.0.10
	 */
	this.init_gridblock_sets = function() {
		// If we do not have IMHWPB.configs, such as in the Customizer, abort.
		if ( typeof IMHWPB.configs === 'undefined' ) {
			return;
		}

		// If we're using the BoldGrid Menu System, update the Top Menu > New >
		// Page link.
		if ( 1 == IMHWPB.configs.settings.boldgrid_menu_option ) {
			jQuery( '#wp-admin-bar-new-page a')
				.attr( 'href', IMHWPB.gridblock_sets_admin );
		}
	};

	/**
	 * Ensure "Customize" link goes to customize.php.
	 *
	 * There are several plugins, such as "theme check", that modify the link
	 * where "Customize" goes. This function will change it back to
	 * customize.php
	 */
	this.update_customizer_link = function() {
		var useAdminMenu = 0;

		// Set useAdminMenu.
		if ( IMHWPB.configs !== undefined && IMHWPB.configs.settings !== undefined &&
			IMHWPB.configs.settings.boldgrid_menu_option !== undefined ) {
				useAdminMenu = IMHWPB.configs.settings.boldgrid_menu_option;
		}

		if ( 1 == useAdminMenu && 'undefined' != typeof pagenow && 'dashboard-network' != pagenow ) {
			// Configure the correct link.
			var correct_link = 'customize.php?return=' +
				encodeURIComponent( window.location.pathname + window.location.search );

			// Apply this link to "Customize".
			jQuery( '#menu-appearance a.menu-top' ).attr( 'href', correct_link );
		}
	};

	/**
	 * Update the shopping cart total.
	 */
	this.update_header_cart = function( change ) {
		// Get the cart element.
		var $cart = jQuery( '#wp-admin-bar-pfp a' );

		// <span class="ab-icon"></span> (10)
		var cart_html = $cart.html();

		// Update the current price by change.
		var current_price = parseInt( cart_html.replace( /\D/g, '' ) );
		var new_price = current_price + parseInt( change );

		// <span class="ab-icon"></span> (20)
		var new_cart_html = cart_html.replace( '(' + current_price + ')',
			'(' + new_price + ')' );

		// Update the cart element.
		$cart.html( new_cart_html );
	};
};

new IMHWPB.BaseAdmin();
