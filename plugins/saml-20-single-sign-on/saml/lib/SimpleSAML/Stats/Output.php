<?php

/**
 * Interface for statistics outputs.
 *
 * @package simpleSAMLphp
 * @version $Id$
 */
abstract class SimpleSAML_Stats_Output {

	/**
	 * Initialize the output.
	 *
	 * @param SimpleSAML_Configuration $config  The configuration for this output.
	 */
	public function __construct(SimpleSAML_Configuration $config) {
		/* Do nothing by default. */
	}


	/**
	 * Write a stats event.
	 *
	 * @param array $data  The event.
	 */
	abstract public function emit(array $data);

}
