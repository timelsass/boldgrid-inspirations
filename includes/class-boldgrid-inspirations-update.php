<?php

/**
 * BoldGrid Source Code
 *
 * @package Boldgrid_Inspirations_Update
 * @copyright BoldGrid.com
 * @version $Id$
 * @author BoldGrid.com <wpb@boldgrid.com>
 */

// Prevent direct calls
if ( ! defined( 'WPINC' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

/**
 * BoldGrid Inspirations (core) update class
 */
class Boldgrid_Inspirations_Update {
	
	/**
	 * BoldGrid Inspirations class object
	 *
	 * @var object
	 */
	private static $boldgrid_inspirations = null;
	
	/**
	 * Setter for the BoldGrid Inspirations class object
	 *
	 * @param object $boldgrid_inspirations        	
	 *
	 * @return bool
	 */
	private static function set_boldgrid_inspirations( $boldgrid_inspirations ) {
		self::$boldgrid_inspirations = $boldgrid_inspirations;
		
		return true;
	}
	
	/**
	 * Getter for the BoldGrid Inspirations class object
	 *
	 * @return object Boldgrid_Inspirations
	 */
	public static function get_boldgrid_inspirations() {
		return self::$boldgrid_inspirations;
	}
	
	/**
	 * Constructor
	 *
	 * @param object $boldgrid_inspirations        	
	 */
	public function __construct( $boldgrid_inspirations ) {
		// Set the BoldGrid Inspirations class object:
		self::set_boldgrid_inspirations( $boldgrid_inspirations );
		
		// Only for wp-admin:
		if ( is_admin() ) {
			// Get the current WordPress page filename:
			global $pagenow;
			
			// Add filters to modify plugin update transient information:
			if ( 'plugins.php' == $pagenow || 'update-core.php' == $pagenow ||
				 'plugin-install.php' == $pagenow ||
				 ( 'admin-ajax.php' == $pagenow && 'update-plugin' == $_REQUEST['action'] ) ) {
				// Add filters:
				add_filter( 'pre_set_site_transient_update_plugins', 
					array (
						$this,
						'custom_plugins_transient_update' 
					), 10, 3 );
				
				add_filter( 'plugins_api', 
					array (
						$this,
						'custom_plugins_transient_update' 
					), 10, 3 );
				
				// Force WP to check for updates, don't rely on cache / transients.
				add_filter( 'site_transient_update_plugins', 
					array (
						$this,
						'site_transient_update_plugins' 
					), 10 );
			}
			
			// Add filters to modify theme update transient information:
			if ( 'themes.php' == $pagenow || 'update-core.php' == $pagenow ) {
				add_filter( 'pre_set_site_transient_update_themes', 
					array (
						$this,
						'custom_themes_transient_update' 
					), 10, 3 );
				
				add_filter( 'site_transient_update_themes', 
					array (
						$this,
						'custom_themes_transient_update' 
					), 10, 3 );
			}
		}
	}
	
	/**
	 * Update api data transient from data on our asset server
	 *
	 * @return object $boldgrid_api_data or false
	 */
	public static function update_api_data() {
		// Get api data transient:
		if ( is_multisite() ) {
			$boldgrid_api_data = get_site_transient( 'boldgrid_api_data' );
		} else {
			$boldgrid_api_data = get_transient( 'boldgrid_api_data' );
		}
		
		// If the API data was just retrieved and is ok, then just return it:
		if ( ! empty( $boldgrid_api_data ) && isset( $boldgrid_api_data->updated ) &&
			 $boldgrid_api_data->updated >= time() - 60 ) {
			
			return $boldgrid_api_data;
		}
		
		// Initialize $boldgrid_api_data:
		$boldgrid_api_data = null;
		
		// Get the BoldGrid Inspirations class object:
		$boldgrid_inspirations = self::get_boldgrid_inspirations();
		
		// Get BoldGrid Inspirations configs, or just set the required info:
		if ( null !== $boldgrid_inspirations ) {
			$boldgrid_configs = $boldgrid_inspirations->get_configs();
		} else {
			$config_include_path = BOLDGRID_BASE_DIR .
				 '/includes/class-boldgrid-inspirations-config.php';
			
			if ( file_exists( $config_include_path ) ) {
				require_once $config_include_path;
				
				$boldgrid_configs = Boldgrid_Inspirations_Config::get_format_configs();
			} else {
				$boldgrid_configs['ajax_calls']['get_version'] = '/api/plugin/check-version';
			}
		}
		
		// If we have no transient but do have configs, then get data and set transient:
		if ( ! empty( $boldgrid_configs ) ) {
			// Load the Boldgrid_Inspirations class if needed:
			if ( ! class_exists( 'Boldgrid_Inspirations' ) ) {
				require_once BOLDGRID_BASE_DIR . '/includes/class-boldgrid-inspirations.php';
			}
			
			// Get the latest version information (API call):
			$boldgrid_api_data = Boldgrid_Inspirations::boldgrid_api_call( 
				$boldgrid_configs['ajax_calls']['get_version'] );
			
			// Check asset server availability:
			if ( isset( $boldgrid_api_data->status ) ) {
				Boldgrid_Inspirations::set_is_asset_server_available( true );
			} else {
				Boldgrid_Inspirations::set_is_asset_server_available( false );
				
				return false;
			}
			
			// Fail if we do not have success:
			if ( 200 != $boldgrid_api_data->status || 'OK' != $boldgrid_api_data->message ) {
				error_log( 
					__METHOD__ . ': Failed to get valid updated boldgrid_api_data.  ' . print_r( 
						array (
							'uri' => $boldgrid_configs['ajax_calls']['get_version'],
							'$boldgrid_api_data' => $boldgrid_api_data 
						), true ) );
				
				return false;
			}
			
			// Add the current timestamp (in seconds):
			$boldgrid_api_data->updated = time();
			
			// Set api data transient, expired in 8 hours:
			if ( is_multisite() ) {
				delete_site_transient( 'boldgrid_api_data' );
				set_site_transient( 'boldgrid_api_data', $boldgrid_api_data, 8 * HOUR_IN_SECONDS );
			} else {
				delete_transient( 'boldgrid_api_data' );
				set_transient( 'boldgrid_api_data', $boldgrid_api_data, 8 * HOUR_IN_SECONDS );
			}
			
			// Update boldgrid_reseller option:
			$boldgrid_reseller_array = array ();
			
			foreach ( $boldgrid_api_data->result->data as $key => $value ) {
				if ( preg_match( '/^reseller_/', $key ) ) {
					$boldgrid_reseller_array[$key] = $boldgrid_api_data->result->data->$key;
				}
			}
			
			// Set the reseller option from api data, or mark as nobrand if no reseller data:
			if ( count( $boldgrid_reseller_array ) ) {
				update_option( 'boldgrid_reseller', $boldgrid_reseller_array );
			} else {
				update_option( 'boldgrid_reseller', 
					array (
						'reseller_nobrand' => true 
					) );
			}
		}
		
		return $boldgrid_api_data;
	}
	
	/**
	 * Update the plugin update transient
	 *
	 * @param object $transient        	
	 * @return object $transient
	 */
	public function custom_plugins_transient_update( $transient, $plugin_info = null, $plugin_info_obj = null ) {
		// Get api data transient:
		if ( is_multisite() ) {
			$boldgrid_api_data = get_site_transient( 'boldgrid_api_data' );
		} else {
			$boldgrid_api_data = get_transient( 'boldgrid_api_data' );
		}
		
		// Get the BoldGrid Inspirations class object:
		$boldgrid_inspirations = self::get_boldgrid_inspirations();
		
		// Get BoldGrid Inspirations configs:
		$boldgrid_configs = $boldgrid_inspirations->get_configs();
		
		// If the api data transient does not exist or is a force check, then get the data and set
		// it:
		if ( empty( $boldgrid_api_data ) || isset( $_GET['force-check'] ) ) {
			$boldgrid_api_data = self::update_api_data();
		}
		
		// If we have no data, then return unchanged plugin update transient:
		if ( false == $boldgrid_api_data ) {
			return $transient;
		}
		
		// Get global $pagenow (current page filename):
		global $pagenow;
		
		// Create a new object to be injected into transient:
		if ( 'plugin-install.php' == $pagenow && isset( $_GET['plugin'] ) &&
			 'boldgrid-inspirations' == $_GET['plugin'] ) {
			// For version information iframe (/plugin-install.php):
			$transient = new stdClass();
			
			// If we have section data, then prepare it for use:
			if ( ! empty( $boldgrid_api_data->result->data->sections ) ) {
				// Remove new lines and double-spaces, to help prevent a broken JSON set:
				$boldgrid_api_data->result->data->sections = preg_replace( '/\s+/', ' ', 
					trim( $boldgrid_api_data->result->data->sections ) );
				
				// Convert the JSON set into an array:
				$transient->sections = json_decode( $boldgrid_api_data->result->data->sections, 
					true );
				
				// If we have data, format it for use, else set a default message:
				if ( ! empty( $transient->sections ) && count( $transient->sections ) ) {
					foreach ( $transient->sections as $section => $section_data ) {
						$transient->sections[$section] = html_entity_decode( $section_data, 
							ENT_QUOTES );
					}
				} else {
					$transient->sections['description'] = 'Data not available';
				}
			} else {
				$transient->sections['description'] = 'Data not available';
			}
			
			// Set the other elements:
			$transient->name = $boldgrid_api_data->result->data->title;
			$transient->requires = $boldgrid_api_data->result->data->requires_wp_version;
			$transient->tested = $boldgrid_api_data->result->data->tested_wp_version;
			// $transient->downloaded = $boldgrid_api_data->result->data->downloads;
			$transient->last_updated = $boldgrid_api_data->result->data->release_date;
			$transient->download_link = $boldgrid_configs['asset_server'] .
				 $boldgrid_configs['ajax_calls']['get_asset'] . '?key=' .
				 $boldgrid_configs['api_key'] . '&id=' . $boldgrid_api_data->result->data->asset_id;
			
			if ( ! empty( $boldgrid_api_data->result->data->compatibility ) && null !== ( $compatibility = json_decode( 
				$boldgrid_api_data->result->data->compatibility, true ) ) ) {
				$transient->compatibility = $boldgrid_api_data->result->data->compatibility;
			}
			
			/*
			 * Not currently showing ratings.
			 * if ( ! ( empty( $boldgrid_api_data->result->data->rating ) ||
			 * empty( $boldgrid_api_data->result->data->num_ratings ) ) ) {
			 * $transient->rating = ( float ) $boldgrid_api_data->result->data->rating;
			 * $transient->num_ratings = ( int ) $boldgrid_api_data->result->data->num_ratings;
			 * }
			 */
			
			$transient->added = '2015-03-19';
			
			if ( ! empty( $boldgrid_api_data->result->data->siteurl ) ) {
				$transient->homepage = $boldgrid_api_data->result->data->siteurl;
			}
			
			if ( ! empty( $boldgrid_api_data->result->data->tags ) &&
				 null !== ( $tags = json_decode( $boldgrid_api_data->result->data->tags, true ) ) ) {
				$transient->tags = $boldgrid_api_data->result->data->tags;
			}
			
			if ( ! empty( $boldgrid_api_data->result->data->banners ) && null !== ( $banners = json_decode( 
				$boldgrid_api_data->result->data->banners, true ) ) ) {
				$transient->banners = $banners;
			}
			
			$transient->plugin_name = 'boldgrid-inspirations.php';
			$transient->slug = 'boldgrid-inspirations';
			$transient->version = $boldgrid_api_data->result->data->version;
			$transient->new_version = $boldgrid_api_data->result->data->version;
			// $transient->active_installs = false;
		} elseif ( 'plugins.php' == $pagenow || 'update-core.php' == $pagenow ||
			 'admin-ajax.php' == $pagenow ) {
			// For plugins.php and update-core.php pages:
			$obj = new stdClass();
			$obj->slug = 'boldgrid-inspirations';
			$obj->plugin = 'boldgrid-inspirations/boldgrid-inspirations.php';
			$obj->new_version = $boldgrid_api_data->result->data->version;
			
			if ( ! empty( $boldgrid_api_data->result->data->siteurl ) ) {
				$obj->url = $boldgrid_api_data->result->data->siteurl;
			}
			
			$obj->package = $boldgrid_configs['asset_server'] .
				 $boldgrid_configs['ajax_calls']['get_asset'] . '?key=' .
				 $boldgrid_configs['api_key'] . '&id=' . $boldgrid_api_data->result->data->asset_id;
			
			$plugin_data = get_plugin_data( BOLDGRID_BASE_DIR . '/boldgrid-inspirations.php', 
				false );
			
			if ( $plugin_data['Version'] != $boldgrid_api_data->result->data->version ) {
				$transient->response[$obj->plugin] = $obj;
				$transient->tested = $boldgrid_api_data->result->data->tested_wp_version;
			} else {
				$transient->no_update[$obj->plugin] = $obj;
			}
		}
		
		return $transient;
	}
	
	/**
	 * Update the theme update transient
	 *
	 * @param object $transient        	
	 * @return object $transient
	 */
	public function custom_themes_transient_update( $transient, $theme_info = null, $theme_info_obj = null ) {
		// If we do not need to check for an update, then just return unchanged transient:
		if ( empty( $transient->checked ) ) {
			return $transient;
		}
		
		// Get global $pagenow
		global $pagenow;
		
		// Check to see if we are on a page which requires a theme version check, return if not:
		if ( 'themes.php' != $pagenow && 'update-core.php' != $pagenow ) {
			return $transient;
		}
		
		// Get api data transient:
		if ( is_multisite() ) {
			$boldgrid_api_data = get_site_transient( 'boldgrid_api_data' );
		} else {
			$boldgrid_api_data = get_transient( 'boldgrid_api_data' );
		}
		
		// Get the BoldGrid Inspirations class object for getting configs:
		$boldgrid_inspirations = self::get_boldgrid_inspirations();
		
		// Get configs:
		$boldgrid_configs = $boldgrid_inspirations->get_configs();
		
		// If the api data transient does not exist or is a force check, then get the data and set
		// it:
		if ( empty( $boldgrid_api_data ) || isset( $_GET['force-check'] ) ) {
			$boldgrid_api_data = self::update_api_data( $boldgrid_configs );
		}
		
		// If we have no data, then return:
		if ( false == $boldgrid_api_data ) {
			return $transient;
		}
		
		// Get installed themes (array of WP_Theme objects):
		$installed_themes = wp_get_themes();
		
		// If themes are found, then iterate through them, adding update info for our themes:
		if ( count( $installed_themes ) ) {
			foreach ( $installed_themes as $installed_theme ) {
				// If the current theme is a BoldGrid theme, then check for an upgrade:
				if ( strpos( $installed_theme->get( 'TextDomain' ), 'boldgrid' ) !== false ) {
					// Get the boldgrid-theme-id from the Tags line in the stylesheet:
					$tags = $installed_theme->get( 'Tags' );
					
					// Iterate through the tags to find theme id (boldgrid-theme-id-##):
					foreach ( $tags as $tag ) {
						if ( preg_match( '/^boldgrid-theme-([0-9]+)$/', $tag, $matches ) ) {
							$boldgrid_tag = $matches[0];
							$theme_id = $matches[1];
							unset( $matches );
							
							break;
						}
					}
					
					// Compare versions:
					if ( isset( $theme_id ) && isset( 
						$boldgrid_api_data->result->data->theme_versions->$theme_id->version ) && version_compare( 
						$installed_theme->Version, 
						$boldgrid_api_data->result->data->theme_versions->$theme_id->version, '<' ) ) {
						
						// Get the theme slug, name, and theme URI:
						$slug = $installed_theme->get_template();
						$theme_name = $installed_theme->get( 'Name' );
						$theme_uri = $installed_theme->get( 'ThemeURI' );
						
						// Add array elements to the transient:
						$transient->response[$slug]['theme'] = $slug;
						$transient->response[$slug]['new_version'] = $boldgrid_api_data->result->data->theme_versions->$theme_id->version;
						
						// URL for the new theme version information iframe:
						$transient->response[$slug]['url'] = empty( $theme_uri ) ? '//www.boldgrid.com/themes/' .
							 strtolower( $theme_name ) : $theme_uri;
						
						// Theme package download link:
						$transient->response[$slug]['package'] = isset( 
							$boldgrid_api_data->result->data->theme_versions->$theme_id->package ) ? $boldgrid_api_data->result->data->theme_versions->$theme_id->package : null;
						
						// $transient->response[$slug]['browse'] = 'updated';
						$transient->response[$slug]['author'] = $installed_theme->Author;
						$transient->response[$slug]['Tag'] = $installed_theme->Tags;
						$transient->response[$slug]['search'] = $boldgrid_tag;
						$transient->response[$slug]['fields'] = array (
							'version' => $boldgrid_api_data->result->data->theme_versions->$theme_id->version,
							'author' => $installed_theme->Author,
							// 'preview_url' => '',
							// 'screenshot_url' = '',
							// 'screenshot_count' => 0,
							// 'screenshots' => array (),
							// 'sections' => array (),
							'description' => $installed_theme->Description,
							'download_link' => $transient->response[$slug]['package'],
							'name' => $installed_theme->Name,
							'slug' => $slug,
							'tags' => $installed_theme->Tags,
							// 'contributors' => '',
							'last_updated' => $boldgrid_api_data->result->data->theme_versions->$theme_id->updated,
							'homepage' => ( isset( $boldgrid_api_data->result->data->siteurl ) ? $boldgrid_api_data->result->data->siteurl : 'http://www.boldgrid.com/' ) 
						);
						unset( $theme_id );
					}
				}
			}
		}
		
		// Return the transient:
		return $transient;
	}
	
	/**
	 * Force WP to check for updates, don't rely on cache / transients.
	 *
	 * @param object $value        	
	 * @return object
	 */
	public function site_transient_update_plugins( $value ) {
		global $pagenow;
		
		// Only require fresh data IF user is clicking "Check Again".
		if ( 'update-core.php' != $pagenow || ! isset( $_GET['force-check'] ) ) {
			return $value;
		}
		
		// Set the last_checked to 1, so it will trigger the timeout and check again.
		if ( isset( $value->last_checked ) ) {
			$value->last_checked = 1;
		}
		
		return $value;
	}
}
