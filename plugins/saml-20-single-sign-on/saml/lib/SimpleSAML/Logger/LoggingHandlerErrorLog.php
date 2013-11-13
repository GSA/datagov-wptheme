<?php

/**
 * A class for logging to the default php error log.
 *
 * @author Lasse Birnbaum Jensen, SDU.
 * @author Andreas Åkre Solberg, UNINETT AS. <andreas.solberg@uninett.no>
 * @author Olav Morken, UNINETT AS.
 * @package simpleSAMLphp
 * @version $ID$
 */
class SimpleSAML_Logger_LoggingHandlerErrorLog implements SimpleSAML_Logger_LoggingHandler {

	/**
	 * This array contains the mappings from syslog loglevel to names.
	 */
	private static $levelNames = array(
		SimpleSAML_Logger::EMERG => 'EMERG',
		SimpleSAML_Logger::ALERT => 'ALERT',
		SimpleSAML_Logger::CRIT => 'CRIT',
		SimpleSAML_Logger::ERR => 'ERR',
		SimpleSAML_Logger::WARNING => 'WARNING',
		SimpleSAML_Logger::NOTICE => 'NOTICE',
		SimpleSAML_Logger::INFO => 'INFO',
		SimpleSAML_Logger::DEBUG => 'DEBUG',
	);


	function log_internal($level, $string) {
		$config = SimpleSAML_Configuration::getInstance();
        assert($config instanceof SimpleSAML_Configuration);
        $processname = $config->getString('logging.processname','simpleSAMLphp');
		
		if(array_key_exists($level, self::$levelNames)) {
			$levelName = self::$levelNames[$level];
		} else {
			$levelName = sprintf('UNKNOWN%d', $level);
		}

		error_log($processname.' - '.$levelName . ': ' . $string);
	}
}

?>