<?php
/**
 * deploy.php
 *
 * This file renders the actual deploy page.
 */

// Prevent direct calls.
require BOLDGRID_BASE_DIR . '/pages/templates/restrict-direct-access.php';
?>

<style>
ul#deploy_log {
	list-style-position: inside;
	list-style-type: disc;
	font-family: "Courier New", Courier, monospace;
	line-height: 11px;
}

/* When deployment is installing a theme, it prints the details of the theme on the screen. This css
   hides those details. */
.wrap:not(.main) {
 	display: none;
}

#deploy_status .spinner {
	visibility: visible;
	float: none;
	margin: 0px 5px 0px 0px;
}

#deploy_text {
	font-style: italic;
}

#deploy_status .boldgrid-loading {
	display: inline-block;
	margin: 15px 0px 20px 0px;
}

h1 .dashicons.dashicons-yes {
	color: green;
	font-size: 30px;
	padding-right: 15px;
}
</style>

<div class="wrap main">

	<?php
	$active_menu_item = 'install';
	require_once BOLDGRID_BASE_DIR . '/pages/includes/boldgrid-inspirations/menu.php';
	?>

	<div name='deploy_status' id='deploy_status' class='screen-contained'>

		<div style="text-align:center;">
			<h1><?php echo esc_html__( 'Installing...', 'boldgrid-inspirations' ); ?> <span class='spinner'></span></h1>
		</div>

		<div class="boldgrid-plugin-card">
			<div class="top">

				<h2 style="text-align:center;"><?php echo esc_html__( 'Premium Key Bonus', 'boldgrid-inspirations' ); ?></h2>
				<p style="text-align:center;max-width:75%;margin-left:auto;margin-right:auto;">
					<?php echo esc_html__( 'Because you\'re a Premium Key holder, we\'re installing the Premium versions of our BoldGrid Plugins.', 'boldgrid-inspirations' ); ?>
				</p>
				<ul style="max-width:75%;margin-left:auto;margin-right:auto;">
					<li style="display:block; width:calc(50% - 7.5px); float:left; text-align:center;">
						<img src="https://repo.boldgrid.com/assets/icon-boldgrid-editor-128x128.png" style="max-width: 100px;"><br />
						BoldGrid Post and Page Builder Premium
					</li>
					<li style="display:block; width:calc(50% - 7.5px); float:right; text-align:center;">
						<img src="https://repo.boldgrid.com/assets/icon-boldgrid-backup-128x128.png" style="max-width: 100px;"><br />
						BoldGrid Backup Premium
					</li>
				</ul>

				<div style="clear:both;"></div>
			</div>
		</div>


		<h2 style='color:red;'>EVERYTHING BELOW THIS IS TO BE REMOVED</h2>

		<p>
			<strong>Installation log:</strong> <a id='toggle_view_deploy_log'>show
				/ hide log</a> (<em><span class='deploy_log_line_count'></span></em>)<br />
			<span name='deploy_text' id='deploy_text'>loading...</span>
		</p>

		<div class='plugin-card hidden'>
			<div class='plugin-card-top'>
				<ul name='deploy_log' id='deploy_log'></ul>
			</div>
		</div>
	</div>
</div>

<?php

Boldgrid_Inspirations_Utility::inline_js_file( 'after_deployment_container.js' );

add_shortcode( 'imhwpb', array (
	'imhwpbDeploy',
	'dummy_shortcode_imhwpb'
) );

$new_deploy = new Boldgrid_Inspirations_Deploy( $this->get_configs() );
$new_deploy->do_deploy();
?>
