<?php
// Prevent direct calls.
require BOLDGRID_BASE_DIR . '/pages/templates/restrict-direct-access.php';

?>
<div class="youtube-container">
	<div class="youtube-player" data-id="fAbVXapGx48"></div>
</div>
<p>We encourage you to watch the video above and review the

<?php
// Use printf to separate out the actual words from HTML
// so it can be sent through translate.
printf( '<a href="%s" class="dashicons-before dashicons-welcome-learn-more">%s</a>',

	// Escape the URL to avoid XSS.
	esc_url(

		// Add our query arguments.
		add_query_arg(

			array(

				// The page we are linking to (BoldGrid Tutorials).
				'page' => 'boldgrid-tutorials',

				// The tab we are linking to, "Advanced For WordPress Users".
				'tab' => 'advanced',
			),

			// The root page we are building our query off of.
			'admin.php'
		)
	),

	// End of Query Argument.

	// End of Escaping.

	// Link title is by itself, esc_html() escapes this output,
	// then __() allows it to be sent to translate. 'boldgrid-inspirations'
	// is our text domain.
	esc_html__( ' Tutorials', 'boldgrid-inspirations' )
);

?> before starting with

<?php
// Get BoldGrid settings.
$boldgrid_settings = get_option( 'boldgrid_settings' );

// Show eiher Inspirations lightbulb or BoldGrid Logo depending on their menu settings.
( 1 == $boldgrid_settings['boldgrid_menu_option'] ? printf(
	'<a href="%s" class="dashicons-before dashicons-lightbulb">' .
		 esc_html__( ' Inspirations', 'boldgrid-inspirations' ) . '</a>.',
		esc_url( add_query_arg( 'page', 'boldgrid-inspirations', admin_url( 'admin.php' ) ) ) ) : printf(
	'<a href="%s" class="dashicons-before boldgrid-icon">' .
	 esc_html__( ' BoldGrid', 'boldgrid-inspirations' ) . '</a>.',
	esc_url( add_query_arg( 'page', 'boldgrid-inspirations', admin_url( 'admin.php' ) ) ) )
);
?>
</p>

<p>
	<b><?php _e( 'Is this WordPress running a live website?', 'boldgrid-inspirations' ); ?></b>
</p>
<p>
<?php
// Use printf to separate out the actual words from HTML so it can be translated.
printf( __( 'Our %s Staging Plugin %s will help you transition to a new site while running your current Active Site.', 'boldgrid-inspirations' ),
	'<a href="https://www.boldgrid.com/support/getting-to-know-boldgrid/how-to-set-up-staging-in-boldgrid/">',
	'</a>'
);
?>
</p>

<p>In addition to the video above, we have more detail on things that have changed like the

<?php
// Use printf to separate out the actual words from HTML
// so it can be sent through translate.
printf( '<a href="%s">%s</a>',

	// Escape the URL to avoid XSS.
	esc_url(

		// Add our query arguments.
		add_query_arg(

			array(

				// The page we are linking to (BoldGrid Tutorials).
				'page' => 'boldgrid-tutorials',

				// The tab we are linking to, "Advanced For WordPress Users".
				'tab' => 'inspirations',

				// The tutorial we are linking to (Admin Menu).
				'tutorial' => '4',
			),

			// The root page we are building our query off of.
			'admin.php'
		)
	),

	// End of Query Argument.

	// End of Escaping.

	// Link title is by itself, esc_html() escapes this output,
	// then __() allows it to be sent to translate. 'boldgrid-inspirations'
	// is our text domain.
	esc_html__( 'Admin Menu', 'boldgrid-inspirations' )
);

?>

 (you can change it back

<?php
// Use printf to separate out the actual words from HTML so it can be translated.
printf( '<a href="%s">%s</a>',
	// Escape the URL to avoid XSS.
	esc_url(
		// Add our query arguments.
		add_query_arg( 'page', 'boldgrid-settings', 'admin.php' )
	),

	esc_html__( 'here', 'boldgrid-inspirations' )
); ?> ).

<div class="boldgrid-button-wrapper-right">

<?php printf( '<a href="https://www.boldgrid.com/support/advanced-tutorials/"><span class="button button-secondary button-hero">%s</span></a>', __( 'Learn More', 'boldgrid-inspirations' ) ); ?>

</div>
<!-- End of Advanced For WordPress Users Section -->
