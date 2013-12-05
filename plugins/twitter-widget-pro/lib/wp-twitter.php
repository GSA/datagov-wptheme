<?php
require_once( 'oauth-util.php' );
class wpTwitter {
	/**
	 * @var string Twitter App Consumer Key
	 */
	private $_consumer_key;

	/**
	 * @var string Twitter App Secret Key
	 */
	private $_consumer_secret;

	/**
	 * @var string Twitter Request or Access Token
	 */
	private $_token;

	private static $_api_url;

	public function __construct( $args ) {
		$defaults = array(
			'api-url' => 'https://api.twitter.com/',
		);
		$args = wp_parse_args( $args, $defaults );
		$this->_consumer_key = $args['consumer-key'];
		$this->_consumer_secret = $args['consumer-secret'];
		self::$_api_url = $args['api-url'];
		if ( !empty( $args['token'] ) )
			$this->_token = $args['token'];
	}

	public static function get_api_endpoint( $method, $format = 'json', $version = '1.1' ) {
		$method = preg_replace( '|[^\w/]|', '', $method );
		if ( ! empty( $format ) )
			$format = '.json';
		if ( ! empty( $version ) )
			$version .= '/';

		return self::$_api_url . $version . $method . $format;
	}

	/**
	 * Get a request_token from Twitter
	 *
	 * @returns a key/value array containing oauth_token and oauth_token_secret
	 */
	public function getRequestToken( $oauth_callback = null ) {
		$parameters = array(
			'oauth_nonce' => md5( microtime() . mt_rand() ),
		);
		if ( ! empty( $oauth_callback ) )
			$parameters['oauth_callback'] = add_query_arg( array('nonce'=>$parameters['oauth_nonce']), $oauth_callback );

		$request_url = self::get_api_endpoint( 'oauth/request_token', '', '' );
		$this->_token = $this->send_authed_request( $request_url, 'GET', $parameters );
		if ( ! is_wp_error( $this->_token ) )
			$this->_token['nonce'] = $parameters['oauth_nonce'];
		return $this->_token;
	}

	private function _get_request_defaults() {
		$params = array(
			'sslverify' => apply_filters( 'twp_sslverify', false ),
			'body'      => array(
				'oauth_version'      => '1.0',
				'oauth_nonce'        => md5( microtime() . mt_rand() ),
				'oauth_timestamp'    => time(),
				'oauth_consumer_key' => $this->_consumer_key,
			),
		);

		if ( ! empty( $this->_token['oauth_token'] ) )
			$params['body']['oauth_token'] = $this->_token['oauth_token'];

		return $params;
	}

	/**
	 * Get the authorize URL
	 *
	 * @returns a string
	 */
	public function get_authorize_url( $screen_name = '' ) {
		if ( empty( $this->_token['oauth_token'] ) )
			return false;

		$query_args = array(
			'oauth_token' => $this->_token['oauth_token']
		);
		if ( !empty( $screen_name ) ) {
			$query_args['screen_name'] = $screen_name;
			$query_args['force_login'] = 'true';
		}
		return add_query_arg( $query_args, self::get_api_endpoint( 'oauth/authorize', '', '' ) );
	}

