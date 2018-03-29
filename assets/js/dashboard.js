/*
 * This document is divided into 2 sections:
 *
 * 1. Render BoldGrid Education
 * 2. Toggle certain parts of the dashboard if needed.
 */

/**
 * ****************************************************************************
 * Render BoldGrid Education
 * ****************************************************************************
 */

var BoldGridDashboard = BoldGridDashboard || {};

( function( $, _, Backbone ) {
	'use strict';

	// This is the primary view for the customer journey information.
	var BoldGridDashboardJourneys = Backbone.View.extend( {
		initialize: function() {
			this.render();
		},

		boldgridAccordion: function() {

			/**
			 * Make an accordion for the text at the top of the widget,
			 * the "Customer Journeys," which are rendered from the
			 * collection, BoldGridCustomerJourney.
			 *
			 * @see : collection : BoldGridCustomerJourney
			 *
			 * @since 1.13
			 */

			jQuery( '.boldgrid-accordion-content:first' ).show();

			jQuery( '.boldgrid-arrow:first' ).toggleClass( 'boldgrid-arrow-toggle' );

			jQuery( '#boldgrid-accordion' )
				.find( '.boldgrid-accordion-toggle' )

				.click( function() {

					// StateMachine open the closed, close the opened etc
					jQuery( this )
						.next()

						.slideToggle( 600 );

					jQuery( 'span', this ).toggleClass( 'boldgrid-arrow-toggle' );

					jQuery( '.boldgrid-accordion-content' )
						.not( jQuery( this ).next() )

						.slideUp( 'fast' );

					jQuery( '.boldgrid-arrow-toggle' )
						.not( jQuery( 'span', this ) )

						.removeClass( 'boldgrid-arrow-toggle' );
				} );
		},

		render: function() {
			var html = '';

			this.collection.each( function( journey ) {
				html +=
					'<h3 class="boldgrid-accordion-toggle"><span class="boldgrid-arrow"></span>' +
					journey.get( 'path' ) +
					'</h3>';

				html += '<div class="boldgrid-accordion-content">' + journey.get( 'text' ) + '</div>';
			} );

			this.$el.html( html );
			this.boldgridAccordion();
		}
	} );

	// This is the collection of the BoldGrid Customer Journeys data
	var BoldGridCustomerJourney = new Backbone.Collection( [
		{
			path: 'First time building a website with BoldGrid or first time building a site at all?',
			text:
				'<p>It is best to start with the videos under the “Inspiration” tab below. It will show you the process for choosing the look and feel of your Base Website (including your WordPress theme) and picking pages typical for your industry (called Page Sets). You can also just start by going to “Inspirations” in the menu at left.</p><p>After you complete the Inspiration phase, you will then customize your site. We created guides based on what we recommend you do based on time available. The three Customization tabs below will guide you through the process.</p>'
		},
		{
			path:
				'Finished your first “Inspiration” install but want to try a different theme or get more pages?',
			text:
				'<p>Just use "Inspirations" in the menu at left again. You can also learn more about it under the Inspiration tab below.</p>'
		},
		{
			path:
				'Familiar with creating sites in WordPress but not running an Active site on this install?',
			text:
				'<p>We have made a few significant changes you may want to review before starting with the Inspiration Tab. Check out the Advanced for WordPress Users tab below. </p>'
		},
		{
			path: 'Running this on a WordPress install with an Active website?',
			text:
				'<p>It is highly advised to review the Advanced for WordPress Users tab below before you start the Inspiration Phase.</p>'
		}
	] );

	// Each BoldGrid customer journey is the text displayed above the tabs
	var boldgridDashboardJourneys = new BoldGridDashboardJourneys( {
		el: '#boldgrid-accordion',

		collection: BoldGridCustomerJourney
	} );

	// This is the primary view for the BoldGrid Dashboard
	var BoldGridDashView = Backbone.View.extend( {

		/**
		 * Main view for BoldGrid Dashboard Widget This is the set of
		 * tabs and actual content displayed in the dashboard widget.
		 */

		// Handlebars template
		template: Handlebars.compile(

			// ID of where to compile template
			jQuery( '#boldgrid-dashboard-view' ).html()
		),

		// establish click even handlers for tabs and tab content
		events: {

			// on click for tab content links
			'click #boldgrid-tab-view-navigation li.boldgrid-tab-links': 'boldgridVideoSelector',

			// on click for actual tabs
			'click li.tab-menu-item': 'boldgridTabReset',

			// on click for playing YouTube videos.
			'click div.play-button': 'boldgridVideoPlay'
		},

		// tab content video swapping
		boldgridVideoSelector: function( e ) {

			/**
			 * Click event handler for displaying the relevant videos
			 * when users click on tab navigation links in the BoldGrid
			 * dashboard widget.
			 *
			 * @since 1.14
			 */

			// get the current target's' data-link-key
			var video = jQuery( e.currentTarget ).data( 'link-key' );

			// show matching content for element's data-link-key
			jQuery( '.boldgrid-tab-content-wrapper[data-link-key=\'' + video + '\']' )

				// make it look pretty
				.fadeIn( 'slow' )

				.show();

			// hide all other elements that don't match data-link-key
			jQuery( '.boldgrid-tab-content-wrapper:not([data-link-key=\'' + video + '\'])' ).hide();

			/**
			 * Kill YouTube Video Players that are playing on tabs if
			 * users navigate away from current tab view. Then replace
			 * them with the corresponding thumbnails.
			 *
			 * @since 1.15
			 */

			// define the BoldGrid video player location
			var bgplayer = document.getElementById( 'youtube-iframe' );

			// only destroy if exists
			if ( 'undefined' != typeof bgplayer && null != bgplayer ) {

				// grab it's parent
				var parent = bgplayer.parentNode;

				// generate the image tag
				var image = document.createElement( 'img' );

				// add css properties
				image.className = 'youtube-thumb';

				// get the thumbnail source from YouTube
				image.setAttribute(
					'src',
					'//i.ytimg.com/vi/' + bgplayer.parentNode.dataset.id + '/sddefault.jpg'
				);

				// add thumb to dom
				parent.appendChild( image );

				// destroy YouTube video player
				parent.removeChild( bgplayer );

				// generate new play button
				var playbutton = document.createElement( 'div' );

				playbutton.className = 'play-button';

				parent.appendChild( playbutton );
			}
		},

		boldgridVideoPlay: function( e ) {

			/**
			 * Play YouTube video if users click on play buttons on the
			 * YouTube thumbnails
			 *
			 * @since 1.15
			 */

			// define the player, ie iframe.
			var iframe = document.createElement( 'iframe' );

			// set src of iframe
			iframe.setAttribute(
				'src',
				'//www.youtube.com/embed/' +

					// grab current target's parent's
					// data-id attr
					e.currentTarget.parentNode.dataset.id +
					'?autoplay=1&autohide=2&border=0&wmode=opaque&enablejsapi=1&controls=1&showinfo=0'
			);

			iframe.setAttribute( 'frameborder', '0' );

			iframe.setAttribute( 'id', 'youtube-iframe' );

			// do the deed
			e.currentTarget.parentNode.replaceChild( iframe, e.currentTarget );
		},

		// reset tab view for navigation
		boldgridTabReset: function() {

			/**
			 * Kill YouTube Video Players that are playing on tabs if
			 * users navigate away from current tab view. Then replace
			 * them with the corresponding thumbnails.
			 *
			 * @since 1.15
			 */

			// define the BoldGrid video player location
			var bgplayer = document.getElementById( 'youtube-iframe' );

			// only destroy if exists
			if ( 'undefined' != typeof bgplayer && null != bgplayer ) {

				// grab it's parent
				var parent = bgplayer.parentNode;

				// generate the image tag
				var image = document.createElement( 'img' );

				// add css properties
				image.className = 'youtube-thumb';

				// get the thumbnail source from YouTube
				image.setAttribute(
					'src',
					'//i.ytimg.com/vi/' + bgplayer.parentNode.dataset.id + '/sddefault.jpg'
				);

				// add thumb to dom
				parent.appendChild( image );

				// destroy YouTube video player
				parent.removeChild( bgplayer );

				// generate new play button
				var playbutton = document.createElement( 'div' );

				playbutton.className = 'play-button';

				parent.appendChild( playbutton );
			}

			/**
			 * Reset the tab content back to 0 for default display when
			 * users switch between tabs.
			 *
			 * @since 1.14
			 */

			// hide all the tab content currently in view
			jQuery( '.boldgrid-tab-content-wrapper' ).hide();

			// show the default content, data-link-key 0
			jQuery( '.boldgrid-tab-content-wrapper[data-link-key=\'0\']' )

				// make it look pretty
				.fadeIn( 'slow' )

				.show();
		},

		// edit Screen Options text in WordPress Dashboard
		boldgridScreenOptions: function() {

			/**
			 * Change the text of the "Welcome Panel" in WordPress and
			 * put the option for BoldGrid, "Inspiration and
			 * Customization Tutorials."
			 *
			 * @since 1.13
			 *
			 * Updated variable names to be more logical in reading.
			 * @since 1.14
			 */

			var wpScreenOptions = jQuery( 'label[for=wp_welcome_panel-hide] input' ),
				welcome = wpScreenOptions[0].nextSibling.nodeValue,
				boldgridScreenOptions = welcome.replace(
					/Welcome/i,
					' Inspiration and Customization Tutorials'
				);

			wpScreenOptions[0].nextSibling.nodeValue = boldgridScreenOptions;
		},

		// Custom YouTube Player
		boldgridYouTubePlayer: function() {
			jQuery( document ).ready( function() {

				/**
				 * Prevent YouTube embeds rendered in
				 * view from draining resources. Also
				 * adds overlay image in orange for
				 * player button, and sets up the video
				 * embed to be done on a click event
				 * opposed to straight embedding it by
				 * grabbing thumbnails from youtube
				 * until video is selected by user.
				 *
				 * @since 1.13
				 */

				( function() {
					var v = document.getElementsByClassName( 'youtube-player' );

					for ( var n = 0; n < v.length; n++ ) {
						var p = document.createElement( 'div' );

						p.innerHTML = boldgridThumb( v[n].dataset.id );

						p.onclick = boldgridIframe;

						v[n].appendChild( p );
					}
				} )();

				function boldgridThumb( id ) {
					return (
						'<img class="youtube-thumb" src="//i.ytimg.com/vi/' +
						id +
						'/sddefault.jpg"><div class="play-button"></div>'
					);
				}

				function boldgridIframe() {
					var iframe = document.createElement( 'iframe' );

					iframe.setAttribute(
						'src',
						'//www.youtube.com/embed/' +
							this.parentNode.dataset.id +
							'?autoplay=1&autohide=2&border=0&wmode=opaque&enablejsapi=1&controls=1&showinfo=0'
					);

					iframe.setAttribute( 'frameborder', '0' );

					iframe.setAttribute( 'id', 'youtube-iframe' );

					this.parentNode.replaceChild( iframe, this );
				}
			} );
		},

		// Tab data to populate our template with - contains $el
		boldgridTabData: function() {
			this.$el.html(
				this.template( {
					title: 'Welcome to BoldGrid!',

					tabs: [
						{
							tab: 'Inspiration',

							links: [
								{
									content_heading: 'Inspiration - Getting Started',
									link: '<a>Introduction to BoldGrid powered by WordPress</a>',
									icon: 'dashicons-nametag',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="2SAUg-281tE"></div></div>' +
										'<h4>Edit Site Title</h4>' +
										'<p>The Site Title, along with other important information in the header of your site, can be edited in the Theme Customizer, available from the left menu in Appearance > Customize. When you are finished editing, make sure to click the blue "Save" button at the top of the Theme Customizer </p>'
								},
								{
									link: '<a>Installing your first BoldGrid Inspiration Site</a>',
									icon: 'dashicons-lightbulb',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="U2782mY-B6w"></div></div>' +
										'<br><h4>Installing your first BoldGrid Site</h4>' +
										'<p>The Site Title, along with other important information in the header of your site, can be edited in the Theme Customizer, available from the left menu in Appearance > Customize. When you are finished editing, make sure to click the blue "Save" button at the top of the Theme Customizer </p>'
								},
								{
									link: '<a>Installing a Replacement Theme</a>',
									icon: 'dashicons-welcome-widgets-menus',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="U2782mY-B6w"></div></div>' +
										'<h4>Installing a Replacement Theme</h4>' +
										'<p>You can easily install an additional theme by going to the Inspiration section of your menu, and selecting "New Theme."You can easily install an additional theme by going to the Inspiration section of your menu, and selecting "New Theme."</p>'
								},
								{
									link: '<a>Installing additional Pages</a>',
									icon: 'dashicons-welcome-add-page',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="2SAUg-281tE"></div></div>' +
										'<h4>Edit Site Title</h4>' +
										'<p>The Site Title, along with other important information in the header of your site, can be edited in the Theme Customizer, available from the left menu in Appearance > Customize. When you are finished editing, make sure to click the blue "Save" button at the top of the Theme Customizer </p>'
								},
								{
									link: '<a>Installing a Replacement Theme and Pages</a>',
									icon: 'dashicons-admin-page',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="U2782mY-B6w"></div></div>' +
										'<h4>Installing a Replacement Theme</h4>' +
										'<p>Installing a replacement Theme and Pages is a simple process with BoldGrid, just navigate to the Inspiration section of your menu on the left and select the option to install a new site.</p>'
								}
							]
						},
						{
							tab: 'Customization – Get it Done (1-2 hrs)',

							links: [
								{
									content_heading: 'Get it Done (1-2 hrs)',
									link: '<a>Evaluating Your BoldGrid Site</a>',
									icon: 'dashicons-visibility',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="Kkf1VOO4p6o"></div></div>' +
										'<h4>Edit Site Title</h4>' +
										'<p>The Site Title, along with other important information in the header of your site, can be edited in the Theme Customizer, available from the left menu in Appearance > Customize. When you are finished editing, make sure to click the blue "Save" button at the top of the Theme Customizer </p>'
								},
								{
									link: '<a>How to Remove Pages From Your Site</a>',
									icon: 'dashicons-media-document',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="_zYQ00ZYu14"></div></div>' +
										'<h4>Edit Site Title</h4>' +
										'<p>The Site Title, along with other important information in the header of your site, can be edited in the Theme Customizer, available from the left menu in Appearance > Customize. When you are finished editing, make sure to click the blue "Save" button at the top of the Theme Customizer </p>'
								},
								{
									link: '<a>Removing Items From Your Site\'s  Menu</a>',
									icon: 'dashicons-menu',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="w_pqQNzGNV0"></div></div>' +
										'<h4>Edit Site Title</h4>' +
										'<p>The Site Title, along with other important information in the header of your site, can be edited in the Theme Customizer, available from the left menu in Appearance > Customize. When you are finished editing, make sure to click the blue "Save" button at the top of the Theme Customizer </p>'
								},
								{
									link: '<a>Changing Your Site Title and Call to Action</a>',
									icon: 'dashicons-welcome-write-blog',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="zPPh8rEJoII"></div></div>' +
										'<h4>Edit Site Title</h4>' +
										'<p>The Site Title, along with other important information in the header of your site, can be edited in the Theme Customizer, available from the left menu in Appearance > Customize. When you are finished editing, make sure to click the blue "Save" button at the top of the Theme Customizer </p>'
								},
								{
									link: '<a>Setting Up Your Social Media Icons</a>',
									icon: 'dashicons-twitter',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="_P0AFSvdU6Q"></div></div>' +
										'<h4>Edit Site Title</h4>' +
										'<p>The Site Title, along with other important information in the header of your site, can be edited in the Theme Customizer, available from the left menu in Appearance > Customize. When you are finished editing, make sure to click the blue "Save" button at the top of the Theme Customizer </p>'
								},
								{
									link: '<a>Modifying and Removing Footer Information</a>',
									icon: 'dashicons-feedback',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="uIGa06hG-2E"></div></div>' +
										'<h4>Edit Site Title</h4>' +
										'<p>The Site Title, along with other important information in the header of your site, can be edited in the Theme Customizer, available from the left menu in Appearance > Customize. When you are finished editing, make sure to click the blue "Save" button at the top of the Theme Customizer </p>'
								},
								{
									link: '<a>Add and Remove Images</a>',
									icon: 'dashicons-feedback',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="9N8DBeKKXCY"></div></div>' +
										'<h4>Edit Site Title</h4>' +
										'<p>The Site Title, along with other important information in the header of your site, can be edited in the Theme Customizer, available from the left menu in Appearance > Customize. When you are finished editing, make sure to click the blue "Save" button at the top of the Theme Customizer </p>'
								},
								{
									link: '<a>Switching Site Colors and Title Color</a>',
									icon: 'dashicons-feedback',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="4ob5F20NFmY"></div></div>' +
										'<h4>Edit Site Title</h4>' +
										'<p>The Site Title, along with other important information in the header of your site, can be edited in the Theme Customizer, available from the left menu in Appearance > Customize. When you are finished editing, make sure to click the blue "Save" button at the top of the Theme Customizer </p>'
								},
								{
									link: '<a>Using The BoldGrid Editor Plugin</a>',
									icon: 'dashicons-feedback',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="wBtdytkONO8"></div></div>' +
										'<h4>Edit Site Title</h4>' +
										'<p>The Site Title, along with other important information in the header of your site, can be edited in the Theme Customizer, available from the left menu in Appearance > Customize. When you are finished editing, make sure to click the blue "Save" button at the top of the Theme Customizer </p>'
								},
								{
									link: '<a>How to Change Your Contact Form</a>',
									icon: 'dashicons-id',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="mqHLCHBUwjs"></div></div>' +
										'<h4>Edit Site Title</h4>' +
										'<p>The Site Title, along with other important information in the header of your site, can be edited in the Theme Customizer, available from the left menu in Appearance > Customize. When you are finished editing, make sure to click the blue "Save" button at the top of the Theme Customizer </p>'
								}
							]
						},
						{
							tab: 'Customization – Get Creative (4-6 hrs)',

							links: [
								{
									content_heading: 'Get Creative (4-6 hrs)',
									link: 'Edit <a>Site Title</a>',
									icon: 'dashicons-admin-settings',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="U2782mY-B6w"></div></div>' +
										'<h4>Edit Site Title</h4>' +
										'<p>The Site Title, along with other important information in the header of your site, can be edited in the Theme Customizer, available from the left menu in Appearance > Customize. When you are finished editing, make sure to click the blue "Save" button at the top of the Theme Customizer </p>'
								},
								{
									link: 'Switch Color Palette',
									icon: 'dashicons-admin-generic',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="2SAUg-281tE"></div></div>' +
										'<h4>Edit Site Title</h4>' +
										'<p>The Site Title, along with other important information in the header of your site, can be edited in the Theme Customizer, available from the left menu in Appearance > Customize. When you are finished editing, make sure to click the blue "Save" button at the top of the Theme Customizer </p>'
								},
								{
									link: 'Add Social Media Links',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="U2782mY-B6w"></div></div>' +
										'<h4>Edit Site Title</h4>' +
										'<p>The Site Title, along with other important information in the header of your site, can be edited in the Theme Customizer, available from the left menu in Appearance > Customize. When you are finished editing, make sure to click the blue "Save" button at the top of the Theme Customizer </p>'
								},
								{
									link: 'Rearrange Menu Links',
									icon: 'dashicons-admin-generic',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="2SAUg-281tE"></div></div>' +
										'<h4>Edit Site Title</h4>' +
										'<p>The Site Title, along with other important information in the header of your site, can be edited in the Theme Customizer, available from the left menu in Appearance > Customize. When you are finished editing, make sure to click the blue "Save" button at the top of the Theme Customizer </p>'
								},
								{
									link: 'Edit Home Page',
									icon: 'dashicons-admin-generic',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="U2782mY-B6w"></div></div>' +
										'<h4>Edit Site Title</h4>' +
										'<p>The Site Title, along with other important information in the header of your site, can be edited in the Theme Customizer, available from the left menu in Appearance > Customize. When you are finished editing, make sure to click the blue "Save" button at the top of the Theme Customizer </p>'
								},
								{
									link: 'Search Images',
									icon: 'dashicons-admin-generic',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="2SAUg-281tE"></div></div>' +
										'<h4>Edit Site Title</h4>' +
										'<p>The Site Title, along with other important information in the header of your site, can be edited in the Theme Customizer, available from the left menu in Appearance > Customize. When you are finished editing, make sure to click the blue "Save" button at the top of the Theme Customizer </p>'
								},
								{
									link: 'Edit About Us Page',
									icon: 'dashicons-admin-generic',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="2SAUg-281tE"></div></div>' +
										'<h4>Edit Site Title</h4>' +
										'<p>The Site Title, along with other important information in the header of your site, can be edited in the Theme Customizer, available from the left menu in Appearance > Customize. When you are finished editing, make sure to click the blue "Save" button at the top of the Theme Customizer </p>'
								},
								{
									link: 'Publish Pages',
									icon: 'dashicons-admin-generic',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="U2782mY-B6w"></div></div>' +
										'<h4>Edit Site Title</h4>' +
										'<p>The Site Title, along with other important information in the header of your site, can be edited in the Theme Customizer, available from the left menu in Appearance > Customize. When you are finished editing, make sure to click the blue "Save" button at the top of the Theme Customizer </p>'
								}
							]
						},
						{
							tab: 'Customization – I\'m Thinking Big (8-24+ hrs)',

							links: [
								{
									content_heading: 'I\'m Thinking Big (8-24+ hrs)',
									link: 'Edit <a>Site Title</a>',
									icon: 'dashicons-admin-settings',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="U2782mY-B6w"></div></div>' +
										'<h4>Edit Site Title</h4>' +
										'<p>The Site Title, along with other important information in the header of your site, can be edited in the Theme Customizer, available from the left menu in Appearance > Customize. When you are finished editing, make sure to click the blue "Save" button at the top of the Theme Customizer </p>'
								},
								{
									link: 'Switch Color Palette',
									icon: 'dashicons-admin-generic',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="2SAUg-281tE"></div></div>' +
										'<h4>Edit Site Title</h4>' +
										'<p>The Site Title, along with other important information in the header of your site, can be edited in the Theme Customizer, available from the left menu in Appearance > Customize. When you are finished editing, make sure to click the blue "Save" button at the top of the Theme Customizer </p>'
								},
								{
									link: 'Add Social Media Links',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="U2782mY-B6w"></div></div>' +
										'<h4>Edit Site Title</h4>' +
										'<p>The Site Title, along with other important information in the header of your site, can be edited in the Theme Customizer, available from the left menu in Appearance > Customize. When you are finished editing, make sure to click the blue "Save" button at the top of the Theme Customizer </p>'
								},
								{
									link: 'Rearrange Menu Links',
									icon: 'dashicons-admin-generic',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="2SAUg-281tE"></div></div>' +
										'<h4>Edit Site Title</h4>' +
										'<p>The Site Title, along with other important information in the header of your site, can be edited in the Theme Customizer, available from the left menu in Appearance > Customize. When you are finished editing, make sure to click the blue "Save" button at the top of the Theme Customizer </p>'
								},
								{
									link: 'Edit Home Page',
									icon: 'dashicons-admin-generic',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="U2782mY-B6w"></div></div>' +
										'<h4>Edit Site Title</h4>' +
										'<p>The Site Title, along with other important information in the header of your site, can be edited in the Theme Customizer, available from the left menu in Appearance > Customize. When you are finished editing, make sure to click the blue "Save" button at the top of the Theme Customizer </p>'
								},
								{
									link: 'Search Images',
									icon: 'dashicons-admin-generic',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="2SAUg-281tE"></div></div>' +
										'<h4>Edit Site Title</h4>' +
										'<p>The Site Title, along with other important information in the header of your site, can be edited in the Theme Customizer, available from the left menu in Appearance > Customize. When you are finished editing, make sure to click the blue "Save" button at the top of the Theme Customizer </p>'
								},
								{
									link: 'Edit About Us Page',
									icon: 'dashicons-admin-generic',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="2SAUg-281tE"></div></div>' +
										'<h4>Edit Site Title</h4>' +
										'<p>The Site Title, along with other important information in the header of your site, can be edited in the Theme Customizer, available from the left menu in Appearance > Customize. When you are finished editing, make sure to click the blue "Save" button at the top of the Theme Customizer </p>'
								},
								{
									link: 'Publish Pages',
									icon: 'dashicons-admin-generic',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="U2782mY-B6w"></div></div>' +
										'<h4>Edit Site Title</h4>' +
										'<p>The Site Title, along with other important information in the header of your site, can be edited in the Theme Customizer, available from the left menu in Appearance > Customize. When you are finished editing, make sure to click the blue "Save" button at the top of the Theme Customizer </p>'
								}
							]
						},
						{
							tab: 'Advanced For WordPress Users',

							links: [
								{
									content_heading: 'Advanced For WordPress Users',
									link: '<a>Advanced For WordPress Users Overview</a>',
									icon: 'dashicons-admin-generic',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="eaY4JkMe1jk"></div></div>' +
										'<h4>Advanced For WordPress Users Overview</h4>' +
										'The BoldGrid drag and drop page editor is very intuitive if you\'ve used a grid based website builder before.  If not, you can watch the video above to understand how it works, or follow these step by step instructions!'
								},
								{
									link:
										'<a>Importing BoldGrid Themes, Pages, Content, and Plugins via Inspiration</a>',
									icon: 'dashicons-admin-settings',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="6DZyoTCMJjw"></div></div>' +
										'<h4>Importing BoldGrid Themes, Pages, Content, and Plugins via Inspiration</h4>' +
										'<p>You can easily get up and running with BoldGrid if you have an existing WordPress website.  We have made a few changes with additional plugins that are installed and ready to go for you.  You can watch the video above to find out more about how BoldGrid will make your WordPress experience easier.</p>'
								},
								{
									link: '<a>How to Use the Drag and Drop Page Editor in TinyMCE</a>',
									icon: 'dashicons-admin-generic',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="U2782mY-B6w"></div></div>' +
										'<h4>How to Use the Drag and Drop Page Editor in TinyMCE</h4>' +
										'The BoldGrid drag and drop page editor is very intuitive if you\'ve used a grid based website builder before.  If not, you can watch the video above to understand how it works, or follow these step by step instructions!'
								},
								{
									link: '<a>Theme Framework and modifying HTML and CSS</a>',
									icon: 'dashicons-admin-generic',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="U2782mY-B6w"></div></div>' +
										'<h4>Edit Site Title</h4>' +
										'<p>The Site Title, along with other important information in the header of your site, can be edited in the Themes section.The Site Title, along with other important information in the header of your site, can be edited in the Themes section.'
								},
								{
									link: '<a>Staging Plugin and Staging New Themes and Pages</a>',
									icon: 'dashicons-admin-generic',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="U2782mY-B6w"></div></div>' +
										'<h4>Staging Plugin and Staging New Themes and Pages</h4>' +
										'You can easily stage a website with BoldGrid.  To access the staging area, you can simply select Staging form the left hand menu item to begin.'
								},
								{
									link: '<a>Downloading Photos through Media Search</a>',
									icon: 'dashicons-admin-generic',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="U2782mY-B6w"></div></div>' +
										'<h4>Downloading Photos through Media Search</h4>' +
										'You can download new images for use on your BoldGrid built websites by using our integrated stock photography search.  This option will be available in the WordPress \'Add Media\' section.'
								},
								{
									link: '<a>Compatibility with non-BoldGrid Themes, Content, etc.</a>',
									icon: 'dashicons-admin-generic',
									content:
										'<div class="youtube-container"><div class="youtube-player" data-id="U2782mY-B6w"></div></div>' +
										'<h4>Compatibility with non-BoldGrid Themes, Content, etc.</h4>' +
										'You can easily stage a website with BoldGrid.  If the staging plugin is installed, then you will see the options to view your active site or your staging site when you hover over your site name in the WordPress admin toolbar.  You can easily stage a website with BoldGrid.'
								}
							]
						}
					]
				} )
			);
		},

		// This generates the main tab view
		boldgridTabs: function() {

			/**
			 * This generates the tabs from the BoldGrid tab data html
			 * that is rendered.
			 *
			 * @since 1.13
			 */

			( function() {
				var Tabs;

				Tabs = ( function() {

					// template for tabs to be rendered
					var templates = {
						tplTabNav: Handlebars.compile(
							'<ul class=\'inline-list tabs-navigation show-desktop\' role=\'tablist\'>{{#each tab}}<li role=\'presentation\' class=\'tab-menu-item\'><a href=\'#{{tabId}}\' id=\'TabController-{{tabId}}\' class=\'txt-btn tabs-navigation__button nav-tab\' role=\'tab\' aria-selected=\'false\' aria-controls=\'{{tabId}}\' tabindex=-1 aria-expanded=\'false\'>{{{tabTitle}}}</a></li>{{/each}}</ul>'
						)
					};

					/**
					 * Sets up the Tabs
					 *
					 * @param $container -
					 *            parent of the items that will be
					 *            tabbed together
					 * @param $options -
					 *            any overrides to the classes set below
					 */

					function Tabs( $container, options ) {
						var defaults = {

							// the default tab to start on (index)
							default_tab: '0',

							// wrapper the entire panel and content in a
							// tab view
							tab_class_panel: '.tabs-container__panel',

							// title for each tab/accordion made
							tab_class_title: '.tabs-container__title',

							// prefix for tab navigation on
							// tab/accordion
							tab_nav_id: 'TabNav'
						};

						this.$container = $container.addClass( 'tabs-init' );

						this.options = jQuery.extend( {}, defaults, options );

						this.currentTab = null;

						this.init();
					}

					/**
					 * Creates a data object for all tabs within the
					 * widget Saves each tab ID and title, to be used to
					 * create desktop tab nav if needed adds ARIA roles
					 * as it grabs tab data
					 */

					Tabs.prototype.fetchTabData = function() {

						// stores data for all tabs in the widget
						this.tabData = [];

						var i = 0,
							$tab_panels = this.$tab_panels,
							len = $tab_panels.length,
							$currentPanel,
							$panelTitle,
							currentPanelData;

						// save each tab from the html
						for ( i; i < len; i++ ) {
							$currentPanel = jQuery( $tab_panels[i] );

							$panelTitle = $currentPanel.prev( this.options.tab_class_title );

							currentPanelData = {
								tabId: $tab_panels[i].id,

								tabTitle: $panelTitle.text()
							};

							this.tabData.push( currentPanelData );

							// update ARIA attrs for the panel and
							// accordion title
							$currentPanel.attr( {
								role: 'tabpanel',
								'aria-hidden': 'true'
							} );

							$panelTitle
								.attr( {
									tabindex: '-1',
									role: 'tab',
									'aria-controls': currentPanelData.tabId,
									'aria-selected': 'false',
									'aria-expanded': 'false'
								} )
								.removeClass( 'nav-tab-active' );
						}
					};

					/**
					 * Creates the HTML for the tab views
					 */

					Tabs.prototype.createTabNav = function() {
						this.tabNav = true;

						this.$tabNav = jQuery(
							templates.tplTabNav( {
								tab: this.tabData
							} )
						).prependTo( this.$container );

						this.$tabNavItems = this.$tabNav.find( 'a' );

						// add class to indicate that there's a
						// navigation
						this.$container.addClass( 'tabs-nav-init' );
					};

					/**
					 * Binds the tab nav events
					 */

					Tabs.prototype.bindNavEvents = function() {
						var app = this;

						this.$tabNav.on( 'click', 'a', function( e ) {
							e.preventDefault();

							var $target = jQuery( e.currentTarget ),
								$tabPanel = jQuery( this.getAttribute( 'href' ) );

							if ( ! app.isCurrentTab( $tabPanel ) ) {
								app.closeTab();

								app.openTab( $tabPanel );
							}
						} );

						this.$tabNav.on( 'keydown', 'a', function( e ) {
							var currentIndex = app.handleKeyPress( e );

							if ( null !== currentIndex ) {
								app.closeTab();

								var panelId = app.tabData[currentIndex].tabId;

								app.openTab( jQuery( document.getElementById( panelId ) ) );

								// @fix - focus only
								// here so doesn't steal
								// focus on pageload
								app.currentTab.$navItem.focus();
							}
						} );
					};

					/**
					 * identify if the clicked tab is what's currently
					 * open
					 *
					 * @param $tab_panel -
					 *            jQuery collection of the tab to be
					 *            evaluated
					 */

					Tabs.prototype.isCurrentTab = function( $tab_panel ) {
						return this.currentTab.$tab_panel.get( 0 ) == $tab_panel.get( 0 );
					};

					/**
					 * Key handler for tabs
					 *
					 * @param e -
					 *            event
					 */

					Tabs.prototype.handleKeyPress = function( e ) {
						var keyCodes,
							currentIndex = this.currentTab.position;

						keyCodes = {
							DOWN: 40,
							ENTER: 13,
							ESCAPE: 27,
							HOME: 36,
							LEFT: 37,
							PAGE_DOWN: 34,
							PAGE_UP: 33,
							RIGHT: 39,
							SPACE: 32,
							TAB: 9,
							UP: 38
						};

						switch ( e.keyCode ) {
							case keyCodes.LEFT:
							case keyCodes.UP:
								currentIndex--;

								if ( 0 > currentIndex ) {
									currentIndex = this.tabData.length - 1;
								}

								break;

							case keyCodes.END:
								currentIndex = this.tabData.length - 1;

								break;

							case keyCodes.HOME:
								currentIndex = 0;

								break;

							case keyCodes.SPACE:
							case keyCodes.ENTER:
								currentIndex = this.handleEnter( currentIndex );

								break;

							case keyCodes.RIGHT:
							case keyCodes.DOWN:
								currentIndex++;

								if ( currentIndex >= this.tabData.length ) {
									currentIndex = 0;
								}

								break;

							default:
								currentIndex = null;
						}

						return currentIndex;
					};

					Tabs.prototype.handleEnter = function( currentIndex ) {

						// enter will select new panel or do nothing if
						// focus is on the active panel
						// so we have to deal with the currently focused
						// element rather than the selected tab
						var currentTabByFocusIndex = document.getElementById(
							document.activeElement.getAttribute( 'aria-controls' )
						);

						if ( currentTabByFocusIndex !== this.currentTab.$tab_panel.get( 0 ) ) {
							currentIndex = this.$tab_panels.index( currentTabByFocusIndex );
						}

						return currentIndex;
					};

					/**
					 * Opens the tab
					 *
					 * @param $tab_panel -
					 *            jQuery collection of opened tab
					 */

					Tabs.prototype.openTab = function( $tab_panel ) {
						var options = this.options;

						this.currentTab = {
							$tab_panel: $tab_panel.attr( {
								'aria-hidden': 'false',
								tabindex: '0'
							} ),

							$title: $tab_panel
								.prev( options.tab_class_title )
								.attr( {
									'aria-selected': true,
									'aria-expanded': true,
									tabindex: '0'
								} )
								.addClass( 'nav-tab-active' ),

							position: this.$tab_panels.index( $tab_panel )
						};

						if ( this.tabNav ) {
							this.updateTabNav();
						}
					};

					/**
					 * closes a tab if there's one open and a new one
					 * has been activated Only have one section open at
					 * a time
					 */

					Tabs.prototype.closeTab = function() {
						var currentTab = this.currentTab;

						currentTab.$tab_panel
							.attr( {
								'aria-hidden': 'true'
							} )
							.removeAttr( 'tabindex' );

						// update accordion title values as well so
						// everything is in synch
						currentTab.$title
							.attr( {
								tabindex: '-1',
								'aria-selected': 'false',
								'aria-expanded': 'false'
							} )
							.removeClass( 'nav-tab-active' );

						if ( this.tabNav ) {
							currentTab.$navItem
								.attr( {
									tabindex: '-1',
									'aria-selected': 'false',
									'aria-expanded': 'false'
								} )
								.removeClass( 'nav-tab-active' );
						}

						this.currentTab = null;
					};

					/**
					 * Updates the tab nav once a new tab has been
					 * opened
					 *
					 * @param $tab -
					 *            jQuery element for the tab that was
					 *            just opened
					 */

					Tabs.prototype.updateTabNav = function() {
						var currentTab = this.currentTab;

						currentTab.$navItem = this.$tabNavItems.eq( currentTab.position );

						currentTab.$navItem
							.attr( {
								tabindex: '0',
								'aria-selected': 'true',
								'aria-expanded': 'true'
							} )
							.addClass( 'nav-tab-active' );
					};

					/**
					 * Binds the Accordion events which is for tablet
					 * and mobile only
					 */

					Tabs.prototype.bindAccordionEvents = function() {
						var app = this;

						this.$accordion
							.on(
								'keydown',

								this.options.tab_class_title,

								function( e ) {
									var currentIndex = app.handleKeyPress( e );

									if ( null !== currentIndex ) {
										app.handleAccordion( app.$tab_panels.eq( currentIndex ) );
									}
								}
							)

							// https://bugs.webkit.org/show_bug.cgi?id=133613
							.find( '.tabs-container__title' )
							.on(
								'click',

								function( e ) {
									e.preventDefault();

									app.handleAccordion( jQuery( e.currentTarget ).next( app.options.tab_class_panel ) );
								}
							);
					};

					Tabs.prototype.handleAccordion = function( $tab_panel ) {
						if ( ! this.isCurrentTab( $tab_panel ) ) {
							this.openAccordion( $tab_panel );
						}
					};

					/**
					 * Open an accordion. open tab, make it open in a
					 * pretty way on tablet/mobile
					 *
					 * @param $tab_panel -
					 *            jQuery element of the tabpnael being
					 *            opened
					 */

					Tabs.prototype.openAccordion = function( $tab_panel ) {
						this.closeTab();

						this.openTab( $tab_panel );

						this.currentTab.$title.focus();

						jQuery( 'html, body' ).animate(
							{
								scrollTop: $tab_panel.offset().top - 110
							},
							500
						);
					};

					/**
					 * Open tab and the initial content to display
					 */

					Tabs.prototype.init = function() {
						var $startingTab;

						// save all elements that will become tabs
						this.$tab_panels = this.$container.find( this.options.tab_class_panel );

						this.fetchTabData();

						this.$accordion = this.$container.find( '.accordion-wrapper' ).attr( 'role', 'tablist' );

						this.bindAccordionEvents();

						// if there's more than 1 tab create the tab
						// navigation
						if ( 1 < this.$tab_panels.length ) {
							this.createTabNav();

							this.bindNavEvents();
						}

						$startingTab = this.$tab_panels.eq( this.options.default_tab );

						if ( this.$tab_panels.filter( '.tabs-container__default' ).length ) {
							$startingTab = this.$tab_panels.filter( '.tabs-container__default' );
						}

						this.openTab( $startingTab );
					};

					return Tabs;
				} )();

				jQuery( function() {
					window.Tabs = Tabs;

					new window.Tabs( jQuery( '#TabContainer' ) );
				} );
			}.call( this ) );
		},

		// initialize
		initialize: function() {
			this.render();
		},

		// render
		render: function() {
			this.boldgridYouTubePlayer();

			this.boldgridTabData();

			this.boldgridTabs();

			this.boldgridScreenOptions();
		}
	} );

	// Router for BoldGrid Dashboard Widget
	var BoldgridRouter = Backbone.Router.extend( {

		/**
		 * This isn't really necessary at the moment, but gives future expansion
		 * of this widget ease. Just using # for now, but eventually having the
		 * widget display relevant information based on the customer's journey
		 * within BoldGrid/WP setting up new views with URL hashes will be
		 * simple and maintainable.
		 *
		 * @since 1.13
		 */

		// home route, or empty @boldgriddashRoute
		routes: {
			'': 'boldgriddashRoute'
		},

		// main route for BoldGridDashView
		boldgriddashRoute: function() {

			// Create new BoldGridDashView to display widget on WP Dashboard
			var boldgriddashView = new BoldGridDashView();

			// @el send data to #boldgrid-welcome-custom
			jQuery( '#boldgrid-welcome-custom' ).html( boldgriddashView.el );

			// since everything is rendered and ready, display the initial set
			// of tabs
			jQuery( '.boldgrid-tab-content-wrapper[data-link-key=\'0\']' ).show();
		}
	} );

	// setup
	var boldgridRouter = new BoldgridRouter();

	// and start
	Backbone.history.start();
} )( jQuery, _, Backbone );

