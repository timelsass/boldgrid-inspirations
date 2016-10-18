<?php
/**
 * BoldGrid Source Code
 *
 * @package Boldgrid_Inspirations_Attribution_Page
 * @copyright BoldGrid.com
 * @version $Id$
 * @author BoldGrid.com <wpb@boldgrid.com>
 */

/**
 * The BoldGrid Attribution Page class.
 *
 * This class includes methods to help work with the Attribution page itself.
 *
 * @since 1.3.1
 */
class Boldgrid_Inspirations_Attribution_Page {

	/**
	 * A language array.
	 *
	 * @since 1.3.1
	 */
	public $lang;

	/**
	 * Constructor.
	 *
	 * @since 1.3.1
	 */
	public function __construct() {
		$this->lang = Boldgrid_Inspirations_Attribution::get_lang();
	}

	/**
	 * Is the current page the Attribution page.
	 *
	 * @since 1.3.1
	 */
	public $is_current = null;

	/**
	 * Add hooks.
	 *
	 * @since 1.3.1
	 */
	public function add_hooks() {
		add_filter( 'post_type_link', array( $this, 'na_remove_slug' ), 10, 3 );

		add_action( 'pre_get_posts', array($this, 'na_parse_request' ) );

		add_action( 'template_redirect', array( $this, 'rebuild' ) );

		add_action( 'template_redirect', array( $this, 'prevent_contamination' ) );

		add_filter( 'boldgrid/display_sidebar', array( $this, 'boldgrid_display_sidebar' ) );

		add_filter( 'single_template', array( $this, 'single_template' ) );

		/*
		 * At this point in the code, we are in the init hook.
		 *
		 * Registering a post type must be done in the init hook, so do that now.
		 */
		self::register_post_type();
	}

	/**
	 * Tell the BoldGrid Theme framework to not display sidebars on the attribution page.
	 *
	 * @since 1.3.1
	 *
	 * @param bool $display False if not displaying sidebars.
	 */
	public function boldgrid_display_sidebar( $display ) {
		return( true === $this->is_current() ? false : $display );
	}

	/**
	 * Get and return the Attribution page.
	 *
	 * If the Attribution page does not exist, create it.
	 *
	 * @since 1.3.1
	 *
	 * $return object|bool If we have an Attribution page, return its page object.
	 */
	public static function get() {
		$lang = Boldgrid_Inspirations_Attribution::get_lang();

		$defaults = array(
			'post_title' => $lang['Attribution'],
			'post_content' => 'Coming soon.',
			'post_type' => $lang['post_type'],
			'post_name' => $lang['attribution'],
			'post_status' => 'publish',
			'page_template' => 'default',
			'comment_status' => 'closed',
		);

		/**
		 * Allow other plugins to modify the Attribution page before it is created.
		 *
		 * For example, if we need to get the Staging Attribution page, allow the Staging plugin
		 * to change the path from attribution to attribution-staging.
		 *
		 * @since 1.3.1
		 *
		 * @param array $defaults.
		 */
		$defaults = apply_filters( 'boldgrid_deployment_pre_insert_post', $defaults );

		// Check to see if the Attribution page has already been created.
		$attribution_page = get_page_by_path( $defaults['post_name'], OBJECT, $lang['post_type'] );

		// If the Attribution page has not already been created, create it.
		if( null === $attribution_page ) {
			$id = wp_insert_post( $defaults );

			if( $id === 0 ) {
				return false;
			}

			$attribution_page = get_page( $id );
		}

		// If we have an attribution page return it, otherwise return false.
		return ( ( null === $attribution_page ) ? false : $attribution_page );
	}

	/**
	 * Is the current page the Attribution page?
	 *
	 * @since 1.3.1
	 *
	 * @global post.
	 *
	 * @return bool The current page is the Attribution page.
	 */
	public function is_current() {
		// If we've already calculated this value, then return it.
		if( ! is_null( $this->is_current ) ) {
			return $this->is_current;
		}

		$attribution_page = $this->get();

		// If we were unable to get the attribution page, then this cannot be the attribution page.
		if( false === $attribution_page ) {
			return false;
		}

		global $post;

		$this->is_current = ( isset( $post->ID ) && $post->ID === $attribution_page->ID );

		return( $this->is_current );
	}

	/**
	 * Remove custom post type from url.
	 *
	 * This is a helper method that helps to make the url /bg_attribution/attribution
	 * simply /attribution.
	 *
	 * @since 1.3.1
	 *
	 * @see http://wordpress.stackexchange.com/questions/203951/remove-slug-from-custom-post-type-post-urls
	 */
	public function na_parse_request( $query ) {
		if ( ! $query->is_main_query() || 2 != count( $query->query ) || ! isset( $query->query['page'] ) ) {
			return;
		}

		if ( ! empty( $query->query['name'] ) ) {
			$query->set( 'post_type', array( 'post', $this->lang['post_type'], 'page' ) );
		}
	}

