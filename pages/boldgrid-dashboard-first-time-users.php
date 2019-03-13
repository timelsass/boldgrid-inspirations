<?php
// Prevent direct calls.
require BOLDGRID_BASE_DIR . '/pages/templates/restrict-direct-access.php';

?>
<div class="youtube-container">
	<div class="youtube-player" data-id="0CMIjXez0nU"></div>
</div>
<p><?php esc_html_e( 'Creating a site with BoldGrid is done in 3 steps:', 'boldgrid-inspirations' ); ?></p>
<ol class="boldgrid-counter">
	<li><?php
		/*
		 * Create the link to Inspirations.
		 *
		 * Show either Inspirations lightbulb or BoldGrid Logo depending on their menu settings.
		 */
		$boldgrid_settings = get_option( 'boldgrid_settings' );
		$inspirations_link = add_query_arg( 'page', 'boldgrid-inspirations', admin_url( 'admin.php' ) );
		if ( 1 == $boldgrid_settings['boldgrid_menu_option'] ) {
			$link = sprintf(
				' <a href="%s" class="dashicons-before dashicons-lightbulb">Inspirations</a> ',
				esc_url( $inspirations_link )
			);
		} else {
			$link = sprintf(
				' <a href="%s" class="dashicons-before boldgrid-icon"> BoldGrid</a> ',
				esc_url( $inspirations_link )
			);
		}

		echo wp_kses(
			sprintf(
				__( 'Go to %1$s to install your starter website and pages typical for your industry.', 'boldgrid-inspirations' ),
				$link
			),
			array( 'a' => array( 'href' => array(), 'class' => array() ) )
		);
	?></li>
	<li><?php
		// Create link to customizer.
		$link = sprintf(
			' <a href="%s" class="dashicons-before dashicons-admin-customize">' . esc_html__( 'Customize', 'boldgrid-inspirations' ) . '</a> ',
			esc_url( add_query_arg( 'return', urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ), admin_url( 'customize.php' ) ) )
		);

		echo wp_kses(
			sprintf(
				__( '%1$s site wide settings like business name, colors, menus and content in your header and footer.', 'boldgrid-inspirations' ),
				$link
			),
			array( 'a' => array( 'href' => array(), 'class' => array() ) )
		);
	?></li>
	<li><?php
		// Create link to All pages.
		$link = sprintf(
			' <a href="%s" class="dashicons-before dashicons-admin-page">' . esc_html__( 'Pages', 'boldgrid-inspirations' ) . '</a> ',
			esc_url( add_query_arg( 'post_type', 'page', admin_url( 'edit.php' ) ) )
		);

		echo wp_kses(
			sprintf(
				__( 'Edit your %1$s to add your content and photos.', 'boldgrid-inspirations' ),
				$link
			),
			array( 'a' => array( 'href' => array(), 'class' => array() ) )
		);
	?></li>
</ol>
<p>
<?php
	// translators: 1 opening anchor tag linking to BoldGrid support center, 2 closing anchor tag.
	printf( __( 'Watching the video above is recommended.  You can also visit our %1$s support center %2$s to learn more.', 'boldgrid-inspirations' ), '<a href="http://www.boldgrid.com/support/" target="_blank">', '</a>' );
	?>
</p>
<div class="boldgrid-button-wrapper-left">
	<?php
	// Use printf to separate out the actual words from HTML
	// so it can be sent through translate.
	printf(
		'<a href="https://www.boldgrid.com/support/" target="_blank"><span class="button button-secondary button-hero">%s</span></a>',
		esc_html__( 'Learn More', 'boldgrid-inspirations' )
	);

	?>
	<span class="boldgrid-between-buttons">or</span>
	<?php

	printf(
		'<a href="%s"><span class="button button-primary button-hero">' .
		esc_html__( 'Get Started', 'boldgrid-inspirations' ) .
		'</span></a>',
		esc_url(
			add_query_arg(
				array(
					'page' => 'boldgrid-inspirations',
					'boldgrid-tab' => 'install',
				),
				admin_url( 'admin.php' )
			)
		)
	);
	?>
</div>
<!-- End of First Time Users Section -->
