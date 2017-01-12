<?php
/**
 * An array of config settings for the Inspirations survey.
 *
 * @since 1.3.6
 */

$email = Boldgrid_Inspirations_Survey::get_value( 'email' );
$address = Boldgrid_Inspirations_Survey::get_value( 'address' );
$display_address = Boldgrid_Inspirations_Survey::should_display( 'address' );
$map_iframe = sprintf(
	'<iframe style="width:100%%;height:100%%;" src="https://maps.google.com/maps?q=%1$s&amp;t=m&amp;z=16&amp;output=embed" frameborder="0"></iframe>',
	urlencode( $address )
);

return array(
	'find_and_replace' => array(
		array(
			'removal_key' =>		'phone',
			'value' =>				Boldgrid_Inspirations_Survey::get_value( 'phone' ),
			'display' =>			Boldgrid_Inspirations_Survey::should_display( 'phone' ),
			'on_success' =>			'node_value',
		),
		array(
			'removal_key' =>		'address',
			'value' =>				Boldgrid_Inspirations_Survey::get_value( 'address' ),
			'display' =>			$display_address,
			'on_success' =>			'node_value',
		),
		array(
			'removal_key' =>		'email',
			'value' =>				$email,
			'display' =>			Boldgrid_Inspirations_Survey::should_display( 'email' ),
			'parent_attributes' =>	array( 'href' => 'mailto:' . $email, ),
			'on_success' =>			'node_value',
		),
		array(
			'removal_key' =>		'map',
			'value' =>				$map_iframe,
			'display' =>			$display_address,
			'on_success' =>			'remove_children',
		),
	)
);
?>