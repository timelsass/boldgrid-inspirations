var IMHWPB = IMHWPB || {};

IMHWPB.Inspiration = function(configs, $) {
	var self = this;

	this.configs = configs;
	this.api_url = this.configs.asset_server;
	this.api_key = this.configs.api_key;
	this.api_param = 'key';
	this.api_key_query_str = this.api_param + "=" + this.api_key;
	this.num_themes_to_load;
	this.compiled_templates = {};
	
	// include additional submodules
	self.ajax = new IMHWPB.Ajax(configs);
	self.baseAdmin = new IMHWPB.BaseAdmin();
	
	// Track if the user has clicked "Install this website!"
	self.clicked_install = false;
	
	// Pre Step 1: Install type: Active or Staging?
	self.install_type = null;
	
	// Step 1: Track the base pageset id of the latest category selected
	self.base_pageset_id = null;
	
	// Step 1: Track the "sub category id" of the latest "sub category" clicked
	self.step_1_last_sub_category_id = null;
	
	// Step 1: Track the name of the last "sub category" clicked.
	self.step_1_last_sub_category_name = null;
	
	// Track the coin value of the latest build selected
	self.latest_single_build_coin_value = null;
	
	// Track the latest theme_id / pde previewed in step 2
	self.latest_step2_preview_theme_id = null;
	self.latest_step2_preview_pde = null;
	
	// Track the total number of themes available.
	// We'll use this data to help determine whether or not to show the
	// 'load more themes' button.
	self.total_theme_count = 0;
	
	// Track, as an array, theme id's currently showing in step 2.
	self.themes_currently_showing_in_step_2 = [];
	
	// Step 2: Set the max number of themes to load per request.
	self.step_2_themes_to_load_per_request = 6;

	$c_wpbody = jQuery('#wpbody');
	

	jQuery(function() {
		self.$select_install_type = jQuery('#select-install-type');
		self.$deploy_script = jQuery('#post_deploy');
		self.$candidate_checkbox = jQuery('input[name="candidate"]');
		self.$candidate_pages_checkbox = jQuery('input[name="candidate_pages"]');
		self.$step_1 = jQuery('#step-1');
		self.$boldgrid_loading = jQuery('#base-website-selection-heading .spinner');
		// Step 2
		self.$step_2_load_more_themes = jQuery('div#step-2-load-more-themes');
		self.$step_2_load_more_themes_button = jQuery('div#step-2-load-more-themes > button');
		self.$step_2_available_themes = jQuery('div#available_themes');
		self.$step_2_request_a_theme = jQuery('div#step-2-request-a-theme');
		self.$step_2_loading_message = jQuery('#base-website-selection .loading-wrapper');
		self.$step_2_additional_themes_message = jQuery('#step-2-additional-themes-message');
		
		self.compiled_templates = self.compile_templates();
		self.load_inspiration_path();
		self.select_submenu_item(Inspiration.page_selection, true);
		self.load_category_search_field();
		self.bind_notice_dismiss();
		self.init_allowed_tabs();

		jQuery('[name="close-inspiration-modal"]').on('click', function() {
			tb_remove();
		});
		
		/**
		 * ********************************************************************
		 * Page wide
		 * ********************************************************************
		 */
		
		// If 'select-install-type' is not visible, then staging is not an
		// option. So, set self.install_type to 'active'
		if( true == jQuery('div#select-install-type').hasClass('hidden') ) {
			self.install_type = 'active';
		}
		
		/**
		 * Warn the user of loss of changes if they leave this page.
		 */
		jQuery(window).on('beforeunload', function(e){
			// If the user is not clicking the install button...
			// If the user is on the initial Inspirations page...
			// If the user has selected in install type
			// Then ask them if they are sure they want to leave the page.
			if('standard' == boldgrid_inspirations_type &&
					false == self.clicked_install && null != self.install_type) {
				return 'You have not installed your BoldGrid website yet. If you leave this page, your changes will be lost. Would you still like to leave this page?';
			}
		});
		
		/**
		 * ********************************************************************
		 * Active / Staging selection
		 * ********************************************************************
		 */
		jQuery('[data-install-type]').on('click',function(){
			self.install_type = jQuery(this).data('install-type');
		});
		
		/**
		 * ********************************************************************
		 * Navigation
		 * ********************************************************************
		 */

		/**
		 * Handle click of a step in the navigation
		 */
		jQuery('#button_navigation a[name*="nav-step"]').on('click',
				function() {
					var $this = jQuery(this);
					self.boldgrid_toggle_steps($this.data('step'));
					var tab_text = $this.text();
					self.select_submenu_item(tab_text.toLowerCase(), false);
				});
		
		/**
		 * ********************************************************************
		 * Step 1 : Browse Categories
		 * ********************************************************************
		 */
		
		/**
		 * When a category is clicked... DEPRECATED
		 */
		/*
		 * jQuery('#imhwpb-categories').on( 'click', '.category', function() { //
		 * IF we're clicking on a category that is already expanded. // THEN
		 * slide that category up and do nothing more. // ELSE hide this one and
		 * show the other category. if( true ==
		 * jQuery(this).children('a').hasClass('expanded')) {
		 * jQuery(this).children('a').removeClass('expanded');
		 * jQuery(this).parent().children('.sub_categories').slideToggle('fast'); }
		 * else { // Collapse all subcategories
		 * jQuery('div.sub_categories').slideUp('slow'); // Reset all arrows
		 * jQuery('div.categories > span.category > a').removeClass('expanded'); //
		 * Expand the sub category
		 * jQuery(this).parent().children('.sub_categories')
		 * .slideToggle('fast'); // Ajust the arrows by pointing it down
		 * jQuery(this).children('a').addClass('expanded'); } });
		 */
		
		/**
		 * When a sub-category is clicked, set some values...
		 * 
		 * The event handler is for both 'categories-left' and
		 * 'categories-right, because the category list is divided among the two
		 * columns.
		 */
		jQuery('#categories-left, #categories-right')
				.on(
						'click',
						'.sub_category',
						function() {
							// set the base pageset id
							self.base_pageset_id = jQuery(this).data('pageSetId');
							
							// set the sub cat id
							self.step_1_last_sub_category_id = jQuery(this).data('sub-category-id');
							
							// set the sub cat name
							self.step_1_last_sub_category_name = jQuery(this).html();
							
							// Update the "Category name" in the step 2 nav bar.
							jQuery('span.category-name').html(self.step_1_last_sub_category_name);
							
							jQuery('#boldgrid_cat_id').val(
									jQuery(this).data('categoryId'));
							jQuery('#boldgrid_page_set_id').val(
									jQuery(this).data('pageSetId'));
							
							// Toggle the check marks next to sub categories.
							jQuery('span.sub_category').parent('li').removeClass('selected');
							jQuery(this).parent('li').addClass('selected');
							
							// Enable the "Select" button.
							jQuery('a.sub-category-select').removeAttr('disabled');
						});
		
		/**
		 * When user clicks "Select" after they have clicked and checked a sub
		 * category.
		 */
		jQuery('a.sub-category-select').on('click',function(){
			// If the button is disabled, return false and do nothing.
			if('disabled' == jQuery(this).attr('disabled')) {
				return false;
			}
			
			// Show the user they can now click the 'step 2' tab.
			jQuery('a#nav-step-2').removeClass('not-allowed');
			
			// Clear any themes we may have already shown and
			// toggle step 2;
			self.reset_available_themes();
			self.boldgrid_toggle_steps(2);

			self.boldgrid_sub_category_selected(self.base_pageset_id, self.step_1_last_sub_category_id);
		});
				
		/**
		 * ********************************************************************
		 * Step 1 : Search Categories
		 * ********************************************************************
		 */
		
		/**
		 * Handle the click of a category search result
		 */
		jQuery('#category_search_results', $c_wpbody).on(
				'click',
				'.category_search_result',
				function() {
					var sub_category_id = jQuery(this).data('sub-category-id');
					
					// When a user clicks on a search result, such as "Thai", it
					// will then trigger a click on "Thai" found under the
					// "Browse Categories" section.
					var sub_category_link = jQuery(
							"div.sub_categories span[data-sub-category-id='"
									+ sub_category_id + "']", $c_wpbody);
					jQuery(sub_category_link).click();
					
					// We also need to click the "Select" button.
					jQuery('a.sub-category-select').click();
					return false;
				});
		


		/**
		 * Step 1: Category search submissions
		 */
		jQuery('form#category_search', $c_wpbody)
				.submit(
						function(event) {
							var q = jQuery('#category-search-input', $c_wpbody)
									.val().trim();

							// if the user is trying to search without a search
							// word...
							if (!q) {
								alert("Oops! It looks like you did not enter a search word.")
								return false;
							}

							jQuery('#category_search_results', $c_wpbody).html(
									"<h4>Searching...</h4>");

							var data = {
								'q' : q
							};

							self.ajax.ajaxCall(data, 'category_search',
									category_search_success_action);

							return false;
						});
		
		/**
		 * ********************************************************************
		 * Step 2
		 * ********************************************************************
		 */
		
		/**
		 * When a budget is clicked:
		 */
		jQuery('a.coin_budget').on('click',function() {
			// If this is not the standard inspirations, abort.
			if('standard' != Inspiration.build_status) {
				return;
			}
			
			/*
			 * ****************************************************************
			 * BEGIN: Toggle class 'current' for coin budget. Not sure why we
			 * need to do this, something change in WP 4.2.3?
			 * ****************************************************************
			 */
			// If this budget is already selected, no need to do
			// anything else, abort.
			if (true == jQuery(this).hasClass('current')) {
				return;
			}

			// Unselect the currently selected budget by
			// removing the 'current' class.
			jQuery(this).closest('ul').find('a.coin_budget.current')
					.removeClass('current');

			// Set this budget as the selected budget by adding
			// the 'current' class.
			jQuery(this).addClass('current');
			/*
			 * ****************************************************************
			 */

			// Get the coin budget the user selected.
			var coin_budget = self.get_selected_coin_budget();
			
			// Update the form's coin_budget/
			jQuery('form#post_deploy #coin_budget').val(coin_budget);
			
			// Clear any themes we may have already shown.
			self.reset_available_themes();
			
			// Mimic a sub-category click from step 1. This will allow previews
			// to change AS a user clicks different budgets.
			var sub_category_id = jQuery('form#post_deploy #boldgrid_sub_cat_id').val();
			self.boldgrid_sub_category_selected(self.base_pageset_id, sub_category_id);
		});

		
		/**
		 * Handle click of "Live Preview" button in step 2
		 */
		jQuery('#base-website-selection, #boldgrid-theme-selection').on(
				'click',
				'.theme-actions .preview-button, .theme-screenshot',
				function() {
					var $this = jQuery(this);
					// If the user clicked on the image, rewrite to the preview
					// button so that the
					// below queries will still work
					if ($this.hasClass('theme-screenshot')) {
						$this = $this.closest('.available_theme').find('.preview-button');
					}
					// Ignore click if preview button not found.
					if ($this.length == false) {
						return false;
					}					
					
					var theme_id = $this.closest('.available_theme')
							.data('theme-id');
					var preview_url = $this.closest('.theme-actions')
							.data('preview-url');
					var theme_title = $this.closest('.theme-actions')
							.data('theme-title');
					var pde = $this.closest('.theme-actions')
							.data('pde');
					
					// Save the coin value from this build
					self.latest_single_build_coin_value = $this.closest('.theme-actions').data('coins');

					var modal_title = 'Step 2/3: Choose your base website';
					if($this.closest('#boldgrid-theme-selection').length) {
						modal_title = "Select a New Theme: ";
					}

					self.boldgrid_preview_theme(preview_url, theme_id, theme_title, pde, modal_title);
				});

		/**
		 * Handle click of "Select" in step 2
		 */
		jQuery('#base-website-selection').on(
				'click',
				'.theme-actions .select-button',
				function() {
					var theme_id = jQuery(this).closest('.available_theme')
							.data('theme-id');
					var pde = jQuery(this).closest('.theme-actions')
							.data('pde');
					var theme_name = jQuery(this).closest('.theme-actions')
							.data('theme-title');
					
					self.boldgrid_select_theme(theme_id, pde, theme_name);
				});
		
		/**
		 * Toggle wp-pointer for build cost during step 2
		 */
		var $body = jQuery('body');
		$body.on('mouseover', '.step-2-theme-coins', function() {
			self.show_custom_pointer(this, 'step-2-theme-coins', 'left');
		});

		$body.on('mouseout', '.step-2-theme-coins', function() {
			jQuery('#step-2-theme-coins').css('display','none');
		});
		
		/**
		 * Handle click of 'load more themes' in step 2
		 */
		self.$step_2_load_more_themes_button.on('click',function(){
			jQuery( self.$step_2_load_more_themes ).hide();
			self.load_more_themes();
			return false;
		});
		
		/**
		 * ********************************************************************
		 * Modal: Preview
		 * ********************************************************************
		 */
		
		// When previewing a page set, no matter if you click "go back" or
		// "select", you will still go to step 3
		jQuery('.previews').on('click', '.goback-to-page-sets', function() {
			self.boldgrid_toggle_steps(self.get_current_step());
		});
		
		jQuery('.previews').on('click', '.goback-to-themes', function() {
			self.boldgrid_toggle_steps(self.get_current_step());
		});

		/**
		 * Handle the clicking of 'select' in the preview modal.
		 * 
		 * This should only be done in traditional.
		 */
		if ( Inspiration.build_status != 'inspired' ) {

			// If we're previewing a page set (coming from step 3)
			jQuery('.previews #preview_page_set_button_set').on('click', '#select',
				function() {
					self.boldgrid_toggle_steps(self.get_current_step());
					setTimeout(function () {
						self.open_install_modal();
					}, 500);
			});
			
			// If we're previewing a theme (coming from step 2)
			jQuery('.previews #preview_theme_button_set').on('click', '#select',
					function() {
						var theme_id = self.latest_step2_preview_theme_id;
						var pde = self.latest_step2_preview_pde;
				
						self.boldgrid_select_theme(theme_id, pde, null);
					});
		}
		
		jQuery('#preview_iframe').load(function() {
			self.hide_loading_message_preview_iframe();
		});

		/**
		 * Monitor / Tablet / Phone previews
		 */
		jQuery('#preview .nav-tab-wrapper').on('click', '#monitor', function() {
			self.toggle_device_view_selected("monitor");
		});

		jQuery('#preview .nav-tab-wrapper').on('click', '#tablet', function() {
			self.toggle_device_view_selected("tablet");
		});

		jQuery('#preview .nav-tab-wrapper').on('click', '#phone', function() {
			self.toggle_device_view_selected("phone");
		});
		
		/**
		 * ********************************************************************
		 * Step 3
		 * ********************************************************************
		 */
		
		/**
		 * Clicking a radio button next to a page set.
		 */
		jQuery('#choose_your_page_set').on('click','input[type="radio"]', function() {
			self.boldgrid_load_page_set_preview(null);
		});
		
		/**
		 * Clicking of "Preview" button above the page set preview.
		 */
		jQuery('#choose_page_set, #add-existing-pages').on(
				'click',
				'#page_set_preview .preview-button',
				function() {
					var $this = jQuery(this);
					var preview_url = $this.closest('.theme-actions')
							.data('preview-url');
					
					var modal_title = 'Step 3/3: Choose Your Pages';
					if ($this.closest('#add-existing-pages')) { 
						modal_title = 'Add New Pages: '; 
					}
					
					self.boldgrid_preview_page_set(preview_url, modal_title);
				});

		/**
		 * Clicking of "Select" button above the page set preview.
		 */
		jQuery('#choose_page_set').on(
				'click',
				'#page_set_preview .select-button',
				function() {
					self.open_install_modal();
				});
		
		/**
		 * ********************************************************************
		 * Modal: Install
		 * ********************************************************************
		 */

		jQuery('.install-modal .goback').on('click', function() {
			self.boldgrid_toggle_steps( self.get_current_step() );
		});

		jQuery('.install-modal button[name="install-button"]').on('click', function() {
			self.boldgrid_install();
		});
		
		/**
		 * ********************************************************************
		 * Other:
		 * ********************************************************************
		 */
		
		jQuery(window).resize(function() {
			// Fix the margins of the themes within the grid.
			self.adjust_theme_grid_margin();			
		});
	});
	
	/**
	 * @summary Fix margins of "additional themes" in step 2.
	 * 
	 * When the themes are loaded into a grid in step 2, the standard margin of
	 * each '.theme' is adjusted by WordPress css. For example, the last
	 * '.theme' in each row usually has a margin-right of 0px, set by something
	 * like '.theme-browser .theme:nth-child(3n)'. The problem is that our
	 * message explaining "Additional themes" disrupts the flow of the
	 * nth-child, thus causing some of those additional themes to display poorly
	 * due to bad margins. This method reviews the margins of the initial themes
	 * and applies them to the additional themes.
	 * 
	 * @since 1.0.5
	 * 
	 * @listens: window:resize
	 */
	this.adjust_theme_grid_margin = function () {
		var count_additional_themes = jQuery('#step-2-additional-themes-message').nextAll('.theme').length;
		
		// Abort if necessary.
		if(2 != self.get_current_step() || 0 == count_additional_themes) {
			return;
		}
		
		// Count of number of themes in a row. Generally a small number, like 1
		// - 5, depending on the user's screensize.
		var count_in_row = 0;
		
		// CSS value of margin-right for last '.theme' in a row.
		// Set to 'false' by default, and updated by 'each' statement below
		// (usually with a value of '0px').
		var last_margin = false;
		
		// CSS value of margin-right for all but the last '.theme' in a row.
		// Set to 0 by default, and updated by 'each' statement below (with a
		// value similar to '35px').
		var standard_margin = 0;

		// Loop through each '.theme'.
		// Our goal is to define the following vars:
		// count_in_row, last_margin, standard_margin.
		jQuery('.themes .theme').each(function( index, value) {
			// Get the CSS value of margin-right for this '.theme'.
			var current_margin = jQuery(this).css('margin-right');
			
			// If this is the first theme in the row, its margin will be set as
			// the standard.
			if( false == last_margin ) {
				standard_margin = current_margin;
			}
			
			// Keep track of how many '.theme' divs are in a row.
			count_in_row++;
			
			// Set the 'last_margin' and determine if we should keep looping.
			// We only need to scan the first row of themes, as the 2nd row and
			// so on will all have the same margins.
			if( false != last_margin && current_margin != last_margin ) {
				last_margin = current_margin;
				
				return false;
			}else {
				last_margin = current_margin;
			}
		});

		// As we loop through the themes below, keep track of which theme in the
		// row we are on.
		var theme_count = 0;

		// Loop through all "Additional themes" and adjust the margins.
		jQuery('#step-2-additional-themes-message').nextAll('.theme').each(function(index,value) {
			theme_count++;
			
			// Apply the correct margin, based upon whether this is the last
			// theme in the row or not.
			if( theme_count == count_in_row ) {
				jQuery(this).css('margin-right',last_margin);
				
				// As this is the last theme in the row, reset theme_count to
				// signify the next iteration of the loop will on a new row.
				theme_count = 0;
			} else {
				jQuery(this).css('margin-right',standard_margin);
			}
		});
	};
	
	this.disable_select_coin_budget = function () {
		jQuery("a.coin_budget").addClass('inactive-link');
	};
	
	this.display_initial_steps = function () {
		self.boldgrid_toggle_steps(1);
		jQuery('#button_navigation').removeClass('hidden');
	};
	
	this.inspiration_load = function () {
		self.boldgrid_load_categories();
	};
	
	this.get_current_step = function () {
		return jQuery('.nav-tab-active:visible').data('step');
	};
	
	this.open_install_modal = function () {
		tb_show("Installation:",
			'#TB_inline?inlineId=install&modal=false', true);
		
		// After showing the modal, remind the user they're installing
		// to active / staged.
		if (true == Inspiration.mode_data.staging_active) {
			// Set the text if we don't have it
			if(typeof Inspiration.mode_data.install_destination_text == 'undefined') {
				Inspiration.mode_data.install_destination_text = ('stage' == Inspiration.mode_data.install_destination) ? 'Staging' : 'Active';	
			}
			
			jQuery('.install-modal-destination')
					.html(
							Inspiration.mode_data.install_destination_text);
		}
	};
	
	/**
	 * Allow notices to be removed
	 */
	this.bind_notice_dismiss = function () {
		$('.imhwpb-step').on('click', '.notice-dismiss', function () {
			$(this).closest('.notice.is-dismissible').hide();
		});
	};
	
	/**
	 * Step 1: Setup autocomplete for category search
	 * 
	 * jQuery's autocomplete by default does not highlight / bold the matched
	 * text. We were able to do this via this guide:
	 * http://salman-w.googlecode.com/svn/trunk/jquery-ui-autocomplete/highlight-matched-text.html
	 */
	this.load_category_search_field = function () {

		var get_category_tags_success_action = function(msg) {
			category_tags = msg.result.data;

			jQuery("#category-search-input", $c_wpbody).autocomplete({
				source : category_tags
			}).data("ui-autocomplete")._renderItem = function(ul, item) {
				var $a = jQuery("<a></a>").text(item.label);
				self.highlightText(this.term, $a);
				return jQuery("<li></li>").append($a).appendTo(ul);
			};
		};

		if (jQuery("#category-search-input").length) {
			self.ajax.ajaxCall({}, 'get_category_tags',
				get_category_tags_success_action);
		}
	};
	
	/**
	 * Give the user the option to install to active or staging.
	 * 
	 * ------------------------------------------------------------------------
	 * Inspiration.build_status
	 * ------------------------------------------------------------------------
	 * Value is either 'standard' or inspired. 'standard' - indicates that the
	 * user is building an entire site 'inspired' - indicates that the user is
	 * adding pages or themes
	 * 
	 * ------------------------------------------------------------------------
	 * Inspiration.mode_data.install_destination
	 * ------------------------------------------------------------------------
	 * Value is either 'active', 'stage', or choice.
	 * 
	 * ------------------------------------------------------------------------
	 * Inspiration.mode_data.staging_active
	 * ------------------------------------------------------------------------
	 * Is the staging plugin installed and activated?
	 */
	this.load_inspiration_path = function () {
		// Abort right away if we need to.
		if( 'inspired' == Inspiration.build_status) {
			return;
		}
		
		self.bind_select_install_type_buttons();
		
		// Show the "select install type" wrapper.
		self.$select_install_type.removeClass('hidden');
		
		// PROMPT USER FOR INSTALL TYPE
		// [Y] [ ] Staging plugin is active
		// [ ] [N] Already has an active site
		// [ ] [N] Already has a staging site
		if(Inspiration.mode_data.staging_active && 'choice' == Inspiration.mode_data.install_destination) {
			self.$select_install_type.find('.specific-to-you').removeClass('hidden');
			
			// Show the text that helps users choose between their type.
			self.$select_install_type.find('.choice_intro_text').removeClass('hidden');
			
			var open_section = Inspiration.mode_data['open-section'];
			if (  open_section ) {
				if ( open_section == 'active' || open_section == 'staging' ) {
					self.$select_install_type
						.find('.button[data-install-type="' + open_section + '"]:first')
						.click();
				}
			}
		}

		// AUTO SELECT "INSTALL TO ACTIVE"
		// [Y] [ ] Staging plugin is active
		// [ ] [N] Already has an active site
		// [Y] [ ] Already has a staging site
		if(Inspiration.mode_data.staging_active && 'active' == Inspiration.mode_data.install_destination) {
			self.$select_install_type.find('.specific-to-you').removeClass('hidden');
			
			self.$select_install_type.find('.staging-plugin-installed.staging-site-installed.active-site-not-installed').removeClass('hidden');
			
			self.show_install_type_reminder_alert();
		}
		
		// AUTO SELECT "INSTALL TO STAGING"
		// [Y] [ ] Staging plugin is active
		// [Y] [ ] Already has an active site
		// [ ] [N] Already has a staging site
		if(Inspiration.mode_data.staging_active && 'stage' == Inspiration.mode_data.install_destination) {
			self.$select_install_type.find('.specific-to-you').removeClass('hidden');
			
			self.$select_install_type.find('.staging-plugin-installed.staging-site-not-installed.active-site-installed').removeClass('hidden');
			
			self.show_install_type_reminder_alert();
		}
		
		// [ ] [N] Staging plugin is active
		// [ ] [N] Already has an active site
		if(false == Inspiration.mode_data.staging_active && 'active' == Inspiration.mode_data.install_destination) {
			// Show the "Continue" button
			self.$select_install_type.find('.no_staging_intro_text').removeClass('hidden');
		}
		
		// [ ] [N] Staging plugin is active
		// [Y] [ ] Already has an active site
		if(false == Inspiration.mode_data.staging_active && 'stage' == Inspiration.mode_data.install_destination) {
		}
		
		// This simply calls self.boldgrid_load_categories();
		self.inspiration_load();
	};
	
	/**
	 * 
	 */
	this.load_more_themes = function() {
		// Show the 'loading themes' image.
		self.$step_2_loading_message.removeClass('hidden');
		
		// Scroll to the bottom of the page so we can see the "loading" image.
		jQuery("html body").animate({ scrollTop: jQuery(document).height() }, 1000,function(){
			// Load additional themes.
			self.boldgrid_sub_category_selected(self.base_pageset_id,self.step_1_last_sub_category_id);
		});
	}
	
	/*
	 * this.boldgrid_tb_show = function(id, width, height) { tb_show(null,
	 * '#TB_inline?width=' + width + '&height=' + height + '&inlineId=' + id,
	 * false); self.resizeTB(width, height); };
	 */
	
	/**
	 * 
	 */
	this.reset_available_themes = function() {
		// Clear the themes.
		self.$step_2_available_themes.empty();
		
		// Hide the 'load more themes' buttons.
		self.$step_2_load_more_themes.hide();
		self.$step_2_request_a_theme.hide();
	}
	
	/**
	 * Fix a bug causing chrome / webkit to crash.
	 * 
	 * We're seeing an issue in which running tb_remove(); is causing chrome /
	 * webkit to crash.
	 * 
	 * tb_remove() calls "trigger('tb_unload')", which ultimately moves all
	 * elements back (from the modal to the "div id='preview'").
	 * 
	 * When moving all elements back, the iframe itself is moved back too. Not
	 * 100% sure, but it is believed that because the iframe is moved and it
	 * reloads, this reloading of the iframe is somehow causing the crash.
	 * 
	 * Our guess seems to be accurrate because below, BEFORE running tb_remove,
	 * we empty the iframe's src, thus preventing any reload.
	 */
	this.reset_preview_iframe_src = function() {
		jQuery('#preview_iframe').attr('src','');
	}

	this.resizeTB = function(width, height) {
		// are we working with %
		var $tbWindow = jQuery(document).find('#TB_window');
		var $tb_ajax_content = jQuery('#TB_ajaxContent');

		if (width.charAt(0) == '0') {
			$tbWindow.css('margin', '0px');
			$tbWindow.width((width * 100) + '%');
			$tbWindow.css('max-height', (height * 100) + '%');

			new_left = (1 - width) / 2 * 100;
			new_top = (1 - height) / 2 * 100;

			$tbWindow.css('left', new_left + '%');
			$tbWindow.css('top', new_top + '%');
		} else {
			$tbWindow.width(width);
			$tbWindow.height(height);

			new_width = $tbWindow.width();
			new_height = $tbWindow.height();
		}
		$tbWindow.css('width', 'auto');
		$tb_ajax_content.css('height', 'auto');
		$tb_ajax_content.css('overflow', 'auto');
	};

	/**
	 * 
	 */
	this.show_custom_pointer = function(element, pointer_id, position) {
		var pointer = jQuery('#' + pointer_id);
		var element_offset = jQuery(element).offset();
		var element_width = jQuery(element).width();
		var scroll = jQuery(window).scrollTop();
		
		switch(position){
		case 'left':
			jQuery(pointer).css('top', (element_offset.top - scroll) + 'px').css('left',
					(element_offset.left + element_width + 15) + 'px').css('display','inline');
			break;
		}
	}
	
	/**
	 * Remind the user which installation type they're installing their site to.
	 * 
	 * For example: "You already have installed a staging site. This will
	 * install to your active site."
	 * 
	 * This generated message is prepended to step 1.
	 */
	this.show_install_type_reminder_alert = function() {
		var handlerbars_data = {};
				
		if ( Inspiration.mode_data.install_destination == 'stage' ) {
			handlerbars_data = {
				'existing_install_type' : 'Active',
				'new_install_type' : 'Staging'
			};
		} else {
			handlerbars_data = {
				'existing_install_type' : 'Staging',
				'new_install_type' : 'Active'
			};
		}
			
		var markup = self.compiled_templates.recognized_site(handlerbars_data);
		self.$step_1.prepend(markup);
	}

	this.boldgrid_show_sub_cats = function(id, default_page_set_id) {
		// SAVE the selected options
		jQuery('#boldgrid_cat_id').val(id);
		jQuery('#boldgrid_page_set_id').val(default_page_set_id);

		container = "#sub_cats_of_" + id;
		jQuery(container).slideToggle('fast');
	};

	this.boldgrid_load_preview_iframe = function(preview_url, modal_title) {
		// SHOW and update message to "loading..."
		jQuery('#preview_div_message').css('display', 'block');
		jQuery('#preview_div_message').html('Loading preview...');
		
		// Update the coin value in the preview modal.
		self.set_preview_modal_coin_value();

		// TOGGLE preview window
		self.boldgrid_toggle_steps('preview');
		jQuery('#TB_ajaxWindowTitle').html(modal_title);

		// UPDATE iframe src
		jQuery('#preview_iframe').attr('src', preview_url);
	};

	this.boldgrid_preview_page_set = function(preview_url, modal_title) {
		self.toggle_device_view_selected("monitor");

		// TOGGLE preview button set
		jQuery('#preview_theme_button_set').css('display', 'none');
		jQuery('#preview_page_set_button_set').css('display', 'block');

		self.boldgrid_load_preview_iframe(preview_url, modal_title);
	};

	/**
	 * Ran when someone clicks "preview" in step 2
	 */
	this.boldgrid_preview_theme = function(preview_url, theme_id, theme_title,
			pde, modal_title) {
		// By default, the preview mode is a monitor.
		self.toggle_device_view_selected("monitor");

		// TOGGLE preview button set
		jQuery('#preview_theme_button_set').css('display', 'block');
		jQuery('#preview_page_set_button_set').css('display', 'none');

		if(theme_title) {
			jQuery('#preview_theme_name').html(theme_title);
		}
		
		// Save the theme_id and pde value
		self.latest_step2_preview_theme_id = theme_id;
		self.latest_step2_preview_pde = pde;
		
		self.boldgrid_load_preview_iframe(preview_url, modal_title);
	};

	this.hide_loading_message_preview_iframe = function() {
		jQuery('#preview_div_message').html('Theme loaded!');
		jQuery('#preview_div_message').slideUp('slow');
	};

	this.boldgrid_preview_tablet = function() {
		self.toggle_device_view_selected("tablet");
	};

	this.boldgrid_preview_phone = function() {
		self.toggle_device_view_selected("phone");
	};

	this.boldgrid_preview_monitor = function() {
		self.toggle_device_view_selected("monitor");
	};

	this.boldgrid_toggle_install_step = function() {
		last_page_set_preview_image = jQuery('#page_set_preview_image').attr(
				'src');

		jQuery('#thumbnail_of_site_to_install').attr('src',
				last_page_set_preview_image);

		self.boldgrid_toggle_steps('install');
	};

	/**
	 * Process the user's click of "Install this website!" during the last step.
	 */
	this.boldgrid_install = function() {
		self.clicked_install = true;
		var $post_deploy = jQuery('#post_deploy');
		
		// Set the theme version type if checked
		if ( self.get_theme_type() ) {
			$post_deploy.find('input[name="boldgrid_theme_version_type"]').val( self.get_theme_type() );
		}
		
		// Set the page version type if checked
		if (  self.get_page_type() ) {
			$post_deploy.find('input[name="boldgrid_page_set_version_type"]').val( self.get_page_type() );
		}
		$post_deploy.submit();
	};
	
	/**
	 * Bind the user's click of a button in step 0.
	 */
	self.bind_select_install_type_buttons = function() {
		// Add the notice towards the top of step 1 reminding the user where
		// they are installing their site.
		self.$select_install_type.find('.button[data-install-type]').one('click', function () {
			/**
			 * ****************************************************************
			 * UX
			 * ****************************************************************
			 */
			
			// Fade out the selection wrapper and display the initial steps.
			if ( !Inspiration.mode_data['open-section'] ) {
				self.$select_install_type.fadeOut( 'slow', self.display_initial_steps );
			} else {
				self.$select_install_type.hide();
				self.display_initial_steps();
			}
			
			/**
			 * ****************************************************************
			 * Abort if necessary
			 * ****************************************************************
			 */
			
			// Abort if the user did not need to make a choice.
			if(Inspiration.mode_data.install_destination != 'choice') {
				return;
			}
			
			var $this = jQuery(this);
				
			// If we're installing a staging site:
			if ( $this.data('install-type') == 'staging' ) {
				Inspiration.mode_data.install_destination_text = "Staging";
				
				var handlebars_data = {
					'install_type' : 'Staging',
				};
						
				self.$step_1.prepend();
						
				// Set the deploy form value of 'staging' to true / 1;
				var $staging_input = self.$deploy_script.find('[name="staging"]');
				$staging_input.val(1);
			} else {
				Inspiration.mode_data.install_destination_text = "Active";
				
				var handlebars_data = {
					'install_type' : 'Active',
				};
			}
			
			handlebars_data.url = Inspiration.mode_data.url;
		
			// var markup is a notice at the top of the page reminding
			// the user whether they're installing to active or staging.
			var markup = self.compiled_templates.selection(handlebars_data);
			self.$step_1.prepend(markup);
		});
	}

	/**
	 * Compiles all templates when the page loads
	 */
	this.compile_templates = function () { 
		var compiled_templates = {};

		var source = jQuery("#inspiration-selection-template").html();
		compiled_templates.selection = Handlebars.compile(source);

		var source = jQuery("#inspiration-recognize-site-template").html();
		compiled_templates.recognized_site = Handlebars.compile(source);
		
		return compiled_templates; 
	};
	
	/**
	 * Ugly code... needs to be cleaned up
	 */
	this.resizeTB = function(width, height) {
		// are we working with %
		if (width.charAt(0) == '0') {
			jQuery(document).find('#TB_window').css('margin', '0px');

			jQuery(document).find('#TB_window').width((width * 100) + '%');
			jQuery(document).find('#TB_window').css('max-height',
					(height * 100) + '%');

			new_left = (1 - width) / 2 * 100;
			new_top = (1 - height) / 2 * 100;

			jQuery(document).find('#TB_window').css('left', new_left + '%');
			jQuery(document).find('#TB_window').css('top', new_top + '%');
		} else {
			jQuery(document).find('#TB_window').width(width);
			jQuery(document).find('#TB_window').height(height);

			new_width = jQuery(document).find('#TB_window').width();
			new_height = jQuery(document).find('#TB_window').height();
		}
		jQuery('#TB_ajaxContent').css('width', 'auto');
		jQuery('#TB_ajaxContent').css('height', 'auto');
		jQuery('#TB_window').css('overflow', 'auto');
	};

	this.toggle_device_view_selected = function(now_selected) {
		var devices = [ "monitor", "tablet", "phone" ];

		/**
		 * Update the device tabs
		 */
		var arrayLength = devices.length;
		for (var i = 0; i < arrayLength; i++) {
			if (now_selected == devices[i]) {
				jQuery('#' + devices[i]).addClass('nav-tab-active');
			} else {
				jQuery('#' + devices[i]).removeClass('nav-tab-active');
			}
		}

		/**
		 * Update the css for the preview div / iframe
		 */
		// loop through each device
		for (var i = 0; i < devices.length; i++) {
			// config the class name
			var className = "preview_" + devices[i];
			// if we clicked on the tab (ie. tablet) that we're currently
			// looping through (ie. table)
			if (now_selected == devices[i]) {
				jQuery('#preview_div').addClass(className);
				jQuery('#preview_iframe').addClass(className);
			} else {
				jQuery('#preview_div').removeClass(className);
				jQuery('#preview_iframe').removeClass(className);
			}
		}

		jQuery('#TB_ajaxContent').css('height', 'auto');	 
	};
	

	
	/**
	 * 
	 */
	this.toggle_load_more_themes_buttion = function() {		
		// How many themes are currently showing?
		var count_step2_theme_previews = jQuery('div#available_themes').children('div.available_theme').length;
		
		// If there are more themes to show, then show the button
		if(count_step2_theme_previews < self.total_theme_count) {			
			self.$step_2_load_more_themes.show();
			self.$step_2_request_a_theme.hide();
			
		} else {
			self.$step_2_load_more_themes.hide();
			self.$step_2_request_a_theme.show();
		}
	}
	
	/**
	 * Give the user the option to 'try again' if we could't load the pageset
	 * preview.
	 */
	this.try_again_load_page_set_preview = function(page_set_id) {
		// Hide the loading message
		var $loading_wrapper = jQuery('#choose_page_set').find('.loading-wrapper');
		$loading_wrapper.addClass('hidden');
		
		// Display our "Try again" message
		var preview_div = jQuery('div#step-3 div#choose_page_set div.row > div:eq(1)');
		var try_again_message = "<p class='page-set-preview-try-again'>There was an error generating the preview for this pageset.<br />" +
		"<button class='button button-primary' id='try_again_page_set_preview'>Try again</button></p>";
		jQuery(preview_div).prepend(try_again_message);
		
		// Add a listener for the newly added 'try again' button
		jQuery('button#try_again_page_set_preview').on('click',function() {
			self.boldgrid_load_page_set_preview(page_set_id);
		});
	}

	this.boldgrid_toggle_steps = function(s) {
		/**
		 * If the user is clicking the 2nd or 3rd step, only allow them to
		 * continue if they are allowed to.
		 */
		if((2 == s || 3 == s) && true == jQuery('a#nav-step-' + s).hasClass('not-allowed')) {
				return;
		}
		
		jQuery('#boldgrid-error-message').addClass('hidden');
		
		if (s == 'preview') {
			// jQuery('#preview').css('display','block');
			tb_show("Preview", '#TB_inline?inlineId=preview&modal=false', true);
			
			var $tb_window = jQuery('#TB_window');
			$tb_window.css('width', '90%');
			$tb_window.css('height', '90%');

			$tb_window.css('top', '5%');
			$tb_window.css('left', '5%');

			$tb_window.css('margin-left', '0px');
			$tb_window.css('margin-top', '0px');

			$tb_window.css('overflow', 'hidden');

			// jQuery('#TB_ajaxContent').css('width', '100%');
			// jQuery('#TB_ajaxContent').css('height', '100%');
			// jQuery('#TB_ajaxContent').css('padding', '0px 15px 0px 15px');

			// if we're showing the preview TB, no need to continue looping and
			// closing steps. if we did though, clicking "x" in the TB would
			// show nothing because all the steps would have been closed.
			return;
		} else {
			if (typeof tb_remove == 'function') {
				// Before running tb_remove, prevent webkit crash:
				self.reset_preview_iframe_src();
				
				tb_remove();
			}
		}

		for (i = 1; i < 4; i++) {
			if (i == s) {
				jQuery('#step-' + i).css('display', 'block');
				// // do we still need this?
				// // jQuery('#nav-step-' + i).addClass('selected');
				jQuery('#nav-step-' + i).addClass('nav-tab-active');
			} else {
				jQuery('#step-' + i).css('display', 'none');
				// // do we still need this?
				// // jQuery('#nav-step-' + i).removeClass('selected');
				jQuery('#nav-step-' + i).removeClass('nav-tab-active');
			}
		}

		if (s == 'install') {
			jQuery('.install-modal').css('display', 'block');
		} else {
			jQuery('.install-modal').css('display', 'none');
		}
	};

	/**
	 * Triggered on click of "Select" button during Step 2
	 * 
	 * Essentially, we need to toggle to step 3 and load a page set preview
	 */
	this.boldgrid_select_theme = function(theme_id, pde, theme_title) {
		// If we're passing arguments, take action on them.
		if (theme_id != null) {
			jQuery('#boldgrid_theme_id').val(theme_id);
		}

		/*
		 * Set the value of the form's #boldgrid_pde input. This is the only
		 * spot in this document in which that value is set.
		 * 
		 * The pde value needs to be a json string, as in:
		 * [{"pde_type_name":"background_image","pde_curated_id":25}]
		 * 
		 * If we're passing in an object, JSON.stringify it before setting the
		 * value of #boldgrid_pde.
		 */
		if (pde != null) {
			if('object' == typeof pde) {
				pde = JSON.stringify(pde);
			}
			jQuery('#boldgrid_pde').val(pde);
		}

		if (theme_title) {
			jQuery('#preview_theme_name').html(theme_title);
		}

		// step 3: start loading the base page set
		self.boldgrid_load_page_set_preview(-1);

		// step 3: toggle to it
		self.boldgrid_toggle_steps(3);
	};

	/**
	 * Enable the selection of a coin budget.
	 */
	this.enable_select_coin_budget = function () {
		jQuery("a.coin_budget").removeClass('inactive-link');
	}; 

	this.get_category_page_sets = function(cat_id) {
		var success_action = function(msg) {
			var source = jQuery("#page-set-selection").html();
			var template = Handlebars.compile(source);
			jQuery('#choose_your_page_set').html(template(msg.result.data));
		};

		self.ajax.ajaxCall({'category_id' : cat_id}, 'get_category_page_sets', success_action);
	};

	/**
	 * Triggered by:
	 * 
	 * Step 2 >> "Select" button click >> self.boldgrid_select_theme()
	 * 
	 * Step 3 >> Pageset radio click >> event handler
	 */
	this.boldgrid_load_page_set_preview = function(page_set_id) {
		// If we don't pass in a page_set_id, set it to the value of the
		// currently selected page set.
		if(null ==  page_set_id) {
			page_set_id = self.get_selected_page_set();
		}
		
		var theme_id = jQuery('#boldgrid_theme_id').val();
		
		if (-1 == theme_id) {
			alert("Error: You MUST choose a theme BEFORE you can load a site preview.");
			return false;
		}
		
		var $spinner = jQuery('<span class="spinner is-active"></span>');
		
		jQuery('#choose_your_page_set input:checked')
			.closest('div')
			.append($spinner);
			
		var $current_radio = jQuery('input[type="radio"]').attr('disabled', 'disabled');

		// IF page_set_id is -1, then we're loading the default page set (which
		// is preselected)
		if (page_set_id == '-1') {
			page_set_id = jQuery('#boldgrid_page_set_id').val();
			// ELSE SAVE the page set id that was selected
		} else {
			jQuery('#boldgrid_page_set_id').val(page_set_id);
		}

		jQuery('#page_set_preview').empty().addClass('boldgrid-loading');
		
		theme_id = jQuery('#boldgrid_theme_id').val();
		
		// Hide any existing 'Try again' messages
		jQuery('p.page-set-preview-try-again').remove();
		
		// Show the user they can now click the 'step 3' tab.
		jQuery('a#nav-step-3').removeClass('not-allowed');

		var data = {
			'theme_id' : theme_id,
			'cat_id' : jQuery('#boldgrid_cat_id').val(),
			'sub_cat_id' : jQuery('#boldgrid_sub_cat_id').val(),
			'page_set_id' : page_set_id,
			'pde' : jQuery('#boldgrid_pde').val(),
			'wp_language' : jQuery('#wp_language').val(),
			'coin_budget' : self.get_selected_coin_budget(),
			'theme_version_type' : self.get_theme_type(),
			'page_version_type' : self.get_page_type(),
			'site_hash' : self.configs['site_hash'],
			'inspirations_mode' : 'standard'
		};
		
		// DEPRECATED $loading_wrapper.removeClass('hidden');

		var success_action = function(msg) {
			if('200' != msg.status){
				self.try_again_load_page_set_preview(page_set_id);
				return false;
			}
			
			var response = msg.result.data.profile;
			// DEPRECATED $loading_wrapper.addClass('hidden');
			jQuery('#page_set_preview').removeClass('boldgrid-loading');

			// save the language id passed back from the asset server
			jQuery('form#post_deploy #boldgrid_language_id').val(
					response.language);
			
			// Save the coin value from this build
			self.latest_single_build_coin_value = response.coins;

			// create the data to pass to handlebars
			var page_set_thumbnail_url = self.create_page_set_thumbnail_url(response.asset_id);
			
			handlebars_data = {
				'page_set_thumbnail_url' : page_set_thumbnail_url,
				'preview_url' : response.preview_url,
				'theme_title' : response.theme_title,
				'coins' : response.coins
			}

			// setup handlebars and pass our data to it
			var psps_source = jQuery("#page-set-preview-select-template")
					.html();
			var psps_template = Handlebars.compile(psps_source);
			jQuery('#page_set_preview').html(psps_template(handlebars_data));
			jQuery('#boldgrid_build_profile_id').val(response.id);
		};
		
		var failure_action = function() {
			self.try_again_load_page_set_preview(page_set_id);
			return false;
		}
		
		// Occurs on success or failure
		var complete_action = function () {
			$spinner.remove();
			$current_radio.removeAttr('disabled');
		};

		self.ajax.ajaxCall(data, 'get_build_profile', success_action, failure_action, complete_action);
	};
	
	this.create_page_set_thumbnail_url = function( asset_id ) {
		return   self.api_url
		+ self.configs.ajax_calls['get_asset'] + "?"
		+ self.api_key_query_str + "&id=" + asset_id;
	};
	
	/**
	 * Get the selected coin budget
	 */
	this.get_selected_coin_budget = function() {
		return jQuery('a.coin_budget.current').data('value');
	}
	
	/**
	 * Get the selected page set id
	 */
	this.get_selected_page_set = function() {
		return jQuery('input[name=available_page_set_id]:checked').val()
	}
	
	/**
	 * Get theme type based on checkbox
	 */
	this.get_theme_type = function () {
		// Default to null to allow backend to choose default
		return self.$candidate_checkbox.prop('checked') ? "inprogress" : null;
	};

	this.get_page_type = function () {
		// Default to null to allow backend to choose default
		return self.$candidate_pages_checkbox.prop('checked') ? "inprogress" : null;
	};
	
	/**
	 * Triggered:
	 * 
	 * Step 1 >> Sub category click >> event handler >>
	 * self.boldgrid_sub_category_selected() >> We loop through each theme and
	 * call this function.
	 */
	this.load_available_theme_div = function(cat_id, default_page_set_id,
			sub_cat_id, theme_id) {
		
		var data = {
			'theme_id' : theme_id,
			'cat_id' : cat_id,
			'sub_cat_id' : sub_cat_id,
			'default_page_set_id' : default_page_set_id,
			'theme_version_type' : self.get_theme_type(),
			'page_version_type' : self.get_page_type(),
			'pde' : jQuery('#boldgrid_pde').val(),
			'wp_language' : jQuery('#wp_language').val(),
			'coin_budget' : self.get_selected_coin_budget(),
			'site_hash' : self.configs['site_hash']
		};
		
		var check_remaining_builds = function () {
			self.num_themes_to_load = self.num_themes_to_load - 1;
			// If we've loaded all of the themes that need to be loaded...
			if (self.num_themes_to_load == 0) {
				
				// Hide the 'loading message'.
				self.$boldgrid_loading.removeClass('is-active');
				self.$step_2_loading_message.addClass('hidden');
				
				// Enable all the radio buttons for choosing a budget.
				self.enable_select_coin_budget();
				
				// Toggle / show the 'load more themes' button.
				self.toggle_load_more_themes_buttion();
				
				// If we ultimately displayed no previews for the user,
				// show an error message.
				if (false == jQuery('#available_themes').html()) {
					jQuery('#step-2').hide();
					self.$step_2_loading_message.addClass('hidden');
					jQuery('#boldgrid-error-message').removeClass('hidden');
				}
				
				// Fix the margins of the themes within the grid.
				self.adjust_theme_grid_margin();
			}
		};

		var success_action = function(msg) {
			var is_active_theme_request = !self.get_theme_type();
			
			// Show theme if is candidate or theme is active
			if ( msg.result.data.theme && ( msg.result.data.theme.is_candidate || is_active_theme_request) ) {
				
				var source = jQuery("#build-profile-template-revised").html();
	
				var template = Handlebars.compile(source);
				msg.result.data.theme_id = theme_id;
				var thumbnail_source = template(msg.result.data);
				jQuery('#available_themes').append(thumbnail_source);
			}
			check_remaining_builds();
		};
		
		var failure_action = function () {
			check_remaining_builds();
		};

		self.ajax.ajaxCall(data, 'get_layouts', success_action, failure_action);
	};

	/**
	 * Load all of the categories into Step 1.
	 */
	this.boldgrid_load_categories = function() {
		var source = jQuery("#get-categories-template").html();
		var template = Handlebars.compile(source);
		var success_action = function(msg) {
			jQuery('#imhwpb-categories').html(template(msg.result.data));
			
			// After loading all of the categories, move them to either the left
			// or right category column.
			var categories_left = jQuery('div#categories-left');
			var categories_right = jQuery('div#categories-right');
			jQuery('div.categories:even').appendTo(categories_left);
			jQuery('#imhwpb-categories').children('div.categories').appendTo(categories_right);
		};
		self.ajax.ajaxCall({'inspirations_mode' : 'standard'}, 'get_categories', success_action);
	};

	/**
	 * Triggered:
	 * 
	 * Step 1 >> Sub category click >> event handler
	 */
	this.boldgrid_sub_category_selected = function(default_page_set_id,
			sub_cat_id) {
		// Disable selection of a coin budget.
		self.disable_select_coin_budget();
		
		// Save the selected options.
		jQuery('#boldgrid_page_set_id').val(default_page_set_id);
		jQuery('#boldgrid_sub_cat_id').val(sub_cat_id);
		self.$boldgrid_loading.addClass('is-active');
		
		self.set_themes_currently_showing_in_step_2();

		// Get the category id
		var cat_id = jQuery('#boldgrid_cat_id').val();
		var data = {
			'cat_id' : sub_cat_id,
			'existing_theme_ids' : self.themes_currently_showing_in_step_2,
			'inspirations_mode' : 'standard',
		};
		
		// If we're currently showing at least 1 theme, pass the 'all'
		// flag in our request.
		if(self.themes_currently_showing_in_step_2.length > 0) {
			data.all = true;
		}

		// Show the "Now loading" message.
		self.$step_2_loading_message.removeClass( 'hidden' );
		
		// Load themes for desired sub-category
		var successAction = function(response) {
			/*
			 * Create the array of available 'themes'. If we don't have any
			 * 'themes' but we do have 'additional_themes', theme
			 * 'additional_themes' will be used instead.
			 */ 
			var available_themes = response.result.data.themes;
			if(available_themes.length == 0 && response.result.data.additional_themes.length > 0) {
				available_themes = response.result.data.additional_themes;
				// We're only going to load X themes at a time,
				// so slice the array.
				available_themes = available_themes.slice(0,self.step_2_themes_to_load_per_request);
				
				// Because we're loading additional themes, we need to display
				// the applicable message if it's not already displayed.
				if( true == self.$step_2_additional_themes_message.hasClass('hidden')) {
					self.$step_2_additional_themes_message.removeClass('hidden').appendTo(self.$step_2_available_themes);
				}
			}
			
			self.num_themes_to_load = available_themes.length;
			
			self.total_theme_count = response.result.data.total_theme_count;

			jQuery.each(available_themes, function(key, theme_id) {
				self.load_available_theme_div(cat_id, default_page_set_id,
						sub_cat_id, theme_id);
			});
		};

		self.ajax.ajaxCall(data, 'get_theme_ids', successAction);

		// Now that we know the category, load the available pagesets for step 3
		self.get_category_page_sets(cat_id);
	};

	/**
	 * this.highlightText code is run from within the
	 * get_category_tags_success_action function above.
	 */
	this.highlightText = function(text, $node) {
		var searchText = jQuery.trim(text).toLowerCase(), currentNode = $node
				.get(0).firstChild, matchIndex, newTextNode, newSpanNode;
		while ((matchIndex = currentNode.data.toLowerCase().indexOf(searchText)) >= 0) {
			newTextNode = currentNode.splitText(matchIndex);
			currentNode = newTextNode.splitText(searchText.length);
			newSpanNode = document.createElement("span");
			newSpanNode.className = "highlight";
			currentNode.parentNode.insertBefore(newSpanNode, currentNode);
			newSpanNode.appendChild(newTextNode);
		}
	};

	/**
	 * Update the following based upon the page being viewed:
	 * 
	 * 1. Document <title>
	 * 
	 * 2. 'current' class of submenu items in left dashboard nav.
	 * 
	 * 3. 'nav-tab-active' class of tabs in Pages / Themes page.
	 */
	this.select_submenu_item = function ( item, update_tab ) {
		var data = {
			'pages' : 'Add New Pages',
			'themes' : 'Install New Themes',
			'install' : 'Install New Site',
			'Inspiration' : ''
		};
		
		// Get the document <title>
		var $page_title = jQuery(document).find("title");
		
		jQuery('#toplevel_page_boldgrid-inspirations .wp-submenu li a').each( function () {
			var $this = jQuery(this);
			var $li = $this.closest('li');
			if ($this.text() == data[item]) {
				/**
				 * Toggle the 'current' class of the submenu items for
				 * Inspirations in the left nav.
				 */
				
				// Remove 'current' class from all submenu items.
				$this.closest('ul').find('li').removeClass('current');
				
				// Add 'current' class to this submenu item.
				$li.addClass('current');
				
				/**
				 * Toggle the value of the document's <title>.
				 */
				
				// Generate the new page title.
				var new_page_title_text = $page_title.text().replace(/.*‹ /,
						data[item] + " ‹ ");
				
				// Set the new page title.
				$page_title.text(new_page_title_text);
				
				if ( update_tab ) {
					jQuery('#button_navigation .nav-tab').each(function () {
						var $this = jQuery(this);
						if ( data[$this.text().toLowerCase()] == data[item]) {
							self.boldgrid_toggle_steps($this.data('step'));
						}
					});
				}
				
				return false;
			}
		});
	};
	
	/**
	 * Set the coin value in the preview modal.
	 */
	this.set_preview_modal_coin_value = function() {
		var coin_value_html = 0 == self.latest_single_build_coin_value ? '0' : '0 - ' + self.latest_single_build_coin_value;
		
		jQuery('div#preview div.coins span.coins').html(coin_value_html);
	}
	
	/**
	 * 
	 */
	this.set_themes_currently_showing_in_step_2 = function() {
		var themes = [];
		
		self.$step_2_available_themes.children('div.available_theme').each(function( key, value ) {
			themes.push(jQuery(value).data('theme-id'));
		});
		
		self.themes_currently_showing_in_step_2 = themes;
	}
	
	/**
	 * Process the category search results
	 */
	var category_search_success_action = function(msg) {
		data = msg.result.data;
		data['query'] = jQuery('#category-search-input', $c_wpbody).val();
		var source = jQuery("#category-search-results-template", $c_wpbody)
				.html();
		var template = Handlebars.compile(source);
		jQuery('#category_search_results', $c_wpbody).html(template(data));
	};
	
	/**
	 * Not all tabs are clickable right away. For example, you can't get to step
	 * 2 before first going through step 1. This method adds css to show the
	 * user they can't click certain tabs.
	 */
	self.init_allowed_tabs = function() {
		// If this is not the standard inspirations, abort.
		if('standard' != Inspiration.build_status) {
			return;
		}
		
		jQuery('a#nav-step-2').addClass('not-allowed');
		jQuery('a#nav-step-3').addClass('not-allowed');
	}
};

IMHWPB.Inspiration.instance = new IMHWPB.Inspiration(IMHWPB.configs, jQuery);
