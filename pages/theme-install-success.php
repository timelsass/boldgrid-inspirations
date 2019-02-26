<?php
// Prevent direct calls.
require BOLDGRID_BASE_DIR . '/pages/templates/restrict-direct-access.php';

?>
<div class="wrap">
	<?php if (false == $error) { ?>
    <h2><?php
    	// translators: 1 the name of the theme being installed.
    	echo printf( __( 'Installing Theme: %1$s', 'boldgrid-inspirations' ), $theme_label ); ?>
    </h2>
    <p><?php echo esc_html__( 'Downloaded install package...', 'boldgrid-inspirations' ); ?></p>
    <p><?php echo esc_html__( 'Unpacked the package...', 'boldgrid-inspirations' ); ?></p>
    <p><?php echo esc_html__( 'Installed the theme...', 'boldgrid-inspirations' ); ?></p>
    <p>
        <?php printf(
			wp_kses(
				// translators: 1 opening strong tag, 2 name of the theme, 3 closing strong tag.
				__( 'Successfully installed the theme %1$s %2$s %3$s.', 'boldgrid-inspirations' ),
				array( 'strong' => array() )
			),
			'<strong>',
			$theme_label,
			'</strong>'
		); ?>
    </p>
    <p>
        <a title="<?php echo esc_attr( __( 'Enable theme for this site', 'boldgrid-inspirations' ) ); ?>" href="<?php echo esc_url( $enable_theme_url ); ?>"><?php echo esc_html__( 'Enable Theme', 'boldgrid-inspirations' ); ?></a> |
        <a title="<?php echo esc_attr( __( 'Return to Theme Installer', 'boldgrid-inspirations' ) ); ?>" href="<?php echo admin_url( 'themes.php' ); ?>"><?php echo esc_html__( 'View Themes', 'boldgrid-inspirations' ); ?></a>
    </p>
    <?php } else { ?>

        <div class="error">
	        <p><?php echo esc_html__( 'An error occurred while updating your theme.', 'boldgrid-inspirations' ); ?></p>
	    </div>

    <?php } ?>
</div>
