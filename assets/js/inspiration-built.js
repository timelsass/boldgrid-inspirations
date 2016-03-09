(function($, IMHWPB) {
	// General Variables.
	var self = {};

	// Compiled Add pages Template
	self.recognize_template_compiled;
	self.selection_template_compiled;
	self.has_not_built_with_either_compiled;

	// State Collections.
	self.num_themes_to_install;
	self.all_themes = {};
	self.install_options = {};

	// State Booleans.
	self.user_selected_path = false;

	/**
	 * The initialize process.
	 */
	self.init = function() {
		if ('inspired' != Inspiration.build_status) {
			return;
		}

		// Initialize elements
		self.$available_themes = jQuery('#available_themes');
		self.$additional_themes = jQuery('#additional_themes');
		self.$theme_count_category = jQuery('.title-count.theme-count[data-theme-type="category-themes"]');
		self.$theme_count_additional = jQuery('.title-count.theme-count[data-theme-type="additional-themes"]');
		self.$build_profile_template = jQuery("#build-profile-template-revised");
		self.$error_message = jQuery('#error-message');
		self.$step_1_loading = jQuery('#step-1 .loading-wrapper');
		self.$step_2_loading = jQuery('#step-2 .loading-wrapper');
		self.$step_2_branding = jQuery('#step-2 .branding-wrapper');
		self.$boldgrid_error_message = jQuery('#boldgrid-error-message');
		self.$theme_selection = jQuery('#boldgrid-theme-selection');
		self.$select_content_install_type = jQuery('#select-content-install-type');
		self.$deploy_script = jQuery('#post_deploy');
		self.$theme_selection_wrapper = $('#theme-selection-wrapper');
		self.$theme_selection_spinner = self.$theme_selection_wrapper.find('.spinner');

		// Hide the nav tabs.
		jQuery('.nav-tab').hide();
		
		// The following 3 vars help with the display of the navbar. For an
		// explanation of how it works, please read note 201507302221.
		self.$step_2_nav_bar = jQuery('.wrap.step-2-nav-bar');
		self.$themes_step_2_nav_bar_wrapper = self.$theme_selection_wrapper
				.find('.step-2-nav-bar-wrapper');

		// Theme type 1/2: Themes belonging to the current category
		self.$themes_current_category = self.$theme_selection_wrapper
				.find('.themes-current-category');
		// Theme type 2/2: "Additional Themes", themes not belonging to the
		// current category.
		self.$themes_other_categories = self.$theme_selection_wrapper
				.find('.themes-other-categories');

		self.$additional_themes_button = jQuery('a.additional_themes');

		// Inputs from the deployment form
		self.deploy_inputs = {
			$deploy_type : self.$deploy_script.find('input[name="deploy-type"]'),
			$theme : self.$deploy_script.find('input[name="boldgrid_theme_id"]'),
			$staging : self.$deploy_script.find('[name="staging"]'),
		};

		// Self Inspiration Load
		if (Inspiration.build_status == 'inspired') {
			self.compile_templates();
			self.bind_select_buttons('install-theme-modal', 'preview_theme_button_set',
					self.$theme_selection);
			self.load_inspiration_path();
		}

		self.set_form_options();
	};

	/**
	 * Display the options so that the user can choose staging or active as the
	 * install destination.
	 */
	self.load_inspiration_path = function() {
		// PROMPT USER FOR INSTALL TYPE
		// [Y] [ ] Staging plugin is active
		// [Y] [ ] Already has an active site
		// [Y] [ ] Already has a staging site
		
		// Define choice_mode.
		var choice_mode = false,
			// Define open_section.
			open_section = Inspiration.mode_data['open-section'];
		
		if (Inspiration.mode_data.staging_active
				&& 'choice' == Inspiration.mode_data.inspired_install_destination) {
			self.$select_content_install_type.removeClass('hidden');

			// Show the text that helps users choose between their type.
			self.$select_content_install_type.find('.choice_intro_text').removeClass('hidden');

			choice_mode = true;
		} else {
			self.load_content();
		}

		self.bind_install_location_buttons();

		if (choice_mode && open_section) {
			if (open_section == 'active' || open_section == 'staging') {
				self.$select_content_install_type.find(
						'.button' + '[data-install-type="' + open_section + '"]:first').click();
			}
		}
	};

	/**
	 * The install options that were passed in are indexed by staging and
	 * active, Depending on the choice of the user or what we have automatically
	 * determined, choose the correct options.
	 */
	self.find_install_options = function() {
		// Define destination.
		var destination = Inspiration.mode_data.inspired_install_destination,
			// Define install_key.
			install_key = 'active_options';
		
		if (destination == 'stage') {
			install_key = 'boldgrid_staging_options';
		}

		self.install_options = Inspiration.install_options[install_key];
	};

	/**
	 * Binds install location selection buttons This allows the user choose if
	 * they would like to install into staging or active only available under
	 * certain conditions.
	 */
	self.bind_install_location_buttons = function() {
		self.$select_content_install_type.find('.button').one('click', function() {
			var $this = $(this);

			if ($this.data('install-type') == 'active') {
				Inspiration.mode_data.inspired_install_destination = 'active';
				Inspiration.mode_data.inspired_install_destination_text = 'Active';
			} else {
				Inspiration.mode_data.inspired_install_destination = 'stage';
				Inspiration.mode_data.inspired_install_destination_text = 'Staging';
				self.deploy_inputs.$staging.val(1);
			}
			self.user_selected_path = true;

			if (!Inspiration.mode_data['open-section']) {
				self.$select_content_install_type.fadeOut('slow', self.load_content);
			} else {
				self.$select_content_install_type.hide();
				self.load_content();
			}
			
			return false;
		});
	};

	/**
	 * Bind additional themes button.
	 * 
	 * When the "Additional themes" button is selected.
	 * 
	 * 1: Toggle the style of the button.
	 * 
	 * This is done by toggling the 'show-filters' class on the <body>. This is
	 * a feature of WordPress that we are replicating.
	 * 
	 * 2: Show the appropriate themes.
	 * 
	 * This is done by hiding the 'current category' themes and showing the
	 * 'other category' themes (and vice versa as the button is clicked).
	 */
	self.bind_additional_themes_button = function() {
		self.$additional_themes_button.on('click', function() {
			// If this is the standard inspirations, abort.
			if ('standard' == Inspiration.build_status) {
				return;
			}

			// Get the current theme type ( current / other ).
			var current_theme_type_show = self.$additional_themes_button.attr('data-theme-type');

			if ('current' == current_theme_type_show) {
				self.$additional_themes_button.attr('data-theme-type', 'other');

				jQuery('body').addClass('show-filters');

				// Show 'other' and hide current
				jQuery('.themes-current-category').addClass('hidden');
				jQuery('.themes-other-categories').removeClass('hidden');
			} else {
				self.$additional_themes_button.attr('data-theme-type', 'current');

				jQuery('.themes-current-category').removeClass('hidden');
				jQuery('.themes-other-categories').addClass('hidden');

				jQuery('body').removeClass('show-filters');
			}
			
			return false;
		});
	}

	/**
	 * When the "Additional themes" button is selected:
	 * 
	 * 1: Toggle the style of the button.
	 * 
	 * This is done by toggling the 'show-filters' class on the <body>. This is
	 * a feature of WordPress that we are replicating.
	 * 
	 * 2: Show the appropriate themes.
	 * 
	 * This is done by hiding the 'current category' themes and showing the
	 * 'other category' themes (and vice versa as the button is clicked).
	 */
	self.bind_additional_themes_button = function() {
		self.$additional_themes_button.on('click', function() {
			// If this is the standard inspirations, abort.
			if ('standard' == Inspiration.build_status) {
				return;
			}

			// Get the current theme type ( current / other )
			var current_theme_type_show = self.$additional_themes_button.attr('data-theme-type');

			if ('current' == current_theme_type_show) {
				self.$additional_themes_button.attr('data-theme-type', 'other');

				jQuery('body').addClass('show-filters');

				// Show 'other' and hide current
				jQuery('.themes-current-category').addClass('hidden');
				jQuery('.themes-other-categories').removeClass('hidden');
			} else {
				self.$additional_themes_button.attr('data-theme-type', 'current');

				jQuery('.themes-current-category').removeClass('hidden');
				jQuery('.themes-other-categories').addClass('hidden');

				jQuery('body').removeClass('show-filters');
			}
		});
	}

	/**
	 * Make all of the ajax calls needed for the "Inspired" state.
	 */
	self.load_content = function() {
		// Self Inspiration load.
		jQuery('#button_navigation').addClass('hidden');
		self.$theme_selection_wrapper.removeClass('hidden');

		// Toggle select the 1st tab.
		IMHWPB.Inspiration.instance.boldgrid_toggle_steps(1);

		self.find_install_options();
		self.show_auto_admin_notices();
		self.update_category_name();
		self.load_builds();
		self.bind_theme_install_buttons();
		self.bind_theme_selection_budget_change();
		self.bind_additional_themes_button();
	};

	/**
	 * When a user changes their budget, reload any previews on the page.
	 */
	self.bind_theme_selection_budget_change = function() {
		jQuery('a.coin_budget').on('click', function() {
			// If this budget is already selected, no need to do anything else, abort.
			if (true == jQuery(this).hasClass('current')) {
				return;
			}

			// Unselect the currently selected budget by removing the 'current' class.
			jQuery(this).closest('ul').find('a.coin_budget.current').removeClass('current');

			// Set this budget as the selected budget by adding the 'current' class.
			jQuery(this).addClass('current');

			// Refresh the builds. Determine if we're adding pages or a theme, 
			// and then run applicable calls.
			if ('pages' == self.selected_tab()) {
				self.do_page_preview();
			} else {
				// Remove all of the existing theme previews.
				jQuery('.theme.available_theme').remove();

				// Create new builds / previews.
				self.load_builds();
			}
			
			return false;
		});
	};

	/**
	 * If the settings for the install destionation have come back indication
	 * that the install distination should be staging, set the staging form value to 1.
	 */
	self.set_form_options = function() {
		if ('stage' == Inspiration.mode_data.install_destination
			|| 'stage' == Inspiration.mode_data.inspired_install_destination) {
			self.deploy_inputs.$staging.val(1);
		}
		
		if ('inspired' == Inspiration.build_status) {
			// Set previous install settings.
			self.deploy_inputs.$theme.val(self.install_options.theme_id);
		}
	};

	/**
	 * Display admin notices on each step.
	 */
	self.show_auto_admin_notices = function() {
		/* The user should only see notices if they are using the staging
		 * plugin. If they aren't using the staging plugin, they will only have
		 * 1 location to install their content.
		 */
		if (false == Inspiration.mode_data.staging_active) {
			return;
		}

		// install_type will either be 'Staging' or 'Active'.
		var install_type;
		
		if ('stage' == Inspiration.mode_data.inspired_install_destination) {
			install_type = 'Staging';
		} else if ('active' == Inspiration.mode_data.inspired_install_destination) {
			install_type = 'Active';
		}

		/*
		 * The user has not built a site. Instead of downloading new themes, 
		 * recommend they start with Inspirations.
		 */
		if (false == Inspiration.mode_data.has_built_with_either
				&& (true == Inspiration.mode_data.has_active_site || true == Inspiration.mode_data.has_staging_site)) {
			self.has_not_built_with_either_compiled;

			// Define add_pages_markup.
			var add_pages_markup = self.has_not_built_with_either_compiled(),
				// Define add_theme_markup.
				add_theme_markup = self.has_not_built_with_either_compiled();

			self.$theme_selection_wrapper.find('.admin-notice-container').html(add_theme_markup);
		} else if (install_type && false == self.user_selected_path) {

			var add_theme_markup = self.recognize_template_compiled({
				'install_type' : install_type,
				'content_type' : 'theme'
			});

			self.$theme_selection_wrapper.find('.admin-notice-container').html(add_theme_markup);
		} else if (true == self.user_selected_path) {
			var url = Inspiration.mode_data.url;
			
			if ('themes' == Inspiration.mode_data.page_selection) {
				url += '&boldgrid-tab=themes';
			}

			var add_content_markup = self.selection_template_compiled({
				'install_type' : install_type,
				'url' : url,
			});
			
			self.$theme_selection_wrapper.find('.admin-notice-container').prepend(
					add_content_markup);
		}
	};

	/**
	 * When the user clicks on preview or install we store the settings so that
	 * we know which theme to install.
	 */
	self.bind_theme_install_buttons = function() {

		var theme_select_handler = function() {
			var $this = jQuery(this),
			// Define theme_id.
			theme_id = $this.closest('.available_theme').data('theme-id'),
				// Define pde.
				pde = $this.closest('.theme-actions').data('pde');

			self.deploy_inputs.$deploy_type.val('theme');
			self.deploy_inputs.$theme.val(theme_id);
		};

		/* Bind this on the select and preview button.
		 * The preview button is needed because the user can click on select
		 * from within that menu too.
		 */
		self.$theme_selection.on('click', '.select-button, .preview-button', theme_select_handler);
	};

	/**
	 * Bind the select button in the "Inspired" mode. These select buttons will
	 * display the install modal.
	 */
	self.bind_select_buttons = function(modal_selector, preview_button_selector, $container) {
		// Define theme_install_title.
		var theme_install_title = jQuery('#' + modal_selector).data('title'),
			// Define show_install_modal.
			show_install_modal;
		
		// Update show_install_modal.
		show_install_modal = function() {
			if (self.valid_page_preview || self.$theme_selection.is(':visible')) {
				tb_show(theme_install_title, '#TB_inline?inlineId=' + modal_selector
						+ '&modal=false', true);

				// After showing the modal, remind the user they're installing to active / staged.
				if (true == Inspiration.mode_data.staging_active) {
					// Set the text if we don't have it.
					if (typeof Inspiration.mode_data.inspired_install_destination_text == 'undefined') {
						Inspiration.mode_data.inspired_install_destination_text = ('stage' == Inspiration.mode_data.inspired_install_destination) ? 'Staging'
								: 'Active';
					}

					jQuery('.install-modal-destination').html(
							Inspiration.mode_data.inspired_install_destination_text);
				}
			}
		};

		$container.on('click', '.select-button', function() {
			show_install_modal();
			
			return false;
		});

		jQuery('.previews #' + preview_button_selector).on('click', '#select', function() {
			tb_remove();
			// Wait for the modal to be remove then open a new 1.
			setTimeout(function() {
				show_install_modal();
			}, 500);
			
			return false;
		});

	};

	/**
	 * Update category names within the page's description.
	 * 
	 * Example:
	 * 
	 * Download and Install a new theme from the BoldGrid collection for FITNESS.
	 */
	self.update_category_name = function() {
		var parent_category_name = ('active' == Inspiration.mode_data.inspired_install_destination) ? Inspiration.install_options.active_options.parent_category_name
				: Inspiration.install_options.boldgrid_staging_options.parent_category_name;

		// Default to the active Category if it exists.
		if (!parent_category_name && Inspiration.install_options.active_options) {
			parent_category_name = Inspiration.install_options.active_options.parent_category_name
		}

		// If we still don't know the category name, remove the category options.
		if (!parent_category_name) {
			jQuery('.filter-links').remove();
			$('#sub-heading-default').hide();
			$('#sub-heading-alternate').removeClass('hidden');
		}

		jQuery('.category-name').html(parent_category_name);
	};

	/**
	 * Create all theme builds.
	 * 
	 * This function reaches out to the ASSET server and gets a set of theme ids.
	 * 
	 * Those theme id's are then loaded into: self.all_themes.category_themes
	 * AND self.all_themes.additional_themes.
	 */
	self.load_builds = function() {
		// Define theme_id.
		var theme_id = self.install_options.theme_id,
			// Define page_set_id.
			page_set_id = self.install_options.page_set_id,
			// Define category_id.
			category_id = self.install_options.category_id,
			// Define subcategory_id.
			subcategory_id = self.install_options.subcategory_id,
			// Define data.
			data;

		// Update data.
		data = {
			'cat_id' : subcategory_id,
			'all' : true,
			'inspirations_mode' : 'inspired',
		};

		self.$theme_count_category.attr('data-count', '0');
		self.$theme_count_additional.attr('data-count', '0');

		/* Make some changes for UX. */

		// Show the loading message.
		self.$theme_selection.find('.boldgrid-loading').removeClass('hidden');

		self.$theme_selection_spinner.addClass('is-active');

		var successAction = function(response) {
			try {
				/* Configure args and vars. */

				self.all_themes.category_themes = response.result.data.themes;
				self.all_themes.additional_themes = response.result.data.additional_themes;

				var available_themes = self.all_themes.category_themes
						.concat(self.all_themes.additional_themes);

				var num_themes = available_themes.length;

				self.num_themes_to_install = num_themes;

				/* Make some changes for UX. */

				// Clear the themes we've already shown.
				
				// Define bpl_source.
				var bpl_source = jQuery("#theme-loading-template").html();
				
				// Define bpl_template.
				var bpl_template = Handlebars.compile(bpl_source);

				/* Loop through each theme_id and send it to self.build_theme. */
				jQuery.each(available_themes, function(key, theme_id) {
					// For each theme, call to build site.
					self.build_theme(theme_id, page_set_id, self.install_options.subcategory_id);
				});
			} catch (err) {
				self.$boldgrid_error_message.removeClass('hidden');
				jQuery('#step-1').hide();
				console.log(err);
			}
		};

		IMHWPB.Inspiration.instance.ajax.ajaxCall(data, 'get_theme_ids', successAction);
	};

	/**
	 * Compile all templates needed on this page load.
	 */
	self.compile_templates = function() {
		var source = jQuery("#inspiration-recognize-template").html();
		self.recognize_template_compiled = Handlebars.compile(source);

		var source = jQuery("#inspiration-has-not-built-with-either-template").html();
		self.has_not_built_with_either_compiled = Handlebars.compile(source);

		var source = jQuery("#inspiration-content-selection-template").html();
		self.selection_template_compiled = Handlebars.compile(source);
	};

	/**
	 * Build 1 Theme for the theme selection process, this is called for each available theme.
	 * 
	 * Function load_builds() loops through a collection of themes. For each of
	 * those themes, it calls this function.
	 */
	self.build_theme = function(theme_id, page_set_id, sub_cat_id) {
		// Define vars.
		var data, check_remaining_builds, success_action, failure_action;
		
		// Update vars.
		data = {
			'build_any' : Inspiration.mode_data.build_any,
			'theme_id' : theme_id,
			'sub_cat_id' : sub_cat_id,
			'default_page_set_id' : page_set_id,
			'pde' : jQuery('#boldgrid_pde').val(),
			'wp_language' : jQuery('#wp_language').val(),
			'site_hash' : IMHWPB.configs.site_hash
		};

		check_remaining_builds = function() {
			self.num_themes_to_install = self.num_themes_to_install - 1;
			
			if (self.num_themes_to_install == 0) {
				// Hide the loading message.
				self.$theme_selection.find('.boldgrid-loading').addClass('hidden');

				self.$theme_selection_spinner.removeClass('is-active');

				if (self.$theme_count_category.attr('data-count') == 0) {
					self.$theme_count_category.html('0');
					jQuery('#boldgrid-error-message').hide();

					self.$theme_selection.siblings('.loading-wrapper').slideUp('slow', function() {
						self.$error_message.removeClass('hidden');
					});
				}
			}
		};

		success_action = function(msg) {
			// Define source.
			var source = self.$build_profile_template.html();
			// Define template.
			var template = Handlebars.compile(source),
				// Define category_theme.
				category_theme = true,
				// Define result.
				result = msg.result.data;
			
			result.theme_id = theme_id;
			
			var thumbnail_source = template(result);
			
			// Swap logo.
			self.$step_2_loading.slideUp('slow', function() {
				jQuery('.theme-browser.themes-current-category').slideDown('slow');
			});

			if (!result.theme) {
				check_remaining_builds();
				
				return;
			}

			if (result.theme.title == self.install_options.theme_name) {
				// Active Theme.
				var $new_element = jQuery(thumbnail_source);
				
				$new_element.addClass('active');
				$new_element.find('.theme-name').prepend('<span>Active: </span>');
				$new_element.find('.theme-actions').remove();
				$new_element.find('.more-details').remove();
				self.$available_themes.prepend($new_element);
			} else if (self.all_themes.category_themes.indexOf(theme_id) != '-1') {
				// These is a category Theme.
				self.$available_themes.append(thumbnail_source);
			} else {
				// This is an additional Theme.
				self.$additional_themes.append(thumbnail_source);
				category_theme = false;
			}

			// Update the counts accordingly/
			if (category_theme) {
				self.increment_theme_count(self.$theme_count_category);
			} else {
				self.increment_theme_count(self.$theme_count_additional);
			}

			check_remaining_builds();
			
			// Hide the coin range; there is currently no cost for themes or homepages.
			jQuery('.step-2-theme-coins').hide();
		};

		failure_action = function(err) {
			check_remaining_builds();
			console.log('Error: ', err);
		};

		IMHWPB.Inspiration.instance.ajax.ajaxCall(data, 'get_layouts', success_action,
				failure_action);
	};

	/**
	 * Increment the count of themes.
	 */
	self.increment_theme_count = function($display_counter) {
		var $count = $display_counter.attr('data-count');
		var new_count = parseInt($count) + 1;

		$display_counter.attr('data-count', new_count);
		$display_counter.html(new_count);
	};

	/**
	 * Toggle the display of the "navbar" and the "Additional themes" button.
	 */
	self.toggle_step_2_nav_bar = function(read_url_boldgrid_tab) {
			// Move the navbar to the themes navbar wrapper.
			self.$step_2_nav_bar.appendTo(self.$themes_step_2_nav_bar_wrapper);

			// Show the "Additional themes" button.
			self.$additional_themes_button.removeClass('hidden');
	}

	return self;

})(jQuery, window.IMHWPB || {}).init();
