var IMHWPB = IMHWPB || {};

/**
 * Inspirstions, design first.
 *
 * @since xxx
 */
IMHWPB.InspirationsDesignFirst = function( $, configs ) {
	var self = this;

	this.configs = configs;
//	this.api_url = this.configs.asset_server;
//	this.api_key = this.configs.api_key;
//	this.api_param = 'key';
//	this.api_key_query_str = this.api_param + "=" + this.api_key;

	self.ajax = new IMHWPB.Ajax( configs );

	self.$categories = $( '#categories' );

	self.categories = '';

	self.$themes = $( '.theme-browser .themes');
	self.themes = '';

	self.$theme = '';
	self.$pageset = '';
	self.$budget = '';

	/**
	 * Enable or disable all actions on the page.
	 *
	 * @since xxx
	 */
	this.allActions = function( effect ) {
		if( 'disable' === effect ) {
			$( 'input[name="coin-budget"]' ).attr( 'disabled', true );
			$( 'input[name="pageset"]' ).attr( 'disabled', true );
			$( 'input[name="sub-category"]' ).attr( 'disabled', true );
			$( '#screen-content .button' ).prop( 'disabled', true );
		} else {
			$( 'input[name="coin-budget"]' ).attr( 'disabled', false );
			$( 'input[name="pageset"]' ).attr( 'disabled', false );
			$( 'input[name="sub-category"]' ).attr( 'disabled', false );
			$( '#screen-content .button' ).prop( 'disabled', false );
		}
	}

	/**
	 * User chooses a theme
	 *
	 * @since xxx
	 */
	this.chooseTheme = function( $theme ) {
		// Immediately hide the iframe to give a better transition effect.
		$( '#screen-content iframe#theme-preview' )
			.addClass( 'hidden' )
			.css( 'display', '' );

		self.toggleStep( 'content' );

		$( '[data-step="content"]' ).removeClass( 'disabled' );

		// Reset the coin budget to 20.
		$( 'input[data-coin="20"]' ).prop( 'checked', true );

		// Load pagesets
		var pagesetSuccess = function( msg ) {
			var template = wp.template('pagesets');

			$( '#pageset-options' ).html( ( template( msg.result.data.pageSets ) ) );

			self.$pageset = $( 'input[name="pageset"]:checked' );
			self.$budget = $( 'input[name="coin-budget"]:checked' );

			self.loadBuild();
		}
		self.ajax.ajaxCall( {'category_id' : $theme.closest( '.theme' ).attr( 'data-category-id' )}, 'get_category_page_sets', pagesetSuccess );
	}

	/**
	 * Init.
	 *
	 * @since xxx
	 */
	this.init = function() {
		self.initCategories();

		$( '.wrap' ).on( 'click', '.expand', function() {
			var $category = $( this ).closest( '.category' );
			self.toggleCategory( $category );
		});

		$( '.wrap' ).on( 'change', 'input[name="sub-category"]', function() {
			var $subCategory = $( this );
			self.toggleSubCategory( $subCategory );
		});

		$( '.wrap' ).on( 'click', '.theme-actions a.button-primary', function() {
			var $theme = $( this );
			self.$theme = $theme;
			self.chooseTheme( $theme );
		});

		$( '.wrap' ).on( 'click', 'input[name="pageset"]', function() {
			self.$pageset = $( this );
			self.loadBuild();
		});

		$( '.wrap' ).on( 'click', 'input[name="coin-budget"]', function() {
			self.$budget = $( this );
			self.loadBuild();
		});

		$( '.wrap' ).on( 'click', '.top-menu a', function() {
			var $link = $( this ),
				step = $link.attr( 'data-step' );

			if( $link.hasClass( 'disabled' ) ) {
				return;
			} else {
				self.toggleStep( step );
			}
		});

		$( '#screen-content iframe#theme-preview' ).on( 'load', function() {
			$( '#screen-content .boldgrid-loading' ).fadeOut( function() {
				self.allActions( 'enable' );
				$( '#build-cost' ).animate( { opacity: 1 }, 400 );
				$( '#screen-content iframe#theme-preview' ).fadeIn();
			} );
		});
	}

	/**
	 * Init the list of categories.
	 *
	 * @since xxx
	 */
	this.initCategories = function( ) {

		var success_action = function( msg ) {
			var template = wp.template('init-categories');

			self.categories = msg.result.data.categories;

			self.$categories.html( ( template( self.categories ) ) );

			self.initThemes();
		};

		self.ajax.ajaxCall( {'inspirations_mode' : 'standard'}, 'get_categories', success_action );
	}

	/**
	 * Init Themes.
	 *
	 * @since xxx
	 */
	this.initThemes = function() {
		var success_action = function( msg ) {
			var template = wp.template( 'theme' );

			self.themes = msg.result.data;


			_.each( self.categories, function( category ) {
				_.each( category.subcategories, function( sub_category ) {
					_.each( self.themes, function( theme ) {
						var successBuildGeneric = function( msg ) {
							//console.log( msg );
							// self.$themes.append( template( msg.result.data.profile ) );
							self.$themes.append( template( { configs: IMHWPB.configs, profile: msg.result.data.profile, sub_category: sub_category, theme: theme, category: category } ) );
						}

						data = {
							'theme_id' :			theme.Id,
							'cat_id' :				category.id,
							'sub_cat_id' :			sub_category.id,
							'page_set_id' :			sub_category.defaultPageSetId,
							'pde' :					null,
							'wp_language' :			'en-US',
							'coin_budget' :			20,
							'theme_version_type' :	null,
							'page_version_type' :	null,
							'site_hash' :			self.configs['site_hash'],
							'inspirations_mode' :	'standard',
							'is_generic' :			'true'
						};
						self.ajax.ajaxCall( data, 'get_build_profile', successBuildGeneric );

						// console.log( { sub_category: sub_category, theme: theme, category: category } );
						// self.$themes.append( template( { sub_category: sub_category, theme: theme, category: category } ) );
					});
				});
			});
		};

		self.ajax.ajaxCall( {'inspirations_mode' : 'standard'}, 'get_all_active_themes', success_action );
	}

	/**
	 * Load a new build on the Content tab.
	 *
	 * @since xxx
	 */
	this.loadBuild = function() {
		// Disable all actions.
		self.allActions( 'disable' );

		// Load the theme title and sub category title.
		$( '#sub-category-title' ).html( '- ' + self.$theme.closest( '.theme' ).attr( 'data-sub-category-title' ) );
		$( '#theme-title' ).html( self.$theme.closest( '.theme' ).attr( 'data-theme-title' ) );

		// Load our loading graphic.
		$( '#build-cost' ).animate( { opacity: 0 }, 400 );
		$( '#screen-content iframe#theme-preview' ).fadeOut( function() {
			$( '#screen-content .boldgrid-loading' ).fadeIn( );
		} );

		// Reset the build cost.
		$( '#build-cost' ).css( 'opacity', '0' );


		var success_action = function( msg ) {
			var $screenContent = $( '#screen-content' ),
				$iframe = $screenContent.find( 'iframe#theme-preview' ),
				url = msg.result.data.profile.preview_url;

			$iframe.attr( 'src', url );

			// Load the cost of the build.
			$( '#build-cost' ).html( msg.result.data.profile.coins )
		}

		data = {
			'theme_id' :			self.$theme.closest( '.theme' ).attr( 'data-theme-id' ),
			'cat_id' :				self.$theme.closest( '.theme' ).attr( 'data-category-id' ),
			'sub_cat_id' :			self.$theme.closest( '.theme' ).attr( 'data-sub-category-id' ),
			'page_set_id' :			self.$pageset.attr( 'data-page-set-id' ),
			'pde' :					null,
			'wp_language' :			'en-US',
			'coin_budget' :			self.$budget.attr( 'data-coin' ),
			'theme_version_type' :	null,
			'page_version_type' :	null,
			'site_hash' :			self.configs['site_hash'],
			'inspirations_mode' :	'standard',
			'car' : 'moose'
		};

		self.ajax.ajaxCall( data, 'get_build_profile', success_action );
	}

	/**
	 *
	 */
	this.toggleCategory = function( $category ) {
		var categoryId = $category.attr( 'data-category-id' ),
			$subCategories = $category.find( '.sub-categories' );

		/*
		 * If the selected category is expanded, slide it up.
		 * Otherwise, slide up all visible categories and slide down our selected category.
		 */
		if( $subCategories.is( ':visible' ) ) {
			$subCategories.slideUp();
		} else {
			$( '.sub-categories:visible' ).slideUp();
			$subCategories.slideDown();
		}
	}

	/**
	 *
	 */
	this.toggleStep = function( step ) {
		var $content = $( '#screen-content' ),
			$design = $( '#screen-design' ),
			$contentLink = $( '[data-step="content"]' ),
			$designLink = $( '[data-step="design"]' );

		if( 'design' === step ) {
			$contentLink.removeClass( 'active' );
			$designLink.addClass( 'active' );

			$content.addClass( 'hidden' );
			$design.removeClass( 'hidden' );
		} else {
			$contentLink.addClass( 'active' );
			$designLink.removeClass( 'active' );

			$content.removeClass( 'hidden' );
			$design.addClass( 'hidden' );
		}
	}

	/**
	 *
	 */
	this.toggleSubCategory = function( $subCategory ) {
		var subCategoryId = $subCategory.attr( 'data-sub-category-id' );

		if( '0' === subCategoryId ) {
			$( '.theme[data-sub-category-id]').removeClass( 'hidden' );
		} else {
			$( '.theme[data-sub-category-id="' + subCategoryId + '"]').removeClass( 'hidden' );
			$( '.theme[data-sub-category-id!="' + subCategoryId + '"]').addClass( 'hidden' );
		}
	}

	$( function() {
		self.init();
	});
};

new IMHWPB.InspirationsDesignFirst( jQuery, IMHWPB.configs );
