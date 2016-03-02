(function($, IMHWPB) {
	// General Variables
	IMHWPB.AddBoldgridPage = {};
	var self = IMHWPB.AddBoldgridPage;

	/** Add to Pages * */
	self.$preview_button, self.$add_to_active_button, self.$add_to_staging_button;

	self.$boldgrid_table_wrap = [];

	// Compiled Add pages Template
	self.add_page_template_compiled;
	self.psps_template_compiled;
	self.recognize_template_compiled;
	self.selection_template_compiled;
	self.has_not_built_with_either_compiled;

	// State Collections
	self.num_themes_to_install;
	self.current_selected_sub_cat = {};
	self.selected_pages_to_add = [];
	self.all_themes = {};
	self.install_options = {};

	// State Booleans
	self.valid_page_preview = false;
	self.preview_in_progress = false;
	self.user_selected_path = false;

	// Include BaseAdmin
	self.baseAdmin = new IMHWPB.BaseAdmin();

	/**
	 * The initialize process
	 */
	self.init = function() {
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
		self.$boldgrid_add_pages = jQuery('#boldgrid-add-pages');
		self.$add_pages_container = jQuery('#add-pages-container');
		self.select_all_checkboxes = jQuery('.accordion-section-content .select-all');
		self.$page_set_preview = jQuery('#page_set_preview');
		self.$theme_selection = jQuery('#boldgrid-theme-selection');
		self.$page_selection = jQuery('#add-existing-pages');
		self.$select_content_install_type = jQuery('#select-content-install-type');
		self.$page_selection_branding = self.$page_selection.find('.branding-wrapper');
		self.$deploy_script = jQuery('#post_deploy');
		self.$theme_selection_wrapper = $('#theme-selection-wrapper');
		self.$theme_selection_spinner = self.$theme_selection_wrapper.find('.spinner');

		// The following 3 vars help with the display of the navbar. For an
		// explanation of how it works, please read note 201507302221.
		self.$step_2_nav_bar = jQuery('.wrap.step-2-nav-bar');
		self.$themes_step_2_nav_bar_wrapper = self.$theme_selection_wrapper
				.find('.step-2-nav-bar-wrapper');
		self.$pages_step_2_nav_bar_wrapper = self.$page_selection.find('.step-2-nav-bar-wrapper');

		// Theme type 1/2: Themes belonging to the current category
		self.$themes_current_category = self.$theme_selection_wrapper
				.find('.themes-current-category');
		// Theme type 2/2: "Additional Themes", themes not belonging to the
		// current category.
		self.$themes_other_categories = self.$theme_selection_wrapper
				.find('.themes-other-categories');

		self.$additional_themes_button = jQuery('a.additional_themes');
		self.toggle_step_2_nav_bar(false);

		// Inputs from the deployment form
		self.deploy_inputs = {
			$deploy_type : self.$deploy_script.find('input[name="deploy-type"]'),
			$theme : self.$deploy_script.find('input[name="boldgrid_theme_id"]'),
			$coin_budget : self.$deploy_script.find('input[name="coin_budget"]'),
			$cat_id : self.$deploy_script.find('input[name="boldgrid_cat_id"]'),
			$sub_cat_id : self.$deploy_script.find('input[name="boldgrid_sub_cat_id"]'),
			$pde : self.$deploy_script.find('#boldgrid_pde'),
			$page_set_id : self.$deploy_script.find('[name="boldgrid_page_set_id"]'),
			$staging : self.$deploy_script.find('[name="staging"]'),
		};

		// Self Inspiration Load
		if (Inspiration.build_status == 'inspired') {
			self.compile_templates();
			self.bind_select_buttons('install-theme-modal', 'preview_theme_button_set',
					self.$theme_selection);
			self.bind_select_buttons('install-page-modal', 'preview_page_set_button_set',
					self.$page_selection);
			self.load_inspiration_path();
		}

		self.set_form_options();

		self.toggle_step_2_nav_bar(true);
	};

	/**
	 * Display the options so that the user can choose staging or active as the
	 * install destination
	 */
	self.load_inspiration_path = function() {
		// PROMPT USER FOR INSTALL TYPE
		// [Y] [ ] Staging plugin is active
		// [Y] [ ] Already has an active site
		// [Y] [ ] Already has a staging site
		var choice_mode = false;
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

		var open_section = Inspiration.mode_data['open-section'];
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
		var destination = Inspiration.mode_data.inspired_install_destination;
		var install_key = 'active_options';
		if (destination == 'stage') {
			install_key = 'boldgrid_staging_options';
		}

		self.install_options = Inspiration.install_options[install_key];
	};

	/**
	 * Binds install location selection buttons This allows the user choose if
	 * they would like to install into staging or active only available under
	 * certain conditions
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
		});
	};

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
	 * Make all of the ajax calls needed for the "Inspired" state
	 */
	self.load_content = function() {
		// Self Inspiration Load
		jQuery('#button_navigation').removeClass('hidden');
		self.$theme_selection_wrapper.removeClass('hidden');
		self.$page_selection.removeClass('hidden');
		self.select_correct_tab();
		self.find_install_options();
		self.show_auto_admin_notices();
		self.update_category_name();
		self.load_builds();
		self.bind_add_pages_inspired_handlers();
		self.load_page_tab();
		self.bind_theme_install_buttons();
		self.bind_page_install_buttons();
		self.bind_checkbox_actions();
		self.bind_theme_selection_budget_change();
		self.bind_nav_steps();
		self.bind_additional_themes_button();
	};

	/**
	 * If only option is to add a theme, select theme
	 */
	self.select_correct_tab = function() {
		// If this user is inspired and there is only 1 option, then preselect
		// theme
		if (Inspiration.mode_data.menu.length === 1) {
			IMHWPB.Inspiration.instance.boldgrid_toggle_steps(2);
		}
	};

	/**
	 * When a user changes their budget, reload any previews on the page.
	 */
	self.bind_theme_selection_budget_change = function() {
		jQuery('a.coin_budget').on('click', function() {
			// If this budget is already selected, no need to do
			// anything else, abort.
			if (true == jQuery(this).hasClass('current')) {
				return;
			}

			// Unselect the currently selected budget by
			// removing the 'current' class.
			jQuery(this).closest('ul').find('a.coin_budget.current').removeClass('current');

			// Set this budget as the selected budget by adding
			// the 'current' class.
			jQuery(this).addClass('current');

			// Refresh the builds. Determine if we're adding
			// pages or a theme, and then run applicable calls.
			if ('pages' == self.selected_tab()) {
				self.do_page_preview();
			} else {
				// Remove all of the existing theme previews.
				jQuery('.theme.available_theme').remove();

				// Create new builds / previews.
				self.load_builds();
			}
		});
	};

	/**
	 * Load the events for the page tab
	 */
	self.load_page_tab = function() {
		// This user should not see the pages tab
		if (false == Inspiration.mode_data.build_any) {
			self.add_pages_load();

			setTimeout(function() {
				self.add_pages_category_update();
			}, 350);

		} else {
			// Remove the page tab for users that have not built with boldgrid
			var step_name = self.$page_selection.closest('.imhwpb-step').attr('name');
			$('#button_navigation').find('[name="nav-' + step_name + '"]').remove();
		}
	};

	/**
	 * If the settings for the install destionation have come back indication
	 * that the install distination should be staging, set the staging form
	 * value to 1
	 */
	self.set_form_options = function() {
		if (Inspiration.mode_data.install_destination == 'stage') {
			self.deploy_inputs.$staging.val(1);
		}
		if (Inspiration.mode_data.inspired_install_destination == 'stage') {
			self.deploy_inputs.$staging.val(1);
		}

		if (Inspiration.build_status == 'inspired') {
			// Set Previous Install Settings
			self.deploy_inputs.$cat_id.val(self.install_options.category_id);
			self.deploy_inputs.$theme.val(self.install_options.theme_id);
			self.deploy_inputs.$sub_cat_id.val(self.install_options.subcategory_id);
			self.deploy_inputs.$page_set_id.val(self.install_options.page_set_id);
			self.deploy_inputs.$pde.val(JSON.stringify(self.install_options.pde));
		}
	};

	/**
	 * Display admin notices on each step
	 */
	self.show_auto_admin_notices = function() {
		// The user should only see notices if they are using the staging
		// plugin. If they aren't using the staging plugin, they will only have
		// 1 location to install their content.
		if (false == Inspiration.mode_data.staging_active) {
			return;
		}

		// install_type will either be 'Staging' or 'Active'.
		var install_type;
		if (Inspiration.mode_data.inspired_install_destination == 'stage') {
			install_type = "Staging";
		} else if (Inspiration.mode_data.inspired_install_destination == 'active') {
			install_type = "Active";
		}

		/**
		 * The user has not built a site. Instead of downloading new themes or
		 * pages, recommend they start with Inspirations.
		 */
		if (false == Inspiration.mode_data.has_built_with_either
				&& (true == Inspiration.mode_data.has_active_site || true == Inspiration.mode_data.has_staging_site)) {
			self.has_not_built_with_either_compiled

			var add_pages_markup = self.has_not_built_with_either_compiled();

			var add_theme_markup = self.has_not_built_with_either_compiled();

			self.$page_selection.prepend(add_pages_markup);
			self.$theme_selection_wrapper.find('.admin-notice-container').html(add_theme_markup);
		} else if (install_type && false == self.user_selected_path) {

			var add_pages_markup = self.recognize_template_compiled({
				'install_type' : install_type,
				'content_type' : 'pages'
			});

			var add_theme_markup = self.recognize_template_compiled({
				'install_type' : install_type,
				'content_type' : 'theme'
			});

			self.$page_selection.prepend(add_pages_markup);
			self.$theme_selection_wrapper.find('.admin-notice-container').html(add_theme_markup);
		} else if (true == self.user_selected_path) {
			var url = Inspiration.mode_data.url;
			if (Inspiration.mode_data.page_selection == 'themes') {
				url += '&boldgrid-tab=themes';
			}

			var add_content_markup = self.selection_template_compiled({
				'install_type' : install_type,
				'url' : url,
			});
			self.$page_selection.prepend(add_content_markup);
			self.$theme_selection_wrapper.find('.admin-notice-container').prepend(
					add_content_markup);
		}
	};

	/**
	 * Handles the user clicking on checkboxes and the input buttons being
	 * enabled or disabled
	 */
	self.bind_checkbox_actions = function() {
		var $selection_warning = self.$boldgrid_add_pages.find('#selection-warning');
		self.$boldgrid_add_pages.on('click', 'input.menu-item-checkbox', function() {
			self.valid_page_preview = self.get_selected_pages().length > 0;

			if (self.valid_page_preview) {
				if (self.preview_in_progress == false) {
					self.$boldgrid_add_pages.find('.button-controls #accordion-preview-button')
							.removeAttr('disabled');
				}

				$selection_warning.addClass('hidden');
				self.$boldgrid_add_pages.find('.button-controls .select-button').removeAttr(
						'disabled');
				self.$page_set_preview.find('.select-button').removeAttr('disabled');
			} else {
				self.$boldgrid_add_pages.find('.button-controls input')
						.attr('disabled', 'disabled');

				$selection_warning.removeClass('hidden');
				self.$page_set_preview.find('.select-button').attr('disabled', 'disabled');
			}
		});
	};

	/**
	 * Find the pages that the user has checked for installation.
	 */
	self.get_selected_pages = function() {
		return self.$boldgrid_add_pages.find('.menu-item-checkbox:checked:not([disabled])');
	};

	/**
	 * When the user clicks on preview or install we store the settings so that
	 * we know Which pages to install
	 */
	self.bind_page_install_buttons = function() {
		var $pages_input = self.$deploy_script.find('input[name="pages"]');

		self.$page_selection.on('click', '.select-button, .preview-button', function() {
			var $this = jQuery(this);
			self.deploy_inputs.$deploy_type.val('pages');
			self.deploy_inputs.$coin_budget.val(self.get_selected_coin_budget());

			var selected_pages_array = [];
			self.get_selected_pages().each(function() {
				selected_pages_array.push(jQuery(this).val());
			});

			// Setting pages array
			$pages_input.val(JSON.stringify(selected_pages_array));
		});
	};

	/**
	 * When the user clicks on preview or install we store the settings so that
	 * we know which theme to install
	 */
	self.bind_theme_install_buttons = function() {

		var theme_select_handler = function() {
			var $this = jQuery(this);
			var theme_id = $this.closest('.available_theme').data('theme-id');
			var pde = $this.closest('.theme-actions').data('pde');

			self.deploy_inputs.$deploy_type.val('theme');
			self.deploy_inputs.$theme.val(theme_id);
			self.deploy_inputs.$pde.val(JSON.stringify(pde));
			self.deploy_inputs.$coin_budget.val(self.get_selected_coin_budget());
		};

		// Bind this on the select and preview button.
		// The preview button is needed because the user can click on select
		// from within that menu too
		self.$theme_selection.on('click', '.select-button, .preview-button', theme_select_handler);
	};

	/**
	 * Bind the select button in the "Inspired" mode. These select buttons will
	 * display the install modal
	 */
	self.bind_select_buttons = function(modal_selector, preview_button_selector, $container) {
		var theme_install_title = jQuery('#' + modal_selector).data('title');

		var show_install_modal = function() {
			if (self.valid_page_preview || self.$theme_selection.is(':visible')) {
				tb_show(theme_install_title, '#TB_inline?inlineId=' + modal_selector
						+ '&modal=false', true);

				// After showing the modal, remind the user they're installing
				// to active / staged.
				if (true == Inspiration.mode_data.staging_active) {
					// Set the text if we don't have it
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
		});

		jQuery('.previews #' + preview_button_selector).on('click', '#select', function() {
			tb_remove();
			// Wait for the modal to be remove then open a new 1
			setTimeout(function() {
				show_install_modal();
			}, 500);
		});

	};

	/**
	 * Take action when a nav tab is clicked.
	 */
	self.bind_nav_steps = function() {
		/*
		 * In the wp-filter nav bar, there is an "Additional Themes" button.
		 * Toggle this button based on the tab the user is on (should only be
		 * shown on the 'themes' tab).
		 */
		jQuery('#button_navigation').on('click', function() {
			self.toggle_step_2_nav_bar(false);
		})
	}

	/**
	 * Update category names within the page's description.
	 * 
	 * Example:
	 * 
	 * Download and Install a new theme from the BoldGrid collection for
	 * FITNESS.
	 */
	self.update_category_name = function() {
		var parent_category_name = ('active' == Inspiration.mode_data.inspired_install_destination) ? Inspiration.install_options.active_options.parent_category_name
				: Inspiration.install_options.boldgrid_staging_options.parent_category_name;

		// Default to the active Category if it exists
		if (!parent_category_name && Inspiration.install_options.active_options) {
			parent_category_name = Inspiration.install_options.active_options.parent_category_name
		}

		// If we still don't know the category name, remove the category options
		if (!parent_category_name) {
			jQuery('.filter-links').remove();
			$('#sub-heading-default').hide();
			$('#sub-heading-alternate').removeClass('hidden');
		}

		jQuery('span.category_name').html(parent_category_name);
	};

	/**
	 * Create all theme builds.
	 * 
	 * This function reaches out to the ASSET server and gets a set of theme
	 * ids.
	 * 
	 * Those theme id's are then loaded into: self.all_themes.category_themes
	 * AND self.all_themes.additional_themes
	 */
	self.load_builds = function() {
		/**
		 * ********************************************************************
		 * Configure args and vars.
		 * ********************************************************************
		 */

		var theme_id = self.install_options.theme_id;
		var page_set_id = self.install_options.page_set_id;
		var category_id = self.install_options.category_id;
		var subcategory_id = self.install_options.subcategory_id;

		var data = {
			'cat_id' : subcategory_id,
			'all' : true,
			'inspirations_mode' : 'inspired',
		};

		self.$theme_count_category.attr('data-count', '0');
		self.$theme_count_additional.attr('data-count', '0');

		/**
		 * ********************************************************************
		 * Make some changes for UX.
		 * ********************************************************************
		 */

		// Show the loading message.
		self.$theme_selection.find('.boldgrid-loading').removeClass('hidden');

		self.disable_select_coin_budget();

		self.$theme_selection_spinner.addClass('is-active');

		var successAction = function(response) {
			try {
				/**
				 * ************************************************************
				 * Configure args and vars.
				 * ************************************************************
				 */

				self.all_themes.category_themes = response.result.data.themes;
				self.all_themes.additional_themes = response.result.data.additional_themes;

				var available_themes = self.all_themes.category_themes
						.concat(self.all_themes.additional_themes);

				var num_themes = available_themes.length;

				self.num_themes_to_install = num_themes;

				/**
				 * ************************************************************
				 * Make some changes for UX.
				 * ************************************************************
				 */

				// clear the themes we've already shown
				// self.$available_themes.empty();
				// self.$additional_themes.empty();
				var bpl_source = jQuery("#theme-loading-template").html();
				var bpl_template = Handlebars.compile(bpl_source);

				/**
				 * Loop through each theme_id and send it to self.build_theme.
				 */
				jQuery.each(available_themes, function(key, theme_id) {
					// For each theme, call to build site
					self.build_theme(theme_id, page_set_id, self.install_options.subcategory_id);
				});
			} catch (err) {
				self.$boldgrid_error_message.removeClass("hidden");
				jQuery('#step-1').hide();
				console.log(err);
			}
		};

		IMHWPB.Inspiration.instance.ajax.ajaxCall(data, 'get_theme_ids', successAction);
	};

	/**
	 * Compile all templates needed on this page load
	 */
	self.compile_templates = function() {
		var add_pages_template = jQuery("#add-boldgrid-page-template").html();
		self.add_page_template_compiled = Handlebars.compile(add_pages_template);

		var source = jQuery("#page-set-preview-select-template").html();
		self.psps_template_compiled = Handlebars.compile(source);

		var source = jQuery("#inspiration-recognize-template").html();
		self.recognize_template_compiled = Handlebars.compile(source);

		var source = jQuery("#inspiration-has-not-built-with-either-template").html();
		self.has_not_built_with_either_compiled = Handlebars.compile(source);

		var source = jQuery("#inspiration-content-selection-template").html();
		self.selection_template_compiled = Handlebars.compile(source);
	};

	/**
	 * Bind all handlers needed for the add pages step
	 */
	self.bind_add_pages_inspired_handlers = function() {
		self.$preview_button = jQuery('#accordion-preview-button');
		self.$add_to_active_button = jQuery('#accordion-active-button');
		self.$add_to_active_button = jQuery('#accordion-staging-button');

		self.bind_select_all_checkboxes();
		self.prevent_accordion_collapse();
		self.bind_preview_pages_button();
		self.bind_accordion_tabs();
		self.preselect_category(self.install_options.subcategory_id);
		self.bind_category_change();
	};

	/**
	 * This is the action that occurs when the user changes the category from
	 * the pages accordion
	 */
	self.bind_category_change = function() {
		self.$boldgrid_add_pages.find('#change-category-container li').on('click', function() {
			var $this = jQuery(this);
			if (false == $this.hasClass('.category-selected')) {
				var sub_cat_id = $this.data('sub-cat-id');
				self.preselect_category(sub_cat_id);
				self.add_pages_category_update();

				// Change Tab
				setTimeout(function() {
					self.activate_add_pages_tab('pages');
				}, 500);
			}
		});
	};

	/**
	 * Activate a tab in the accordion
	 */
	self.activate_add_pages_tab = function(type) {
		var $this = self.$boldgrid_add_pages.find('#posttype-page-tabs li[data-body="' + type
				+ '"]');
		var body_data = $this.data('body');
		self.$boldgrid_add_pages.find('.categorychecklist').hide();
		self.$boldgrid_add_pages.find('.categorychecklist[data-body="' + body_data + '"]').show();
		self.$boldgrid_add_pages.find('#posttype-page-tabs li').removeClass('tabs');
		$this.addClass('tabs');
	};

	/**
	 * Select a page Category
	 */
	self.preselect_category = function(sub_cat_id) {
		self.$boldgrid_add_pages.find('#change-category-container li').removeClass(
				'category-selected');
		var cat_name = self.$boldgrid_add_pages.find(
				'.categorychecklist li[data-sub-cat-id="' + sub_cat_id + '"]').addClass(
				'category-selected').text();

		self.current_selected_sub_cat.id = sub_cat_id;
		self.current_selected_sub_cat.name = cat_name;
	};

	/**
	 * Event for the accordion
	 */
	self.bind_accordion_tabs = function() {
		self.$boldgrid_add_pages.find('#posttype-page-tabs li').on('click', function() {
			self.activate_add_pages_tab(jQuery(this).data('body'));
		});
	};

	/**
	 * Get required arguments for building a custom pageset, then call
	 * build_custom_pageset with those arguments.
	 */
	self.do_page_preview = function() {
		if (false == self.valid_page_preview) {
			return;
		}

		var pages = [];
		jQuery('.menu-item-checkbox:checked').each(function() {
			pages.push(jQuery(this).val());
		});

		self.build_custom_pageset(pages);
	}

	/**
	 * Event to Preview Pages
	 */
	self.bind_preview_pages_button = function() {
		// When the preview button is clicked:
		self.$preview_button.on('click', function() {
			self.do_page_preview();
		});

		// When the coin budget is clicked:
		/*
		 * jQuery('#budget_container input[name="pages_coin_budget"]').on(
		 * 'change', function() { if (self.$page_set_preview.is(':visible')) {
		 * self.do_page_preview(); } });
		 */
	};

	/**
	 * Since the add pages accordion is only 1 frame, prevent collapse
	 */
	self.prevent_accordion_collapse = function() {
		jQuery('.accordion-section-title').on('click', function() {
			return false;
		});
	};

	/**
	 * Allow the user to click the select all button on the add pages accordion.
	 */
	self.bind_select_all_checkboxes = function() {
		self.select_all_checkboxes.on('click', function() {
			var select_all = self.select_all_checkboxes.attr('data-select-all');
			var $checkbox = jQuery('.menu-item-checkbox:not([disabled])');
			if (select_all == "true") {
				$checkbox.attr('checked', 'checked').prop('checked', true);

				self.select_all_checkboxes.attr('data-select-all', 'false');
				self.select_all_checkboxes.html('Deselect All');
			} else {
				$checkbox.removeAttr('checked').prop('checked', false);

				self.select_all_checkboxes.attr('data-select-all', 'true');
				self.select_all_checkboxes.html('Select All');
			}
		});
	};

	/**
	 * Remove all boxes that are unchecked and update the array
	 */
	self.remove_unchecked = function() {
		// Remove all boxes that are unchecked, and save all boxes that have
		// been checked
		self.$add_pages_container.find('li').each(function() {
			var $this = jQuery(this);
			if (false == $this.find('input').prop("checked")) {
				var index = self.selected_pages_to_add.indexOf($this.find('input').val());
				if (index > -1) {
					self.selected_pages_to_add.splice(index);
				}

				$this.remove();
			} else {
				self.selected_pages_to_add.push($this.find('input').val());
			}
		});
	};

	/**
	 * Remove all sections that do not have pages
	 */
	self.remove_empty_sections = function() {
		self.$add_pages_container.find('.page_cat_divider').each(
				function() {
					var $this = jQuery(this);
					var $wrapper = self.$add_pages_container
							.find('.page-cat-wrap[data-sub-cat-id="' + $this.data('sub-cat-id')
									+ '"]');
					var checked_pages = $wrapper.find('li').length > 0;
					if (false == checked_pages) {
						$wrapper.remove();
						$this.next('hr').remove();
						$this.remove();
					}
				});
	};

	/**
	 * Load all of the pages that the users has installed on their WP, sorted by
	 * category
	 */
	self.add_pages_load = function() {
		var data = {
			'page_ids' : self.install_options.installed_pages,
			'sub_category_id' : self.install_options.subcategory_id
		};

		var category_pages_callback = function(name) {

			var $existing_wrap = self.$add_pages_container.find('.page-cat-wrap[data-sub-cat-id="'
					+ this.id + '"]');

			var template_data = {
				'pages' : this.pages,
				'sub_cat_id' : this.id,
				'sub_cat_name' : name,
				'section_doesnt_exist' : false == $existing_wrap.length
			};

			// Create the new markup
			var checkboxes = self.add_page_template_compiled(template_data);

			if ($existing_wrap.length) {
				$existing_wrap.append(checkboxes);
			} else {
				self.$add_pages_container.append(checkboxes);
			}
		};

		var successAction = function(response) {
			$.each(response.result.data.primary_category, category_pages_callback);
			$.each(response.result.data.additional_categories, category_pages_callback);

			self.display_branding();

			// Make sure that the pages which the user already has are checked.
			self.check_all_existing_pages();

			/*
			 * In the nav bar, it displays "Category: X". Replace "X" with the
			 * actual category name.
			 */
			var sub_category_name = jQuery('#add-pages-container h4').html();
			jQuery('.category-name').html(sub_category_name);
		}

		IMHWPB.Inspiration.instance.ajax.ajaxCall(data, 'get_page_set_custom_details',
				successAction);
	};

	/**
	 * Make an ajax call to get all of the pages for the category
	 */
	self.add_pages_category_update = function() {
		var theme_id = self.install_options.theme_id;
		var page_set_id = self.install_options.page_set_id;
		var subcategory_id = self.current_selected_sub_cat.id;

		var data = {
			'theme_id' : theme_id,
			'subcategory_id' : subcategory_id,
			'page_set_id' : page_set_id,
			'force_kitchen_sink' : true
		};

		var success_action = function(response) {

			try {
				// Update the array of selected
				self.remove_unchecked();

				// Remove all empty
				self.remove_empty_sections();

				// Remove all entries which the user has already selected to add
				var pages = [];
				jQuery.each(response.result.data.pages, function(key, value) {
					if ('-1' == self.selected_pages_to_add.indexOf(value.id)) {
						pages.push(value);
					}
				});

				// Remove post pages:
				jQuery.each(response.result.data.pages, function(key, value) {
					if ('post' == value.post_type) {
						var index = pages.indexOf(value);
						if (-1 != index) {
							pages.splice(index, 1);
						}
					}
				});

				// Check if we have already added a conatiner to place the pages
				var $existing_wrap = self.$add_pages_container
						.find('.page-cat-wrap[data-sub-cat-id="' + subcategory_id + '"]');

				var template_data = {
					'pages' : pages,
					'sub_cat_id' : subcategory_id,
					'sub_cat_name' : self.current_selected_sub_cat.name,
					'section_doesnt_exist' : false == $existing_wrap.length
				};

				// Create the new markup
				var checkboxes = self.add_page_template_compiled(template_data);

				if ($existing_wrap.length) {
					$existing_wrap.append(checkboxes);
				} else {
					self.$add_pages_container.append(checkboxes);
				}

				self.display_branding();

				// Make sure that the pages which the user already has are
				// checked.
				self.check_all_existing_pages();

			} catch (err) {
				self.$boldgrid_error_message.removeClass("hidden");
				jQuery('#step-1').hide();
				console.log(err);
			}
		};

		IMHWPB.Inspiration.instance.ajax.ajaxCall(data, 'get_page_set', success_action);
	};

	/**
	 * Check if we should display branding if so display it
	 */
	self.display_branding = function() {
		if (false == self.$page_set_preview.is(':visible') && false == self.preview_in_progress) {
			self.$page_selection_branding.removeClass('hidden');
			self.$step_1_loading.addClass('hidden');
		}
	};

	/**
	 * Mark the checkboxes of existing pages
	 */
	self.check_all_existing_pages = function() {
		jQuery.each(self.install_options.installed_pages, function(key, value) {
			self.selected_pages_to_add.push(value);
			self.$add_pages_container.find('input[value=' + value + ']').attr('disabled',
					'disabled').attr('checked', 'checked');
		});
	};

	/**
	 * Build Custom Pageset
	 * 
	 */
	self.build_custom_pageset = function(pages) {
		var data = {
			'theme_id' : self.install_options.theme_id,
			// Optionally overrides theme_id, in case the user has switched
			// their theme
			'theme_stylesheet' : self.install_options.theme_stylesheet,
			'cat_id' : self.install_options.category_id,
			'sub_cat_id' : self.install_options.subcategory_id,
			'default_page_set_id' : self.install_options.page_set_id,
			'pages' : pages,
			'pde' : jQuery('#boldgrid_pde').val(),
			'wp_language' : jQuery('#wp_language').val(),
			'coin_budget' : self.get_selected_coin_budget(),
			'site_hash' : IMHWPB.configs.site_hash
		};

		self.preview_in_progress = true;

		// Show Progress Spinner
		self.$preview_button.attr('disabled', 'disabled').closest('.row').find('.spinner')
				.addClass('is-active');

		self.disable_select_coin_budget();

		var done_action = function() {
			self.preview_in_progress = false;
			// Reenable button
			if (self.valid_page_preview) {
				self.$preview_button.removeAttr('disabled');
			} else {
				// If the user cannot build yet, disabled the select button
				self.$page_set_preview.find('.select-button').attr('disabled', 'disabled');
			}

			// Hide spinner
			self.$preview_button.closest('.row').find('.spinner').removeClass('is-active');

			self.enable_select_coin_budget();
		};

		var failure_action = function() {
			self.$boldgrid_error_message.removeClass("hidden");
			self.$page_selection.hide();
			done_action();
		};

		var success_action = function(result) {
			done_action();
			try {
				var response = result.result.data.theme;

				var page_set_thumbnail_url = IMHWPB.Inspiration.instance
						.create_page_set_thumbnail_url(result.result.data.assetId);

				var handlebars_data = {
					'page_set_thumbnail_url' : page_set_thumbnail_url,
					'preview_url' : response.previewUrl,
					'theme_title' : response.title,
					'coins' : response.coins
				};

				// Reach over to the Inspiration object and set the
				// "latest coin value"
				IMHWPB.Inspiration.instance.latest_single_build_coin_value = response.coins;

				self.$page_set_preview.html(self.psps_template_compiled(handlebars_data));
				self.$page_set_preview.removeClass('hidden');
				self.$page_selection.find('.boldgrid-loading').addClass('hidden');
			} catch (err) {
				console.log(err);
				failure_action();
			}
		};

		jQuery('#page_set_preview').addClass('hidden');
		self.$page_selection_branding.addClass('hidden');
		self.$page_selection.find('.boldgrid-loading').removeClass('hidden');
		IMHWPB.Inspiration.instance.ajax.ajaxCall(data, 'get_layouts', success_action,
				failure_action);
	};

	/**
	 * Build 1 Theme for the theme selection process, this is called for each
	 * available theme.
	 * 
	 * Function load_builds() loops through a collection of themes. For each of
	 * those themes, it calls this function.
	 */
	self.build_theme = function(theme_id, page_set_id, sub_cat_id) {
		var data = {
			'build_any' : Inspiration.mode_data.build_any,
			'theme_id' : theme_id,
			'sub_cat_id' : sub_cat_id,
			'default_page_set_id' : page_set_id,
			'pde' : jQuery('#boldgrid_pde').val(),
			'wp_language' : jQuery('#wp_language').val(),
			'coin_budget' : self.get_selected_coin_budget(),
			'site_hash' : IMHWPB.configs.site_hash
		};

		var check_remaining_builds = function() {
			self.num_themes_to_install = self.num_themes_to_install - 1;
			if (self.num_themes_to_install == 0) {
				// Hide the loading message.
				self.$theme_selection.find('.boldgrid-loading').addClass('hidden');

				self.$theme_selection_spinner.removeClass('is-active');

				self.enable_select_coin_budget();

				if (self.$theme_count_category.attr('data-count') == 0) {
					self.$theme_count_category.html("0");
					jQuery('#boldgrid-error-message').hide();

					self.$theme_selection.siblings('.loading-wrapper').slideUp('slow', function() {
						self.$error_message.removeClass('hidden');
					});
				}
			}
		};

		var success_action = function(msg) {
			var source = self.$build_profile_template.html();
			var template = Handlebars.compile(source);
			var result = msg.result.data;
			result.theme_id = theme_id;
			var thumbnail_source = template(result);
			// Swap logo
			self.$step_2_loading.slideUp('slow', function() {
				jQuery('.theme-browser.themes-current-category').slideDown('slow');
			});

			if (!result.theme) {
				check_remaining_builds();
				return;
			}

			var category_theme = true;
			if (result.theme.title == self.install_options.theme_name) {
				// Active Theme
				var $new_element = jQuery(thumbnail_source);
				$new_element.addClass('active');
				$new_element.find('.theme-name').prepend('<span>Active: </span>');
				$new_element.find('.theme-actions').remove();
				$new_element.find('.more-details').remove();
				self.$available_themes.prepend($new_element);
			} else if (self.all_themes.category_themes.indexOf(theme_id) != "-1") {
				// These is a category Theme
				self.$available_themes.append(thumbnail_source);
			} else {
				// This is an additional Theme
				self.$additional_themes.append(thumbnail_source);
				category_theme = false;
			}

			// Update the counts accordingly
			if (category_theme) {
				self.increment_theme_count(self.$theme_count_category);
			} else {
				self.increment_theme_count(self.$theme_count_additional);
			}

			check_remaining_builds();
		};

		var failure_action = function(err) {
			check_remaining_builds();
			console.log("Error: ", err);
		};

		IMHWPB.Inspiration.instance.ajax.ajaxCall(data, 'get_layouts', success_action,
				failure_action);
	};

	/**
	 * Increment the count of themes
	 */
	self.increment_theme_count = function($display_counter) {
		var $count = $display_counter.attr('data-count');
		var new_count = parseInt($count) + 1;
		$display_counter.attr('data-count', new_count);
		$display_counter.html(new_count);
	};

	/**
	 * COIN BUDGET: Get the selected coin budget
	 */
	self.get_selected_coin_budget = function() {
		return jQuery('a.coin_budget.current').attr('data-value');
	}

	/**
	 * COIN BUDGET: Disable the selection of a coin budget.
	 */
	self.disable_select_coin_budget = function() {
		jQuery("a.coin_budget").addClass('inactive-link');
	};

	/**
	 * COIN BUDGET: Enable the selection of a coin budget.
	 */
	self.enable_select_coin_budget = function() {
		jQuery("a.coin_budget").removeClass('inactive-link');
	};

	/**
	 * Return the tab the user is on (pages or themes).
	 * 
	 * We are assuming that the first tab is 'pages' and the second is 'themes'.
	 * If this changes, then it will break.
	 */
	self.selected_tab = function() {
		if (true === jQuery('#nav-step-1').hasClass('nav-tab-active')) {
			return 'pages';
		} else {
			return 'themes';
		}
	}

	/**
	 * Toggle the display of the "navbar" and the "Additional themes" button.
	 * 
	 * Note 201507302221: To prevent a redesign of "add-pages-to-existing" and
	 * "theme_selection", we display the navbar in "pages" by default. As the
	 * user changes tabs, the navbar is moved to the appropriate navbar-wrapper
	 * (one located in each pages and themes).
	 * 
	 * The "Additional themes" button should show on the "Install New Themes"
	 * page, but not on the "Add new Pages" or "Standard Inspirations" pages.
	 */
	self.toggle_step_2_nav_bar = function(read_url_boldgrid_tab) {
		/**
		 * Get the tab.
		 * 
		 * If true == read_url_boldgrid_tab, then var tab will be based on
		 * 'boldgird-tab' in the url. Else, var tab will be based on the tab
		 * selected on the page.
		 */
		if (true == read_url_boldgrid_tab) {
			var tab = ('themes' == self.baseAdmin.GetURLParameter('boldgrid-tab')) ? 'themes'
					: 'pages';
		} else {
			var tab = self.selected_tab();
		}

		/**
		 * Toggle the location of the nav bar, and toggle the 'additional
		 * themes' button.
		 */
		if ('themes' == tab) {
			// Move the navbar to the themes navbar wrapper.
			self.$step_2_nav_bar.appendTo(self.$themes_step_2_nav_bar_wrapper);

			// Show the "Additional themes" button.
			self.$additional_themes_button.removeClass('hidden');
		} else {
			// Move the navbar to the pages navbar wrapper.
			self.$step_2_nav_bar.appendTo(self.$pages_step_2_nav_bar_wrapper);

			// Hide the "Additional themes" button.
			self.$additional_themes_button.addClass('hidden');
		}
	}

	return self;

})(jQuery, window.IMHWPB || {}).init();
