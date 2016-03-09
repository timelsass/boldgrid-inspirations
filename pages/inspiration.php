<?php

/*
 * ****************************************************************************
 * Notes:
 * ****************************************************************************
 * $nav_steps are defined in classes/class-boldgrid-inspirations-built.php
 */

// Prevent direct calls
if ( ! defined( 'WPINC' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

include BOLDGRID_BASE_DIR . '/pages/templates/get_categories.php';
include BOLDGRID_BASE_DIR . '/pages/templates/build_profile.php';
include BOLDGRID_BASE_DIR . '/pages/templates/get_page_sets.php';
include BOLDGRID_BASE_DIR . '/pages/templates/template-inspiration-notices.php';
include BOLDGRID_BASE_DIR . '/pages/includes/install-selection-type.php';

add_thickbox();
?>

<h2 class="nav-tab-wrapper hidden" id='button_navigation'>
	<a href="#" id='nav-step-1' data-step='1'
		class="nav-tab nav-tab-active"><?php echo $nav_steps['step-1']['title']; ?></a>
		
		<?php if (isset($nav_steps['step-3'])) { ?>
	<a href="#" id='nav-step-2' data-step='2' class="nav-tab"><?php echo $nav_steps['step-2']['title']; ?></a>
		<?php } ?>
		
		<?php if (isset($nav_steps['step-3'])) { ?>
		 	<a href="#" id='nav-step-3' data-step='3' class="nav-tab"><?php echo $nav_steps['step-3']['title']; ?></a> 
		<?php } ?>
</h2>

<div id="boldgrid-error-message" class="error hidden">
	<p>An error occurred while processing your request, please try again
		later.</p>
</div>

<?php
/**
 * ****************************************************************************
 * Step 1: includes/inspiration_category_select.php
 * ****************************************************************************
 */
?>
<div class='imhwpb-step' id='step-1'>
	<?php require $nav_steps['step-1']['content']; ?>
</div>

<?php
/**
 * ****************************************************************************
 * Step 2: includes/base_website.php
 * ****************************************************************************
 */
?>
<div class='imhwpb-step' id='step-2'>
	<?php
	if ( isset( $nav_steps['step-2']['content'] ) ) {
		require $nav_steps['step-2']['content'];
	}
	?>
</div>

<?php
/**
 * ****************************************************************************
 * Step 3: includes/page_set_selection.php
 * ****************************************************************************
 */
?>
<div class='imhwpb-step' id='step-3'>
	<?php
	if ( isset( $nav_steps['step-3']['content'] ) ) {
		require $nav_steps['step-3']['content'];
	}
	?>
</div>

<?php
/**
 * ****************************************************************************
 * Preview
 * ****************************************************************************
 */
?>
<div class='imhwpb-step' id='preview'>
	<div class='coins'>
		<span class='coins'></span> Coins.<br /> For businesses, we recommend
		paid licenses for images - <a
			href="http://www.boldgrid.com/faqs#paid-licenses-recommendation"
			target="blank">learn more</a>. If you ultimately use your own images
		or a free alternative in place of suggested paid images, your site
		will have a zero Copyright Coins cost.
	</div>
	<h3 class="nav-tab-wrapper">
		<a id="monitor" class='nav-tab nav-tab-active' href='#'><?php _e('Monitor','boldgrid-inspirations'); ?></a>
		<a id="tablet" class='nav-tab' href='#'><?php _e('Tablet','boldgrid-inspirations'); ?></a>
		<a id="phone" class='nav-tab' href='#'><?php _e('Phone','boldgrid-inspirations'); ?></a>
	</h3>
	<h3 id='preview_theme_name'><?php _e('Theme name','boldgrid-inspirations'); ?></h3>
	<div id='preview_div'>
		<div id='preview_div_message'><?php _e('Please select a category in step 1.','boldgrid-inspirations'); ?></div>
		<iframe id='preview_iframe' allowfullscreen></iframe>
	</div>
	<div class="previews">
		<div id='preview_theme_button_set'>
			<button class="goback-to-themes button button-secondary"><?php _e('Go back','boldgrid-inspirations'); ?></button>
			<button class='button button-primary' id="select"><?php _e('Select','boldgrid-inspirations'); ?></button>
		</div>
		<div id='preview_page_set_button_set'>
			<button class="goback-to-page-sets button button-secondary"><?php _e('Go back','boldgrid-inspirations'); ?></button>
			<button class='button button-primary' id="select"><?php _e('Select','boldgrid-inspirations'); ?></button>
		</div>
	</div>
</div>

<?php
/**
 * ****************************************************************************
 * Install modals
 * ****************************************************************************
 */
?>
<!-- INSTALL MODALS -->
<div class='imhwpb-step install-modal' id='install'>
	<h1><?php _e('Install your new website!','boldgrid-inspirations'); ?></h1>
	<p><?php _e('<strong>Congratulations</strong>, you\'ve completed the first three steps!','boldgrid-inspirations'); ?></p>
	<p><?php _e('Before you can add your own personal touches to your <span class=\'install-modal-destination\'></span> website, we\'ll first need to install your new website for you. After installation, you can add your own images, change text, etc.','boldgrid-inspirations'); ?></p>
	<p><?php _e('Are you ready to install this website?','boldgrid-inspirations'); ?></p>
	<p class='center' id='install-buttons'>
		<button id="goback" class="goback button button-secondary"><?php _e('Go back','boldgrid-inspirations'); ?></button>
		<button name='install-button' class='button button-primary'><?php _e('Install this website!','boldgrid-inspirations'); ?></button>
	</p>
</div>
<div class='imhwpb-step install-modal' data-title="Install Theme"
	id='install-theme-modal'>
	<h1><?php _e('Install your new theme!','boldgrid-inspirations'); ?></h1>
	<p><?php _e('Before you can add your own personal touches to your new <span class=\'install-modal-destination\'></span> theme, we\'ll first need to install the theme. After installation, you can add your own images, change text, etc.','boldgrid-inspirations'); ?></p>
	<p><?php _e('Are you ready to install this theme?','boldgrid-inspirations'); ?></p>
	<p class='center' id='install-buttons'>
		<button id="goback" class="goback button button-secondary"><?php _e('Go back','boldgrid-inspirations'); ?></button>
		<button name='install-button' class='button button-primary'><?php _e('Install this theme!','boldgrid-inspirations'); ?></button>
	</p>
</div>
<!-- END INSTALL MODALS -->

<?php
/**
 * ****************************************************************************
 * Form
 * ****************************************************************************
 */
?>
<form method='post' name='post_deploy' id='post_deploy'
	style='display: none;' action='admin.php?page=boldgrid-inspirations'>
	<input type='hidden' name='task' id='task' value='deploy' />
	<?php wp_nonce_field( 'deploy' ); ?>
	<table style='margin: 100px 0px;'>
		<tr>
			<td>category id</td>
			<td><input type='text' name='boldgrid_cat_id' id='boldgrid_cat_id'
				value='-1' /></td>
		</tr>
		<tr>
			<td>sub category id</td>
			<td><input type='text' name='boldgrid_sub_cat_id'
				id='boldgrid_sub_cat_id' value='-1' /></td>
		</tr>
		<tr>
			<td>theme id</td>
			<td><input type='text' name='boldgrid_theme_id'
				id='boldgrid_theme_id' value='-1' /></td>
		</tr>
		<tr>
			<td>page set id</td>
			<td><input type='text' name='boldgrid_page_set_id'
				id='boldgrid_page_set_id' value='-1' /></td>
		</tr>
		<tr>
			<td>api key hash</td>
			<td><input type='text' name='boldgrid_api_key_hash'
				id='boldgrid_api_key_hash'
				value='<?php echo (isset($boldgrid_configs['api_key']) ? $boldgrid_configs['api_key'] : null); ?>'
				style='width: 600px;' /></td>
		</tr>
		<tr>
			<td>new site's path</td>
			<td><input type='text' name='boldgrid_new_path'
				id='boldgrid_new_path'
				value='<?php echo str_replace('.','',str_replace(' ','',microtime())); ?>' /></td>
		</tr>
		<tr>
			<td>pde id</td>
			<td><input type='text' name='boldgrid_pde' id='boldgrid_pde' value='' /></td>
		</tr>
		<tr>
			<td>wp_language</td>
			<td><input type='hidden' id='wp_language'
				value='<?php echo bloginfo( 'language' ); ?>' /></td>
		</tr>
		<tr>
			<td>language id</td>
			<td><input type='text' name='boldgrid_language_id'
				id='boldgrid_language_id' value='' /></td>
		</tr>
		<tr>
			<td>build profile id</td>
			<td><input type='text' name='boldgrid_build_profile_id'
				id='boldgrid_build_profile_id' value='' /></td>
		</tr>
		<tr>
			<td>Type</td>
			<td><input type='text' name='deploy-type' value='' /></td>
		</tr>
		<tr>
			<td>Pages</td>
			<td><input type='text' name='pages' value='' /></td>
		</tr>
		<tr>
			<td>Staging</td>
			<td><input type='text' name='staging' value='' /></td>
		</tr>
		<tr>
			<td>coin budget</td>
			<td><input type='text' name='coin_budget' id='coin_budget' value='20' /></td>
		</tr>
		<tr>
			<td>Theme Version</td>
			<td><input type='text' name='boldgrid_theme_version_type'
				id='boldgrid_theme_version_type' value='active' /></td>
		</tr>
		<tr>
			<td>Page Version</td>
			<td><input type='text' name='boldgrid_page_set_version_type'
				id='boldgrid_page_set_version_type' value='active' /></td>
		</tr>
	</table>
	<input type='submit' value='Deploy' />
</form>

<?php
/**
 * ****************************************************************************
 * Pointers: Copyright coins
 * ****************************************************************************
 */
?>
<div id='step-2-theme-coins' class="wp-pointer wp-pointer-left">
	<div class="wp-pointer-content">
		<h3>Copyright Coins</h3>
		<p>
			Copyright Coins allow you to easily purchase content with paid
			licenses. You will not be charged until you download images <b>without</b>
			watermarks.
		</p>
	</div>
	<div class="wp-pointer-arrow">
		<div class="wp-pointer-arrow-inner"></div>
	</div>
</div>