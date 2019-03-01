<?php
/**
 * BoldGrid Source Code
 *
 * @package Boldgrid_Inspirations_Deploy
 * @copyright BoldGrid.com
 * @version $Id$
 * @author BoldGrid <support@boldgrid.com>
 */

/**
 * BoldGrid Inspirations Deploy class.
 */
class Boldgrid_Inspirations_Deploy {
	/**
	 * BoldGrid configs array.
	 *
	 * @access protected
	 *
	 * @var array $configs
	 */
	protected $configs;

	/**
	 * A list of all the installed pages.
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected $full_page_list;

	/**
	 * Is this a generic build?
	 *
	 * @access protected
	 *
	 * @var bool
	 */
	protected $is_generic = false;

	/**
	 * Is this a preview server?
	 *
	 * @access protected
	 *
	 * @var bool
	 */
	protected $is_preview_server = false;

	/**
	 * Default post status.
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $post_status = 'publish';

	/**
	 * Subcategory ID.
	 *
	 * @access protected
	 *
	 * @var int
	 */
	protected $subcategory_id = null;

	/**
	 * Class property for the asset cache object (only for preview servers).
	 *
	 * @since 1.1.2
	 * @access private
	 *
	 * @var object|null
	 */
	private $asset_cache = null;

	/**
	 * The Boldgrid Inspirations Asset Manager class object.
	 *
	 * @var Boldgrid_Inspirations_Asset_Manager
	 */
	private $asset_manager;

	/**
	 * Asset user_id.
	 *
	 * @since 1.7.0
	 * @access private
	 * @var int
	 */
	private $asset_user_id;

	/**
	 * The Boldgrid Inspirations Built Photo Search class object.
	 *
	 * @var Boldgrid_Inspirations_Built_Photo_Search
	 */
	private $built_photo_search;

	/**
	 * Build profile id.
	 *
	 * @since 1.7.0
	 * @access private
	 * @var int
	 */
	private $boldgrid_build_profile_id;

	/**
	 * Coin budget.
	 *
	 * @since 1.7.0
	 * @access private
	 * @var int
	 */
	private $coin_budget;

	/**
	 * Whether or not to create a preview site.
	 *
	 * Used only on the preview server.
	 *
	 * @since 1.7.0
	 * @access private
	 * @var bool
	 */
	private $create_preview_site;

	/**
	 * Current build cost.
	 *
	 * @since 1.7.0
	 * @access private
	 * @var int
	 */
	private $current_build_cost = 0;

	/**
	 * Custom pages.
	 *
	 * Optional. Passing this array of page id's will install only these pages.
	 *
	 * @since 1.7.0
	 * @access private
	 * @var bool
	 */
	private $custom_pages;

	/**
	 * Is author.
	 *
	 * @since 1.7.0
	 * @access private
	 * @var bool
	 */
	private $is_author;

	/**
	 * Language id.
	 *
	 * Used when getting built_photos_search photos.
	 *
	 * @since 1.7.0
	 * @access private
	 * @var int
	 */
	private $language_id;

	/**
	 * An instance of Boldgrid_Inspirations_Deploy_Messages
	 *
	 * @since 1.7.0
	 * @access private
	 * @var Boldgrid_Inspirations_Deploy_Messages
	 */
	private $messages;

	/**
	 * New path.
	 *
	 * Required only on preview server.
	 *
	 * @since 1.7.0
	 * @access private
	 * @var bool
	 */
	private $new_path;

	/**
	 * Page set id to install.
	 *
	 * Required. This tells us which individual pages to download.
	 *
	 * @access private
	 * @var int
	 */
	private $page_set_id;

	/**
	 * Page set version type.
	 *
	 * Such as 'active'.
	 *
	 * @since 1.7.0
	 * @access private
	 * @var string
	 */
	private $page_set_version_type;

	/**
	 * Primary design elements.
	 *
	 * Used to get primary display elements.
	 *
	 * @since 1.7.0
	 * @access private
	 * @var mixed
	 */
	private $pde;

	/**
	 * The time the deployment began.
	 *
	 * Start time is used to measure deployment time, which is returned to the asset server.
	 *
	 * @since 1.7.0
	 * @access private
	 * @var int
	 */
	private $start_time;

	/**
	 * Theme id.
	 *
	 * Required.
	 *
	 * @since 1.7.0
	 * @access private
	 * @var int
	 */
	private $theme_id;

	/**
	 * Theme version type.
	 *
	 * Such as 'active'.
	 *
	 * @since 1.7.0
	 * @access private
	 * @var string
	 */
	private $theme_version_type;

	/**
	 * Ticket number.
	 *
	 * Only required on author server.
	 *
	 * @since 1.7.0
	 * @access private
	 * @var int
	 */
	private $ticket_number;

	/**
	 * The BoldGrid Forms class object.
	 *
	 * @since 1.4.8
	 *
	 * @var \Boldgrid\Library\Form\Forms
	 */
	public $bgforms;

	/**
	 * Install a sample blog.
	 *
	 * @since  1.3.6
	 * @access public
	 * @var    bool True to install a sample blog.
	 */
	public $install_blog = false;

	/**
	 * A variable to store the menu_id that we create using:
	 * $menu_id = wp_create_nav_menu( 'primary' );
	 *
	 * @access protected
	 *
	 * @var int
	 */
	public $primary_menu_id;

	/**
	 * As the installation process runs,
	 * we will record data about the plugins that are installed.
	 *
	 * @access protected
	 *
	 * @var array
	 */
	public $plugin_installation_data = array();

	/**
	 * Does the user want to start over before deployment?
	 *
	 * @since 1.2.3
	 * @access public
	 */
	public $start_over = false;

	/**
	 * Instance of the Survey class.
	 *
	 * @since  1.3.6
	 * @access public
	 * @var    Boldgrid_Inspirations_Survey
	 */
	public $survey;

	/**
	 * Tags containing background images.
	 *
	 * When importing pages, certain tags will have background images set within their style that
	 * we'll need to download.
	 *
	 * @since  1.4.3
	 * @access public
	 * @var    array
	 */
	public $tags_having_background = array( 'div' );

	/**
	 * Constructor.
	 *
	 * @see \Boldgrid\Library\Form\Forms()
	 *
	 * @param array $configs BoldGrid configuration array.
	 */
	public function __construct( $configs ) {
		$this->start_time = time();

		$this->configs = $configs;

		require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations-deploy-pages.php';

		require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations-asset-manager.php';
		$this->asset_manager = new Boldgrid_Inspirations_Asset_Manager();

		$this->install_blog = isset( $_REQUEST['install-blog'] ) && 'true' === $_REQUEST['install-blog'];

		$this->survey = new Boldgrid_Inspirations_Survey();

		$this->asset_cache = $this->asset_manager->get_asset_cache();

		$this->messages = new Boldgrid_Inspirations_Deploy_Messages();

		require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations-built-photo-search.php';
		$this->built_photo_search = new Boldgrid_Inspirations_Built_Photo_Search();

		$this->built_photo_search_log          = array ();
		$this->built_photo_search_log['count'] = 0;

		require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations-external-plugin.php';
		$this->external_plugin = new Boldgrid_Inspirations_External_Plugin();

		add_filter( 'http_request_host_is_external', array( $this, 'allow_downloads_over_the_backlan' ), 10, 3 );

		$this->bgforms = new Boldgrid\Library\Form\Forms();

		$deploy_image = new BoldGrid_Inspirations_Deploy_Image();
		$deploy_image->add_hooks();
	}

	/**
	 * Get the api key hash.
	 *
	 * @since 1.7.0
	 *
	 * @todo This method can probably go into another class. Created during 1.7.0 while doing some
	 * code cleanup.
	 *
	 * @return string
	 */
	public function get_api_key_hash() {
		$api_key_hash = $this->asset_manager->api->get_api_key_hash();

		// If the hash is missing, then try getting it from the configs.
		if ( empty( $api_key_hash ) ) {
			$api_key_hash = isset( $this->configs['api_key'] ) ? sanitize_text_field( $this->configs['api_key'] ) : null;
		}

		// If the hash is still not found, then check $_REQUEST['key'].
		if ( empty( $api_key_hash ) && ! empty( $_REQUEST['key'] ) ) {
			$api_key_hash = sanitize_text_field( $_REQUEST['key'] );
		}

		return $api_key_hash;
	}

	/**
	 * Getter for configs array.
	 *
	 * @return array $configs
	 */
	public function get_configs() {
		return $this->configs;
	}

	/**
	 * Setter for configs array.
	 *
	 * @param array $configs
	 *
	 * @return bool
	 */
	public function set_configs( $configs ) {
		$this->configs = $configs;
		return true;
	}

	/**
	 * Get deploy details.
	 *
	 * Get all of the details needed so we can deploy a new website.
	 * For example, we need to know which theme to install, which category, etc.
	 *
	 * @todo We are hard coding the details below. In the future, the values
	 *       will be grabbed from the options table.
	 *
	 * @see Boldgrid_Inspirations_Api::get_api_key_hash().
	 */
	public function get_deploy_details() {
		$boldgrid_configs = $this->get_configs();

		$this->page_set_id = intval( $_POST['boldgrid_page_set_id'] );

		$api_key_hash = $this->get_api_key_hash();

		$this->set_site_hash();

		$this->theme_id = intval( $_POST['boldgrid_theme_id'] );

		$this->set_pde();

		$this->set_subcategory_id();

		$this->language_id = ( isset( $_POST['boldgrid_language_id'] ) ? intval( $_POST['boldgrid_language_id'] ) : null );

		$this->asset_user_id = ( isset( $_POST['asset_user_id'] ) ? intval( $_POST['asset_user_id'] ) : null );

		$this->new_path = ( isset( $_POST['boldgrid_new_path'] ) ? trim( $_POST['boldgrid_new_path'] ) : '' );

		$this->create_preview_site = ( isset( $_POST['create_preview_site'] ) && $_POST['create_preview_site'] ? true : false );

		$this->ticket_number = ( isset( $_POST['boldgrid_ticket_number'] ) ? sanitize_text_field( $_POST['boldgrid_ticket_number'] ) : false );

		$this->custom_pages = $this->get_pages_param();

		$is_preview_server        = $boldgrid_configs['preview_server'] == 'https://' . $_SERVER['SERVER_NAME'];
		$is_author_preview_server = $boldgrid_configs['author_preview_server'] == 'https://' . $_SERVER['SERVER_NAME'];
		$this->is_preview_server  = $is_preview_server || $is_author_preview_server;

		$this->current_build_cost = 0;

		$this->coin_budget = isset( $_POST['coin_budget'] ) ? intval( $_POST['coin_budget'] ) : 20;

		$this->theme_version_type = isset( $_POST['boldgrid_theme_version_type'] ) ? sanitize_text_field( $_POST['boldgrid_theme_version_type'] ) : 'active';

		$this->page_set_version_type = isset( $_POST['boldgrid_page_set_version_type'] ) ? sanitize_text_field( $_POST['boldgrid_page_set_version_type'] ) : 'active';

		$this->is_author = isset( $_POST['author_type'] ) ? true : false;

		$this->boldgrid_build_profile_id = isset( $_POST['boldgrid_build_profile_id'] ) ? intval( $_POST['boldgrid_build_profile_id'] ) : null;

		if( $this->is_preview_server && isset( $_POST['is_generic'] ) && '1' === $_POST['is_generic'] ) {
			$this->is_generic = true;
		}

		if( isset( $_POST['start_over'] ) && 'true' === $_POST['start_over'] ) {
			$this->start_over = true;
		}

		/**
		 * Filter $this->tags_having_background.
		 *
		 * For example, authors should not process background images.
		 *
		 * @since 1.4.5
		 *
		 * @param array $this->tags_having_background
		 * @param bool  $this->is_author
		 */
		$this->tags_having_background = apply_filters( 'boldgrid_deploy_background_tags', $this->tags_having_background, $this->is_author );
	}

	/**
	 * Sets Deploy Options.
	 *
	 * Store these install options for later use.
	 */
	public function update_install_options() {
		$boldgrid_install_options = array(
			'author_type'           => isset( $_POST['author_type'] ) ? sanitize_text_field( $_POST['author_type'] ) : null,
			'language_id'           => isset( $_POST['language_id'] ) ? intval( $_POST['language_id'] ) : null,
			'theme_group_id'        => isset( $_POST['theme_group'] ) ? sanitize_text_field( $_POST['theme_group'] ) : null,
			'theme_id'              => intval( $this->theme_id ),
			'theme_version_type'    => sanitize_text_field( $this->theme_version_type ),
			'category_id'           => isset( $_POST['boldgrid_cat_id'] ) ? intval( $_POST['boldgrid_cat_id'] ) : null,
			'subcategory_id'        => intval( $this->subcategory_id ),
			'page_set_id'           => intval( $this->page_set_id ),
			'page_set_version_type' => sanitize_text_field( $this->page_set_version_type ),
			'pde'                   => $this->pde,
			'new_path'              => trim( $this->new_path ),
			'ticket_number'         => sanitize_text_field( $this->ticket_number ),
			'build_profile_id'      => intval( $this->boldgrid_build_profile_id ),
			'custom_pages'          => $this->custom_pages,
			'install_timestamp'     => time(),
		);

		update_option( 'boldgrid_install_options', $boldgrid_install_options );
	}

