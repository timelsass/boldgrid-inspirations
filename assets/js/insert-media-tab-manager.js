/**
 * Throughout this document, "BoldGrid Connect Search" will be refered to as
 * BGCS.
 *
 * You may also see a few references to .last(). This is because several media
 * modal's may be on the same page, not all of them active / visible. Using
 * last() seems to successfully grab the active media modal.
 */

var IMHWPB = IMHWPB || {};

IMHWPB.InsertMediaTabManager = function() {
	/**
	 * ************************************************************************
	 * Configure args and vars.
	 * ************************************************************************
	 */
	var self = this;

	var post_id_param = (typeof IMHWPB.post_id === 'undefined') ? ''
			: '&post_id=' + IMHWPB.post_id;

	// Are we in the customizer?
	var customizephp = 'customize.php';
	self.in_customizer = window.location.pathname.slice(-customizephp.length) === customizephp;

	// Are we editing a page?
	var postphp = 'post.php';
	self.in_post = window.location.pathname.slice(-postphp.length) === postphp;

	// Are we creating a new page?
	var postnewphp = 'post-new.php';
	self.in_post_new = window.location.pathname.slice(-postnewphp.length) === postnewphp;

	// Configure the &ref= for the BGCS iframe.
	var ref = '';
	if (true == self.in_customizer) {
		ref = 'dashboard-customizer';
	} else if ( true == self.in_post || true == self.in_post_new ) {
		ref = 'dashboard-post'
	} else {
		ref = 'dashboard-media';
	}

	// Configure the iframe of BGCS.
	self.iframe_html = '<div id="boldgrid_connect_search_container">'
			+ '<iframe src="media-upload.php?chromeless=1' + post_id_param
			+ '&tab=image_search&ref=' + ref
			+ '" id="boldgrid_connect_search" class="hidden"></iframe>'
			+ '</div>';

	/**
	 * ************************************************************************
	 * On dom load:
	 * ************************************************************************
	 */
	jQuery(function() {
		/**
		 * Customizer.
		 */
		if (true == self.in_customizer) {
			// Add the BGCS tab.
			self.add_bgcs_tab_via_backbone();

			// When "Add media" button is clicked, modify the BGCS and add the
			// new class.
			self.bind_click_customizer_add_new_buttons();

			self.bind_click_of_tabs();
		}

		/**
		 * Add media via page / post editor.
		 */
		if (true == self.in_post || true == self.in_post_new) {
			/*
			 * Add the BGCS tab when:
			 *
			 * 1: The user clicks the "Add Media" button.
			 *
			 * 2. The user clicks the "Insert media" link in left menu of the
			 * media modal.
			 *
			 * 3. The user clicks the "Add Media" button (visible in the button
			 * menu when a user clicks on an image, setup within the
			 * Inspirations Editor) - 'div[aria-label="Add Media"] button'
			 */
			jQuery(document.body)
					.on(
							'click',
							'#insert-media-button, .media-menu .media-menu-item:contains("Insert Media"), div[aria-label="Add Media"] button',
							function() {
								self.add_bgcs_tab_via_jquery();

								// Add the iframe
								self.add_bgcs_iframe();

								// Hide the "Image Search" tab in the left menu
								// of the media modal.
								jQuery(
										"a.media-menu-item:contains('Image Search')")
										.remove();

								self.media_modal_toggle_default_tab
							});

			self.bind_click_of_tabs();
		}
	});

	/**
	 * ************************************************************************
	 * Define our methods.
	 * ************************************************************************
	 */

	/**
	 * @thanks http://pucksart.com/wordpress-javascript-media-library/
	 */
	self.add_bgcs_tab_via_backbone = function() {
		wp.media.view.MediaFrame.Select = wp.media.view.MediaFrame.Select
				.extend({
					browseRouter : function(routerView) {
						"use strict";

						routerView.set({
							upload : {
								text : _wpMediaViewsL10n.uploadFilesTitle,
								priority : 20
							},
							browse : {
								text : _wpMediaViewsL10n.mediaLibraryTitle,
								priority : 40
							},
							myaction : {
								text : "BoldGrid Connect Search",
								priority : 50
							}
						});
					}
				});
	}

	/**
	 * Add tab "BoldGrid Connect Search".
	 *
	 * If it doesn't already exist, add the tab. Then bind the click event of
	 * the tab.
	 */
	self.add_bgcs_tab_via_jquery = function() {
		var tab = '<a href="#" class="media-menu-item boldgrid-connect-search">BoldGrid Connect Search</a>';

		// Only add the tab if we're on the "Insert Media" tab (left nav). If
		// the tab is not active, then we're not on it, so abort.
		if (!jQuery('.media-menu .media-menu-item:contains("Insert Media")')
				.hasClass('active')) {
			return;
		}

		// Only add the tab if it doesn't already exist.
		if (!jQuery('.media-router .boldgrid-connect-search').length) {
			// Add the tab.
			jQuery('.media-router').append(tab);

			// Add the event handler for the clicking of the tab.
			self.bind_boldgird_connect_search_tab_click();
		}
	}

	/**
	 *
	 */
	self.add_bgcs_iframe = function() {
		if (!jQuery('#boldgrid_connect_search_container').length) {
			jQuery(self.iframe_html).appendTo('.media-frame.mode-select');

			self.$bolgrid_connect_search = jQuery('#boldgrid_connect_search');
		}
	}

	/**
	 * Event handler for clicking any of the tabs: (Upload Files / Media Library /
	 * BoldGrid Connect Search)
	 *
	 * 1: Toggle the 'active' state of the tab.
	 *
	 * 2: If necessary, show 'BoldGrid Connect Search".
	 */
	self.bind_boldgird_connect_search_tab_click = function() {
		jQuery('.media-menu-item').on('click', function() {
			self.toggle_active_class_of_tags(this);

			// Handle the display of "BoldGrid Connect Search".
			self.toggle_boldgrid_connect_search(this);
		});
	}

	/**
	 *
	 */
	self.bind_click_of_tabs = function() {
		jQuery(document.body).on('click', '.media-router .media-menu-item',
				function() {
					// When a tab is clicked, toggle BGCS.
					self.toggle_boldgrid_connect_search(this);
				});
	}

	/**
	 * When you click "Add Media" for the first time, there may not be an active
	 * tab by default. If this is the case, load the Media Library tab.
	 */
	self.media_modal_toggle_default_tab = function() {
		// If no tabs are active:
		if (!jQuery('.media-frame-routher').last().find(
				'.media-router .media-menu-item.active').length) {
			var $library_tab = jQuery('.media-frame-router').last().find(
					'.media-router .media-menu-item').eq(1);

			$library_tab.addClass('active').click();
		}
	}

	/**
	 *
	 */
	self.bind_click_customizer_add_new_buttons = function() {
		// .customize-control-background .thumbnail img
		// The above references the thumbnail of the current background image.

		jQuery(document.body)
				.on(
						'click',
						'#background_image-button, .button.new, #boldgrid_logo_setting-button, #site_icon-button, .customize-control-background .thumbnail img',
						function() {
							var this_button = jQuery(this);

							/*
							 * Find our BGCS tab.
							 *
							 * There may be multiple wp-uploader-id's on the
							 * page, so grab the last() one, which should be the
							 * active one.
							 */
							$bgcs_tab = jQuery(
									'.media-frame-router .media-router .media-menu-item:contains("BoldGrid Connect Search")')
									.last();

							/*
							 * BGCS is not applicable for "Site Logo" and "Site
							 * Icon". If this media modal is for either of these
							 * features, hide the BGCS button.
							 */
							var is_site_logo_button = (this_button
									.is('#boldgrid_logo_setting-button')) ? true
									: false;

							var is_site_icon_button = (this_button
									.is('#site_icon-button')) ? true : false;

							if (is_site_logo_button || is_site_icon_button) {
								$bgcs_tab.addClass('hidden');
							} else {
								$bgcs_tab.removeClass('hidden');
							}

							// If it has the active class, remove it
							if (true == $bgcs_tab.hasClass('active')) {
								$bgcs_tab.removeClass('active');
							}

							// Add our class.
							$bgcs_tab.addClass('boldgrid-connect-search');

							// Add the iframe
							self.add_bgcs_iframe();

							self.media_modal_toggle_default_tab();
						});
	}

	/**
	 * Handle the .active class of the tabs.
	 */
	self.toggle_active_class_of_tags = function(clicked_tab) {
		// First, remove the .active class from all tabs.
		jQuery('.media-router .media-menu-item').each(function() {
			jQuery(this).removeClass('active');
		});

		// Then, add the .active class to the current tab
		// clicked.
		jQuery(clicked_tab).addClass('active');
	}

	/**
	 * Handle the display of the boldgrid-connect-search iframe.
	 *
	 * This is ran after one of the tabs is clicked (Upload files / Media
	 * library etc).
	 */
	self.toggle_boldgrid_connect_search = function(clicked_tab) {
		self.$media_frame_content = jQuery('.media-frame-content').last();
		self.$media_frame_toolbar = jQuery('.media-frame-toolbar').last();

		// If this is the boldgrid-connect-search tab
		if (jQuery(clicked_tab).hasClass('boldgrid-connect-search')) {
			// If the iframe does not exist:
			if (!jQuery('.media-frame-content #boldgrid_connect_search_clone').length) {
				// Clone the iframe.
				self.$new_iframe = self.$bolgrid_connect_search.clone();

				// Show the iframe.
				self.$new_iframe.attr('id', 'boldgrid_connect_search_clone')
						.removeClass('hidden');

				// Add the iframe to media-frame-content.
				self.$new_iframe.appendTo('.media-frame-content');
			} else {
				// Because it exists, move it to the correct location.
				$last_media_frame_content = jQuery('.media-frame-content')
						.last();
				self.$new_iframe.appendTo($last_media_frame_content);

				// If the iframe does exist, then simply show it.
				self.$new_iframe.removeClass('hidden');
			}

			self.toggle_bottom_frame('hide');
		} else {
			jQuery('#boldgrid_connect_search_clone').addClass('hidden');
			self.toggle_bottom_frame('show');
		}
	}

	/**
	 *
	 */
	self.toggle_bottom_frame = function(action) {
		self.$media_frame_content = jQuery('.media-frame-content').last();
		self.$media_frame_toolbar = jQuery('.media-frame-toolbar').last();

		switch (action) {
		case 'show':
			self.$media_frame_toolbar.removeClass('hidden');
			self.$media_frame_content.css('bottom', '61px');
			break;
		case 'hide':
			self.$media_frame_toolbar.addClass('hidden');
			self.$media_frame_content.css('bottom', '0px');
			break;
		}
	}
};

new IMHWPB.InsertMediaTabManager();