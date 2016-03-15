<?php

// Prevent direct calls
if ( ! defined( 'WPINC' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

?>
<div class="misc-pub-section boldgrid-auto-add-to-menu">
	In menu: <span id='selected-menu-names'></span> <a
		href="#edit-boldgrid-auto-add-to-menu"
		class="edit-boldgrid-auto-add-to-menu hide-if-no-js"><span
		aria-hidden="true">Edit</span></a>
	<div id="boldgrid-auto-add-to-menu-menu-listing" class="hide-if-js">
		<?php echo $nav_menus_html; ?>
		<p>
			<a class="hide-boldgrid-auto-add-to-menu button">OK</a> <a
				class="hide-boldgrid-auto-add-to-menu button-cancel"
				id="cancel-add-to-menu" href="#edit-boldgrid-auto-add-to-menu">Cancel</a>
		</p>
	</div>
	<input type='hidden' name='boldgrid_auto_add_to_menu_page_id'
		id='boldgrid-auto-add-to-menu-page-id'
		value='<?php echo $post->ID; ?>'
		data-is-new-page='<?php echo $is_new_page; ?>' />
</div>