/**
 * ****************************************************************************
 * Toggle certain parts of the dashboard if needed.
 * ****************************************************************************
 */
var IMHWPB = IMHWPB || {};

IMHWPB.ScreenIdDashboard = function() {
	var self = this;

	self.baseAdmin = new IMHWPB.BaseAdmin();

	jQuery( function() {

		/**
		 * Toogle get_it_done if we just finished inspirations deployment
		 */
		if ( 'get_it_done' == self.baseAdmin.GetURLParameter( 'toggle' ) ) {
			self.toggle_get_it_done();
		} else {

			// show Installing your first BoldGrid Inspiration Site as default
			// video on first tab otherwise
			jQuery( '#boldgrid-tab-view-navigation > li:nth-child(3) > a' ).click();

			// add create your site and button on tabs display
			jQuery(
				'<br><div id="boldgrid-button-in-tabs"><h2>Create Your Site</h2><h4>Choose your theme and pages with</h4><h4><b>BoldGrid Inspirations.</b></h4><a href="admin.php?page=boldgrid-inspirations&boldgrid-tab=install"><span class="button button-primary button-hero">Get Started</span></a></div>'
			).insertAfter( 'li[data-tab-key=\'0\'][data-link-key=\'4\']' );
		}
	} );

	/**
	 * Toogle get_it_done if we just finished inspirations deployment
	 */
	this.toggle_get_it_done = function() {
		jQuery( 'a#TabController-Tab1' ).click();
	};
};

new IMHWPB.ScreenIdDashboard();
