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
			$( '.top-menu a' ).addClass( 'disabled' );
		} else {
			$( 'input[name="coin-budget"]' ).attr( 'disabled', false );
			$( 'input[name="pageset"]' ).attr( 'disabled', false );
			$( 'input[name="sub-category"]' ).attr( 'disabled', false );
			$( '#screen-content .button' ).prop( 'disabled', false );
			$( '.top-menu a' ).removeClass( 'disabled' );
		}
	};

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

		// Load the theme title and sub category title.
		$( '#sub-category-title' ).html( '- ' + self.$theme.closest( '.theme' ).attr( 'data-sub-category-title' ) );
		$( '#theme-title' ).html( self.$theme.closest( '.theme' ).attr( 'data-theme-title' ) );

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
		};
		self.ajax.ajaxCall( {'category_id' : $theme.closest( '.theme' ).attr( 'data-category-id' )}, 'get_category_page_sets', pagesetSuccess );
	};

	this.toggleCheckbox = function () {
		var $subCategory = $( 'input[name="sub-category"]:checked' );
		$subCategory.parent().css( 'background', 'blue' );
	};

	this.devicePreviews = function () {
		var previewer = $( '#theme-preview' );
		// Desktop previews.
		$( '.wrap' ).on( 'click', '.preview-desktop', function() {
			$( '.devices .active' )
				.attr( 'aria-pressed', 'false' )
				.removeClass( 'active' );
			$( this ).attr( 'aria-pressed', 'true' ).addClass( 'active' );
			previewer.removeClass();
		});
		// Tablet previews.
		$( '.wrap' ).on( 'click', '.preview-tablet', function() {
			$( '.devices .active' )
				.attr( 'aria-pressed', 'false' )
				.removeClass( 'active' );
			$( this ).attr( 'aria-pressed', 'true' ).addClass( 'active' );
			previewer.removeClass().addClass( 'preview-tablet' );
		});
		// Mobile previews.
		$( '.wrap' ).on( 'click', '.preview-mobile', function() {
			$( '.devices .active' )
				.attr( 'aria-pressed', 'false' )
				.removeClass( 'active' );
			$( this ).attr( 'aria-pressed', 'true' ).addClass( 'active' );
			previewer.removeClass().addClass( 'preview-mobile' );
		});
	};
	/**
	 * Init.
	 *
	 * @since xxx
	 */
	this.init = function() {
		self.initCategories();
		self.toggleCheckbox();
		self.devicePreviews();

		// Hovers.
		$( '.wrap' ).on( 'mouseenter mouseleave', '.sub-category, .pageset-option, .coin-option', function() {
			$( this ).toggleClass( 'blue' );
		});

		$( '.wrap' ).on( 'change', 'input[name="sub-category"]', function() {
			var $subCategory = $( this );
			self.toggleSubCategory( $subCategory );
		});

		// Subcategories.
		$( '.wrap' ).on( 'click', '.sub-category', function() {
			var $subCategory = $( this ).find( 'input[name="sub-category"]' );
			$( '.sub-category.active' ).removeClass( 'active' );
			$subCategory.prop( 'checked', true );
			if ( $subCategory.is( ':checked' ) ) {
				$( this ).addClass( 'active' );
			}
			self.toggleSubCategory( $subCategory );
		});

		$( '.wrap' ).on( 'click', '.theme-actions a.button-primary', function() {
			var $theme = $( this );
			self.$theme = $theme;
			self.chooseTheme( $theme );
		});

		// Pageset Options.
		$( '.wrap' ).on( 'click', '.pageset-option', function() {
			var $pagesetInput = $( this ).find( 'input[name="pageset"]' );
			$( '.pageset-option.active' ).removeClass( 'active' );
			$pagesetInput.prop( 'checked', true );
			if ( $pagesetInput.is( ':checked' ) ) {
				$( this ).addClass( 'active' );
			}
			self.loadBuild();
		});

		// Coin Budgets.
		$( '.wrap' ).on( 'click', '.coin-option', function() {
			var $coinInput = $( this ).find( 'input[name="coin-budget"]' );
			$( '.coin-option.active' ).removeClass( 'active' );
			$coinInput.prop( 'checked', true );
			if ( $coinInput.is( ':checked' ) ) {
				$( this ).addClass( 'active' );
			}
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
			var $iframe = $( this );
			$( '#screen-content .boldgrid-loading' ).fadeOut( function() {
				self.allActions( 'enable' );
				$( '#build-cost' )
					.html( $iframe.attr( 'data-build-cost') )
					.animate( { opacity: 1 }, 400 );
				$( '#screen-content iframe#theme-preview' ).fadeIn();
			} );
		});
	};

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
	};

	/**
	 * Init Themes.
	 *
	 * @since xxx
	 */
	this.initThemes = function() {
		var template = wp.template( 'theme' );

		data = {
			'site_hash' : self.configs.site_hash,
		};

		var cow = function( msg ) {
			_.each( msg.result.data, function( build ){
				self.$themes.append( template( { configs: IMHWPB.configs, build: build } ) );
			});

			$( "img.lazy" ).lazyload({threshold : 400});
		};

		self.ajax.ajaxCall( data, 'get_generic', cow );

//		return;

//		var success_action = function( msg ) {
//			var template = wp.template( 'theme' );
//
//			self.themes = msg.result.data;
//
//
//			_.each( self.categories, function( category ) {
//				_.each( category.subcategories, function( sub_category ) {
//					_.each( self.themes, function( theme ) {
//						var successBuildGeneric = function( msg ) {
//							console.log( msg );
//							// self.$themes.append( template( msg.result.data.profile ) );
//							self.$themes.append( template( { configs: IMHWPB.configs, profile: msg.result.data.profile, sub_category: sub_category, theme: theme, category: category } ) );
//						}
//
//						data = {
//							'theme_id' :			theme.Id,
//							'cat_id' :				category.id,
//							'sub_cat_id' :			sub_category.id,
//							'page_set_id' :			sub_category.defaultPageSetId,
//							'pde' :					null,
//							'wp_language' :			'en-US',
//							'coin_budget' :			20,
//							'theme_version_type' :	null,
//							'page_version_type' :	null,
//							'site_hash' :			self.configs['site_hash'],
//							'inspirations_mode' :	'standard',
//							'is_generic' :			'true'
//						};
//						self.ajax.ajaxCall( data, 'get_generic', successBuildGeneric );
//
//						// console.log( { sub_category: sub_category, theme: theme, category: category } );
//						// self.$themes.append( template( { sub_category: sub_category, theme: theme, category: category } ) );
//					});
//				});
//			});
//		};

		//self.ajax.ajaxCall( {'inspirations_mode' : 'standard'}, 'get_all_active_themes', success_action );
	};

	/**
	 * Load a new build on the Content tab.
	 *
	 * @since xxx
	 */
	this.loadBuild = function() {
		// Disable all actions.
		self.allActions( 'disable' );

		// Load our loading graphic.
		$( '#build-cost' ).animate( { opacity: 0 }, 400 );
		$( '#screen-content iframe#theme-preview' ).fadeOut( function() {
			$( '#screen-content .boldgrid-loading' ).fadeIn( );
		} );


		var success_action = function( msg ) {
			var $screenContent = $( '#screen-content' ),
				$iframe = $screenContent.find( 'iframe#theme-preview' ),
				url = msg.result.data.profile.preview_url;

			$iframe
				.attr( 'src', url )
				.attr( 'data-build-cost', msg.result.data.profile.coins );
		};

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
			'site_hash' :			self.configs.site_hash,
			'inspirations_mode' :	'standard',
			'is_generic' :			( '1' === self.$pageset.attr( 'data-is-default' ) ? 'true' : 'false' ),
		};

		self.ajax.ajaxCall( data, 'get_build_profile', success_action );
	};

	/**
	 *
	 */
	this.toggleCategory = function( $category, $expander ) {
		var categoryId = $category.attr( 'data-category-id' ),
			$subCategories = $category.find( '.sub-categories' );

		/*
		 * If the selected category is expanded, slide it up.
		 * Otherwise, slide up all visible categories and slide down our selected category.
		 */
		if( $subCategories.is( ':visible' ) ) {
			$subCategories.slideUp();
			$expander.removeClass( 'expanded' );
		} else {
			$( '.sub-categories:visible' ).siblings( '.expand' ).removeClass( 'expanded' );
			$( '.sub-categories:visible' ).slideUp();

			$subCategories.slideDown();
			$expander.addClass( 'expanded' );
		}
	};

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
	};

	/**
	 *
	 */
	this.toggleSubCategory = function( $subCategory ) {
		var subCategoryId = $subCategory.attr( 'data-sub-category-id' );

		if( '0' === subCategoryId ) {
			$( '.theme[data-sub-category-id]').removeClass( 'hidden' );
		} else {
			$( '.theme[data-sub-category-id="' + subCategoryId + '"]').removeClass( 'hidden' );
			$( '.theme[data-sub-category-id!="' + subCategoryId + '"]')
				.addClass( 'hidden' )
				.appendTo( '.themes' );
		}

		$("img.lazy").lazyload({threshold : 400});
	};

	$( function() {
		self.init();
	});
};

new IMHWPB.InspirationsDesignFirst( jQuery, IMHWPB.configs );
