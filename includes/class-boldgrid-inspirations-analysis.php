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
	 * @staticvar
	 *
	 * @var bool $enabled TRUE if analysis processing is enabled, FALSE if not enabled or unknown.
	 */
	public static $enabled = false;

	/**
	 * Start time.
	 *
	 * @since 1.1
	 * @access public
	 * @staticvar
	 *
	 * @var float $start_time The analysis start time, in seconds, from microtime().
	 */
	public static $start_time = null;

	/**
	 * End time.
	 *
	 * @since 1.1
	 * @access public
	 * @staticvar
	 *
	 * @var float $end_time The analysis end time, in seconds, from microtime().
	 */
	public static $end_time = null;

	/**
	 * Log entry array.
	 *
	 * @since 1.1
	 * @access public
	 * @staticvar
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
	 * Log file path.
	 *
	 * @since 1.2
	 * @access protected
	 * @staticvar
	 *
	 * @var string
	 */
	protected static $log_file = 'boldgrid-inspirations-analysis.log';

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

		// Get backtrace data array, with no object.
		$backtrace = debug_backtrace( false );

		// Determine value for $function and $parent_function.
		$function = '';
		$parent_function = '';

		if ( false === empty( $backtrace ) ) {
			// Determine value for $function.
			if ( false === empty( $backtrace[1]['class'] ) ) {
				$function .= $backtrace[1]['class'];
			}

			if ( false === empty( $backtrace[1]['type'] ) ) {
				$function .= $backtrace[1]['type'];
			}

			if ( false === empty( $backtrace[1]['function'] ) ) {
				$function .= $backtrace[1]['function'];
			}

			// Determine value for $parent_function.
			if ( false === empty( $backtrace[2]['class'] ) ) {
				$parent_function .= $backtrace[2]['class'];
			}

			if ( false === empty( $backtrace[2]['type'] ) ) {
				$parent_function .= $backtrace[2]['type'];
			}

			if ( false === empty( $backtrace[2]['function'] ) ) {
				$parent_function .= $backtrace[2]['function'];
			}
		}

		// Record time and memory usage in array.
		self::$log_entries[] = array (
			'time' => microtime( true ),
			'memory' => memory_get_usage(),
			'note' => $note,
			'function' => $function,
			'parent_function' => $parent_function
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
	 *        	If TRUE, then write the report to the log file. Default is FALSE.
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
			 number_format( ( self::$end_time - self::$start_time ), 4, '.', '' ) . ' seconds' .
			 PHP_EOL;

		$report .= 'Log entry count: ' . count( self::$log_entries ) . PHP_EOL;

		$report .= 'Log entries: ' . PHP_EOL;

		$report .= str_pad( 'Unix Time', 15 ) . ' | ' . str_pad( 'Duration', 6 ) . ' | ' .
			 str_pad( 'Mem Bytes', 9 ) . ' | ' . str_pad( 'Parent Function', 50 ) . ' | ' .
			 str_pad( 'Current Function', 50 ) . ' | Note' . PHP_EOL;

		// Initialize $index for identifying the previous log entry.
		$index = 0;

		foreach ( self::$log_entries as $log_entry ) {
			// Determine duration since last log line, of not the first line.
			if ( 0 !== $index ) {
				$duration = number_format(
					( $log_entry['time'] - self::$log_entries[$index - 1]['time'] ), 4, '.', '' );
			} else {
				$duration = 'N/A';
			}

			// Get the parent function.
			$parent_function = empty( $log_entry['parent_function'] ) ? 'N/A' : $log_entry['parent_function'];

			// Get the current function.
			$current_function = empty( $log_entry['function'] ) ? 'N/A' : $log_entry['function'];

			// Add the log entry line.
			$report .= str_pad( $log_entry['time'], 15 ) . ' | ' .
				 str_pad( $duration, 8, ' ', STR_PAD_LEFT ) . ' | ' .
				 str_pad( $log_entry['memory'], 9, ' ', STR_PAD_LEFT ) . ' | ' .
				 str_pad( $parent_function, 50 ) . ' | ' . str_pad( $current_function, 50 ) . ' | ' .
				 $log_entry['note'] . PHP_EOL;

			// Increment $index.
			$index ++;
		}

		// Add a line break and the end of the report.
		$report .= PHP_EOL;

		// If requested, write to the log file.
		if ( true === $write_log ) {
			$log_file_path = ABSPATH . self::$log_file;
			file_put_contents( $log_file_path, $report, FILE_APPEND );

			chmod( $log_file_path, 0600 );
		}

		// Return the report.
		return $report;
	}
}