	/**
	 * Grab installation details from the asset server.
	 *
	 * This method is intended to retrieve options in bulk instead of retrieving install data
	 * 1 call at a time.
	 *
	 * @since 1.1.2
	 * @see Boldgrid_Inspirations_Api::get_api_key_hash().
	 *
	 * @return array Array of pages.
	 */
	public function remote_install_options() {
		// Get configs.
		$boldgrid_install_options = get_option( 'boldgrid_install_options' );
		$boldgrid_configs = $this->get_configs();

		// Reach out to the asset server to get a collection of install options.
		$get_install_details = $boldgrid_configs['asset_server'] .
			 $boldgrid_configs['ajax_calls']['get_install_details'];

		// Get the API key hash.
		$api_key_hash = $this->asset_manager->api->get_api_key_hash();

		$arguments = array (
			'method'  => 'POST',
			'body'    => array (
				'subcategory_id' => $boldgrid_install_options['subcategory_id'],
				'page_set_id'    => $boldgrid_install_options['page_set_id'],
				'key'            => ! empty( $api_key_hash ) ? $api_key_hash : null
			),
			'timeout' => 20
		);

		$response = wp_remote_retrieve_body( wp_remote_post( $get_install_details, $arguments ) );
		$response = json_decode( $response ?  : '', true );

		$remote_options = ( ! empty( $response['result']['data'] ) ? $response['result']['data'] : array() );

		$boldgrid_install_options = array_merge( $boldgrid_install_options, $remote_options );
		update_option( 'boldgrid_install_options', $boldgrid_install_options );
	}

	/**
	 * Get pages from POST request and return them in an array.
	 *
	 * @return array Array of pages.
	 */
	public function get_pages_param() {
		$pages = array ();

		if ( isset( $_POST['pages'] ) ) {
			$pages = is_array( $_POST['pages'] ) ? $_POST['pages'] : json_decode( stripslashes( trim( $_POST['pages'] ) ), true );
		}

		return $pages;
	}

	/**
	 * Get image data for an image that will replace a placeholder.
	 *
	 * This method helps solve a long time bug. Images used to be called in this manner:
	 * $this->image_placeholders_needing_images['by_page_id'][$page->ID][$asset_image_position]['attachment_url'];
	 *
	 * The problem is that $asset_image_position in that array is simply an auto incremented key.
	 * It's not actually the $asset_image_position or $bps_image_position like we intended.
	 *
	 * This method loops through all the images until it finds the correct $type and $position.
	 *
	 * @since 1.3.2
	 *
	 * @param  int        $page_id
	 * @param  string     $type Either 'asset_image_position' or 'bps_image_position'.
	 * @param  int        $position
	 * @return array|null
	 */
	public function get_placeholder_image( $page_id, $type, $position ) {
		foreach( $this->image_placeholders_needing_images['by_page_id'][$page_id] as $image ) {
			if( isset( $image[$type] ) && $position === $image[$type] ) {
				return $image;
			}
		}

		return null;
	}

	/**
	 * Get remote page id from local page id.
	 *
	 * @param integer $page_id
	 */
	public function get_remote_page_id_from_local_page_id( $page_id ) {
		foreach ( $this->installed_page_ids as $remote_page_id => $local_page_id ) {
			if ( $local_page_id == $page_id ) {
				return $remote_page_id;
			}
		}
	}

	/**
	 * Set our site hash.
	 *
	 * @since 1.7.0
	 */
	public function set_site_hash() {
		$this->site_hash = ( isset( $_REQUEST['site_hash'] ) ? sanitize_title_with_dashes( trim( $_REQUEST['site_hash'] ) ) : null );

		$this->site_hash = ( ( null == $this->site_hash && isset( $boldgrid_configs['site_hash'] ) ) ? sanitize_title_with_dashes( $boldgrid_configs['site_hash'] ) : $this->site_hash );
	}

	/**
	 * Set subcategory id.
	 *
	 * @since 1.7.0
	 *
	 * @todo subcategory_id is used in deploy_page_sets to get homepage data. Should this actually
	 *       be category_id ?
	 */
	private function set_subcategory_id() {
		$this->subcategory_id = null;

		if ( ! empty( $_POST['boldgrid_sub_cat_id'] ) ) {
			// For most requests:
			$this->subcategory_id = intval( $_POST['boldgrid_sub_cat_id'] );
		} elseif ( ! empty( $_POST['subcategory_id'] ) ) {
			// For direct call to deploy_page_sets:
			$this->subcategory_id = intval( $_POST['subcategory_id'] );
		} else {
			// If subcategory is not available in POST, then try to get it from the install options:
			if ( $this->is_staging_install() ) {
				$install_options = get_option( 'boldgrid_staging_boldgrid_install_options' );
			} else {
				$install_options = get_option( 'boldgrid_install_options' );
			}

			if ( ! empty ( $install_options['subcategory_id'] ) ) {
				$this->subcategory_id = $install_options['subcategory_id'];
			}
		}
	}

	/**
	 * Create a new site.
	 *
	 * If we're on the preview server, create a new site
	 */
	public function create_new_install() {
		if ( $this->is_preview_server && $this->create_preview_site ) {
			$blog_title = 'Company Name';

			// create the new blog
			$new_blog_id = wpmu_create_blog( $_SERVER['SERVER_NAME'], '/' . $this->new_path, $blog_title, get_current_user_id() );

			if ( is_object( $new_blog_id ) ) {
				echo '<pre>' . print_r( $new_blog_id, 1 ) . '</pre>';
			}

			switch_to_blog( $new_blog_id );

			// Set the blog's admin email address using the network admin email address.
			$email_address = get_site_option( 'admin_email' );

			update_option( 'admin_email' , $email_address );

			// If this is a generic build, then set an option to identify it later (purges, etc.).
			if ( $this->is_generic ) {
				update_option( 'is_generic_build', true );
			}

			// Ensure that we have the current boldgrid_asset information (should be empty).
			$this->asset_manager->get_wp_options_asset();

			// Site needs to be https.
			$path_to_new_blog = esc_url( 'https://' . $_SERVER['SERVER_NAME'] . '/' . $this->new_path );
			update_option( 'siteurl', $path_to_new_blog );
			update_option( 'home', $path_to_new_blog );
			update_option( 'upload_url_path', $path_to_new_blog . '/wp-content/uploads' );

			// Disable comments:
			update_option( 'default_comment_status', 'closed' );
		}
	}

	/**
	 * http://stackoverflow.com/questions/10589889/returning-header-as-array-using-curl
	 *
	 * @param string $response
	 *
	 * @return array
	 */
	public function curl_response_arrayify( $response ) {
		$headers = array();
		$file    = array();

		$strpos_rnrn = strpos( $response, "\r\n\r\n" );

		$header_text = substr( $response, 0, $strpos_rnrn );
		$body = substr( $response, $strpos_rnrn );

		foreach ( explode( "\r\n", $header_text ) as $i => $line ) {
			if ( $i === 0 )
				$headers['http_code'] = $line;
			else {
				list ( $key, $value ) = explode( ': ', $line );

				$headers[strtolower( $key )] = $value;
			}
		}

		$file['headers'] = $headers;
		$file['body'] = trim( $body );

		return $file;
	}

	/**
	 * Delete sample pages.
	 *
	 * @since 1.7.0
	 *
	 * @link https://wordpress.org/support/topic/remove-default-pages-created-on-all-multisites
	 */
	private function delete_sample_pages() {
		$defaultPage = get_page_by_title( __( 'Sample Page' ) );
		if ( $defaultPage ) {
			wp_delete_post( $defaultPage->ID );
		}

		$defaultPage = get_page_by_title( __( 'Hello world!' ), OBJECT, 'post' );
		if ( $defaultPage ) {
			wp_delete_post( $defaultPage->ID );
		}
	}

	/**
	 * Update a site (network) option until successful or timeout.
	 * Same as update_site_option, except with a retry feature with a timeout.
	 * Also returns true if the old value matches the new value, instead of false.
	 *
	 * @param string $option Option name.
	 * @param mixed $value Option value.
	 * @param int $timeout A timeout in seconds. Default is 5 seconds.
	 *
	 * @return bool
	 */
	public function update_site_option_retry( $option = null, $value = null, $timeout = 5 ) {
		// Validate input:
		if ( empty( $option ) || empty( $value ) || ! is_numeric( $timeout ) || $timeout < 0 ) {
			return false;
		}

		// If the current value matches the new value, then return true:
		if ( get_site_option( $option, false, false ) == $value ) {
			return true;
		}

		$start_time = time();
		$success    = false;
		$deadline   = $start_time + $timeout;

		while ( ! $success && time() < $deadline ) {
			if ( update_site_option( $option, $value ) ) {
				// Success: Return true:
				return true;
			}

			usleep( rand( 150000, 250000 ) );
		}

		return false;
	}

