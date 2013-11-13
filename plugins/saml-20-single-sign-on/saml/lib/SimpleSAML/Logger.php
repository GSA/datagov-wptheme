<?php

/**
 * A class for logging
 *
 * @author Lasse Birnbaum Jensen, SDU.
 * @author Andreas Åkre Solberg, UNINETT AS. <andreas.solberg@uninett.no>
 * @package simpleSAMLphp
 * @version $ID$
 */

interface SimpleSAML_Logger_LoggingHandler {
    function log_internal($level,$string);
}

class SimpleSAML_Logger {
	private static $loggingHandler = null;
	private static $logLevel = null;
	
	private static $captureLog = FALSE;
	private static $capturedLog = array();

	/**
	 * Array with log messages from before we
	 * initialized the logging handler.
	 *
	 * @var array
	 */
	private static $earlyLog = array();


	/**
	 * This constant defines the string we set the trackid to while we are fetching the
	 * trackid from the session class. This is used to prevent infinite recursion.
	 */
	private static $TRACKID_FETCHING = '_NOTRACKIDYET_';

	/**
	 * This variable holds the trackid we have retrieved from the session class.
	 * It can also hold NULL, in which case we haven't fetched the trackid yet, or
	 * TRACKID_FETCHING, which means that we are fetching the trackid now.
	 */
	private static $trackid = null;

/*
	 *		LOG_ERR				No statistics, only errors
	 *		LOG_WARNING			No statistics, only warnings/errors
	 *		LOG_NOTICE			Statistics and errors 
	 *		LOG_INFO			Verbose logs
	 *		LOG_DEBUG			Full debug logs - not reccomended for production

*/
	const EMERG = 0;
	const ALERT = 1;
	const CRIT = 2;
	const ERR = 3;
	const WARNING = 4;
	const NOTICE = 5;
	const INFO = 6;
	const DEBUG = 7;

	static function emergency($string) {
		self::log_internal(self::EMERG,$string);
	}

	static function critical($string) {
		self::log_internal(self::CRIT,$string);
	}

	static function alert($string) {
		self::log_internal(self::ALERT,$string);
	}

	static function error($string) {
		self::log_internal(self::ERR,$string);
	}

	static function warning($string) {
		self::log_internal(self::WARNING,$string);
	}

	/**
	 * We reserve the notice level for statistics, so do not use
	 * this level for other kind of log messages.
	 */
	static function notice($string) {
		self::log_internal(self::NOTICE,$string);
	}

	/**
	 * Info messages is abit less verbose than debug messages. This is useful
	 * for tracing a session. 
	 */
	static function info($string) {
		self::log_internal(self::INFO,$string);
	}
	
	/**
	 * Debug messages is very verbose, and will contain more inforation than 
	 * what is neccessary for a production system.
	 */
	static function debug($string) {
		self::log_internal(self::DEBUG,$string);
	}

	/**
	 * Statisitics
	 */
	static function stats($string) {
		self::log_internal(self::NOTICE,$string,true);
	}
	
	
	
	public static function createLoggingHandler() {

		/* Set to FALSE to indicate that it is being initialized. */
		self::$loggingHandler = FALSE;

		/* Get the configuration. */
		$config = SimpleSAML_Configuration::getInstance();
		assert($config instanceof SimpleSAML_Configuration);

		/* Get the metadata handler option from the configuration. */
		$handler = $config->getString('logging.handler', 'syslog');

		/*
		 * setting minimum log_level
		 */
		self::$logLevel = $config->getInteger('logging.level',self::INFO);

		$handler = strtolower($handler);

		if($handler === 'syslog') {
			$sh = new SimpleSAML_Logger_LoggingHandlerSyslog();

		} elseif ($handler === 'file')  {
			$sh = new SimpleSAML_Logger_LoggingHandlerFile();
		} elseif ($handler === 'errorlog')  {
			$sh = new SimpleSAML_Logger_LoggingHandlerErrorLog();
		} else {
			throw new Exception('Invalid value for the [logging.handler] configuration option. Unknown handler: ' . $handler);
		}
		/* Set the session handler. */
		self::$loggingHandler = $sh;
	}
	
	public static function setCaptureLog($val = TRUE) {
		self::$captureLog = $val;
	}
	
	public static function getCapturedLog() {
		return self::$capturedLog;
	}	
	
	static function log_internal($level,$string,$statsLog = false) {
		if (self::$loggingHandler === NULL) {
			/* Initialize logging. */
			self::createLoggingHandler();

			if (!empty(self::$earlyLog)) {
				error_log('----------------------------------------------------------------------');
				/* Output messages which were logged before we initialized to the proper log. */
				foreach (self::$earlyLog as $msg) {
					self::log_internal($msg['level'], $msg['string'], $msg['statsLog']);
				}
			}

		} elseif (self::$loggingHandler === FALSE) {
			/* Some error occurred while initializing logging. */
			if (empty(self::$earlyLog)) {
				/* This is the first message. */
				error_log('--- Log message(s) while initializing logging ------------------------');
			}
			error_log($string);

			self::$earlyLog[] = array('level' => $level, 'string' => $string, 'statsLog' => $statsLog);
			return;
		}

		
		if (self::$captureLog) {
			$ts = microtime(TRUE);
			$msecs = (int)(($ts - (int)$ts) * 1000);
			$ts = GMdate('H:i:s', $ts) . sprintf('.%03d', $msecs) . 'Z';
			self::$capturedLog[] = $ts . ' ' . $string;
		}
		
		if (self::$logLevel >= $level || $statsLog) {
			if (is_array($string)) $string = implode(",",$string);
			$string = '['.self::getTrackId().'] '.$string;
			if ($statsLog) $string = 'STAT '.$string;  
			self::$loggingHandler->log_internal($level,$string);
		}
	}
	

	/**
	 * Retrieve the trackid we should use for logging.
	 *
	 * It is used to avoid infinite recursion between the logger class and the session class.
	 *
	 * @return The trackid we should use for logging, or 'NA' if we detect recursion.
	 */
	private static function getTrackId() {

		if(self::$trackid === self::$TRACKID_FETCHING) {
			/* Recursion detected. */
			return 'NA';
		}

		if(self::$trackid === NULL) {
			/* No trackid yet, fetch it from the session class. */

			/* Mark it as currently being fetched. */
			self::$trackid = self::$TRACKID_FETCHING;

			/* Get the current session. This could cause recursion back to the logger class. */
			$session = SimpleSAML_Session::getInstance();

			/* Update the trackid. */
			self::$trackid = $session->getTrackId();
		}

		assert('is_string(self::$trackid)');
		return self::$trackid;
	}
}
