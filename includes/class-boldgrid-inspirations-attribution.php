<?php
/**
 * BoldGrid Source Code
 *
 * @package Boldgrid_Inspirations_Attribution
 * @copyright BoldGrid.com
 * @version $Id$
 * @author BoldGrid.com <wpb@boldgrid.com>
 */

/**
 * The BoldGrid Attribution class.
 */
class Boldgrid_Inspirations_Attribution {

	public $assets;

	/**
	 * Language strings.
	 *
	 * @since 1.2.9
	 * @var array
	 */
	public $lang;

	public $license_details;

	/**
	 * Constructor
	 *
	 * @param array $settings
	 */
	public function __construct( ) {
		// Define our language strings.
		$this->lang = array(
			'Attribution' => __( 'Attribution', 'boldgrid-inspirations' ),
		);

		$this->set_license_details();

		$this->assets = get_option( 'boldgrid_asset', array() );
	}

	/**
	 * Add hooks.
	 */
	public function add_hooks() {
		if ( is_admin() ) {
			add_action( 'save_post', array( $this, 'save_post' ) );
		}

		/*
		 * At this point in the code, we are in the init hook.
		 *
		 * Registering a post type must be done in the init hook, so do that now.
		 */
		$this->register_post_type();
	}

	/**
	 * Add frontend hooks.
	 *
	 * @since 1.1.2
	 */
	public function add_wp_hooks() {
		/*
		 * Add a noindex meta tag to the attribution page.
		 *
		 * This action is intended to add 'noindex' to the attribution page so it is not picked up
		 * by search engines. This however is not yet ready for launch, so we'll return and abort.
		 */
		return;
		add_action( 'wp_head',
			array(
				$this,
				'noindex',
			)
		);
	}

	/**
	 * creates / update / build the attribution page (if needed).
	 *
	 * This is the meat and potatoes of this Attribution class.
	 */
	public function build_attribution_page() {
		// Get our attribution page. If it doesn't exist, this function will also create it.
		$this->attribution_page = Boldgrid_Inspirations_Attribution_Page::get();

		// Loop through each asset and determine if it needs attribution.
		$this->flag_assets_that_need_attribution();

		// Create the html of the attribution page.
		$this->update_html_of_the_attribution_page_object();
	}