	/**
	 * Download the theme chosen by the user and set it as the active theme
	 *
	 * @todo Refactor/rework this method. It should be moved to Boldgrid_Inspirations_Theme_Install.
	 *
	 * @see Boldgrid_Inspirations_Api::get_api_key_hash().
	 *
	 * @return string or false
	 */
	public function deploy_theme() {
		// Get configs:
		$boldgrid_configs = $this->get_configs();

		// Connect to the asset server and get all of the details for our theme.
		$url_to_get_theme_details = $boldgrid_configs['asset_server'] .
			 $boldgrid_configs['ajax_calls']['get_theme_details'];

		// Get the API key hash.
		$api_key_hash = $this->asset_manager->api->get_api_key_hash();

		$arguments = array (
			'method' => 'POST',
			'body' => array (
				'theme_id' => $this->theme_id,
				'page_set_id' => $this->page_set_id,
				'theme_version_type' => $this->theme_version_type,
				'is_preview_server' => $this->is_preview_server,
				'build_profile_id' => $this->boldgrid_build_profile_id,
				'is_staged' => ! empty( $_POST['staging'] ) ? trim( $_POST['staging'] ) : null,
				'key' => ! empty( $api_key_hash ) ? $api_key_hash : null,
				'site_hash' => ! empty( $boldgrid_configs['site_hash'] ) ? $boldgrid_configs['site_hash'] : null
			),
			'timeout' => 20
		);

		$response = wp_remote_post( $url_to_get_theme_details, $arguments );

		if ( is_wp_error( $response ) ) {
			// LOG:
			error_log(
				__METHOD__ .
					 ': Error: Received WP_Error in wp_remote_post to the asset server. Response: ' .
					 print_r( $response, true ) );

			// Unrecoverable error:
			$this->messages->print_notice( esc_html__( 'Error: Failed to retrieve theme!', 'boldgrid-inspirations' ) );

			return false;
		}

		$this->theme_details = json_decode( $response['body'] );

		if ( ! isset( $this->theme_details->status ) || 200 != $this->theme_details->status ) {
			$this->messages->print_notice( esc_html__( 'Error: Received an unsuccessful return code when retrieving theme information!', 'boldgrid-inspirations' ) );
			return;
		}

		$this->theme_details = $this->theme_details->result->data;

		/*
		 * Temporarily save the theme details.
		 *
		 * When the deployment script was initially written, $this->theme_details was always used
		 * under the assumption that there was no parent / child themes being used.
		 *
		 * $this->theme_details is used in several places within the deployment script. Other than
		 * this deploy_theme method, $this->theme_details is always referring to a single theme
		 * (ie not a parent and a child theme)
		 *
		 * Whew... So basically, if this is the child theme, store the theme_details temporarily.
		 * After the deploy_theme method is complete, set
		 * $this->theme_details = $this->child_theme_details
		 */
		$this->theme_details_original = $this->theme_details;

		$this->messages->print_theme( $this->theme_details );

		// If this is a site preview, set the site title to that of the theme.
		if( $this->is_preview_server && isset( $this->theme_details->themeRevision->Title ) ) {
			update_option( 'blogname', $this->theme_details->themeRevision->Title );
		}

		foreach ( array (
			'child',
			//'parent'
		) as $entity ) {
			if ( 'parent' == $entity ) {
				$this->theme_details = $this->theme_details->parent;
				// If parent doesnt exists, continue (skip this iteration)
				if ( empty( $this->theme_details ) ) {
					continue;
				}
			}

			// Theme Folder name is the same as theme name.
			$theme_folder_name = $this->theme_details->theme->Name;

			if ( $this->is_preview_server ) {
				// Use the random filename instead.
				$theme_folder_name = wp_basename( $this->theme_details->themeAssetFilename, '.zip' );
			}

			$theme = wp_get_theme( $theme_folder_name );

			// Get the installed theme version timestamp from wp options:
			$theme_version_option_name = 'boldgrid_theme_revision_' .
				 $this->theme_details->themeRevision->Title;

			$theme_dir = ABSPATH . 'wp-content/themes/' . $theme_folder_name;
			$theme_dir_exists = is_dir( $theme_dir );

			if ( is_multisite() ) {
				$installed_theme_version = get_site_option( $theme_version_option_name, null,
					false );

				if ( $installed_theme_version && ! $theme_dir_exists ) {
					delete_site_option( $theme_version_option_name );

					$installed_theme_version = null;
				}
			} else {
				$installed_theme_version = get_option( $theme_version_option_name );

				if ( $installed_theme_version && ! $theme_dir_exists ) {
					delete_option( $theme_version_option_name );

					$installed_theme_version = null;
				}
			}

			/*
			 * If attempting to install over a .git directory, don't install theme. Only do this if
			 * is author because if a git is accidently commited, theme will not install for anyone.
			 */
			$is_git_theme = false;
			if ( $theme_dir_exists && $this->is_author && ! $this->is_preview_server ) {
				$is_git_theme = in_array( '.git', scandir( $theme_dir ) );
			}

			$incoming_theme_version   = $this->theme_details->themeRevision->RevisionNumber;
			$incoming_version_number  = ! empty( $this->theme_details->themeRevision->VersionNumber ) ? $this->theme_details->themeRevision->VersionNumber : null;
			$installed_version_number = is_object( $theme ) ? $theme->get('Version') : null;
			$is_version_change        = $incoming_version_number && ( $incoming_version_number != $installed_version_number );
			$install_this_theme       = ( $is_version_change || ! $theme_dir_exists ) && ! $is_git_theme;

			// Check if theme is already installed and the latest version:
			if ( $install_this_theme ) {
				$theme_url = $boldgrid_configs['asset_server'] .
					 $boldgrid_configs['ajax_calls']['get_asset'] . '?id=' .
					 $this->theme_details->themeRevision->AssetId;

				if ( ! empty( $api_key_hash ) ) {
					$theme_url .= '&key=' . $api_key_hash;
				}

				// If this is a user environment, install for repo.boldgrid.com.
				if ( ! $this->is_preview_server ) {
					$theme_url = $this->theme_details->repo_download_link;
				}

				$theme_installation_done = false;
				$theme_installation_failed_attemps = 0;

				while ( false == $theme_installation_done ) {
					if ( is_multisite() && $this->is_preview_server ) {
						global $wp_version;

						/*
						 * If WordPress >=4.4.0, flush the WordPress object cache, or rely on the
						 * 3rd parameter of get_site_option as false to disable cache.
						 */
						if ( version_compare( $wp_version, '4.4.0', '>=' ) ) {
							wp_cache_flush();
						}

						// Get the WP Option boldgrid_we_are_currently_installing_a_theme:
						$we_are_currently_installing_a_theme = get_site_option(
							'boldgrid_we_are_currently_installing_a_theme', false, false
						);

						if ( $theme_installation_failed_attemps >
							$boldgrid_configs['installation']['max_num_install_attempts'] ) {

							$this->messages->print_notice( esc_html__( 'Error: Exceeded max theme install attempts!', 'boldgrid-inspirations' ) );

							return false;
						}

						/*
						 * Should we install this theme?
						 *
						 * For example, if we're already installing a different theme, then we'll
						 * want to wait before that completes before we install this theme.
						 */
						$theme_install_wait_time = 20;

						if ( false == $we_are_currently_installing_a_theme ) {
							$installed_theme_version = get_site_option( $theme_version_option_name, null, false );

							// Check the current theme version against the incoming version:
							$install_this_theme = ( $installed_theme_version != $incoming_theme_version );

							// Latest theme already installed, so break out of the while loop.
							if ( ! $install_this_theme ) {
								break;
							}
						} elseif ( time() - $we_are_currently_installing_a_theme <
							 $theme_install_wait_time ) {
							// The last install was initiated within the last 20 seconds,
							// so wait a little longer.
							$install_this_theme = false;
						} else {
							/*
							 * The last install theme install fatally failed and/or was more than 20
							 * seconds ago. Either way, try to install this theme.
							 */
							$install_this_theme = true;
						}

						/*
						 * If we do have the 'go ahead' to install a this theme right now, let's try
						 * to 'lock it' so that other themes aren't installed this very moment.
						 */
						if ( true == $install_this_theme ) {
							if ( ! $this->update_site_option_retry( 'boldgrid_we_are_currently_installing_a_theme', time() ) ) {
								$install_this_theme = false;
							} else {
								$this->update_site_option_retry( 'boldgrid_we_are_currently_installing_this_theme', $this->theme_details->theme->Name );
							}
						}

						/*
						 * Multiple themes could be locking at the same time. Let's make sure the
						 * current theme is the the theme that set the lock.
						 */
						if ( true == $install_this_theme ) {
							$we_are_currently_installing_this_theme = get_site_option( 'boldgrid_we_are_currently_installing_this_theme', false, false );
							if ( $this->theme_details->theme->Name != $we_are_currently_installing_this_theme ) {
								$install_this_theme = false;
							}
						}
					} else {
						// Else, we are not on a multisite, so go ahead and try to install the theme.
						$install_this_theme = true;
					}

					/*
					 * If we ultimately decided not to attempt theme installation at this second,
					 * sleep for a bit and try again.
					 */
					if ( false == $install_this_theme ) {
						sleep( 1 );

						$theme_installation_failed_attemps += 0.5;
					} else {
						// Delete the old theme, if exists. We'll update to the latest copy.
						if ( $theme->exists() ) {
							delete_theme( $theme_folder_name );
						}

						// Install the theme:
						include_once ABSPATH . 'wp-admin/includes/file.php';
						include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

						$upgrader = new Theme_Upgrader( new Theme_Installer_Skin( compact( 'title', 'url', 'nonce', 'plugin', 'api' ) ) );

						// Download and install the theme:
						$wp_theme_install_success = $upgrader->install( $theme_url,
							array (
								'clear_destination' => true,
								'abort_if_destination_exists' => false
							) );

						$this->theme_name = $upgrader->result['destination_name'];

						// If Theme_Upgrader::install reports failure or we have no theme name, then
						// something went wrong.
						if ( ( ! $wp_theme_install_success || empty( $this->theme_name ) ) ) {
							delete_theme( $this->theme_details->theme->Name );

							$theme_installation_failed_attemps ++;

							$this->messages->print_notice( esc_html__( 'Error: Exiting theme deployment.', 'boldgrid-inspirations' ) );

							// On multisite, remove locks.
							if ( is_multisite() ) {
								$we_are_currently_installing_this_theme = get_site_option( 'boldgrid_we_are_currently_installing_this_theme', false, false );

								if ( $this->theme_details->theme->Name === $we_are_currently_installing_this_theme ) {
									delete_site_option( 'boldgrid_we_are_currently_installing_this_theme' );
									delete_site_option( 'boldgrid_we_are_currently_installing_a_theme' );
								}
							}

							return false;
						} else {
							// Looks like the theme was installed successfully.
							$theme_installation_done = true;

							// Set wp options to mark the newly-installed them version.
							if ( is_multisite() ) {
								$this->update_site_option_retry( $theme_version_option_name, $incoming_theme_version );
							} else {
								update_option( $theme_version_option_name, $incoming_theme_version );
							}
						}

						// Regardless of whether we failed or succeeded, we're no longer installing.
						if ( is_multisite() ) {
							delete_site_option( 'boldgrid_we_are_currently_installing_a_theme' );
							delete_site_option( 'boldgrid_we_are_currently_installing_this_theme' );
						}
					}
				} // End of while.
			}

			// Enable Theme Sitewide.
			$allowed_themes = get_site_option( 'allowedthemes' );
			$allowed_themes[$theme_folder_name] = true;
			$this->update_site_option_retry( 'allowedthemes', $allowed_themes );

			if ( 'child' == $entity ) {
				// Save the theme id as a theme mod.
				$this->set_theme_mod_id( $theme_folder_name, $this->theme_details->theme->Id );

				$activation_theme = $theme_folder_name;

				// For authors, activate the git repo instead of the theme.
				if ( $this->is_author && ! empty( $this->theme_details->theme->GitRepoUrl ) ) {
					$repo_name = basename( $this->theme_details->theme->GitRepoUrl );
					if ( file_exists( get_theme_root() . '/' . $repo_name ) ) {
						$activation_theme = $repo_name;
					}
				}

				// Activate the theme.
				switch_theme( $activation_theme );
				update_option( Boldgrid_Inspirations_Deploy_Theme::$theme_deployed, $activation_theme );
			}

			// Enable theme options:
			if ( isset( $this->theme_details->options ) ) {
				foreach ( $this->theme_details->options as $option_k => $option_obj ) {
					update_option( $option_obj->name, $option_obj->value, '', $option_obj->autoload );
				}
			}
		} // foreach( array ( 'child', 'parent' ) as $entity )

		// Reset the $this->theme_details variable. Refer to loooon comment above as to why.
		$this->theme_details = $this->theme_details_original;

		do_action( 'boldgrid_deployment_deploy_theme_pre_return', $theme_folder_name );

		return $theme_folder_name;
	}

	/**
	 * Set the theme id of the given theme, as a theme mod
	 *
	 * @param string $theme_name
	 * @param int    $theme_id
	 */
	public function set_theme_mod_id( $theme_name, $theme_id ) {
		$theme_mods = get_option( 'theme_mods_' . $theme_name );
		if ( ! $theme_mods ) {
			$theme_mods = array ();
		}

		$theme_mods['_boldgrid_theme_id'] = $theme_id;
		update_option( 'theme_mods_' . $theme_name, $theme_mods );
	}

	/**
	 * Start over before deployment.
	 *
	 * @since 1.2.3
	 */
	public function start_over() {
		if( ! $this->start_over ) {
			return;
		}

		check_admin_referer( 'deploy', 'deploy' );

		$start_over = new BoldGrid_Inspirations_Start_over();

		$start_over->start_over_active = ( false === $this->is_staging_install() );

		$start_over->start_over_staging = (true === $this->is_staging_install() );

		$start_over->delete_forms = false;

		$start_over->delete_pages = false;

		$start_over->delete_themes = false;

		$start_over->start_over();
	}

	/**
	 * When we install a page we attach post meta data to indicate that it is a boldgrid page
	 * This function returns all pages that are still installed on the users wordpress that were
	 * created by boldgrid
	 *
	 * @return array
	 */
	public function get_existing_pages() {
		$previous_install_options = Boldgrid_Inspirations_Built::find_all_install_options();

		if ( true == $this->is_staging_install() ) {
			$installed_pages = $previous_install_options['boldgrid_staging_options']['installed_pages'];
		} else {
			$installed_pages = $previous_install_options['active_options']['installed_pages'];
		}

		return is_array( $installed_pages ) ? $installed_pages : array ();
	}

	/**
	 * Get media pages.
	 *
	 * Media pages are posts and pages that we just installed and we need to loop through and
	 * replace the images within.
	 *
	 * The code within this method was duplicated in this class several times. As of 1.4, it has
	 * been consolidated into this method.
	 *
	 * @since 1.4
	 *
	 * @return array An array of WP_Post objects.
	 */
	public function get_media_pages() {
		$posts = array();

		$post_params = array (
			'posts_per_page' => -1,
			'post__in'       => $this->installed_page_ids,
			'post_type'      => array (
				'page',
				'post',
			)
		);

		if ( 'publish' != $this->post_status ) {
			$post_params['post_status'] = $this->post_status;
		}

		/*
		 * Only get_posts if we have an array of installed_page_ids.
		 *
		 * Otherwise, we'll end up getting all pages. This would be bad because if there were 100+
		 * pages already existing, we would end up scanning each page, looking at the image tags,
		 * and trying to download those images (which would cause lots of errors).
		 */
		if ( ! empty( $this->installed_page_ids ) ) {
			$posts = get_posts( $post_params );
		}

		/**
		 * Filter posts in which we download images for.
		 *
		 * @since 1.4
		 *
		 * @param array $posts                    An array of post objects.
		 * @param array $this->installed_page_ids An array of pages we've installed.
		*/
		$posts = apply_filters( 'boldgrid_deploy_media_pages', $posts, $this->installed_page_ids );

		return $posts;
	}

