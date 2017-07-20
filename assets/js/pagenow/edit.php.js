var IMHWPB = IMHWPB || {};

/**
 * A class to load when WordPress' pagenow is 'edit.php'.
 *
 * @since 1.0.10
 */
IMHWPB.PagenowEdit = function( $ ) {
	var self = this;

	self.baseAdmin = new IMHWPB.BaseAdmin();

	$( function() {
		self.init();
	});

	/**
	 * Append "New From GridBlocks" button to the top of the page.
	 *
	 * @since 1.0.10
	 */
	this.gridblock_sets_append_button = function() {
		self.baseAdmin.$wrap.find( '.page-title-action' ).after( '<a href="' +
			IMHWPB.gridblock_sets_admin + '" class="page-title-action">' +
			self.baseAdmin.strings.add_gridblock_set + '</a>' );
	};

	/**
	 * Init.
	 *
	 * @since 1.0.10
	 */
	this.init = function() {
		self.init_gridblock_sets();
	};

	/**
	 * Init all actions on 'edit.php' pertaining to GridBlock Sets.
	 *
	 * @since 1.0.10
	 */
	this.init_gridblock_sets = function() {
		// If we're not on "Pages", abort.
		if ( 'page' != self.baseAdmin.GetURLParameter( 'post_type' ) ) {
			return;
		}

		// If we're not using the BoldGrid menu:
		if ( 0 === IMHWPB.configs.settings.boldgrid_menu_option ) {
			self.gridblock_sets_append_button();
		} else {
			self.baseAdmin.$wrap.find( '.page-title-action' )
				.attr( 'href', IMHWPB.gridblock_sets_admin );
		}
	};
};

new IMHWPB.PagenowEdit( jQuery );
