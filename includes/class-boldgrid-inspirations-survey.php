<?php
/**
 * BoldGrid Source Code
 *
 * @package Boldgrid_Inspirations_Survey
 * @copyright BoldGrid.com
 * @version $Id$
 * @author BoldGrid.com <wpb@boldgrid.com>
 */

/**
 * The BoldGrid Inspirations Survey class.
 *
 * @since 1.3.1
 */
class Boldgrid_Inspirations_Survey {
	/**
	 * The 'data-if-removed' attribute of an element.
	 *
	 * @since 1.3.4
	 */
	public $data_if_removed = 'data-if-removed';

	/**
	 * The 'data-removal-key' attribute of an element.
	 *
	 * @since 1.3.4
	 */
	public $data_removal_key = 'data-removal-key';

	/**
	 * The option name survey data is stored.
	 *
	 * @since 1.3.5
	 */
	public $option = 'boldgrid_survey';

	/**
	 * Add hooks.
	 *
	 * @since 1.3.4
	 */
	public function add_hooks() {
		add_filter( 'boldgrid_theme_framework_config', array( $this, 'bgtfw_config' ), 15 );

		add_filter( 'boldgrid_deployment_pre_insert_post', array( $this, 'update_post' ) );
	}

	/**
	 * Filter bgtfw configs.
	 *
	 * This allows Inspirations to change widgets before the bgtfw can create them.
	 *
	 * @since 1.3.4
	 *
	 * @param  array $configs Bgtfw configs.
	 * @return array $configs.
	 */
	public function bgtfw_config( $configs ) {

		// If the user has never taken the survey, use the default configs.
		if( ! $this->has_taken() ) {
			return $configs;
		}

		$configs = $this->update_contact_blocks( $configs );

		$configs = $this->update_social( $configs );

		$configs = $this->update_widgets( $configs );

		return $configs;
	}

	/**
	 * Remove an element from the dom if its attribute is a certain value.
	 *
	 * For example, if the user does not want their phone number listed on the contact us page,
	 * we need to remove several elements on the page that have data-removal-key=phone-number.
	 *
	 * @since 1.3.4
	 *
	 * @param  object $dom A DOMDocument object.
	 * @param  string $attribute
	 * @param  string $value
	 * @return A DOMDocument object
	 */
	public function delete_by_attribute( $dom, $attribute, $value ) {
		$finder = new DomXPath( $dom );

		$elements = $finder->query('//*[contains(@' . $attribute . ',"' . $value . '")]');

		foreach( $elements as $element ) {
			$element->parentNode->removeChild( $element );
		}

		return $dom;
	}

	/**
	 * Cleanup code.
	 *
	 * @since 1.3.4
	 *
	 * @param  object $dom A DOMDocument object.
	 * @return string
	 */
	public function dom_clean_save( $dom ) {
		// An array of attributes that need to be removed.
		$attributes = array(
			$this->data_if_removed,
			$this->data_removal_key,
		);

		/*
		 * Removed unneedd attributes.
		 *
		 * There are certain attributes we use to store data temporarily, such as data-if-removed.
		 * Remove all these attributes from all elements.
		 */
		foreach( $dom->getElementsByTagName('*') as $element ) {
			foreach( $attributes as $attribute ) {
				$element->removeAttribute( $attribute );
			}
		}

		/*
		 * Remove doctype, html, and body tags.
		 *
		 * When using $dom->loadHTML, doctype/html/body tags are automatically added.
		 * @see http://fr.php.net/manual/en/domdocument.savehtml.php#85165
		 *
		 * As of PHP 5.4 and Libxml 2.6, there are optional parameters to pass to $dom->loadHTML
		 * which will not add the doctype/html/body tags.
		 * @see http://stackoverflow.com/questions/4879946/how-to-savehtml-of-domdocument-without-html-wrapper
		 *
		 * @todo Update this section of code when PHP standards are changed.
		*/
		return preg_replace(
			'/^<!DOCTYPE.+?>/',
			'',
			str_replace(
				array('<html>', '</html>', '<body>', '</body>'),
				array('', '', '', ''),
				$dom->saveHTML()
			)
		);
	}

