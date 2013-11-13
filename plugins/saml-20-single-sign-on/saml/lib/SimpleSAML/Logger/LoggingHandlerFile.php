<?php

/**
 * A class for logging
 *
 * @author Lasse Birnbaum Jensen, SDU.
 * @author Andreas Åkre Solberg, UNINETT AS. <andreas.solberg@uninett.no>
 * @package simpleSAMLphp
 * @version $ID$
 */

class SimpleSAML_Logger_LoggingHandlerFile implements SimpleSAML_Logger_LoggingHandler {
    
	/**
	 * This array contains the mappings from syslog loglevel to names. Copied
	 * more or less directly from SimpleSAML_Logger_LoggingHandlerErrorLog.
	 */
	private static $levelNames = array(
		SimpleSAML_Logger::EMERG => 'EMERGENCY',
		SimpleSAML_Logger::ALERT => 'ALERT',
		SimpleSAML_Logger::CRIT => 'CRITICAL',
		SimpleSAML_Logger::ERR => 'ERROR',
		SimpleSAML_Logger::WARNING => 'WARNING',
		SimpleSAML_Logger::NOTICE => 'NOTICE',
		SimpleSAML_Logger::INFO => 'INFO',
		SimpleSAML_Logger::DEBUG => 'DEBUG',
	);

    private $logFile = null;
    private $processname = null;

    function __construct() {
        $config = SimpleSAML_Configuration::getInstance();
        assert($config instanceof SimpleSAML_Configuration);

        /* Get the metadata handler option from the configuration. */
        $this->logFile = $config->getPathValue('loggingdir', 'log/').$config->getString('logging.logfile', 'simplesamlphp.log');
		$this->processname = $config->getString('logging.processname','simpleSAMLphp');
		
        if (@file_exists($this->logFile)) {
            if (!@is_writeable($this->logFile)) throw new Exception("Could not write to logfile: ".$this->logFile);
        }
        else
        {
            if (!@touch($this->logFile))  throw new Exception("Could not create logfile: ".$this->logFile." Loggingdir is not writeable for the webserver user.");
        }

	SimpleSAML_Utilities::initTimezone();
    }

    function log_internal($level, $string) {
        if ($this->logFile != null) {
            
            // Set human-readable log level. Copied from SimpleSAML_Logger_LoggingHandlerErrorLog.
        	if(array_key_exists($level, self::$levelNames))
			    $levelName = self::$levelNames[$level];
		    else
			    $levelName = sprintf('UNKNOWN%d', $level);
            
            $line = sprintf("%s %s %s %s\n", strftime("%b %d %H:%M:%S"), $this->processname, $levelName, $string);
            file_put_contents($this->logFile, $line, FILE_APPEND);
        }
    }
}

?>