	/**
	 * Is current page the attribution page?
	 *
	 * @return bool
	 */
	public function current_page_is_attribution_page() {
		// If we don't have an attribution page, abort right away and return false.
		if ( ! isset( $this->wp_options_attribution['page']['id'] ) ) {
			return false;
		}

		// Get the id of the current page.
		$page_id = get_the_ID();

		if ( $page_id === $this->wp_options_attribution['page']['id'] ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Add 'noindex' to attribution page.
	 *
	 * @since 1.1.2
	 * @see Boldgrid_Inspirations_Attribution::current_page_is_attribution_page().
	 * @link https://support.google.com/webmasters/answer/93710?hl=en
	 */
	public function noindex() {
		if ( $this->current_page_is_attribution_page() ) {
			echo "\n<meta name='robots' content='noindex'>\n";
		}
	}

	/**
	 *
	 */
	public function register_post_type() {
		$args = array(
			'public' => true,
			'publicly_queryable' => true,
			'query_var' => true,
			'show_ui' => false,
			'show_in_nav_menus' => false,
		);
		register_post_type( 'bg_attribution', $args );

		flush_rewrite_rules();
	}


	/**
	 * Loop through each asset and determine if it needs attribution.
	 *
	 * There's a lot of if's and foreach's here.
	 * Essentially we're getting to our assets and sending them to:
	 * $this->asset_needs_attribution( $asset, $asset_type )
	 */
	public function flag_assets_that_need_attribution() {
		$attribution_asset = new Boldgrid_Inspirations_Attribution_Asset();

		if( empty( $this->assets['image'] ) ) {
			return;
		}

		foreach( $this->assets['image'] as $asset_key => $asset ) {
			if( true === $attribution_asset->needs_attribution( $asset, 'image' ) ) {
				$this->assets['image'][$asset_key]['needs_attribution'] = true;

				//$this->attribution_status['number_of_assets_needing_attribution'] ++;
			}
		}
	}

	/**
	 * Creates the html that will make up the attribution page.
	 *
	 * This function is called from build_attribution_page().
	 *
	 * This function sets the value of $this->attribution_page->post_content.
	 *
	 * It is then up to the build_attribution_page() method to save the page by calling
	 * wp_update_post_attribution_page().
	 */
	public function update_html_of_the_attribution_page_object() {
		include BOLDGRID_BASE_DIR . '/pages/attribution.php';

		$image_attribution_html = '';

		$image_attribution_array = array();

		$html = $attribution_heading;

		$column_css = 'col-xs-12 col-sm-3 col-md-3 col-lg-3 attributed';

		$style = '
				<style>
					.attributed{height:250px;overflow:hidden;}
					.attributed img{max-height:180px;}
				</style>
				<div class="row">
			';


		/*
		 * Create an array of html markup that provides attribution per image.
		 */

		if ( ! empty( $this->assets['image'] ) ) {
			foreach ( $this->assets['image'] as $asset ) {

				if ( isset( $asset['needs_attribution'] ) && true === $asset['needs_attribution'] ) {

					$attribution_details = array(
						'thumbnail' => wp_get_attachment_image_src( $asset['attachment_id'] ),
						'details' => json_decode( $asset['attribution'] ),
					);

					$image_attribution_array[] = $this->set_attribution_html_for_one_item( $attribution_details );
				}
			}

			if ( count( $image_attribution_array ) > 0 ) {
				$image_attribution_html .= $style;

				foreach ( $image_attribution_array as $array_key => $single_image_html ) {
					$image_attribution_html .= sprintf(
						'<div class="%s"> %s </div>',
						$column_css,
						$single_image_html
					);
				}

				$image_attribution_html .= '</div>';
			}
		}




		// If we have HTML to attribute our images, then update $html to include it.
		if ( ! empty( $image_attribution_html ) ) {
			$html .= $attribution_image_heading . $image_attribution_html;

			$in_addition_this = 'In addition, this ';
		} else {
			$in_addition_this = 'This ';
		}

		// Add attribution to WordPress and Inspirations.
		$html .= '<hr />' . sprintf( $attribution_wordpress_and_inspirations, $in_addition_this );

		// Add attribution for additional plugins.
		$html .= $attribution_additional_plugins;

		// Allow the <style> tag on the attribution page.
		add_filter( 'wp_kses_allowed_html',
			array(
				$this,
				'attribution_wp_kses_allowed_html',
			), 1
		);

		$this->attribution_page->post_content = $html;
		wp_update_post( $this->attribution_page );
	}

	/**
	 * Allow the <style> tag on the attribution page.
	 */
	public function attribution_wp_kses_allowed_html( $tags ) {
		if ( ! isset( $tags['style'] ) ) {
			$tags['style'] = array ();
		}

		return $tags;
	}

	/**
	 * Remove count of staged pages.
	 *
	 *
	 * At the top of "All Pages" is a page count, such as All(9).
	 *
	 * "Staged" pages, those created by the BoldGrid Staging plugin, add to this count.
	 *
	 * If you have "staged" pages but don't have the BoldGrid Staging plugin enabed:
	 * The "All" count will not be accurate to the number of pages listed. Staged pages
	 * will count towards the count, but will not show in the list of pages below.
	 */
	public function remove_staging_from_page_count( $counts, $type ) {
		if ( ! is_plugin_active( 'boldgrid-staging/boldgrid-staging.php' ) ) {
			unset( $counts->staging );
		}

		return $counts;
	}

	/**
	 *
	 */
	public function save_post( $post_id ) {


			update_option( 'boldgrid_build_attribution_page', true );


		return;
	}

	/**
	 * Set attribution HTML for one item.
	 *
	 * @param array $attribution_details Attribution details array.
	 *
	 * @return string
	 */
	public function set_attribution_html_for_one_item( $attribution_details ) {
		if ( isset( $attribution_details['details']->license ) &&
		is_numeric( $attribution_details['details']->license ) ) {
			$license_id = $attribution_details['details']->license;
		}

		/*
		 * Create the image to show.
		 */
		if ( isset( $attribution_details['thumbnail'][0] ) ) {
			$image_tag = "<img src='" . $attribution_details['thumbnail'][0] . "' />";
		} else {
			$image_tag = "<img src='http://placehold.it/300x150&text=Image%20not%20available' />";
		}

		/*
		 * Create the link to the image's homepage.
		 */
		if ( isset( $attribution_details['details']->image_homepage ) ) {
			$image_tag = "<a href='" . $attribution_details['details']->image_homepage .
				 "' target='_blank'>" . $image_tag . "</a>";
		}

		/*
		 * Create the link to the author.
		 */
		$author = "<strong>Author</strong>: ";
		if ( isset( $attribution_details['details']->author_username ) ) {
			if ( isset( $attribution_details['details']->author_url ) ) {
				$author .= "<a href='" . $attribution_details['details']->author_url .
					 "' target='_blank'>" . $attribution_details['details']->author_username . "</a>";
			} else {
				$author .= $attribution_details['details']->author_username;
			}
		} else {
			$author .= "<em>Unknown</em>";
		}

		/*
		 * Create the link to the license.
		 */
		$license = "<strong>License</strong>: ";
		if ( isset( $license_id ) && isset( $this->license_details[$license_id] ) ) {
			$license .= "<a href='" . $this->license_details[$license_id]['url'] .
				 "' target='_blank'><img src='" . $this->license_details[$license_id]['icon'] .
				 "' title='" . $this->license_details[$license_id]['name'] . "' /></a>";
		} else {
			$license .= "<em>Unknown license</em>";
		}

		return $image_tag . "<br />" . $author . "<br />" . $license;
	}

	/**
	 * Create an array of attribution page id's.
	 */
	public function set_attribution_page_ids() {
		if ( isset( $this->wp_options_attribution['page']['id'] ) ) {
			$this->attribution_page_ids[] = $this->wp_options_attribution['page']['id'];
		}

		// Allow other plugins to modify this array.
		$this->attribution_page_ids = apply_filters( 'boldgrid_attribution_page_ids',
			$this->attribution_page_ids );

		// Make sure we don't have any duplicate id's in the array.
		$this->attribution_page_ids = array_unique( $this->attribution_page_ids );
	}

	/**
	 * Flickr license id's: https://www.flickr.com/services/api/flickr.photos.licenses.getInfo.html .
	 *
	 * Create Commons icons: https://licensebuttons.net/l/ .
	 */
	public function set_license_details() {
		$this->license_details = array(
			'4' => array(
				'name' => 'Attribution License',
				'icon' => 'https://licensebuttons.net/l/by/2.0/80x15.png',
				'url' => 'http://creativecommons.org/licenses/by/2.0/',
			),
			'5' => array(
				'name' => 'Attribution-ShareAlike License',
				'icon' => 'https://licensebuttons.net/l/by-sa/2.0/80x15.png',
				'url' => 'http://creativecommons.org/licenses/by-sa/2.0/',
			),
			'6' => array(
				'name' => 'Attribution-NoDerivs License',
				'icon' => 'https://licensebuttons.net/l/by-nd/2.0/80x15.png',
				'url' => 'http://creativecommons.org/licenses/by-nd/2.0/',
			)
		);
	}
}