	/**
	 * Remove custom post type from url.
	 *
	 * This is a helper method that helps to make the url /bg_attribution/attribution
	 * simply /attribution.
	 *
	 * @since 1.3.1
	 *
	 * @see http://wordpress.stackexchange.com/questions/203951/remove-slug-from-custom-post-type-post-urls
	 */
	public function na_remove_slug( $post_link, $post, $leavename ) {
		$post_statuses = array( 'publish', 'staging' );

		/*
		 * If we're not looking at an Attribution page, or this post does not have a
		 * publish/staging status, abort.
		 */
		if ( $this->lang['post_type'] != $post->post_type || ! in_array( $post->post_status, $post_statuses ) ) {
			return $post_link;
		}

		$post_link = str_replace( '/' . $post->post_type . '/', '/', $post_link );

		return $post_link;
	}

	/**
	 * Activation hook.
	 *
	 * When activating BoldGrid Inspirations, any existing Attribution pages, convert them into
	 * bg_attribution page types.
	 *
	 * @since 1.3.1
	 */
	public static function on_activate() {
		self::register_post_type();

		$lang = Boldgrid_Inspirations_Attribution::get_lang();

		$paths = array( $lang['attribution'], $lang['attribution'] . '-staging' );

		foreach( $paths as $path ) {
			$attribution_page = get_page_by_path( $path, OBJECT, 'page' );

			if( null === $attribution_page ) {
				continue;
			} else {
				$attribution_page->post_type = $lang['post_type'];
				wp_update_post( $attribution_page );
			}
		}
	}

	/**
	 * Deactivation hook.
	 *
	 * When BoldGrid Inspirations is deactivated, change the the Attribition page's post_types to
	 * 'page', so the user can edit them if need by.
	 *
	 * @since 1.3.1
	 */
	public static function on_deactivate() {
		$lang = Boldgrid_Inspirations_Attribution::get_lang();

		$paths = array( $lang['attribution'], $lang['attribution'] . '-staging' );

		foreach( $paths as $path ) {
			$attribution_page = get_page_by_path( $path, OBJECT, $lang['post_type'] );

			if( null === $attribution_page ) {
				continue;
			} else {
				$attribution_page->post_type = "page";
				wp_update_post( $attribution_page );
			}
		}
	}

	/**
	 * Prevent viewing of Active Attribution page in Staging environment, and vice versa.
	 *
	 * @since 1.3.1
	 *
	 * @global $post object Post object.
	 */
	public static function prevent_contamination() {
		global $post;

		$lang = Boldgrid_Inspirations_Attribution::get_lang();

		// If we don't have a post status, return.
		if( empty ( $post->post_status ) ) {
			return;
		}

		// If we're not looking at the Attribution page, abort.
		if( $lang['Attribution'] !== $post->post_title ) {
			return;
		}

		$is_contaminated = false;

		$is_contaminated = apply_filters( 'boldgrid_staging_is_contaminated', $post->post_status );

		if( true === $is_contaminated ) {
			global $wp_query;
			$wp_query->set_404();
			status_header( 404 );
		}
	}

	/**
	 * Rebuild the Attribution page.
	 *
	 * @since 1.3.1
	 */
	public function rebuild() {
		// If we don't need to rebuild the Attribution page, abort.
		if( false === get_option( 'boldgrid_attribution_rebuild' ) ) {
			return;
		}

		$attribution_page = Boldgrid_Inspirations_Attribution_Page::get();

		if( true === $this->is_current() ) {
			$attribution = new Boldgrid_Inspirations_Attribution();
			$attribution->build_attribution_page();

			/*
			 * We just built the Attribution page, so no need to build it again. Delete the flag
			 * that tells us to rebuild.
			 */
			delete_option( 'boldgrid_attribution_rebuild' );

			/*
			 * The Attribution page has been rebuilt. Because of hook order, if we continue loading
			 * the current page, it will not be the page we just built. Refresh the page so we'll
			 * see the new Attribution page on the next load.
			 */

			header('Location: '.$_SERVER['REQUEST_URI']);
			die();
		}
	}

	/**
	 * Register our custom post type for Attribution pages.
	 *
	 * @since 1.3.1
	 */
	public static function register_post_type() {
		$lang = Boldgrid_Inspirations_Attribution::get_lang();

		$args = array(
			'public' => true,
			'publicly_queryable' => false,
			'query_var' => true,
			'show_ui' => false,
			'show_in_nav_menus' => false,
			'show_in_menu' => false,
			'exclude_from_search' => true,
		);

		register_post_type( $lang['post_type'], $args );

		/*
		 * If this is our first time registering this custom post type, we need to flush the rewrite
		 * rules.
		 */
		if( false === get_option( 'boldgrid_attribution_upgraded_to_cpt' ) ) {
			flush_rewrite_rules();
			update_option( 'boldgrid_attribution_upgraded_to_cpt', true );
		}
	}

	/**
	 * Adjust the template for a single page.
	 *
	 * If this is the Attribution page, return null so that index.php will be used.
	 *
	 * @since 1.3.1
	 *
	 * @param string $original
	 */
	public function single_template( $original ) {
		return ( true === $this->is_current() ? null : $original );
	}
}