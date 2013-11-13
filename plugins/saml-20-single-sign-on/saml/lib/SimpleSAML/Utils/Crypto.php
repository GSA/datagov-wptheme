<?php

/**
 * A class for crypto related functions
 *
 * @author Dyonisius Visser, TERENA. <visser@terena.org>
 * @package simpleSAMLphp
 * @version $Id$
 */
class SimpleSAML_Utils_Crypto {

	/**
	 * This function generates a password hash
	 * @param $password  The unencrypted password
	 * @param $algo      The hashing algorithm, capitals, optionally prepended with 'S' (salted)
	 * @param $salt      Optional salt
	 */
	public static function pwHash($password, $algo, $salt = NULL) {
		assert('is_string($algo)');
		assert('is_string($password)');

		if(in_array(strtolower($algo), hash_algos())) {
			$php_algo = strtolower($algo); // 'sha256' etc
			// LDAP compatibility
			return '{' . preg_replace('/^SHA1$/', 'SHA', $algo) . '}'
				.base64_encode(hash($php_algo, $password, TRUE));
		}

		// Salt
		if(!$salt) {
			// Default 8 byte salt, but 4 byte for LDAP SHA1 hashes
			$bytes = ($algo == 'SSHA1') ? 4 : 8;
			$salt = SimpleSAML_Utilities::generateRandomBytes($bytes, TRUE);
		}

		if($algo[0] == 'S' && in_array(substr(strtolower($algo),1), hash_algos())) {
			$php_algo = substr(strtolower($algo),1); // 'sha256' etc
			// Salted hash, with LDAP compatibility
			return '{' . preg_replace('/^SSHA1$/', 'SSHA', $algo) . '}' .
				base64_encode(hash($php_algo, $password.$salt, TRUE) . $salt);
		}

		throw new Exception('Hashing algoritm \'' . strtolower($algo) . '\' not supported');

	}


	/**
	 * This function checks if a password is valid
	 * @param $crypted  Password as appears in password file, optionally prepended with algorithm
	 * @param $clear    Password to check
	 */
	public static function pwValid($crypted, $clear) {
		assert('is_string($crypted)');
		assert('is_string($clear)');

		// Match algorithm string ('{SSHA256}', '{MD5}')
		if(preg_match('/^{(.*?)}(.*)$/', $crypted, $matches)) {

			// LDAP compatibility
			$algo = preg_replace('/^(S?SHA)$/', '${1}1', $matches[1]);

			$cryptedpw =  $matches[2];

			if(in_array(strtolower($algo), hash_algos())) {
				// Unsalted hash
				return ( $crypted == self::pwHash($clear, $algo) );
			}

			if($algo[0] == 'S' && in_array(substr(strtolower($algo),1), hash_algos())) {
				$php_algo = substr(strtolower($algo),1);
				// Salted hash
				$hash_length = strlen(hash($php_algo, 'whatever', TRUE));
				$salt = substr(base64_decode($cryptedpw), $hash_length);
				return ( $crypted == self::pwHash($clear, $algo, $salt) );
			}

			throw new Exception('Hashing algoritm \'' . strtolower($algo) . '\' not supported');

		} else {
			return $crypted === $clear;
		}
	}

	/**
	 * This function generates an Apache 'apr1' password hash, which uses a modified
	 * version of MD5: http://httpd.apache.org/docs/2.2/misc/password_encryptions.html
	 * @param $password  The unencrypted password
	 * @param $salt      Optional salt
	 */
	public static function apr1Md5Hash($password, $salt = NULL) {
		assert('is_string($password)');

		$chars = "./0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
		if(!$salt) {
			$salt = substr(str_shuffle($allowed_chars), 0, 8);
		}

		$len = strlen($password);
		$text = $password.'$apr1$'.$salt;
		$bin = pack("H32", md5($password.$salt.$password));
		for($i = $len; $i > 0; $i -= 16) { $text .= substr($bin, 0, min(16, $i)); }
		for($i = $len; $i > 0; $i >>= 1) { $text .= ($i & 1) ? chr(0) : $password{0}; }
		$bin = pack("H32", md5($text));
		for($i = 0; $i < 1000; $i++) {
			$new = ($i & 1) ? $password : $bin;
			if ($i % 3) $new .= $salt;
			if ($i % 7) $new .= $password;
			$new .= ($i & 1) ? $bin : $password;
			$bin = pack("H32", md5($new));
		}
		$tmp= '';
		for ($i = 0; $i < 5; $i++) {
			$k = $i + 6;
			$j = $i + 12;
			if ($j == 16) $j = 5;
			$tmp = $bin[$i].$bin[$k].$bin[$j].$tmp;
		}
		$tmp = chr(0).chr(0).$bin[11].$tmp;
		$tmp = strtr(
			strrev(substr(base64_encode($tmp), 2)),
			"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/",
			$chars
		);
		return "$"."apr1"."$".$salt."$".$tmp;
	}


	/**
	 * This function verifies an Apache 'apr1' password hash
	 */
	public static function apr1Md5Valid($crypted, $clear) {
		assert('is_string($crypted)');
		assert('is_string($clear)');
		$pattern = '/^\$apr1\$([A-Za-z0-9\.\/]{8})\$([A-Za-z0-9\.\/]{22})$/';

		if(preg_match($pattern, $crypted, $matches)) {
			$salt = $matches[1];
			return ( $crypted == self::apr1Md5Hash($clear, $salt) );
		}
		return FALSE;
	}
}