	/**
	 * Create pages.
	 *
	 * Download and import the page set the user selected
	 *
	 * @see Boldgrid_Inspirations_Api::get_api_key_hash().
	 * @link http://codex.wordpress.org/Function_Reference/wp_insert_post
	 */
	public function deploy_page_sets() {
		$pages_created = 0;

		// Set the menu name
		$menu_name = 'primary';

		// Allow plugins, like BoldGrid Staging, to create 'primary-staging' instead of 'primary'.
		$menu_name = apply_filters( 'boldgrid_deployment_primary_menu_name', $menu_name );

		// We want to start fresh, so if the menu exists, delete it.
		$menu_exists = wp_get_nav_menu_object( $menu_name );
		if ( true == $menu_exists ) {
			wp_delete_nav_menu( $menu_name );
		}

		// Create the menu
		$menu_id = wp_create_nav_menu( $menu_name );
		$this->primary_menu_id = $menu_id;

		$this->assign_menu_id_to_all_locations( $menu_id );

		if( $this->install_blog ) {
			$this->blog->create_category();
			$this->set_permalink_structure( '/%category%/%postname%/' );
			$this->blog->create_menu_item( $this->primary_menu_id, 150 );
		}

		$boldgrid_configs = $this->get_configs();

		$page_set_url = $boldgrid_configs['asset_server'] . $boldgrid_configs['ajax_calls']['get_page_set'];

		// Determine the release channel:
		$options = get_option( 'boldgrid_settings' );

		$release_channel = isset( $options['release_channel'] ) ? $options['release_channel'] : 'stable';

		// Get the theme id, category id, etc.
		$this->get_deploy_details();

		// Build API call arguments:
		$arguments = array (
			'method' => 'POST',
			'body'   => array (
				'page_set_id'           => $this->page_set_id,
				'theme_id'              => $this->theme_id,
				'subcategory_id'        => $this->subcategory_id,
				'page_set_version_type' => $this->page_set_version_type,
				'custom_pages'          => $this->custom_pages,
				'homepage_only'         => false,
				'channel'               => $release_channel
			)
		);

		$api_key_hash = $this->asset_manager->api->get_api_key_hash();

		if ( ! empty( $api_key_hash ) ) {
			$arguments['body']['key'] = $api_key_hash;
		}

		$response = wp_remote_post( $page_set_url, $arguments );

		// Check response:
		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			$this->messages->print_notice( esc_html__( 'WP ERROR', 'boldgrid-inspirations' ) . ': ' . esc_html( $error_message ) );
		}

		$json_response = json_decode( $response['body'] );

		if ( 200 != $json_response->status ) {
			$this->messages->print_notice( esc_html__( 'Error: Asset server did not return HTTP 200 OK!', 'boldgrid-inspirations' ) );
		}

		if ( empty( $json_response->result->data ) ) {
			$this->messages->print_notice( esc_html__( 'Error: Asset server returned an empty data set!', 'boldgrid-inspirations' ) );
		}

		// Download Plugins needed for pages.
		if ( isset( $json_response->result->data->plugins ) ) {
			foreach ( $json_response->result->data->plugins as $plugin ) {
				$this->download_and_install_plugin(
					$plugin->plugin_zip_url,
					$plugin->plugin_activate_path,
					$plugin->version,
					$plugin
				);

				// If the we have defined configurations for this plugin, configure it.
				if ( ! empty( $plugin->config_script ) ) {
					// Passing page_id to config script.
					$plugin_install_details = $this->plugin_installation_data[ $plugin->plugin_activate_path ];

					$post_id = ! empty( $page_id_to_post[ $plugin->page_id ] ) ? $page_id_to_post[ $plugin->page_id ] : null;

					// Configure Plugin.
					if ( file_exists( BOLDGRID_BASE_DIR . '/includes/configure_plugin/' . $plugin->config_script ) ) {
							require_once BOLDGRID_BASE_DIR . '/includes/configure_plugin/' . $plugin->config_script;
					}
				}
			}
		}

		// Save the parent category name, if available:
		if ( ! empty( $json_response->result->data->parent_category_name ) ) {
			$this->update_existing_install_options(
				array (
					'parent_category_name' => $json_response->result->data->parent_category_name
				) );
		}

		$pages_in_pageset = isset( $json_response->result->data->pages ) ? $json_response->result->data->pages : array ();
		$additional_pages = ! empty( $json_response->result->data->additional_pages ) ? $json_response->result->data->additional_pages : array ();

		/*
		 * This is a list of the pages that the user requested as well additional pages included in
		 * their category that will later be used to create grid blocks and pages.
		 */
		$this->full_page_list = array (
			'pages' => array (
				'pages_in_pageset' => $pages_in_pageset,
				'additional'       => $additional_pages,
			),
		);

		$this->installed_page_ids = array ();

		$boldgrid_installed_pages_metadata = array ();

		$existing_pages_from_meta_data = $this->get_existing_pages();

		foreach ( $pages_in_pageset as $page_k => $page_v ) {
			if ( ! is_object( $page_v ) ) {
				continue;
			}

			$is_blog_post = ( isset( $page_v->is_blog_post ) && '1' === $page_v->is_blog_post );

			if( $is_blog_post && ! $this->install_blog ) {
				continue;
			}

			$this->messages->print_page( $page_v );

			/*
			 * Prevent the user from installing the same page twice.
			 *
			 * This was put in place in order to prevent homepages that were installed automatically
			 * from being installed multiple times.
			 */
			if ( in_array( $page_v->id, $existing_pages_from_meta_data ) ) {
				continue;
			}

			$post = array();

			$post['post_content']   = $page_v->code;
			$post['post_name']      = $page_v->page_slug;
			$post['post_title']     = $page_v->page_title;
			$post['post_status']    = $this->post_status;
			$post['post_type']      = $page_v->post_type;
			$post['comment_status'] = 'closed';

			// Allow other plugins to modify the post.
			$post = apply_filters( 'boldgrid_deployment_pre_insert_post', $post );

			$post_id = wp_insert_post( $post );

			// store the pages we created for later use
			$this->installed_page_ids[$page_v->id] = $post_id;

			// Store additional info about the pages.
			$boldgrid_installed_pages_metadata[$post_id] = array(
				'is_readonly' => $page_v->is_readonly,
				'post_type'   => $post['post_type'],
				'post_status' => $post['post_status'],
			);

			// Assign this blog post to our blog category.
			if( $is_blog_post && $this->install_blog ) {
				wp_set_post_categories( $post_id, array( $this->blog->category_id ) );
			}

			// add page to menu
			if ( '1' == $page_v->in_menu ) {
				// configure the url to this page
				$menu_item_url = home_url( '/' );

				if ( ! $page_v->homepage_theme_id ) {
					$menu_item_url = home_url( '/' ) . "?p=$post_id";
				}

				$menu_item_db_id = wp_update_nav_menu_item( $menu_id, 0,
					array (
						'menu-item-object-id' => $post_id,
						'menu-item-parent-id' => 0,
						'menu-item-object'    => 'page',
						'menu-item-type'      => 'post_type',
						'menu-item-status'    => 'publish'
					) );
			}

			// set homepage
			if ( $page_v->homepage_theme_id ) {
				update_option( 'show_on_front', 'page' );
				update_option( 'page_on_front', $post_id );
			}

			/*
			 * Determine if there is post meta to be set.
			 *
			 * This code block below only assigns page templates to pages. Page templates meaning
			 * page-full.php, page-inside.php, etc.
			 */
			if ( isset( $this->theme_details->postmeta ) ) {
				foreach ( $this->theme_details->postmeta as $post_meta ) {
					/*
					 * The column_name will always be "layout". This is because 'layout' is the only
					 * distinct value for columnName currently in the table.
					 */
					$column_name = $post_meta->ColumnName;

					if ( $page_v->$column_name == $post_meta->ColumnValue ) {
						add_post_meta( $post_id, $post_meta->KeyName, $post_meta->KeyValue );
					}
				}
			}

			// Take action if we have a featured image.
			if ( $page_v->featured_image_asset_id ) {
				$this->asset_manager->download_and_attach_asset( $post_id, true, $page_v->featured_image_asset_id );
			}

			$pages_created ++;

			$page_id_to_post[ $page_v->id ] = $post_id;

			// Add the page id so that we can recognize it
			add_post_meta( $post_id, 'boldgrid_page_id', $page_v->id );
		}

		$updated = update_option( 'blogdescription', '' );

		// Store the pages we created:
		update_option( 'boldgrid_installed_page_ids', $this->installed_page_ids );
		update_option( 'boldgrid_installed_pages_metadata', $boldgrid_installed_pages_metadata );

		$this->delete_sample_pages();

