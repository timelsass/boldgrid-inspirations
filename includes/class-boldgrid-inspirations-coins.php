<?php

/**
 * BoldGrid Source Code
 *
 * @package Boldgrid_Inspirations_Coins
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
 * BoldGrid Coins class
 */
class Boldgrid_Inspirations_Coins extends Boldgrid_Inspirations {
	
	/**
	 * Constructor
	 *
	 * @param unknown $pluginPath        	
	 */
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * Get the user's coin balance.
	 *
	 * First, try getting it from the transient.
	 * If it doesn't exist there, reach out to the asset server to get it.
	 *
	 * @return number|mixed
	 */
	public function get_coin_balance() {
		$user_coin_balance = get_transient( 'boldgrid_coin_balance' );
		
		// If we have an invalid balance, get the latest balance from the asset server.
		if ( false === $user_coin_balance && true === parent::get_is_asset_server_available() ) {
			// Configure our API call
			$boldgrid_configs = $this->get_configs();
			
			$url_to_get_balance = $boldgrid_configs['asset_server'] .
				 $boldgrid_configs['ajax_calls']['get_coin_balance'];
			
			$arguments = array (
				'method' => 'POST',
				'body' => array (
					'key' => $this->api_key_hash 
				) 
			);
			
			// Make API Call
			$response = wp_remote_post( $url_to_get_balance, $arguments );
			
			// If the API call failed...
			if ( is_wp_error( $response ) ) {
				// DEUBG
				error_log( 
					print_r( 
						array (
							'ERROR' => 'Error getting copyright coin balance.',
							'$url_to_get_balance' => $url_to_get_balance,
							'$arguments' => $arguments,
							'$response' => $response 
						), 1 ) );
				return '?';
			}
			
			// Process API call results and save transient:
			$json_decode_response = json_decode( $response['body'] );
			$user_coin_balance = $json_decode_response->result->data->balance;
			set_transient( 'boldgrid_coin_balance', $user_coin_balance, 10 * MINUTE_IN_SECONDS );
		}
		
		return false !== $user_coin_balance ? $user_coin_balance : '?';
	}
}
