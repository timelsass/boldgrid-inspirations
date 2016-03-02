<?php

// Prevent direct calls
if ( ! defined( 'WPINC' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

?>

<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam at
	justo dictum, varius ipsum eu, posuere velit. Sed condimentum aliquam
	ex, nec sollicitudin diam cursus sit amet. Proin a odio varius, feugiat
	eros ut, tempus dui. Nulla porttitor mollis imperdiet. Sed quis varius
	odio. Cras nisl purus, eleifend id nisi tempus, consectetur interdum
	augue. Sed sit amet justo semper purus pretium laoreet. Nunc fringilla
	justo posuere, dignissim felis id, fringilla nunc.</p>

<a href='<?php echo get_admin_url( null, 'admin.php?page=imh-wpb'); ?>'
	class='button button-primary'>Get Started!</a>
