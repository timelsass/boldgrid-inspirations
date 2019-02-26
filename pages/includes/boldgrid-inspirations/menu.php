<?php
/**
 * Top menu.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$active_menu_item = empty( $active_menu_item ) ? 'welcome' : $active_menu_item;

$steps = array(
	array(
		'class'     => 'welcome' === $active_menu_item ? 'active' : 'disabled',
		'data-step' => 'welcome',
		'disabled'  => 'welcome' === $active_menu_item ? false : true,
		'title'     => esc_html__( 'Welcome', 'boldgrid-inspirations' ),
	),
	array(
		'class'     => 'disabled',
		'data-step' => 'design',
		'disabled'  => true,
		'title'     => esc_html__( 'Design', 'boldgrid-inspirations' ),
	),
	array(
		'class'     => 'disabled',
		'data-step' => 'content',
		'disabled'  => true,
		'title'     => esc_html__( 'Content', 'boldgrid-inspirations' ),
	),
	array(
		'class'     => 'disabled',
		'data-step' => 'contact',
		'disabled'  => true,
		'title'     => esc_html__( 'Essentials', 'boldgrid-inspirations' ),
	),
	array(
		'class'     => 'install' === $active_menu_item ? 'active' : 'disabled',
		'data-step' => 'install',
		'disabled'  => 'install' === $active_menu_item ? false : true,
		'title'     => esc_html__( 'Finish', 'boldgrid-inspirations' ),
	),
);

?>

<div class="top-menu welcome">
	<button type="button" class="notice-dismiss">
		<span class="screen-reader-text"><?php echo esc_html__( 'Dismiss this notice.', 'boldgrid-inspirations' ); ?></span>
	</button>

	<div>


		<?php
		$last_class = '';

		foreach( $steps as $step ) {
			$class = $step['class'];
			$class .= 'active' === $step['class'] ? ' boldgrid-orange-important' : '';
			$class .= 'active' === $last_class ? ' next' : '';

			echo '<a
					style="position: relative;"
					class="' . esc_attr( $class ) . '"
					data-step="' . esc_attr( $step['data-step'] ) . '" ' .
					( ! empty( $step['disabled'] ) ? 'data-disabled' : '' ) . '>' .
					esc_html( $step['title'] ) . '</a>';

					$last_class = $step['class'];
		}

		/*
		<a class="active" data-step="welcome" ><?php echo $lang['Welcome'] ?></a>
		<a class="disabled" data-step="design" ><?php echo $lang['Design'] ?></a>
		<a class="disabled" data-step="content" data-disabled ><?php echo $lang['Content']; ?></a>
		<a class="disabled" data-step="contact" data-disabled ><?php echo esc_html__( 'Essentials', 'boldgrid-inspirations' ); ?></a>
		<a class="disabled" data-step="install" data-disabled ><?php echo esc_html__( 'Finish', 'boldgrid-inspirations'); ?></a>
		*/

		?>
	</div>
</div>