	/**
	 * Update the contact blocks within the bgtfw.
	 *
	 * @since 1.3.5
	 *
	 * @param  array $configs Bgtfw configs.
	 * @return array
	 */
	public function update_contact_blocks( $configs ) {
		// If the user has never taken the survey, use the default contact blocks.
		if( ! $this->has_taken() ) {
			return $configs;
		}

		$survey = $this->get();

		$blocks = array( 'address', 'phone', 'email' );

		// Configure our copyright block.
		$defaults[]['contact_block'] = '© ' . date( 'Y' ) . ' ' . get_bloginfo( 'name' );

		// Configure our other blocks.
		foreach( $blocks as $block ) {
			if( $this->should_display( $block ) ) {
				$defaults[]['contact_block'] = esc_attr( $this->get_value( $block ) );
			}
		}

		$configs['customizer-options']['contact-blocks']['defaults'] = $defaults;

		return $configs;
	}

	/**
	 * Filter posts based on survey data.
	 *
	 * @since 1.3.4
	 *
	 * @param  array $post Post data before it's passed to wp_insert_post.
	 * @return array
	 */
	public function update_post( $post ) {
		$dom = new DOMDocument;
		$dom->loadHTML( $post['post_content'] );
		$finder = new DomXPath( $dom );

		$phone = $this->get_value( 'phone' );
		$display_phone = $this->should_display( 'phone' );

		$phone_numbers = $finder->query( '//*[@' . $this->data_if_removed . ' and contains(@' . $this->data_removal_key . ', "phone-number")]' );

		foreach( $phone_numbers as $phone_number ) {
			/*
			 * If the user did not check "do not display" AND entered a phone number, replace the
			 * phone number.
			 *
			 * Otherwise, process the data-if-removed value.
			 */
			if( $display_phone && ! is_null( $phone ) ) {
				$phone_number->nodeValue = $phone;
			} elseif( 'key' === $phone_number->getAttribute( $this->data_if_removed ) ) {
				$removal_key = $phone_number->getAttribute( $this->data_removal_key );
				$dom = $this->delete_by_attribute( $dom, $this->data_removal_key, $removal_key );
			}
		}

		$post['post_content'] = $this->dom_clean_save( $dom );

		return $post;
	}

	/**
	 * Get survey data.
	 *
	 * @since 1.3.4
	 *
	 * @return array
	 */
	public function get() {
		return get_option( $this->option, array() );
	}

	/**
	 * Get the value of a specific survey entry.
	 *
	 * For example, pass in 'phone', and we'll return $survey['phone']['value'].
	 *
	 * @since 1.3.5
	 *
	 * @param  string $key
	 * @return string|null
	 */
	public function get_value( $key ) {
		$survey = $this->get();

		return ( ! empty( $survey[$key]['value'] ) ? $survey[$key]['value'] : null );
	}

	/**
	 * Return an array of social networks.
	 *
	 * The array will be empty, unless we can retrieve it from the
	 * Boldgrid_Framework_Social_Media_Icons class.
	 *
	 * @since 1.3.4
	 *
	 * @return array.
	 */
	public function get_networks() {
		$networks = array();

		if( class_exists( 'Boldgrid_Framework_Social_Media_Icons' ) ) {

			// The Boldgrid_Framework_Social_Media_Icons requies configs to be passed in.
			$config = array(
				'social-icons' => array(
					'size' => null,
					'type' => null,
					'hide-text' => null,
				),
			);

			$icons = new Boldgrid_Framework_Social_Media_Icons( $config );

			$networks = $icons->networks;
		}

		return $networks;
	}