	/**
	 * Format and sign an OAuth / API request
	 */
	public function send_authed_request( $request_url, $method, $body_parameters = array() ) {
		$parameters = $this->_get_request_defaults();
		$parameters['body'] = wp_parse_args( $body_parameters, $parameters['body'] );
		if ( ! filter_var( $request_url , FILTER_VALIDATE_URL ) )
			$request_url = self::get_api_endpoint( $request_url );
		$this->sign_request( $parameters, $request_url );
		switch ($method) {
			case 'GET':
				$request_url = $this->get_normalized_http_url( $request_url ) . '?' . twpOAuthUtil::build_http_query( $parameters['body'] );
				unset( $parameters['body'] );
				$resp = wp_remote_get( $request_url, $parameters );
				break;
			default:
				$parameters['method'] = $method;
				$resp = wp_remote_request( $request_url, $parameters );
		}

		if ( !is_wp_error( $resp ) && $resp['response']['code'] >= 200 && $resp['response']['code'] < 300 ) {
			$decoded_response = json_decode( $resp['body'] );
			/**
			 * There is a problem with some versions of PHP that will cause
			 * json_decode to return the string passed to it in certain cases
			 * when the string isn't valid JSON.  This is causing me all sorts
			 * of pain.  The solution so far is to check if the return isset()
			 * which is the correct response if the string isn't JSON.  Then
			 * also check if a string is returned that has an = in it and if
			 * that's the case assume it's a string that needs to fall back to
			 * using wp_parse_args()
			 * @see https://bugs.php.net/bug.php?id=45989
			 * @see https://github.com/OpenRange/twitter-widget-pro/pull/8
			 */
			if ( ( ! isset( $decoded_response ) && ! empty( $resp['body'] ) ) || ( is_string( $decoded_response ) && false !== strpos( $resp['body'], '=' ) ) )
				$decoded_response = wp_parse_args( $resp['body'] );
			return $decoded_response;
		} else {
			if ( is_wp_error( $resp ) )
				return $resp;
			return new WP_Error( $resp['response']['code'], 'Could not recognize the response from Twitter' );
		}
	}

	/**
	 * parses the url and rebuilds it to be
	 * scheme://host/path
	 */
	public function get_normalized_http_url( $url ) {
		$parts = parse_url( $url );

		$scheme = (isset($parts['scheme'])) ? $parts['scheme'] : 'http';
		$port = (isset($parts['port'])) ? $parts['port'] : (($scheme == 'https') ? '443' : '80');
		$host = (isset($parts['host'])) ? strtolower($parts['host']) : '';
		$path = (isset($parts['path'])) ? $parts['path'] : '';

		if (($scheme == 'https' && $port != '443') || ($scheme == 'http' && $port != '80'))
			$host = "$host:$port";

		return "$scheme://$host$path";
	}

	public function sign_request( &$parameters, $request_url, $method = 'GET' ) {
		$parameters['body']['oauth_signature_method'] = 'HMAC-SHA1';
		$parameters['body']['oauth_signature'] = $this->build_signature( $parameters['body'], $request_url, $method );
	}

	/**
	* The request parameters, sorted and concatenated into a normalized string.
	* @return string
	*/
	public function get_signable_parameters( $parameters ) {
		// Remove oauth_signature if present
		// Ref: Spec: 9.1.1 ("The oauth_signature parameter MUST be excluded.")
		if ( isset( $parameters['oauth_signature'] ) )
			unset( $parameters['oauth_signature'] );

		return twpOAuthUtil::build_http_query( $parameters );
	}

	public function build_signature( $parameters, $request_url, $method = 'GET' ) {
		$parts = array(
			$method,
			$this->get_normalized_http_url( $request_url ),
			$this->get_signable_parameters( $parameters )
		);

		$parts = twpOAuthUtil::urlencode_rfc3986($parts);

		$base_string = implode('&', $parts);
		$token_secret = '';

		if ( ! empty( $this->_token['oauth_token_secret'] ) )
			$token_secret = $this->_token['oauth_token_secret'];

		$key_parts = array(
			$this->_consumer_secret,
			$token_secret,
		);

		$key_parts = twpOAuthUtil::urlencode_rfc3986( $key_parts );
		$key = implode( '&', $key_parts );

		return base64_encode( hash_hmac( 'sha1', $base_string, $key, true ) );
	}

	/**
	 * Exchange request token and secret for an access token and
	 * secret, to sign API calls.
	 *
	 * @returns array containing oauth_token,
	 *                           oauth_token_secret,
	 *                           user_id
	 *                           screen_name
	 */
	function get_access_token( $oauth_verifier = false ) {
		$parameters = array(
			'oauth_nonce' => md5( microtime() . mt_rand() ),
		);
		if ( ! empty( $oauth_verifier ) )
			$parameters['oauth_verifier'] = $oauth_verifier;

		$request_url = self::get_api_endpoint( 'oauth/access_token', '', '' );
		$this->_token = $this->send_authed_request( $request_url, 'GET', $parameters );
		return $this->_token;
	}

	public function set_token( $token ) {
		$this->_token = $token;
	}
}
