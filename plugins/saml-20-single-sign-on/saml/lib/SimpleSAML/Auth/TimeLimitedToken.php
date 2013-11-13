<?php


class SimpleSAML_Auth_TimeLimitedToken {

	var $secretSalt;
	var $lifetime;
	var $skew;

	/**
	 * @param $secretSalt Must be random and unique per installation
	 * @param $lifeTime Token lifetime in seconds
	 * @param $skew  Allowed time skew between server that generates and the one that calculates the token
	 */
	public function __construct( $lifetime = 900, $secretSalt = NULL, $skew = 1) {
		if ($secretSalt === NULL) {
			$secretSalt = SimpleSAML_Utilities::getSecretSalt();
		}
	
		$this->secretSalt = $secretSalt;
		$this->lifetime = $lifetime;
		$this->skew = $skew;
	}
	
	public function addVerificationData($data) {
		$this->secretSalt .= '|' . $data;
	}
	
	
	/**
	 * Calculate the current time offset to the current time slot.
	 * With some amount of time skew
	 */
	private function get_offset() {
		return ( (time() - $this->skew) % ($this->lifetime + $this->skew) );
	}
	
	/**
	 * Calculate the given time slot for a given offset.
	 */
	private function calculate_time_slot($offset) {
	
		#echo 'lifetime is: ' . $this->lifetime;
		
		$timeslot = floor( (time() - $offset) / ($this->lifetime + $this->skew) );
		return $timeslot;
	}
	
	/**
	 * Calculates a token value for a given offset
	 */
	private function calculate_tokenvalue($offset) {
		// A secret salt that should be randomly generated for each installation.
		#echo 'Secret salt is: ' . $this->secretSalt;
		
		#echo '<p>Calculating sha1( ' . $this->calculate_time_slot($offset) . ':' . $this->secretSalt . '  )<br />';
		
		return sha1( $this->calculate_time_slot($offset) . ':' . $this->secretSalt);
	}
	
	/**
	 * Generates a token which contains of a offset and a token value. Using current offset
	 */
	public function generate_token() {
		$current_offset = $this->get_offset();
		return dechex($current_offset) . '-' . $this->calculate_tokenvalue($current_offset);
	}
	
	/**
	 * Validates a full token, by calculating the token value for the provided 
	 * offset and compares.
	 */
	public function validate_token($token) {
		$splittedtoken = explode('-', $token);
		$offset = hexdec($splittedtoken[0]);
		$value  = $splittedtoken[1];
		
		
		#echo 'compare [' . $this->calculate_tokenvalue($offset). '] with [' . $value . '] offset was [' . $offset. ']';
		
		return ($this->calculate_tokenvalue($offset) === $value);
	}
	
}