	/**
	 * Whether or not the user has taken the survey.
	 *
	 * @since 1.3.5
	 *
	 * @return bool True if the user has taken the survey.
	 */
	public function has_taken() {
		return ( false !== get_option( $this->option ) );
	}

	/**
	 * Prepend a $url with a protocol.
	 *
	 * For example, if you pass in facebook.com, we'll return http://facebook.com.
	 *
	 * @since 1.3.5
	 *
	 * @param  string $url
	 * @return string
	 */
	public function prepend_protocol( $url ) {
		$starts_with_http = ( 'http' === substr( $url, 0, 4 ) );

		if( is_email( $url ) ) {
			$url = 'mailto:' . $url;
		} elseif( ! $starts_with_http ) {
			$url = 'http://' . $url;
		}

		return $url;
	}

	/**
	 * Save survey data to the 'boldgrid_survey' option.
	 *
	 * @since 1.3.4
	 *
	 * @param array $survey An array of survey data.
	 */
	public function save( $survey ) {
		// We will review the $survey and build $sanitized_survey;
		$sanitized_survey = array();

		// These standard keys have the same format, and can be sanitized the same way.
		$standard_keys = array( 'blogname', 'email', 'phone', 'address' );

		// Sanitize our data.
		foreach( $standard_keys as $key ) {
			// If we passed in an empty value, skip this key. It won't be in our survey.
			if( empty( $survey[$key]['value'] ) ) {
				continue;
			}

			$is_blogname = ( 'blogname' === $key );
			$is_email = ( 'email' === $key );
			$raw_value = $survey[$key]['value'];

			$sanitized_value = ( $is_email ? sanitize_email( $raw_value ) : sanitize_text_field( $raw_value ) );

			// If after sanitizing this value its empty, skip this key. It won't be in our survey.
			if( empty( $sanitized_value ) ) {
				continue;
			}

			$sanitized_survey[$key]['value'] = $sanitized_value;

			if( $is_blogname ) {
				update_option( 'blogname', $sanitized_value );
			}

			if( isset( $survey[$key]['do-not-display'] ) & ! $is_blogname ) {
				$sanitized_survey[$key]['do-not-display'] = true;
			}
		}

		// Ensure social is an array.
		if( empty( $survey['social'] ) || ! is_array( $survey['social'] ) ) {
			$survey['social'] = array();
		}

		// Fix URLs for the survey. Ensure they start with http://.
		foreach( $survey['social'] as $icon => $url ) {
			if( 'do-not-display' === $icon ) {
				$sanitized_survey['social'][$icon] = true;
			} else {
				$sanitized_survey['social'][$icon] = $this->prepend_protocol( $url );
			}
		}

		update_option( $this->option, $sanitized_survey );
	}

