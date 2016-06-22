var IMHWPB = IMHWPB || {};

IMHWPB.StockImageSearch = function( configs, $ ) {
	var self = this;

	this.configs = configs;

	this.api_url = this.configs.asset_server;
	this.api_key = this.configs.api_key;

	this.api_param = 'key';
	this.api_key_query_str = this.api_param + "=" + this.api_key;

	this.last_query = '';
	this.page = 1;
	this.currently_searching = 0;

	// include additional submodules
	self.ajax = new IMHWPB.Ajax( configs );
	self.baseAdmin = new IMHWPB.BaseAdmin();

	$c_imhmf = jQuery( '.imhwpb-media-frame' );
	$c_sr = jQuery( '#search_results', $c_imhmf );

	jQuery( function() {
		// When the page has finished loading, enable the search button.
		$( '#image_search .button-primary', $c_imhmf ).prop( 'disabled', false );

		// event handler: user clicks search
		jQuery( '#image_search', $c_imhmf ).on( 'submit', function() {
			self.initiate_stock_image_search();
			return false;
		} );

		// event handler: user filters by license attribution
		jQuery( '#attribution', $c_imhmf ).on( 'click', function( value ) {
			self.toggle_search_results_by_requires_attribution();
		} );

		jQuery( '#search_results', $c_imhmf ).scroll( function() {
			self.search_results_scroll();
		} );
	} );

	// this function is triggered by the click/button/search event handler
	this.initiate_stock_image_search = function() {
		var query = jQuery( '#media-search-input', $c_imhmf ).val();

		// if we're searching for a different word, reset the search
		if ( self.last_query != '' && query != self.last_query ) {
			self.reset_search();
		}
		self.last_query = query;

		// prevent empty searches
		if ( query.trim() == '' ) {
			alert( "Please enter a search term." );
			return false;
		}

		// Are we already search?
		if ( 1 == self.currently_searching ) {
			return false;
		} else {
			self.currently_searching = 1;
		}

		// Show "searching" message
		if ( self.page == 1 ) {
			jQuery( $c_sr ).append( "<div class='loading_message pointer'>Searching...</div>" );
		} else {
			jQuery( '.loading_message', $c_sr ).html( "Searching..." );
		}

		// setup our variables
		var data = {
			'query' : query,
			'free' : jQuery( '#free', $c_imhmf ).val(),
			'attribution' : jQuery( '#attribution', $c_imhmf ).is( ':checked' ),
			'paid' : jQuery( '#paid', $c_imhmf ).val(),
			'palette' : jQuery( '#palette', $c_imhmf ).val(),
			'page' : self.page,
		};

		var api_call_image_search_success_action = function( msg ) {
			// if we have search results
			if ( msg.result.data.length > 0 ) {
				var source = jQuery( "#search-results-template" ).html();
				var template = Handlebars.compile( source );
				jQuery( '#search_results', $c_imhmf ).append( template( msg.result ) );

				// event handler: user clicks search result
				jQuery( 'li.attachment', $c_imhmf ).on( 'click', function() {
					self.event_handler_search_result_click( this );
				} );

				var $search_results = jQuery( '#search_results', $c_imhmf );

				jQuery( '.loading_message', $c_sr )
				    .appendTo( $c_sr )
				    .css( 'display', 'inherit' )
				    .html(
				        '<strong>Scroll down</strong> or <strong>click here</strong> to load more search results' )
				    .on( 'click', function() {
					    self.initiate_stock_image_search();
					    return false;
				    } );

				// update the page value (page number for pagination)
				self.page++;

				// else [we have no search results]
			} else {
				var $search_results = jQuery( '#search_results', $c_imhmf );

				if ( '1' == self.page ) {
					var message = 'No search results.';
				}

				var no_search_results = '1' == self.page ? 'No search results'
				    : 'No more search results';

				jQuery( '.loading_message', $c_sr ).appendTo( $c_sr ).css( 'display', 'inherit' )
				    .html( no_search_results );
			}

			self.currently_searching = 0;

			// Toggle attribution:
			self.toggle_search_results_by_requires_attribution();
		};

		self.ajax.ajaxCall( data, 'image_search', api_call_image_search_success_action );
	};

	/**
	 *
	 */
	this.event_handler_search_result_click = function( result ) {
		var image_provider_id = jQuery( result ).data( 'image-provider-id' );
		var id_from_provider = jQuery( result ).data( 'id-from-provider' );

		var attachment_details = jQuery( '#attachment_details', $c_imhmf );

		// show loading message...
		jQuery( attachment_details )
		    .empty()
		    .html(
		        "<div class='loading_message white-bg'><span class='spinner is-active'></span>Loading image details</div>" );

		/**
		 * Toggle 'details selected' classes
		 */
		jQuery( 'li.attachment', $c_imhmf ).each( function() {
			if ( this != result ) {
				jQuery( this ).removeClass( 'details selected' );
			}
		} );
		jQuery( result ).toggleClass( 'details selected' );

		// configure data to send with ajax request
		var data = {
		    'image_provider_id' : image_provider_id,
		    'id_from_provider' : id_from_provider
		};

		// after ajax command, run this
		var api_call_image_get_details_success_action = function( msg ) {
			/*
			 * Determine if we had a successful call. Currently determined by
			 * whether or not an array of downloadable sizes was returned.
			 */
			var sizes = msg.result.data.sizes;
			var has_sizes = ( true == jQuery.isArray( sizes ) && 0 < jQuery( sizes ).length ) ? true
			    : false;

			if ( true === has_sizes ) {
				/*
				 * We successfully fetched the details of the image. Display
				 * those attachment details for the user.
				 */
				var source = jQuery( "#attachment-details-template" ).html();
				var template = Handlebars.compile( source );
				jQuery( '#attachment_details', $c_imhmf ).html( template( msg.result.data ) );

				// PreSelect Alignment if replacing an image
				self.select_image_alignment();

				/**
				 * Display the pointer if applicable.
				 */
				if ( typeof WPHelpPointerIndex != 'undefined' ) {
					var pointer_index = WPHelpPointerIndex[ '#image_size' ];
					if ( typeof pointer_index != 'undefined' ) {
						if ( 'yes' != WPHelpPointer.pointers[ pointer_index ][ 'is-dismissed' ] ) {
							setTimeout( function() {
								self.baseAdmin
								    .show_pointer( jQuery( '#imaeg_size' ), '#image_size' );
							}, 1000 );
						}
					}
				}

				// event handler: user clicks "Insert into page"
				jQuery( '#download_and_insert_into_page' ).on( 'click', function() {
					self.download_and_insert_into_page( this );
				} );
			} else {
				/*
				 * There was an issue fetching the image details. Display an
				 * applicable message.
				 */
				var source = jQuery( "#attachment-details-error-template" ).html();
				var template = Handlebars.compile( source );
				jQuery( '#attachment_details', $c_imhmf ).html( template() );
			}

		};

		/**
		 * ajax / reach out for the attachment details
		 */
		self.ajax.ajaxCall( data, 'image_get_details', api_call_image_get_details_success_action );

	};

	/**
	 * Set the alignment to the current image's alignment
	 */
	this.select_image_alignment = function() {

		if ( parent.tinymce && parent.tinymce.activeEditor ) {
			var $current_selection = jQuery( parent.tinymce.activeEditor.selection.getNode() );
			var $alignment_sidebar = jQuery( '.attachments-browser select.alignment' );

			// Determine if the current selection has a class.
			if ( $current_selection.is( 'img' ) ) {
				var classes = $current_selection.attr( 'class' );
				var current_classes = [];
				if ( classes ) {
					current_classes = $current_selection.attr( 'class' ).split( /\s+/ );
				}

				var value_selection = 'none';
				jQuery.each( current_classes, function( index, class_item ) {
					if ( class_item == "aligncenter" ) {
						value_selection = "center";
						return false;
					} else if ( class_item == "alignnone" ) {
						value_selection = "none";
						return false;
					} else if ( class_item == "alignright" ) {
						value_selection = "right";
						return false;
					} else if ( class_item == "alignleft" ) {
						value_selection = "left";
						return false;
					}
				} );

				if ( $alignment_sidebar.length ) {
					$alignment_sidebar.val( value_selection ).change();
				}
			}
		}
	};

	/**
	 *
	 */
	this.download_and_insert_into_page = function( anchor ) {
		var $c_ad = jQuery( '#attachment_details' );

		/**
		 * Are we already downloading?
		 */
		var currently_downloading = jQuery( '#currently_downloading_image', $c_ad );
		// If we're already downloading...
		if ( '1' == jQuery( currently_downloading ).val() ) {
			// then abort the current download request
			return;
			// else [we're not currenlty downloading]]
		} else {
			// flag that we're currently downloading
			jQuery( currently_downloading ).val( '1' );
		}

		jQuery( anchor ).attr( 'disabled', true ).text( "Downloading image..." );

		var $image_size_option_selected = jQuery( '#image_size option:selected', $c_imhmf );

		var data = {
		    'action' : 'download_and_insert_into_page',
		    'id_from_provider' : jQuery( '#id_from_provider', $c_imhmf ).val(),
		    'image_provider_id' : jQuery( '#image_provider_id', $c_imhmf ).val(),
		    'image_size' : jQuery( '#image_size', $c_imhmf ).val(),
		    'post_id' : IMHWPB.post_id,
		    'title' : jQuery( '#title', $c_ad ).val(),
		    'caption' : jQuery( '#caption', $c_ad ).val(),
		    'alt_text' : jQuery( '#alt_text', $c_ad ).val(),
		    'description' : jQuery( '#description', $c_ad ).val(),
		    'alignment' : jQuery( '#alignment', $c_ad ).val(),
		    'width' : $image_size_option_selected.attr( 'data-width' ),
		    'height' : $image_size_option_selected.attr( 'data-height' ),
		};

		jQuery
		    .post(
		        ajaxurl,
		        data,
		        function( response ) {
			        // Are we in the Customizer?
			        var in_customizer = ( 'dashboard-customizer' == self.baseAdmin
			            .GetURLParameter( 'ref' ) ) ? true : false;

			        response = JSON.parse( response );

			        jQuery( anchor ).text( "Image downloaded!" );

			        // Success action for 'Replace Image' state.
			        if ( typeof parent.wp.media.frame !== 'undefined'
			            && 'replace-image' === parent.wp.media.frame._state ) {
				        self.refresh_media_library();

				        // Wait 1 second for the media library to refresh.
				        setTimeout( function() {
					        // In the media library, click the image we just
					        // downloaded.
					        $( '.attachments', window.parent.document ).children(
					            "[data-id=" + response.attachment_id + "]" ).find(
					            '.attachment-preview' ).click();

					        // Click the replace button.
					        $( '.media-button-replace', window.parent.document ).click();
				        }, 1000 );
				        return;
			        }

			        /*
					 * If this function exists && we're not in the customizer,
					 * then send the image to the editor.
					 */
			        if ( typeof parent.window.send_to_editor == 'function'
			            && false === in_customizer ) {
				        parent.window.send_to_editor( response.html_for_editor );
			        }

			        // If 'image search' is being called from the
			        // Dashboard >> Media:
			        if ( 'dashboard-media' == self.baseAdmin.GetURLParameter( 'ref' ) ) {
				        var anchor_to_view_attachment_details_media_library = '<a href="post.php?post='
				            + response.attachment_id
				            + '&action=edit" target="_parent" class="button button-small view-image-in-library">View image in Media Library</a>';
				        jQuery( anchor ).after( anchor_to_view_attachment_details_media_library );

			        }

			        // If we're in the customzer
			        if ( true === in_customizer ) {
				        self.download_success_action_customizer( response );
			        }
		        } );
	};

	/**
	 * If in the Customizer, this is the function to run after a successful
	 * image download.
	 */
	this.download_success_action_customizer = function( response ) {
		// If we're not in the customizer, abort.
		if ( 'dashboard-customizer' !== self.baseAdmin.GetURLParameter( 'ref' ) ) {
			return;
		}

		self.refresh_media_library();

		// In the media library, click the image that was just downloaded.
		// Then, click the select button.
		setTimeout( function() {
			jQuery( '.attachments', window.parent.document ).children(
			    "[data-id=" + response.attachment_id + "]" ).find( '.attachment-preview' ).click();

			jQuery( '.media-button-select', window.parent.document ).click();
		}, 1000 );

		// Make sure the toolbar at the bottom is visible. After selecting
		// the image, we'll be cropping it. We'll need to see the buttons
		// for 'crop / not now'.
		jQuery( '.media-frame-toolbar', window.parent.document ).last().removeClass( 'hidden' );
	};

	/**
	 * Refresh the images in the library.
	 */
	this.refresh_media_library = function() {
		var haveCollection = ( typeof window.parent.wp.media.frame.content.get().collection !== 'undefined' ),
		// Do we have a library?
		haveLibrary = typeof window.parent.wp.media.frame.library !== 'undefined';

		if ( window.parent.wp.media.frame.content.get() !== null && haveCollection ) {
			window.parent.wp.media.frame.content.get().collection.props.set( {
				ignore : ( +new Date() )
			} );
			window.parent.wp.media.frame.content.get().options.selection.reset();
		} else if ( haveLibrary ) {
			window.parent.wp.media.frame.library.props.set( {
				ignore : ( +new Date() )
			} );
		}
	};

	/**
	 *
	 */
	this.reset_search = function() {
		self.page = 1;
		self.last_query = '';

		jQuery( $c_sr ).empty();
	};

	/**
	 *
	 */
	this.search_results_scroll = function() {
		var scrollTop = jQuery( '#search_results', $c_imhmf ).scrollTop();
		var height = jQuery( '#search_results', $c_imhmf ).height();
		var scrollHeight = jQuery( '#search_results', $c_imhmf )[ 0 ].scrollHeight;
		var pixels_bottom_unseen = scrollHeight - height - scrollTop;
		var loading_message_outer_height = jQuery( '.loading_message', $c_sr ).outerHeight( false );

		if ( pixels_bottom_unseen <= loading_message_outer_height ) {
			self.initiate_stock_image_search();
		}
	};

	/**
	 *
	 */
	this.toggle_search_results_by_requires_attribution = function() {
		// determine whether or not "Attribution" is checked
		need_to_show = jQuery( '#attribution', $c_imhmf ).is( ':checked' );

		// loop through each image in the search results
		jQuery( "#search_results li", $c_imhmf ).each( function( index, li ) {
			// grab the value of "data-requires-attribution"
			var li_requires_attribution = jQuery( li ).data( 'requires-attribution' );

			// if this image requires attribution
			if ( '1' == li_requires_attribution ) {
				// If the user checked "attribution"
				if ( true == need_to_show ) {
					// then fade this image in
					jQuery( li ).fadeIn();
					// else [the user unchecked "attribution"
				} else {
					// then fade this image out
					jQuery( li ).fadeOut();
				}
			}
		} );
	};
};

new IMHWPB.StockImageSearch( IMHWPB.configs, jQuery );