		// Setup our custom homepage (per theme_id and homepage).
		if ( isset( $this->theme_details->homepage ) ) {
			$this->set_custom_homepage();
		}
	}

	/**
	 * Strip uneeded markup
	 *
	 * @param DOMDOcument $dom
	 * @return string
	 */
	public function format_html_fragment( $dom ) {
		$html = preg_replace( '/^<!DOCTYPE.+?>/', '',
			str_replace( array (
				'<html>',
				'</html>',
				'<body>',
				'</body>'
			), array (
				'',
				'',
				'',
				''
			), $dom->saveHTML() ) );

		return $html;
	}

	/**
	 * Gather media information.
	 *
	 * Deploy page sets: Media: Find placeholders.
	 *
	 * @see Boldgrid_Inspirations_Api::get_api_key_hash().
	 */
	public function deploy_page_sets_media_find_placeholders() {
		$boldgrid_configs = $this->get_configs();

		$pages_and_posts = $this->get_media_pages();

		$api_key_hash = $this->asset_manager->api->get_api_key_hash();

		$this->image_placeholders_needing_images['bps_build_info'] = array (
			'subcategory_id'     => $this->subcategory_id,
			'page_set_id'        => $this->page_set_id,
			'theme_id'           => $this->theme_id,
			'language_id'        => $this->language_id,
			'asset_user_id'      => $this->asset_user_id,
			'key'                => $api_key_hash,
			'current_build_cost' => $this->current_build_cost,
			'coin_budget'        => $this->coin_budget,
			'site_hash'          => $this->site_hash
		);

		foreach ( $pages_and_posts as $k => $page ) {
			// Get all of the images.
			$dom = new DOMDocument();
			@$dom->loadHTML( Boldgrid_Inspirations_Utility::utf8_to_html( $page->post_content ) );
			$images = $dom->getElementsByTagName( 'img' );

			// Keep track of the order in which built_photo_search images appear on the page.
			// For further info, see docBlock for set_built_photo_search_placement()
			$remote_page_id = $this->get_remote_page_id_from_local_page_id( $page->ID );

			$bps_image_position     = 0;
			$asset_image_position   = 0;
			$gallery_image_position = 0;

			// Loop through every image in this page.
			foreach ( $images as $image ) {
				// Reset the placeholder.
				$image_placeholder = array ();

				$asset_id = $image->getAttribute( 'data-imhwpb-asset-id' );

				$built_photo_search = $image->getAttribute( 'data-imhwpb-built-photo-search' );

				$source = $image->getAttribute( 'src' );

				// Take action if we're downloading an asset_id.
				if ( ! empty( $asset_id ) ) {
					$image_placeholder = array (
						'page_id' => $page->ID,
						'asset_id' => $asset_id,
						'asset_image_position' => $asset_image_position
					);

					$asset_image_position ++;
				}

				// Take action if we're downloading an image from "built_photo_search".
				if ( ! empty( $built_photo_search ) && false == $this->is_author ) {
					// keep track of the number of bps we've requested
					$this->built_photo_search_log['count'] ++;

					// keep track of the src for this bps
					$this->built_photo_search_log['sources'][] = $built_photo_search;

					// get built_photo_search details (query_id | orientation)
					$exploded_bps = explode( '|', $built_photo_search );

					$bps_query_id = $exploded_bps[0];

					$bps_orientation = ! empty( $exploded_bps[1] ) ? $exploded_bps[1] : 'any';

					/*
					 * Get width and height from src url.
					 *
					 * Example $source: https://placehold.it/200x200&text=200x200+(dynamic+image)
					 *
					 * Regular expression match looks for: /###x###
					 */
					preg_match( '/\/([0-9]*)x([0-9]*)/', $source, $matches );
					$width = ! empty( $matches[1] ) ? $matches[1] : null;
					$height = ! empty( $matches[2] ) ? $matches[2] : null;

					$image_placeholder = array (
						'page_id'            => $page->ID,
						'asset_id'           => null,
						'bps_image_position' => $bps_image_position,
						'bps_query_id'       => $bps_query_id,
						'bps_orienation'     => $bps_orientation,
						'bps_width'          => $width,
						'bps_height'         => $height,
						'remote_page_id'     => $remote_page_id
					);

					$bps_image_position ++;
				}

				if ( ! empty( $image_placeholder ) ) {
					$this->image_placeholders_needing_images['by_page_id'][$page->ID][] = $image_placeholder;
				}
			}

			// Take action if we have a [gallery] on the page.
			if ( preg_match_all( '/\[gallery .+?\]/i', $page->post_content, $matches ) ) {
				foreach ( $matches[0] as $index => $match ) {
					preg_match( '/data-imhwpb-assets=\'.*\'/', $match, $data_assets );

					$images = array ();

					if ( preg_match( '/data-imhwpb-assets=\'(.+)\'/', $data_assets[0],
						$asset_images_ids ) ) {
						$images = ( explode( ',', $asset_images_ids[1] ) );
					}

					foreach ( $images as $image_asset_id ) {
						$image_placeholder = array (
							'page_id'                => $page->ID,
							'asset_id'               => $image_asset_id,
							'gallery_image_position' => $gallery_image_position
						);

						$gallery_image_position ++;

						$this->image_placeholders_needing_images['by_page_id'][$page->ID][] = $image_placeholder;
					}
				}
			}

			/*
			 * Find any background images within style tags that need to be downloaded.
			 *
			 * @since 1.4.3
			 */
			foreach( $this->tags_having_background as $tag ) {

				$tag_position = 0;

				$elements = $dom->getElementsByTagName( $tag );

				foreach ( $elements as $element ) {
					$asset_id = $element->getAttribute( 'data-imhwpb-asset-id' );

					if( empty ( $asset_id ) ) {
						continue;
					}

					$this->image_placeholders_needing_images['by_page_id'][$page->ID][] = array (
						'page_id'              => $page->ID,
						'asset_id'             => $asset_id,
						$tag . '_tag_position' => $tag_position,
					);

					$tag_position++;
				}
			}

		}

		// Get all the bps data from the image server.
		$params = array (
			'key'                               => $api_key_hash,
			'image_placeholders_needing_images' => json_encode( $this->image_placeholders_needing_images ),
			'coin_budget'                       => $this->coin_budget,
			'is_generic'                        => $this->is_generic,
		);

		// Get configs:
		$boldgrid_configs = $this->get_configs();

		// Set the URL address:
		$url = $boldgrid_configs['asset_server'] . $boldgrid_configs['ajax_calls']['bps-get-photos'];

		// Make a call to the asset server:
		$response = wp_remote_post( $url, array (
			'body'    => $params,
			'timeout' => 60
		) );

		// Decode response into an array:
		$response_body = json_decode( $response['body'], true );

		// Validate response:
		if ( empty( $response_body['result']['data'] ) || ( isset(
			$response_body['result']['status'] ) && 200 != $response_body['result']['status'] ) ) {
			return;
		}

		// Set $response_data from the response data:
		$response_data = $response_body['result']['data'];

		// Update our ['by_page_id'] value with that of the API call return.
		$this->image_placeholders_needing_images['by_page_id'] = $response_data;

		// Do one final loop, and create the download urls.
		foreach ( $this->image_placeholders_needing_images['by_page_id'] as $page_id => $images_array ) {
			// If $images_array is empty or is not an array, then skip this iteration:
			if ( empty( $images_array ) || ! is_array( $images_array ) ) {
				continue;
			}

			// Iterate through $images_array:
			foreach ( $images_array as $images_array_key => $image_data ) {
				// Are we downloading an asset?
				if ( isset( $image_data['asset_id'] ) && is_numeric( $image_data['asset_id'] ) ) {
					$download_url = $boldgrid_configs['asset_server'] .
						 $boldgrid_configs['ajax_calls']['get_asset'] . '?id=' .
						 $image_data['asset_id'] . '&key=' . $api_key_hash;

					$this->image_placeholders_needing_images['by_page_id'][$page_id][$images_array_key]['download_type'] = 'get';
					$this->image_placeholders_needing_images['by_page_id'][$page_id][$images_array_key]['download_url'] = $download_url;
				}

				// Are we downloading a bps?
				if ( isset( $image_data['bps_query_id'] ) ) {
					$download_url = $boldgrid_configs['asset_server'] .
						 $boldgrid_configs['ajax_calls']['image_download'];

					/* @formatter:off */
					$download_params = array(
						'key' => $api_key_hash,
						'id_from_provider'         => isset( $image_data['getPhotoAction']['id_from_provider'] ) ? $image_data['getPhotoAction']['id_from_provider'] : null,
						'image_provider_id'        => isset( $image_data['getPhotoAction']['image_provider_id'] ) ? $image_data['getPhotoAction']['image_provider_id'] : null,
						'imgr_image_id'            => isset( $image_data['getPhotoAction']['imgr_image_id'] ) ? $image_data['getPhotoAction']['imgr_image_id'] : null,
						'width'                    => isset( $image_data['bps_width'] ) ? $image_data['bps_width'] : null,
						'height'                   => isset( $image_data['bps_height'] ) ? $image_data['bps_height'] : null,
						'orientation'              => isset( $image_data['bps_orientation'] ) ? $image_data['bps_orientation']  : null,
						'image_size'               => isset( $item['params']['image_size'] ) ? $image_data['params']['image_size']  : null,
						'is_redownload'            => isset( $item['params']['is_redownload'] ) ? $image_data['params']['is_redownload'] : false,
						'user_transaction_item_id' => isset( $item['params']['user_transaction_item_id'] ) ? $image_data['params']['user_transaction_item_id'] : null,
						'boldgrid_connect_key'     => isset( $item['params']['boldgrid_connect_key'] ) ? $image_data['params']['boldgrid_connect_key'] : null,
					);
					/* @formatter:on */

					$this->image_placeholders_needing_images['by_page_id'][$page_id][$images_array_key]['download_type']   = 'post';
					$this->image_placeholders_needing_images['by_page_id'][$page_id][$images_array_key]['download_url']    = $download_url;
					$this->image_placeholders_needing_images['by_page_id'][$page_id][$images_array_key]['download_params'] = $download_params;
				}
			}
		}
	}

	/**
	 * Download media.
	 *
	 * Deploy page sets: Media: Process image queue
	 */
	public function deploy_page_sets_media_process_image_queue() {
		// Return if we have no media to download for pages.
		if ( empty( $this->image_placeholders_needing_images['by_page_id'] ) ) {
			return;
		}

		// Create our image queue.
		foreach ( $this->image_placeholders_needing_images['by_page_id'] as $page_id => $images_array ) {
			// If $images_array is empty or is not an array, then skip this iteration:
			if ( empty( $images_array ) || ! is_array( $images_array ) ) {
				continue;
			}

			foreach ( $images_array as $images_array_key => $image_data ) {
				$image_queue[] = array (
					'download_type'    => isset( $image_data['download_type'] ) ? $image_data['download_type'] : null,
					'download_url'     => isset( $image_data['download_url'] ) ? $image_data['download_url'] : null,
					'download_params'  => isset( $image_data['download_params'] ) ? $image_data['download_params'] : null,
					'post_id'          => isset( $image_data['page_id'] ) ? $image_data['page_id'] : null,
					'images_array_key' => $images_array_key,
				);
			}
		}

		// Using curl_multi_:
		$mh = curl_multi_init();

		global $wp_version;

		$user_agent = 'WordPress/' . $wp_version . '; ' . get_site_url();

		// If $image_queue is empty, then return:
		if ( empty( $image_queue ) ) {
			return;
		}

		foreach ( $image_queue as $image_key => $image_data ) {
			// If image caching is enabled, then check cache.
			if ( null !== $this->asset_cache ) {
				// Create an array to be used to set a cache id.
				if ( isset( $image_data['bps_query_id'] ) ) {
					$cache_array = array (
						'id_from_provider' => $image_data['download_params']['id_from_provider'],
						'image_provider_id' => $image_data['download_params']['image_provider_id'],
						'imgr_image_id' => $image_data['download_params']['imgr_image_id'],
						'width' => $image_data['download_params']['width'],
						'orientation' => $image_data['download_params']['orientation'],
						'image_size' => $image_data['download_params']['image_size']
					);
				} else {
					$cache_array = $image_data;
				}

				// Set the cache id.
				$image_queue[$image_key]['cache_id'] = $this->asset_cache->set_cache_id(
					$cache_array );

				// Try to get the $response from cache.
				if ( ! empty( $image_queue[$image_key]['cache_id'] ) ) {
					$response[$image_key] = $this->asset_cache->get_cache_files(
						$image_queue[$image_key]['cache_id'] );
				}
			}

			// If there was no cached response, then queue the download.
			if ( empty( $response[$image_key] ) ) {
				// Using curl_multi_:
				${'ch' . $image_key} = curl_init();

				curl_setopt( ${'ch' . $image_key}, CURLOPT_URL, $image_data['download_url'] );
				curl_setopt( ${'ch' . $image_key}, CURLOPT_HEADER, true );
				curl_setopt( ${'ch' . $image_key}, CURLOPT_RETURNTRANSFER, true );
				curl_setopt( ${'ch' . $image_key}, CURLOPT_USERAGENT, $user_agent );

				if ( 'post' == $image_data['download_type'] &&
					 ! empty( $image_data['download_params'] ) ) {
					curl_setopt( ${'ch' . $image_key}, CURLOPT_POST, true );
					curl_setopt( ${'ch' . $image_key}, CURLOPT_POSTFIELDS,
						http_build_query( $image_data['download_params'] ) );
				}

				curl_multi_add_handle( $mh, ${'ch' . $image_key} );

				$image_queue[$image_key]['cached'] = false;
			} else {
				$image_queue[$image_key]['cached'] = true;
			}
		}

		// If any curl handle was added to the $mh handle, then get data from curl_multi_.
		if ( isset( $mh ) ) {
			// Using curl_multi_.
			$still_running = null;

			do {
				$mrc = curl_multi_exec( $mh, $still_running );
			} while ( $still_running > 0 );

			while ( $still_running && CURLM_OK == $mrc ) {
				if ( curl_multi_select( $mh ) != - 1 ) {
					do {
						$mrc = curl_multi_exec( $mh, $still_running );
					} while ( $still_running > 0 );
				}
			}

			foreach ( $image_queue as $image_key => $image_data ) {
				if ( empty( $response[$image_key] ) ) {
					$response[$image_key] = curl_multi_getcontent( ${'ch' . $image_key} );

					curl_multi_remove_handle( $mh, ${'ch' . $image_key} );
				}
			}
		}

		// Check responses.
		foreach ( $image_queue as $image_key => $image_data ) {
			if ( isset( $response[$image_key]['headers']['z-filename'] ) ) {
				$arrayify = $response[$image_key];
			} else {
				$arrayify = $this->curl_response_arrayify( $response[$image_key] );
			}

			// If we did not receive a filename in the headers, then log and skip.
			if ( empty( $arrayify['headers']['z-filename'] ) ) {
				error_log( 'Failed to download image during deployment, ["headers"]["z-filename"] was empty.' );
				continue;
			} else {
				// If appplicable, save to cache.
				if ( null !== $this->asset_cache && ! empty( $image_data['cache_id'] ) && ! $image_data['cached'] ) {
					$this->asset_cache->save_cache_files( $image_data['cache_id'], $arrayify );
				}
			}

			$attachment_data = $this->asset_manager->attach_asset(
				array (
					'headers' => $arrayify['headers'],
					'body' => $arrayify['body'],
					'post_id' => $image_data['post_id'],
					'featured_image' => false,
					'return' => 'all',
					'add_meta_data' => ( isset(
						$this->image_placeholders_needing_images['by_page_id'][$image_data['post_id']][$image_data['images_array_key']]['gallery_image_position'] ) )
				) );

			$this->messages->print_image( $attachment_data['attachment_id'] );

			$attachment_url = $attachment_data['uploaded_url'];

			/**
			 * Filter the url to replace placeholder url with.
			 *
			 * @since 1.4.8
			 *
			 * @param int $attachment_data['attachment_id']
			 * @param int $image_data['download_params']['width']
			 * @param int $image_data['download_params']['height']
			 */
			$attachment_url = apply_filters( 'boldgrid_deploy_post_process_image', $attachment_data['attachment_id'], $image_data['download_params']['width'], $image_data['download_params']['height'] );

			// Update our data...
			$this->image_placeholders_needing_images['by_page_id'][$image_data['post_id']][$image_data['images_array_key']]['attachment_url'] = $attachment_url;
			$this->image_placeholders_needing_images['by_page_id'][$image_data['post_id']][$image_data['images_array_key']]['attachment_id'] = $attachment_data['attachment_id'];
			$this->image_placeholders_needing_images['by_page_id'][$image_data['post_id']][$image_data['images_array_key']]['asset_id'] = $attachment_data['asset_id'];

			// Update the cost of this build.
			if ( isset( $attachment_data['coin_cost'] ) ) {
				$this->current_build_cost += $attachment_data['coin_cost'];
			}
		}
	}

	/**
	 * Replace media in pages.
	 *
	 * Deploy page sets: Media: Replace placeholders.
	 *
	 * This method updates a post's content in 2 ways:
	 * # Updating the $dom, then saving the $dom to post_content.
	 * # Updating post_content directly.
	 *
	 * The reason this is done in 2 ways is because the $dom can parse tags, but it cannot parse
	 * WordPress shortcodes.
	 *
	 * # Standard images    $dom
	 * # Built photo images $dom
	 * # Background images  $dom
	 * # Gallery images     post_content
	 */
	public function deploy_page_sets_media_replace_placeholders() {
		$pages_and_posts = $this->get_media_pages();

		foreach ( $pages_and_posts as $k => $page ) {
			$dom = new DOMDocument();
			@$dom->loadHTML( Boldgrid_Inspirations_Utility::utf8_to_html( $page->post_content ) );

			$images = $dom->getElementsByTagName( 'img' );
			$remote_page_id = $this->get_remote_page_id_from_local_page_id( $page->ID );

			$dom_changed = false;
			$content_changed = false;
			$built_photo_search_counter = 0;
			$bps_image_position = 0;
			$asset_image_position = 0;

			foreach ( $images as $image ) {
				$asset_id = $image->getAttribute( 'data-imhwpb-asset-id' );
				$built_photo_search = $image->getAttribute( 'data-imhwpb-built-photo-search' );
				$source = $image->getAttribute( 'src' );

				// Get the image that belongs in this placeholder.
				if ( ! empty( $asset_id ) ) {
					$placeholder = $this->get_placeholder_image( $page->ID, 'asset_image_position', $asset_image_position );
				} elseif ( ! empty( $built_photo_search ) && false === $this->is_author ) {
					$placeholder = $this->get_placeholder_image( $page->ID, 'bps_image_position', $bps_image_position );
				} else {
					$placeholder = array();
				}

				// Check if we have the information we need, or skip this iteration.
				if ( empty( $placeholder['attachment_url'] ) || empty( $placeholder['asset_id'] ) ) {
					continue;
				}

				$attachment_url = $placeholder['attachment_url'];
				$attachment_id = ( isset( $placeholder['attachment_id'] ) ? (int) $placeholder['attachment_id'] : null );

				/*
				 * Determine our wp-image-## class.
				 *
				 * This class is required if WordPress is to later add the srcset attribute.
				 */
				$new_image_class = null;
				if ( ! empty( $attachment_id ) ) {
					$new_image_class = $this->dom_element_append_attribute( $image, 'class', 'wp-image-' . $attachment_id );
				}

				// If we're downloading an asset_id...
				if ( ! empty( $asset_id ) ) {
					$image->setAttribute( 'src', $attachment_url );

					if ( ! is_null( $new_image_class ) ) {
						$image->setAttribute( 'class', $new_image_class );
					}

					$dom_changed = true;
					$asset_image_position ++;
				}

				// Build photo search.
				if ( ! empty( $built_photo_search ) && false === $this->is_author ) {
					$this->built_photo_search_log['count'] ++;
					$this->built_photo_search_log['sources'][] = $built_photo_search;

					// Update and save the <img> tag.
					$image->setAttribute( 'src', $attachment_url );
					$image->setAttribute( 'width', $placeholder['bps_width'] );

					if ( $this->is_preview_server ) {
						$image->setAttribute( 'data-id-from-provider', $placeholder['download_params']['id_from_provider'] );
						$image->setAttribute( 'data-image-provider-id', $placeholder['download_params']['image_provider_id'] );
					}

					if ( ! is_null( $new_image_class ) ) {
						$image->setAttribute( 'class', $new_image_class );
					}

					$this->set_built_photo_search_placement( $remote_page_id,
						$built_photo_search_counter,
						$placeholder['asset_id']
					);

					// Increment our counters.
					$bps_image_position ++;
					$built_photo_search_counter ++;
					$dom_changed = true;
				}
			} // End of foreach images.

			/*
			 * Set background images within the style tag.
			 *
			 * @since 1.4.3
			 */
			foreach ( $this->tags_having_background as $tag ) {

				$tag_position = 0;

				$elements  = $dom->getElementsByTagName( $tag );

				foreach ( $elements as $element ) {

					$asset_id = $element->getAttribute( 'data-imhwpb-asset-id' );

					if ( empty( $asset_id ) ) {
						continue;
					}

					$placeholder = $this->get_placeholder_image( $page->ID, $tag . '_tag_position', $tag_position );

					$style = $element->getAttribute( 'style' );

					preg_match( '/(background:|background-image:).*(url\()[\'"](.*)[\'"]\)/', $style, $matches );

					if ( empty( $matches ) ) {
						continue;
					}

					// Create our new style tag, update it within the dom, and save post_content.
					$updated_matches_0 = str_replace( $matches[3], $placeholder['attachment_url'], $matches[0] );
					$new_style = str_replace( $matches[0], $updated_matches_0, $style );
					$element->setAttribute( 'style', $new_style );

					$element->setAttribute( 'data-image-url', $placeholder['attachment_url'] );

					$dom_changed = true;
					$tag_position++;
				}
			}

			if ( $dom_changed ) {
				$dom->saveHTML();
				$page->post_content = $this->format_html_fragment( $dom );
				$content_changed = true;
			}

			// Get asset ids for gallery images and swap data with the attachment ids in the shortcode.
			if ( preg_match_all( '/\[gallery .+?\]/i', $page->post_content, $matches ) ) {
				// Create an array of asset_id's to local attachment_id's.
				foreach ( $this->image_placeholders_needing_images['by_page_id'][ $page->ID ] as $image ) {
					$assets[ $image['asset_id'] ] = $image['attachment_id'];
				}

				foreach ( $matches[0] as $index => $match ) {
					preg_match( '/data-imhwpb-assets=\'.*\'/', $match, $data_assets );

					$images = array();

					if ( preg_match( '/data-imhwpb-assets=\'(.+)\'/i', $data_assets[0], $asset_images_ids ) ) {
						$images = ( explode( ',', $asset_images_ids[1] ) );
					}

					$attachment_ids = array();

					foreach ( $images as $asset_id ) {
						if ( ! empty( $assets[ $asset_id ] ) ) {
							$attachment_ids[ $asset_id ] = $assets[ $asset_id ];
						}
					}

					$attribute_value = ' ids="' . implode( ',', $attachment_ids ) . '" ';

					$updated_match = str_ireplace( 'ids=""', $attribute_value, $match );

					$page->post_content = str_ireplace( $match, $updated_match, $page->post_content );
					$content_changed = true;
				}
			}

			if ( $content_changed ) {
				wp_update_post( $page );
			}
		} // End of foreach pages_and_posts.
	}

	/**
	 * Setup primary design elements.
	 *
	 * Primary Design Elements (pde) vary based upon theme group. This function will download and
	 * setup the appropriate pde's.
	 *
	 * @see Boldgrid_Inspirations_Api::get_api_key_hash().
	 */
	public function deploy_pde( $params = array() ) {
		$defaults = array(
			'update_current_themes_mods' => true,
		);

		$params = wp_parse_args( $params, $defaults );

		if ( is_array( $this->pde ) ) {
			foreach ( $this->pde as $pde ) {

				if ( 'header_image' == $pde['pde_type_name'] ||
					 'background_image' == $pde['pde_type_name'] ) {
					/**
					 * ********************************************************
					 * Step 1: Get the asset id of the pde
					 * ********************************************************
					 */
					// get curated item object
					$boldgrid_configs = $this->get_configs();

					$get_curated_url = $boldgrid_configs['asset_server'] .
						 $boldgrid_configs['ajax_calls']['get_curated'];

					$arguments = array (
						'method' => 'POST',
						'body' => array (
							'curated_id' => $pde['pde_curated_id']
						)
					);

					// Get the API key hash.
					$api_key_hash = $this->asset_manager->api->get_api_key_hash();

					if ( ! empty( $api_key_hash ) ) {
						$arguments['body']['key'] = $api_key_hash;
					}

					$response = wp_remote_post( $get_curated_url, $arguments );

					if ( $response instanceof WP_Error ) {
						throw new Exception( esc_html__( 'Error downloading asset.', 'boldgrid-inspirations' ) );
					}

					$data = json_decode( $response['body'] );

					$asset_id = $data->result->data->asset_id;

					/**
					 * ********************************************************
					 * Step 2: Download and attach this asset_id
					 * ********************************************************
					 */
					// Set the last argument to true in order to 'add_meta_data'.
					// This is because the attribution class looks for thumbnails.
					$pde_url = $this->asset_manager->download_and_attach_asset( false, false,
						$asset_id, 'url', true );

					/**
					 * ********************************************************
					 * Step 3: Set the theme mod
					 * ********************************************************
					 */
					if ( $params['update_current_themes_mods'] ) {
						set_theme_mod( 'default_' . $pde['pde_type_name'], $pde_url );
					}

					/*
					 * There may be times we don't want to update the theme mods for the current
					 * theme. If we're using Inspiration's "install new themes", let's save this
					 * theme mod to the new theme's theme_mods, which will become activated once
					 * the user enables the new theme. If we actually DID set the theme mod (done
					 * above), it would affect the user's current live site (IE change their
					 * background / header image).
					 */
					if ( ! $params['update_current_themes_mods'] &&
						 isset( $params['stylesheet'] ) ) {
						$staging_prefix = $this->is_staging_install() ? 'boldgrid_staging_' : '';

						// Create the name of the option we'll be working with.
						$option_name = $staging_prefix . 'theme_mods_' . $params['stylesheet'];

						// If this theme already has theme_mods, get them.
						$theme_mods_for_new_theme = get_option( $option_name );

						// Set the theme mod.
						$theme_mods_for_new_theme[$pde['pde_type_name']] = $pde_url;

						// Save the theme mod. It will take effect if/when the user enables this
						// theme.
						update_option( $option_name, $theme_mods_for_new_theme );
					}
				}
			}
		}
	}

	/**
	 * This function exists and does nothing.
	 *
	 * This is because we're using get_shortcode_regex and we need "imhwpb"
	 * added to the regex list.
	 * In order to do this, we need to use add_shortcode, which requires a
	 * function.
	 *
	 * http://codex.wordpress.org/Function_Reference/add_shortcode
	 * http://codex.wordpress.org/Function_Reference/get_shortcode_regex
	 */
	public function dummy_shortcode_imhwpb() {
	}

	/**
	 * Allow downloads over the backlan.
	 *
	 * WordPress blocks 10.x.x.x connections.
	 *
	 * @thanks http://www.emanueletessore.com/wordpress-download-failed-valid-url-provided/
	 */
	public function allow_downloads_over_the_backlan( $allow, $host, $url ) {
		$boldgrid_configs = $this->get_configs();

		if ( $host == str_replace( 'https://', '', $boldgrid_configs['asset_server'] ) ) {
			$allow = true;
		}

		return $allow;
	}

	/**
	 * Fail deployment
	 *
	 * @param string $message
	 */
	public function fail_deployment( $message ) {
		$message = esc_html__( 'Deployment failed. We\'re sorry but unfortunately the site deployment failed with the following message:', 'boldgrid-inspirations' ) . ' ' . esc_html( $message );
		$this->messages->print_notice( $message );
	}

	/**
	 * Add to list of allowed attributes.
	 *
	 * @since 1.3.6
	 *
	 * @param  array $allowed
	 * @param  array $context
	 * @return array
	 */
	public function filter_allowed_html( $allowed, $context ) {
		if ( is_array( $context ) ) {
			return $allowed;
		}

		if ( 'post' === $context || 'page' === $context ) {
			$allowed['iframe'] = array(
				'frameborder' => true,
				'src' => true,
				'style' => true,
			);
		}

		return $allowed;
	}

	/**
	 * This function handles things to do after the deployment is done.
	 */
	public function finish_deployment() {
		// This may not be our first deployment. If we have prior kitchen sink data, remove it.
		delete_transient( 'boldgrid_inspirations_kitchen_sink' );

		// Log how long deployment took.
		$install_time = time() - $this->start_time;
		$this->deploy_results['install_time_in_seconds'] = $install_time;

		$this->deploy_results['built_photo_search_placement'] = isset( $this->built_photo_search_placement ) ? $this->built_photo_search_placement : null;

		$this->deploy_results['built_photo_search_log'] = $this->built_photo_search_log;

		$path = $this->new_path;

		update_option( 'boldgrid_has_built_site', 'yes' );
		update_option( 'boldgrid_show_tip_start_editing', 'yes' );

		if ( $this->create_preview_site ) {
			update_option( 'boldgrid_built_as_preview_site', 'yes' );
		}

		// Grab the total coin cost to purchase for publish.
		require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations-purchase-for-publish.php';
		$purchase_for_publish = new Boldgrid_Inspirations_Purchase_For_Publish( array (
				'configDir' => BOLDGRID_BASE_DIR . '/includes/config',
		) );

		$this->deploy_results['total_cost_to_purchase_for_publish'] = $purchase_for_publish->get_total_cost_to_purchase_for_publishing();

		/**
		 * After the deployment process is complete. Fire off a completion event.
		 *
		 * @since 1.5.5
		 */
		do_action( 'boldgrid_inspirations_deploy_complete', get_option( 'boldgrid_install_options', array() ) );

		/*
		 * We inteded for the preview server to return a json string. This was not possible however
		 * because WordPress has data echoing that cannot be canceled.
		 *
		 * For example, WordPress prints a "status log" as it's installing a theme / plugin.
		 * This printing cannot be disabled.
		 *
		 * To work around this, We are surrounding our json data with "[RETURN_ARRAY]". We can use
		 * explode to then get the data we need.
		 */
		if ( $this->is_preview_server ) {
			echo '[RETURN_ARRAY]' . json_encode( $this->deploy_results ) . '[RETURN_ARRAY]';
		}

		// Reach out and hit the front end of the site to make sure all after theme switch hooks are fired.
		Boldgrid_Inspirations_Utility::inline_js_file( 'deploy_stop_and_explain.js' );

		$this->messages->print_heading( 'complete', esc_html__( '&#10003; Installation complete! Redirecting you to the Inspirations dashboard...', 'boldgrid-inspirations' ) . ' <span class="spinner inline"></span>' );

		// Redirect the user to "My Inspiration".
		Boldgrid_Inspirations_Utility::inline_js_oneliner( 'window.location.href = "' . admin_url( 'admin.php?page=my-inspiration&new_inspiration=1' ) . '";' );
	}

	/**
	 * Install sitewide plugins.
	 *
	 * This plugin requests a list of sitewide plugins to be installed, and then installs them.
	 *
	 * @see Boldgrid_Inspirations_Api::get_api_key_hash().
	 */
	public function install_sitewide_plugins() {
		$boldgrid_configs = $this->get_configs();

		$get_plugins_url = $boldgrid_configs['asset_server'] .
			 $boldgrid_configs['ajax_calls']['get_plugins'];

		// Determine the release channel.
		$options = get_option( 'boldgrid_settings' );

		$release_channel = isset( $options['release_channel'] ) ? $options['release_channel'] : 'stable';

		// Build API call arguments:
		$arguments = array (
			'method' => 'POST',
			'body' => array(
				'channel' => $release_channel
			)
		);

		// Get the API key hash.
		$api_key_hash = $this->asset_manager->api->get_api_key_hash();

		if ( ! empty( $api_key_hash ) ) {
			$arguments['body']['key'] = $api_key_hash;
		}

		$response = wp_remote_post( $get_plugins_url, $arguments );

		if ( $response instanceof WP_Error ) {
			throw new Exception( esc_html__( 'Error downloading plugin list.', 'boldgrid-inspirations' ) );
		}

		$plugin_list = json_decode( $response['body'] );

		$plugin_list = isset( $plugin_list->result->data ) ? $plugin_list->result->data : array ();

		// Print plugins we've already installed.
		$this->messages->print_plugins();

		if ( count( $plugin_list ) ) {
			foreach ( $plugin_list as $plugin_list_k => $plugin_list_v ) {
				$slug = explode( '/', $plugin_list_v->plugin_activate_path );
				$slug = $slug[0];
				$this->messages->print_plugin( $plugin_list_v->plugin_title, $slug );

				$this->download_and_install_plugin( $plugin_list_v->plugin_zip_url, $plugin_list_v->plugin_activate_path, $plugin_list_v->version, $plugin_list_v );
			}
		}
	}

	/**
	 * Determine if this install is for a staging site
	 *
	 * @return bool
	 */
	public function is_staging_install() {
		return ( isset( $_POST['staging'] ) && 1 == $_POST['staging'] );
	}

	/**
	 * If we activated any existing plugins on behalf of the user, print this notices
	 */
	public function get_plugin_activation_notices() {
		$plugin_titles = array ();

		$notices = '';

		foreach ( $this->plugin_installation_data as $plugin ) {
			if ( ! empty( $plugin['forked_plugin_activated'] ) && ! empty( $plugin['full_data']->plugin_title ) ) {
				$plugin_titles[] = $plugin['full_data']->plugin_title;
			}
		}

		if ( count( $plugin_titles ) ) {
			$notices = '<div class="updated auto-updated-plugins"><p>' . esc_html__( 'The following existing plugins where activated for use on your new BoldGrid site:', 'boldgrid-inspirations' ) . '</p><ul>';

			foreach ( $plugin_titles as $plugin_title ) {
				$notices .= "<li>{$plugin_title}</li>";
			}

			$notices .= '</ul></div>';
		}

		return $notices;
	}

	/**
	 * Download and activate a plugin.
	 *
	 * @see Boldgrid_Inspirations_Api::get_api_key_hash()
	 * @see \Boldgrid\Library\Form\Forms::get_preferred_slug()
	 * @see \Boldgrid\Library\Form\Forms::check_wpforms()
	 * @see \Boldgrid\Library\Form\Forms::install()
	 *
	 * @param string $url A URL such as "https://downloads.wordpress.org/plugin/quick-cache.140829.zip".
	 * @param string $activate_path A plugin path such as "quick-cache/quick-cache.php".
	 * @param string $version Version number.
	 * @param object $full_plugin_data Plugin details.
	 */
	public function download_and_install_plugin( $url, $activate_path, $version, $full_plugin_data ) {
		$installing_form_plugin = preg_match( '/^(boldgrid-ninja-forms|wpforms)/', $activate_path );

		if ( ! $installing_form_plugin ) {
			$this->messages->add_plugin( $full_plugin_data );
		}

		// If trying to install boldgrid-ninja-forms, then try WPForms instead.
		if ( $installing_form_plugin ) {
			// Prevent PHP notice before trying to run a config script.
			$this->plugin_installation_data[ $activate_path ] = null;

			if ( $this->bgforms->get_preferred_slug() ) {
				$result = $this->bgforms->install();

				$this->bgforms->check_wpforms();

				/*
				 * If we have a $result, WPForms is installed and activated. Otherwise, a BoldGrid
				 * form plugin is already installed.
				 */
				if ( $result ) {
					$this->messages->add_plugin_wpforms();
				}

				if ( ! $this->bgforms->activate_preferred_plugin() ) {
					$this->messages->print_notice( esc_html__( 'Error: Form plugin activation failed!', 'boldgrid-inspirations' ) );
				}

				return;
			}

			$this->messages->add_plugin_wpforms();

			// If $result, then WPForms was installed successfully.
			$result = $this->bgforms->install();

			if ( ! $result ) {
				$this->messages->print_notice( esc_html__( 'Error: Plugin installation failed!', 'boldgrid-inspirations' ) );
			}

			return;
		}

		$plugin_path = ABSPATH . 'wp-content/plugins/';

		// Get BoldGrid data for checking wporg plugins.
		$boldgrid_api_data = get_site_transient( 'boldgrid_api_data' );

		// If an old plugin is installed, then do not install the new.  Ensure activation.
		if ( $boldgrid_api_data && ! empty( $boldgrid_api_data->result->data->wporg_plugins ) ) {
			foreach ( $boldgrid_api_data->result->data->wporg_plugins as $wporg_plugin ) {
				$old_plugin_file = $wporg_plugin->old_slug . '/' . $wporg_plugin->old_slug . '.php';

				// The plugin already exists.
				if ( false !== strpos( $activate_path, $wporg_plugin->slug ) &&
					file_exists( $plugin_path . $old_plugin_file ) ) {

						// Activate, if needed.
						if ( ! $this->external_plugin->is_active( $old_plugin_file ) ) {
							$result = activate_plugin( $old_plugin_file );

							if ( is_wp_error( $result ) ) {
								$this->messages->print_notice( esc_html__( 'Plugin activation failed.', 'boldgrid-inspirations' ) );
							}
						}

						return;
				}
			}
		}

		$boldgrid_configs = $this->get_configs();

		// If ASSET_SERVER in plugin url name, then replace it from configs.
		if ( false !== strpos( $url, 'ASSET_SERVER' ) ) {
			// Replace ASSET_SERVER with the asset server name
			$url = str_replace( 'ASSET_SERVER', $boldgrid_configs['asset_server'], $url );

			$api_key_hash = $this->asset_manager->api->get_api_key_hash();

			// Attach the api key:
			if ( ! empty( $api_key_hash ) ) {
				$url .= '&key=' . $api_key_hash;
			}
		}

		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

		// Check if the version we are trying to install.
		$plugin_version_already_exists = false;

		$absolute_activation_path = $plugin_path . $activate_path;

		if ( file_exists( $absolute_activation_path ) ) {
			$plugin_version_already_exists = true;

			/*
			 * For reference of $comparison:
			 *
			 * -1: An older version of the plugin is installed. Update version using WordPress Updates.
			 *  0: Plugin version is already installed.
			 *  1: A newer version (%1$s) of the plugin is already installed.
			 */
			// $plugin_data = get_plugin_data( $absolute_activation_path );
			// $comparison = version_compare( $plugin_data['Version'], $version );
		}

		// If the user already has the parent plugin skip this installation, init settings:
		$this->plugin_installation_data[$activate_path] = array (
			'forked_plugin_exists'    => false,
			'forked_plugin_active'    => false,
			'forked_plugin_activated' => false,
			'full_data'               => $full_plugin_data
		);

		// Do not install plugins if the forked plugin exists:
		$original_active_path = $activate_path; // <-- overwriting if activating forked plugin

		$forked_plugin_active = false;

		// Check for a forked version.
		if ( ! empty( $full_plugin_data->forked_plugin_path ) &&
			file_exists( $plugin_path . $full_plugin_data->forked_plugin_path ) ) {
				$forked_plugin_active = $this->external_plugin->is_active( $full_plugin_data->forked_plugin_path );

				$this->plugin_installation_data[$activate_path] = array (
					'forked_plugin_active' => $forked_plugin_active,
					'forked_plugin_exists' => true,
				);
		}

		// If the plugin still needs to be installed, then do it.
		if ( ! $plugin_version_already_exists ) {
			$upgrader = new Plugin_Upgrader( new Plugin_Installer_Skin( compact( 'title', 'url', 'nonce', 'plugin', 'api' ) ) );

			/*
			 * Install the plugin.
			 *
			 * There's an issue if:
			 * 1. is_object( $upgrader->skin->result ) &&
			 * 2. ( is_wp_error( $upgrader->skin->result ) || false == $upgrader->skin->result )
			 *
			 * Plugin installation not complete. Either the destination folder already exists, or
			 * the plugin files already exist. Review $upgrader->skin->result->get_error_message().
			 */
			$upgrader->install( $url );
		}

		$boldgrid_plugin_active = $this->external_plugin->is_active( $activate_path );

		// Activate the plugin, if the BoldGrid or forked plugins are not already active.
		if ( ! $boldgrid_plugin_active && ! $forked_plugin_active ) {
			$result = activate_plugin( $activate_path );

			// Check for activation error.
			if ( is_wp_error( $result ) ) {
				$this->messages->print_notice( esc_html__( 'Error: Plugin activation failed!', 'boldgrid-inspirations' ) );
			} elseif ( $this->plugin_installation_data[$original_active_path]['forked_plugin_exists'] && false == $forked_plugin_active ) {
				/*
				 * In the case that the activation of this plugin was a success and the plugin was a
				 * fork, set "forked_plugin_activated" so that we can display a message to the user
				 */
				$this->plugin_installation_data[$original_active_path]['forked_plugin_activated'] = true;
			}
		}
	}

	/**
	 * Include grid system css.
	 *
	 * @return null
	 */
	public function add_grid_system() {
		// Before we can include the grid system, we need to allow css files to be uploaded.
		// @thanks http://www.paulund.co.uk/change-wordpress-upload-mime-types
		add_filter( 'upload_mimes', 'boldgrid_add_custom_mime_types' );

		/**
		 *
		 * @param array $mimes An associative array of mime types
		 *
		 * @return array Merged associative array of mime types.
		 */
		function boldgrid_add_custom_mime_types( $mimes ) {
			return array_merge( $mimes, array (
				'css' => 'text/css'
			) );
		}
	}

	/**
	 * Keep track of the order in which built_photo_search images appear on the page.
	 *
	 * Ultimately, we want to know for this images we've dynamically loaded:
	 *
	 * [built_photo_search_placement] => stdClass Object (
	 * [45] => Array
	 * . (
	 * . . [0] => 9558
	 * . . [1] => 10658
	 * . )
	 *
	 * This equates to:
	 *
	 * [built_photo_search_placement] => stdClass Object (
	 * [remote_page_id] => Array
	 * . (
	 * . . [counter] => asset_id
	 * . . [counter] => asset_id
	 * . )
	 */
	public function set_built_photo_search_placement( $remote_page_id, $counter, $asset_id ) {
		$this->built_photo_search_placement[$remote_page_id][$counter] = $asset_id;
	}

	/**
	 * Set custom homepage
	 */
	public function set_custom_homepage() {
		$homepage_var = '';

		foreach ( $this->theme_details->homepage as $homepage_step_key => $homepage_step_obj ) {
			switch ( $homepage_step_obj->action ) {
				case 'page' :

					$page_type = $homepage_step_obj->page->post_type;

					// only create the page if it doesn't already exist
					$existing_page = get_page_by_title( $homepage_step_obj->page->page_title,
						OBJECT, $page_type );

					if ( null === $existing_page ) {
						// insert the page
						$post['post_content'] = $homepage_step_obj->page->code;
						$post['post_name']    = $homepage_step_obj->page->page_slug;
						$post['post_title']   = $homepage_step_obj->page->page_title;
						$post['post_status']  = 'publish';
						$post['post_type']    = $page_type;

						$post_id = wp_insert_post( $post );
					} else
						$post_id = $existing_page->ID;

					// Take action if we have any featured images.
					if ( 0 != $homepage_step_obj->page->featured_image_asset_id ) {
						$this->asset_manager->download_and_attach_asset( $post_id, true,
							$homepage_step_obj->page->featured_image_asset_id );
					}

					if ( ! empty( $homepage_step_obj->return_value_save_as ) ) {
						switch ( $homepage_step_obj->return_value_save_as ) {
							case 'array_item' :
								$homepage_var[$homepage_step_obj->return_value_save_to][] = $post_id;

								break;

							case 'option' :
								update_option( $homepage_step_obj->return_value_save_to, $post_id );

								break;

							default :
								// if there is a ':' after 'option'
								if ( substr( $homepage_step_obj->return_value_save_as, 0, 7 ) == 'option:' ) {
									$exploded_return_value_save_as = explode( ':', $homepage_step_obj->return_value_save_as );

									$option_value_key = $exploded_return_value_save_as[1];

									// if the key ends in "[]"
									if ( substr( $option_value_key, - 2 ) == '[]' ) {
										$option_value_key = str_replace( '[]', '', $option_value_key );

										$current_option = get_option( $homepage_step_obj->return_value_save_to );

										$current_option[$option_value_key][] = $post_id;

										update_option( $homepage_step_obj->return_value_save_to, $current_option );
									}
								}

								break;
						}
					}

					break;

				case 'option' :

					// update sting option
					if ( empty( $homepage_step_obj->option->value_key ) ) {
						update_option( $homepage_step_obj->option->name, $homepage_step_obj->option->value );
					} else {
						$current_option = get_option( $homepage_step_obj->option->name );

						$current_option[$homepage_step_obj->option->value_key] = $homepage_step_obj->option->value_value;

						update_option( $homepage_step_obj->option->name, $current_option );
					}

					break;

				case 'process_return_value' :
					update_option( $homepage_step_obj->return_value_save_to, $homepage_var[$homepage_step_obj->return_value_save_to] );

					break;

				case 'download_asset' :
					$url_to_uploaded_asset = $this->asset_manager->download_and_attach_asset( false, false, $homepage_step_obj->action_id );

					if ( substr( $homepage_step_obj->return_value_save_as, 0, 7 ) == 'option:' ) {
						$exploded_return_value_save_as = explode( ':', $homepage_step_obj->return_value_save_as );

						$option_value_key = $exploded_return_value_save_as[1];

						$current_option = get_option( $homepage_step_obj->return_value_save_to );

						$current_option[$option_value_key] = $url_to_uploaded_asset;

						update_option( $homepage_step_obj->return_value_save_to, $current_option );
					}

					break;

				case 'get_permalink' :
					$existing_page = get_page_by_title( $homepage_step_obj->page->page_title, OBJECT, $homepage_step_obj->page->post_type );

					$post_id = $existing_page->ID;

					$permalink = get_permalink( $post_id );

					update_option( $homepage_step_obj->return_value_save_to, $permalink );

					break;

				case 'add_widget_text' :

					/**
					 * First we need to add the new text widget to the database
					 */

					// create the widget array
					$widget['title'] = $homepage_step_obj->widget_text->title;
					$widget['text'] = $homepage_step_obj->widget_text->text;
					$widget['filter'] = $homepage_step_obj->widget_text->filter;
					$widget['_multiwidget'] = $homepage_step_obj->widget_text->_multiwidget;

					// get current widgets
					$current_widgets = get_option( 'widget_text' );

					// add our new text widget
					$current_widgets[] = $widget;

					end( $current_widgets );
					$widget_key = key( $current_widgets );

					// update widgets
					update_option( 'widget_text', $current_widgets );

					// Update the sidebar widget.
					$current_sidebar_widgets                                             = get_option( 'sidebars_widgets' );
					$current_sidebar_widgets[$homepage_step_obj->return_value_save_to][] = "text-" . $widget_key;
					update_option( 'sidebars_widgets', $current_sidebar_widgets );

					break;
			}
		}
	}

	/**
	 * Change the permalink structure.
	 *
	 * @since 1.3.6
	 *
	 * @global object $wp_rewrite.
	 *
	 * @param string $structure
	 */
	public function set_permalink_structure( $structure ) {
		global $wp_rewrite;

		$set_permalinks = true;

		/**
		 * Continue with setting permalink structure.
		 *
		 * Filter to allow a plugin to determine whether or not to proceed with this request to
		 * set new permalinks.
		 *
		 * @since 1.3.6
		 *
		 * @param bool $set_permalinks On true, continue on to setting permalinks.
		 */
		$set_permalinks = apply_filters( 'pre_set_permalinks', $set_permalinks );

		if( ! $set_permalinks ) {
			return;
		}

		$wp_rewrite->set_permalink_structure( $structure );

		update_option( 'category_base', '.' );

		/*
		 * We need to make sure that a .htaccess file is being created. If it is not, 404 pages may
		 * be handled by .htaccess rules set in higher directories.
		 *
		 * The parameter we're passing, $hard, is defined by WordPress as so:
		 * Whether to update .htaccess (hard flush) or just update rewrite_rules
		 * transient (soft flush).
		 */
		flush_rewrite_rules( true );
	}

	/**
	 * Set the pde.
	 *
	 * @since 1.7.0
	 */
	private function set_pde() {
		if ( isset( $_POST['boldgrid_pde'] ) ) {
			if ( is_array( $_POST['boldgrid_pde'] ) ) {
				$this->pde = is_array( $_POST['boldgrid_pde'] ) ? $_POST['boldgrid_pde'] : null;
			} else {
				$this->pde = json_decode( stripslashes( $_POST['boldgrid_pde'] ), true );
			}
		} else {
			$this->pde = null;
		}
	}

	/**
	 * Assign a menu_id to all locations
	 */
	public function assign_menu_id_to_all_locations( $menu_id ) {
		/*
		 * Get the current assignment of menu_id's to theme menu locations. Generally will be blank
		 * at this point.
		 */
		$locations = get_theme_mod( 'nav_menu_locations' );

		/*
		 * Get the nav menus registered by current theme.
		 *
		 * $registered_nav_menus = Array
		 * (
		 * * [primary] => Primary Menu
		 * )
		 *
		 * The keys are the locations, while the values are the descriptions.
		 */
		$registered_nav_menus = get_registered_nav_menus();

		/*
		 * There may be a timing issue related to this that we'll need to resolve in the future.
		 * We're trying to get registered_nav_menus before the theme has been able to register them.
		 * If this is the case, the theme data from the asset server may need to contain the
		 * registered nav menus.
		 */
		$additional_menus_to_register = array (
			'primary' => 'Primary menu'
		);

		foreach ( $additional_menus_to_register as $name => $description ) {
			if ( ! isset( $registered_nav_menus[$name] ) ) {
				$registered_nav_menus[$name] = $description;
			}
		}

		// Assign this new menu_id to those locations if they're not already set.
		$locations['primary'] = $menu_id;

		/*
		 * We've finished updating $locations, it now looks like this:
		 *
		 * $locations = Array
		 * (
		 * * [primary] => 2
		 * )
		 */
		set_theme_mod( 'nav_menu_locations', $locations );
	}

	/**
	 * Build the attribution page.
	 */
	public function build_attribution_page() {
		// create the attribution page
		$settings = array (
			'configDir' => BOLDGRID_BASE_DIR . '/includes/config',
			'menu_id'   => $this->primary_menu_id
		);

		require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations-attribution.php';

		$attribution = new Boldgrid_Inspirations_Attribution( $settings );

		$attribution->build_attribution_page();
	}

	/**
	 * Full deployment.
	 *
	 * @return boolean
	 */
	public function full_deploy() {
		$this->create_new_install();

		$this->update_install_options();

		/*
		 * Pass the requested install options to the asset server and return install
		 * options that will be stored in the users WP.
		 */
		$this->remote_install_options();

		// Install the selected theme.
		$deploy_theme_success = $this->deploy_theme();

		// If theme deployemnt fails, then show a message to choose a different theme.
		if ( ! $deploy_theme_success ) {
			// Add info to the deployment log.
			$this->messages->print_notice( esc_html__( 'Theme deployment failed.  Please choose another theme.', 'boldgrid-inspirations' ) );

			return false;
		}

		if( $this->install_blog ) {
			$this->blog = new Boldgrid_Inspirations_Blog( $this->configs );
			$this->blog->create_sidebar_widgets();
		}

		// import the selected page set.
		$this->deploy_page_sets();

		$boldgrid_inspiration_deploy_pages = new Boldgrid_Inspirations_Deploy_Pages( array (
			'post_status' => $this->post_status,
		) );

		// Create temp pages in order to force image creation.
		$this->installed_page_ids = $boldgrid_inspiration_deploy_pages->deploy_temp_pages( $this->full_page_list, $this->installed_page_ids );

		// Download / setup the images required for each page/post.
		$this->deploy_page_sets_media_find_placeholders();
		$this->deploy_page_sets_media_process_image_queue();
		$this->deploy_page_sets_media_replace_placeholders();

		// Remove Temp pages that were created in order to force image creation.
		$boldgrid_inspiration_deploy_pages->cleanup_temp_pages( $this->full_page_list, $this->installed_page_ids );

		// download / setup the primary design elements.
		$this->deploy_pde();

		// create the attribution page.
		$this->build_attribution_page();

		// Install Site Wide Plugins.
		if ( false == $this->is_preview_server ) {
			$this->install_sitewide_plugins();
		}

		$this->finish_deployment();
	}

	/**
	 * Update existing install options
	 *
	 * @param array $options
	 */
	public function update_existing_install_options( $options = array() ) {
		( $existing_options = get_option( 'boldgrid_install_options' ) ) ||
			 ( $existing_options = get_option( 'imhwpb_install_options' ) ) ||
			 ( $existing_options = array () );

		$options_merged = array_merge( $existing_options, $options );

		update_option( 'boldgrid_install_options', $options_merged );
	}

	/**
	 * Check the permalink structure, if no active site, set to "/%postname%/" if needed
	 */
	private function check_permalink_structure() {
		$permalink_structure = get_option( 'permalink_structure' );

		if ( '/%postname%/' != $permalink_structure && ! Boldgrid_Inspirations_Built::has_active_site() ) {
			 $this->set_permalink_structure( '/%postname%/' );
		}
	}

	/**
	 * Deployment
	 */
	public function do_deploy() {
		$boldgrid_configs = $this->get_configs();

		// Set the PHP max_execution_time to 120 seconds (2 minutes):
		@ini_set( 'max_execution_time', 120 );

		// Start XHProf.
		if ( ! empty( $boldgrid_configs['xhprof'] ) && extension_loaded( 'xhprof' ) ) {
			xhprof_enable( XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY );
		}

		$this->get_deploy_details();

		$this->check_permalink_structure();

		$this->start_over();

		$this->survey->deploy();

		/*
		 * During deployment only, allow iframes (Google map iframe). This seems to be required
		 * on multisite / preview servers.
		 */
		add_filter( 'wp_kses_allowed_html', array( $this, 'filter_allowed_html', ), 10, 2 );

		$this->full_deploy();

		// Save report to the log.
		if ( ! empty( $boldgrid_configs['xhprof'] ) && extension_loaded( 'xhprof' ) ) {
			$xhprof_data = xhprof_disable();

			$xhprof_utils_path = '/usr/share/pear/xhprof_lib/utils';

			if ( file_exists( $xhprof_utils_path . '/xhprof_lib.php' ) &&
				 file_exists( $xhprof_utils_path . '/xhprof_runs.php' ) ) {
				require_once $xhprof_utils_path . '/xhprof_lib.php';
				require_once $xhprof_utils_path . '/xhprof_runs.php';

				$xhprof_runs = new XHProfRuns_Default();
				$run_id = $xhprof_runs->save_run( $xhprof_data, 'xhprof_testing' );

				error_log(
					__METHOD__ . ': https://' . $_SERVER['HTTP_HOST'] . '/xhprof/index.php?run=' .
						 $run_id . '&source=xhprof_testing' );
			}
		}
	}

	/**
	 * Modify a dom element's attribute.
	 *
	 * For example, let's say we want to add a new class to an image, 'my-class'. If we simply set
	 * the image's class to 'my-class', then any other classes previously set will be lost. This
	 * method instead takes into consideration any values currently existing for a given attribute.
	 *
	 * @since 1.0.8
	 *
	 * @param object $dom_item A dom element, gathered elsewhere via a DOMDocument object.
	 * @param string $attribute An attribute of dom element, such as 'class' or 'src'.
	 * @param string $value A value to set for the above attribute.
	 * @return string An updated $value.
	 */
	public function dom_element_append_attribute( $dom_item, $attribute, $value ) {
		$current_value = $dom_item->getAttribute( $attribute );

		if ( ! empty( $current_value ) ) {
			$new_value = $current_value . ' ' . $value;
		} else {
			$new_value = $value;
		}

		return $new_value;
	}
}