	/**
	 * Determine if we should display a survey key.
	 *
	 * @since 1.3.5
	 *
	 * @param  string $key
	 * @return bool        True if we have a value and the user did not check 'do not display'.
	 */
	public function should_display( $key ) {
		$survey = $this->get();

		$value = $this->get_value( $key );

		// If there is no value for the key, we can't display it, return false.
		if( empty( $value ) ) {
			return false;
		}

		// If the do_not_display flag is set, we won't display it, return false.
		if( ! empty( $survey[$key]['do-not-display'] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Filter items in the bgtfw's "social" default menu.
	 *
	 * We're actually either removing this menu or replacing everything in it.
	 *
	 * If we have social media networks saved from the survey, use those.
	 *
	 * @since 1.3.4
	 *
	 * @param  array $configs Bgtfw configs.
	 * @return array
	 */
	public function update_social( $configs ) {
		$networks = $this->get_networks();

		$survey = $this->get();

		// Grab the value of 'do-not-display' and then unset it.
		$display_social = ! isset( $survey['social']['do-not-display'] );
		unset( $survey['social']['do-not-display'] );

		/*
		 * If the user doesn't want to display a social menu, or they don't have any social networks
		 * added, unset the social default menu and return.
		 */
		if( ! $display_social || empty( $survey['social'] ) ) {
			unset( $configs['menu']['default-menus']['social'] );
			return $configs;
		}

		foreach( $survey['social'] as $icon => $url ) {
			// Get the host from the url.
			$host = parse_url( $url, PHP_URL_HOST );

			// Whether or not this is a mailto:// link.
			$is_mailto = ( 'mailto:' === substr( $url, 0, 7 ) );

			$item = array(
				'menu-item-url' => $url,
				'menu-item-status' => 'publish',
				// These titles will be replaced by the bgtfw if found in $networks.
				'menu-item-title' => $host,
				'menu-item-attr-title' => $host,
			);

			// Open links in a new tab.
			if( ! $is_mailto ) {
				$item['menu-item-target'] = '_blank';
			}

			foreach ( $networks as $nework_url => $network ) {
				if ( false !== strpos( $url, $nework_url ) ) {
					$item['menu-item-classes'] = $network['class'];
					$item['menu-item-attr-title'] = $network['name'];
					$item['menu-item-title'] = $network['name'];
				}
			}

			$items[] = $item;
		}

		$configs['menu']['default-menus']['social']['items'] = $items;

		return $configs;
	}

	/**
	 * Update a widget based upon our survey data.
	 *
	 * Currently, this method is specific to Pavilion and its phone number widget. If other themes
	 * add a phone number widget, they simply need to ensure the phone number is within a span with
	 * the phone-number class (see Pavilion). Remember, if the user decides to not show the phone
	 * number, then entire widget will be deleted, thus the reference to a widget specific to a
	 * phone number only.
	 *
	 * @since 1.3.4
	 *
	 * @param  array $widget An array of widget data.
	 * @return array $widget.
	 */
	public function update_widget( $widget ) {
		// If our widget is not an array or the text is empty, abort.
		if( ! is_array( $widget ) || empty( $widget['text'] ) ) {
			return $widget;
		}

		$dom = new DOMDocument;
		$dom->loadHTML( $widget['text'] );
		$finder = new DomXPath( $dom );

		$phone = $this->get_value( 'phone' );
		$display_phone = $this->should_display( 'phone' );

		$phone_numbers = $finder->query("//*[contains(@class,'phone-number')]");

		foreach( $phone_numbers as $phone_number ) {
			/*
			 * If the user did not check "do not display" AND entered a phone number, replace the
			 * phone number. Otherwise, flag the widget to be deleted.
			 */
			if( $display_phone && ! is_null( $phone ) ) {
				$phone_number->nodeValue = $phone;
			} else {
				$widget['delete'] = true;
			}
		}

		$widget['text'] = $this->dom_clean_save( $dom );

		return $widget;
	}

	/**
	 * Update Widgets.
	 *
	 * This method essentially loops through each widget_instance and calls update_widget(). So,
	 * update_widgets() calls update_widget().
	 *
	 * @since 1.3.5
	 *
	 * @param  array $configs Bgtfw configs.
	 * @return array
	 */
	public function update_widgets( $configs ) {
		// If we don't have any widget instances, abort.
		if( empty( $configs['widget']['widget_instances'] ) ) {
			return $configs;
		}

		$widget_instances = $configs['widget']['widget_instances'];

		/*
		 * Loop through all of the widgets and call the update method. If we set a 'delete' flag,
		 * delete the widget entirely, otherwise update its contents.
		 */
		foreach( $widget_instances as $widget_area => $widgets ) {
			foreach( $widgets as $widget_key => $widget ) {
				$updated_widget = $this->update_widget( $widget );

				if( isset( $updated_widget['delete'] ) ) {
					unset( $configs['widget']['widget_instances'][$widget_area][$widget_key] );
				} else {
					$configs['widget']['widget_instances'][$widget_area][$widget_key] = $updated_widget;
				}
			}
		}

		return $configs;
	}
}
?>