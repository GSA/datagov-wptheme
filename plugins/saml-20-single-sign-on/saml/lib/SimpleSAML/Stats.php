<?php

/**
 * Statistics handler class.
 *
 * This class is responsible for taking a statistics event and logging it.
 *
 * @package simpleSAMLphp
 * @version $Id$
 */
class SimpleSAML_Stats {

	/**
	 * Whether this class is initialized.
	 * @var boolean
	 */
	private static $initialized = FALSE;


	/**
	 * The statistics output callbacks.
	 * @var array
	 */
	private static $outputs = NULL;


	/**
	 * Create an output from a configuration object.
	 *
	 * @param SimpleSAML_Configuration $config  The configuration object.
	 * @return
	 */
	private static function createOutput(SimpleSAML_Configuration $config) {
		$cls = $config->getString('class');
		$cls = SimpleSAML_Module::resolveClass($cls, 'Stats_Output', 'SimpleSAML_Stats_Output');

		$output = new $cls($config);
		return $output;
	}


	/**
	 * Initialize the outputs.
	 */
	private static function initOutputs() {

		$config = SimpleSAML_Configuration::getInstance();
		$outputCfgs = $config->getConfigList('statistics.out', array());

		self::$outputs = array();
		foreach ($outputCfgs as $cfg) {
			self::$outputs[] = self::createOutput($cfg);
		}
	}


	/**
	 * Notify about an event.
	 *
	 * @param string $event  The event.
	 * @param array $data  Event data. Optional.
	 */
	public static function log($event, array $data = array()) {
		assert('is_string($event)');
		assert('!isset($data["op"])');
		assert('!isset($data["time"])');
		assert('!isset($data["_id"])');

		if (!self::$initialized) {
			self::initOutputs();
			self::$initialized = TRUE;
		}

		if (empty(self::$outputs)) {
			/* Not enabled. */
			return;
		}

		$data['op'] = $event;
		$data['time'] = microtime(TRUE);

		/* The ID generation is designed to cluster IDs related in time close together. */
		$int_t = (int)$data['time'];
		$hd = SimpleSAML_Utilities::generateRandomBytes(16);
		$data['_id'] = sprintf('%016x%s', $int_t, bin2hex($hd));

		foreach (self::$outputs as $out) {
			$out->emit($data);
		}
	}

}
