/**
 * This file helps with adding the "BoldGrid Connect Search" tab to the media
 * modal.
 *
 * Throughout this document, "BoldGrid Connect Search" will be refered to as
 * BGCS. You may also see a few references to .last() or :visible. This is
 * because several media modal's may be on the same page, not all of them active /
 * visible. Using last() / :visible seems to successfully grab the active media
 * modal.
 *
 * @summary Add the BGCS tab to media modals.
 *
 * @since 0.1
 */

var IMHWPB = IMHWPB || {};

/**
 * Add the BGCS tab to media modals.
 *
 * @since 0.1
 */
IMHWPB.InsertMediaTabManager = function( $ ) {
	var self = this;

	/**
	 * A list of selectors, when clicked, that cause the BGCS tab to be added.
	 *
	 * @since 1.1.2
	 */
	self.addTabTriggers = 'div[aria-label="Change"],' +
	// "Add Media" button.
	'#insert-media-button,' +
	// "Insert Media" button.
	'.media-menu-item:contains("' + _wpMediaViewsL10n.insertMediaTitle + '"),' +
	// Customizer Header "Add new image" button.
	'#customize-control-header_image .button.new,' +
	// Customizer Background "Select Image" button.
	'#background_image-button,' +
	// Customizer Background thumbnail.
	'.customize-control-background img.attachment-thumb,' +
	// Customizer Site Icon "Select Image" button.
	'#site_icon-button,' +
	// Customizer Site Logo "Select Image" button.
	'#boldgrid_logo_setting-button';

	$( function() {
		/*
		 * When one of our addTabTrigger elements is clicked, wait 2/10's of a
		 * second and then add our BoldGrid Connect Search tab. The timout is
		 * there to ensure that the media modal has opened, we cannot add the
		 * tab until it has opened. Please advise if you have a better technique
		 * for adding tabs to the media modal.
		 */
		$( document.body ).on( 'click', self.addTabTriggers, function() {
			setTimeout( function() {
				self.addTab();
			}, 200 )
		} );

		self.setIframe();

		self.onTabClick();
	} );

	/**
	 * Add iframe loading message.
	 *
	 * The BGCS iframe takes a few seconds to load. To ease the transition,
	 * we'll show a loading message.
	 *
	 * @since 1.1.2
	 */
	this.addLoadingMessage = function() {
		var $content = $( '.media-frame-content:visible' ), $spinner = $( '<span class="spinner boldgrid_connect_search">Loading BoldGrid Connect Search.</span>' );

		// Add the spinner.
		$content.append( $spinner );

		// Show the spinner for 2 seconds, then fade out half a second.
		setTimeout( function() {
			$spinner.fadeOut( 500, function() {
				$spinner.remove();
			} );
		}, 2000 );
	}

	/**
	 * Add our BGCS tab.
	 *
	 * @since 1.1.2
	 */
	this.addTab = function() {
		var addTab = false,
		// In the left menu, there is an "Image Search" tab.
		$imageSearchTab = $( "a.media-menu-item:contains('Image Search')" ),
		// There may be multiple menus, find the one that is visible.
		$mediaRouter = $( '.media-router:visible' ),
		// Define the html that makes up our tab.
		$tab = $( '<a href="#" class="media-menu-item boldgrid-connect-search hidden">BoldGrid Connect Search</a>' ),
		// Check if there is already a visible "BoldGrid Connect Search" tab.
		$bgcsTab = $mediaRouter.find( '.boldgrid-connect-search' ),
		// Get our "Media Library" tab.
		$libraryTab = $mediaRouter.find( '.media-menu-item:contains("'
		    + _wpMediaViewsL10n.mediaLibraryTitle + '")' ),
		// Get our "Upload Files" tab.
		$uploadTab = $( '.media-menu-item:visible:contains("' + _wpMediaViewsL10n.uploadFilesTitle
		    + '")' ),
		// Find the number of active tabs.
		activeTabs = $mediaRouter.find( '.media-menu-item.active' ).length;

		/*
		 * There are some cases when we don't need to add the tab. For example,
		 * if we're on "Add GridBlocks", we don't want to add our tab. If we
		 * don't see either the "Upload files" or "Media Library" tabs, then
		 * abort.
		 */
		if ( 0 === $libraryTab.length || 0 === $uploadTab.length ) {
			return;
		}

		/*
		 * There are some instances, though rare, that no tabs are selected. If
		 * this is the case, click the "Media Library" tab.
		 */
		if ( activeTabs.length === 0 ) {
			$libraryTab[ 0 ].click();
		}

		/*
		 * Normally, when adding 'tabs' to the wp.media, they're added in the
		 * left menu. BoldGrid Connect Search started off as a left menu item,
		 * but for easier accessability, it was added as a main tab next to
		 * "Upload Files" and "Insert Media". We no longer need the link in the
		 * left menu, so remove it.
		 */
		$imageSearchTab.remove();

		/*
		 * Take action if the tab already exists. For example, the user may have
		 * been on the tab already and clicked 'x' to close the modal, then they
		 * reopened the modal.
		 */
		if ( 1 === $bgcsTab.length ) {
			/*
			 * If the tab is active, 'reset' things by clicking the "Media
			 * Library" tab.
			 */
			if ( $bgcsTab.hasClass( 'active' ) ) {
				$libraryTab[ 0 ].click();
				return;
			} else {
				return;
			}
		}

		$mediaRouter.append( $tab );
		$tab.fadeIn( 500 );
	}

	/**
	 * Event handler for tab clicks.
	 *
	 * @since 1.1.2
	 */
	this.onTabClick = function() {
		$( document.body )
		    .on(
		        'click',
		        '.media-router .media-menu-item',
		        function() {
			        var $content = $( '.media-frame-content:visible' ),
			        // Our BGCS iframe.
			        $iframe = $content.find( '#boldgrid_connect_search' ),
			        // The content for the "Media Library" tab.
			        $library = $content.find( '.attachments-browser' ),
			        // The media router.
			        $mediaRouter = $( '.media-router:visible', window.parent.document ), $priorTab = $mediaRouter
			            .find( '.media-menu-item.active' ), $newTab = $( this ),
			        // The "Media Library" tab.
			        $libraryTab = $mediaRouter.find( '.media-menu-item:contains("'
			            + _wpMediaViewsL10n.mediaLibraryTitle + '")' ),
			        // The tab clicked.
			        $tab = $( this ),
			        // The toolbar, which is located under the content.
			        $toolbar = $( '.media-frame-toolbar:visible' ),
			        // The content for the "Upload Files" tab.
			        $uploader = $content.find( '.uploader-inline-content' ),
			        // The "BoldGrid Connect Search" tab.
			        $bgcsTab = $mediaRouter.find( '.media-menu-item.boldgrid-connect-search',
			            window.parent.document );

			        /*
					 * In order for BGCS to work properly, there needs to be an
					 * .attachments-browser within the DOM. That needed element
					 * is created when the user clicks the "Media Library" tab.
					 * If we've clicked the BGCS tab, and our last tab wasn't
					 * the "Media Library", then we don't have a library. Click
					 * the "Media Library" tab to generate our library, then
					 * click the BGCS tab.
					 */
			        if ( $newTab.is( $bgcsTab ) && !$priorTab.is( $libraryTab ) ) {
				        $libraryTab[ 0 ].click();
				        $bgcsTab[ 0 ].click();
				        return;
			        }

			        /*
			         * Refresh the Media Library if we're going from the BGCS tab to the Library
			         * tab. We may have downloaded an image while in the BGCS tab, so refresh the
			         * Library so we can see our new image.
			         */
			        if( $newTab.is( $libraryTab ) && $priorTab.is( $bgcsTab ) ) {
			        	if( wp.media.frame.content.get() !== null ) {
			        		wp.media.frame.content.get().collection.props.set( { ignore: ( + new Date() ) } );
			        		wp.media.frame.content.get().options.selection.reset();
			        	} else {
			        		wp.media.frame.library.props.set( { ignore: ( + new Date() ) } );
			        	}
			        }

			        // Toggle the '.active' state of the tabs.
			        $( '.media-router:visible .media-menu-item' ).removeClass( 'active' );
			        $tab.addClass( 'active' );

			        // If we have clicked on the BoldGrid tab.
			        if ( $tab.hasClass( 'boldgrid-connect-search' ) ) {
				        // Hide the uploader and the library.
				        $uploader.addClass( 'hidden' );
				        $library.addClass( 'hidden' );

				        // If we don't already have our BoldGrid iframe, add it.
				        if ( $iframe.length == 0 ) {
					        self.addLoadingMessage();
					        $content.append( self.iframe );
				        }
				        $iframe.removeClass( 'hidden' );

				        // Hide the bottom tollbar.
				        $toolbar.addClass( 'hidden' );
				        $content.css( 'bottom', '0px' );
			        } else {
				        // Hide the BGCS iframe.
				        $iframe.addClass( 'hidden' );

				        // Show the uploader and library.
				        $uploader.removeClass( 'hidden' );
				        $library.removeClass( 'hidden' );

				        // Show the bottom toolbar.
				        $toolbar.removeClass( 'hidden' );
				        $content.css( 'bottom', '61px' );
			        }
		        } );
	}

	/**
	 * Configure our BoldGrid Connect Search iframe.
	 *
	 * @since 1.1.2
	 */
	this.setIframe = function() {
		// Configure our post_id parameter for the iframe.
		var post_id_param = ( typeof IMHWPB.post_id === 'undefined' ) ? '' : '&post_id='
		    + IMHWPB.post_id, ref;

		// Configure our referrer parameter for the iframe.
		if ( 'object' == typeof window._wpCustomizeSettings ) {
			ref = 'dashboard-customizer';
		} else if ( 'post' == pagenow || 'page' == pagenow ) {
			ref = 'dashboard-post';
		} else {
			ref = 'dashboard-media';
		}

		self.iframe = '<iframe src="media-upload.php?chromeless=1' + post_id_param
		    + '&tab=image_search&ref=' + ref + '" id="boldgrid_connect_search"></iframe>';
	}
};

new IMHWPB.InsertMediaTabManager( jQuery );