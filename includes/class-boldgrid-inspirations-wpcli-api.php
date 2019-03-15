<?php
/**
 * BoldGrid Source Code
 *
 * @package Boldgrid_Inspirations_Wpcli
 * @copyright BoldGrid.com
 * @version $Id$
 * @author BoldGrid <support@boldgrid.com>
 */

/**
 * BoldGrid Inspirations Deploy API class.
 *
 * This class is responsible for making api calls related to the deployment process.
 *
 * @since 1.7.0
 */
class Boldgrid_Inspirations_Wpcli_Api {

	/**
	 * Args.
	 *
	 * @since 1.7.0
	 * @var array
	 */
	private $args;

	/**
	 * Assoc args.
	 *
	 * @since 1.7.0
	 * @var array
	 */
	private $assoc_args;

	/**
	 * Configs.
	 *
	 * Set with a call to $this->init().
	 *
	 * @since 1.7.0
	 * @var array
	 */
	private $configs;

	/**
	 * An instance of the Boldgrid_Inspirations_Deploy_Api class.
	 *
	 * Set with a call to $this->init().
	 *
	 * @since 1.7.0
	 * @var Boldgrid_Inspirations_Deploy_Api
	 */
	private $deploy_api;

	/**
	 *
	 */
	public function __construct( $args, $assoc_args ) {
		$this->args = $args;

		$this->assoc_args = $assoc_args;
	}

	/**
	 * Get plugins.
	 *
	 * @since 1.7.0
	 */
	public function get_plugins() {
		$this->init();

		$channel = ! empty( $this->assoc_args['channel'] ) ? $this->assoc_args['channel'] : 'stable';
		$key     = ! empty( $this->assoc_args['key'] ) ? $this->assoc_args['key'] : $this->configs['api_key'];

		$args = array(
			'channel' => $channel,
			'key'     => $key,
		);

		$plugins = $this->deploy_api->get_plugins( $args );

		if ( 200 !== $plugins->status ) {
			WP_CLI::error( 'Unknown error. ' . (int) $plugins->status . ' response received from API server.' );
		} else {
			WP_CLI::log( print_r( $plugins->result->data, 1 ) );
		}
	}

	/**
	 * Init.
	 *
	 * @since 1.7.0
	 */
	public function init() {
		if ( ! empty( $this->configs ) ) {
			return;
		}

		$this->configs = Boldgrid_Inspirations_Config::get_format_configs();

		$this->deploy_api = new Boldgrid_Inspirations_Deploy_Api( $this->configs );
	}
}
