<?php

/**
 * Exception which will show a 400 Bad Request error page.
 *
 * This exception can be thrown from within an module page handler. The user will then be
 * shown a 400 Bad Request error page.
 *
 * @author Olav Morken, UNINETT AS.
 * @package simpleSAMLphp
 * @version $Id$
 */
class SimpleSAML_Error_BadRequest extends SimpleSAML_Error_Error {


	/**
	 * Reason why this request was invalid.
	 */
	private $reason;


	/**
	 * Create a new BadRequest error.
	 *
	 * @param string $reason  Description of why the request was unacceptable.
	 */
	public function __construct($reason) {
		assert('is_string($reason)');

		$this->reason = $reason;
		parent::__construct(array('BADREQUEST', '%REASON%' => $this->reason));
	}


	/**
	 * Retrieve the reason why the request was invalid.
	 *
	 * @return string  The reason why the request was invalid.
	 */
	public function getReason() {
		return $this->reason;
	}


	/**
	 * Set the HTTP return code for this error.
	 *
	 * This should be overridden by subclasses who want a different return code than 500 Internal Server Error.
	 */
	protected function setHTTPCode() {
		header('HTTP/1.0 400 Bad Request');
	}

}

?>