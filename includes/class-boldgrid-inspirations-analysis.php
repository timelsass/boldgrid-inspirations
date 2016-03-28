<?php

/**
 * BoldGrid Source Code
 *
 * @package Boldgrid_Inspirations_Analysis
 * @copyright BoldGrid.com
 * @version $Id$
 * @author BoldGrid.com <wpb@boldgrid.com>
 */

/**
 * BoldGrid Inspiration Analysis class.
 *
 * This class is static; functions can be called anywhere after Inspirations hooks have been added.
 *
 * @since 1.1
 */
class Boldgrid_Inspirations_Analysis {
	/**
	 * Analysis enabled.
	 *
	 * @since 1.1
	 * @access public
	 * @static
	 *
	 * @var bool $enabled TRUE if analysis processing is enabled, FALSE if not enabled or unknown.
	 */
	public static $enabled = false;

	/**
	 * Start time.
	 *
	 * @since 1.1
	 * @access public
	 * @static
	 *
	 * @var float $start_time The analysis start time, in seconds, from microtime().
	 */
	public static $start_time = null;

	/**
	 * End time.
	 *
	 * @since 1.1
	 * @access public
	 * @static
	 *
	 * @var float $end_time The analysis end time, in seconds, from microtime().
	 */
	public static $end_time = null;

	/**
	 * Log entry array.
	 *
	 * @since 1.1
	 * @access public
	 * @static
	 *
	 * @var array $log_entries {
	 *      An array of log entry arrays.
	 *
	 *      @type float $time Time in seconds, from microtime().
	 *
	 *      @type int $memory The current amount of memory used, in bytes.
	 *
	 *      @type string $note A note for the log entry.
	 *      }
	 */
	public static $log_entries = array ();

	/**
	 * Start analysis and record the start time.
	 *
	 * @since 1.1
	 * @static
	 *
	 * @return null
	 */
	public static function start() {
		// If analysis is not enabled, then check configs to see if it should be.

		// Get the configs:
		$configs = Boldgrid_Inspirations_Config::get_format_configs();

		if ( false === empty( $configs['analysis_enabled'] ) && true === $configs['analysis_enabled'] ) {
			self::$enabled = true;
		} else {
			return;
		}

		// Check if already started.
		if ( false === is_null( self::$start_time ) ) {
			return;
		}

		// Set the start time.
		self::$start_time = microtime( true );

		// Record time and memory usage in array.
		self::$log_entries[] = array (
			'time' => microtime( true ),
			'memory' => memory_get_usage(),
			'note' => 'Analysis Started.'
		);

		return;
	}

	/**
	 * Record a log entry in the array.
	 *
	 * @since 1.1
	 * @static
	 *
	 * @param string $note
	 *        	A note for the log entry.
	 * @return null
	 */
	public static function log_entry( $note = null ) {
		// If not enabled, then return.
		if ( false === self::$enabled ) {
			return;
		}

		// Record the start time, if needed.
		if ( is_null( self::$start_time ) ) {
			self::start();
		}

		// Validate input $note.
		if ( empty( $note ) || false === is_string( $note ) ) {
			$note = null;
		}

		// Record time and memory usage in array.
		self::$log_entries[] = array (
			'time' => microtime( true ),
			'memory' => memory_get_usage(),
			'note' => $note
		);

		return;
	}

	/**
	 * Stop analysis and record the end time.
	 *
	 * @since 1.1
	 * @static
	 *
	 * @return null
	 */
	public static function stop() {
		// If not enabled, then return.
		if ( false === self::$enabled ) {
			return;
		}

		// Record time and peak memory usage in array.
		self::$log_entries[] = array (
			'time' => microtime( true ),
			'memory' => memory_get_peak_usage(),
			'note' => 'Analysis complete.  Recorded peak memory usage.'
		);

		// Set the end time.
		self::$end_time = microtime( true );

		return;
	}

	/**
	 * Get the analysis report.
	 *
	 * This function will stop the analysis and return a report.
	 *
	 * @since 1.1
	 * @static
	 *
	 * @param bool $write_log
	 *        	If TRUE, then write the report to the log file.
	 * @return string A string containing the completed analysis report.
	 */
	public static function report( $write_log = false ) {
		// Check if analysis started.
		if ( is_null( self::$start_time ) ) {
			return 'Analysis processing has not been started.' . PHP_EOL;
		}

		// If not enabled, then return.
		if ( false === self::$enabled ) {
			return 'Analysis processing is not enabled in the configuration.' . PHP_EOL;
		}

		// Stop the analysis, if needed.
		if ( is_null( self::$end_time ) ) {
			self::stop();
		}

		// Print report.
		$report = 'Analysis Report:' . PHP_EOL;

		$report .= 'Started: ' . date( 'Y-m-d H:i:s', self::$start_time ) . PHP_EOL;

		$report .= 'Completed: ' . date( 'Y-m-d H:i:s', self::$end_time ) . PHP_EOL;

		$report .= 'Duration: ' .
			 number_format( ( self::$end_time - self::$start_time ), 2, '.', '' ) . ' seconds' .
			 PHP_EOL;

		$report .= 'Log entry count: ' . count( self::$log_entries ) . PHP_EOL;

		$report .= 'Log entries: (Unix Time | Memory in Bytes | Note):' . PHP_EOL;

		foreach ( self::$log_entries as $log_entry ) {
			$report .= $log_entry['time'] . ' | ' . $log_entry['memory'] . ' | ' . $log_entry['note'] .
				 PHP_EOL;
		}

		// If requested, write to the log file.
		if ( true === $write_log ) {
			$log_file = ABSPATH . 'boldgrid-inspirations-analysis.log';

			file_put_contents( $log_file, $report, FILE_APPEND );

			chmod( $log_file, 0600 );
		}

		// Return the report.
		return $report;
	}
}
