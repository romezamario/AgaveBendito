<?php
/**
 * Copyright (с) Cloud Linux GmbH & Cloud Linux Software, Inc 2010-2025 All Rights Reserved
 *
 * Licensed under CLOUD LINUX LICENSE AGREEMENT
 * https://www.cloudlinux.com/legal/
 */

namespace CloudLinux\Imunify\App\Defender;

/**
 * Request class for handling HTTP request data.
 *
 * Provides a clean abstraction over global variables like $_SERVER, $_GET, $_POST, etc.
 * This makes the code more testable and provides a consistent interface.
 *
 * @since 2.1.0
 */
class Request {

	/**
	 * Request method.
	 *
	 * @var string
	 */
	private $method;

	/**
	 * Server variables.
	 *
	 * @var array
	 */
	private $server;

	/**
	 * GET parameters.
	 *
	 * @var array
	 */
	private $get;

	/**
	 * POST parameters.
	 *
	 * @var array
	 */
	private $post;

	/**
	 * Cookies.
	 *
	 * @var array
	 */
	private $cookies;

	/**
	 * File uploads.
	 *
	 * @var array
	 */
	private $files;

	/**
	 * Constructor.
	 *
	 * @param array $server  Server variables (defaults to $_SERVER).
	 * @param array $get     GET parameters (defaults to $_GET).
	 * @param array $post    POST parameters (defaults to $_POST).
	 * @param array $cookies Cookies (defaults to $_COOKIE).
	 * @param array $files   File uploads (defaults to $_FILES).
	 */
	public function __construct( $server = null, $get = null, $post = null, $cookies = null, $files = null ) {
		// phpcs:disable WordPress.Security.NonceVerification.Missing,WordPress.Security.NonceVerification.Recommended
		$this->server  = null !== $server ? $server : $_SERVER;
		$this->get     = null !== $get ? $get : $_GET;
		$this->post    = null !== $post ? $post : $_POST;
		$this->cookies = null !== $cookies ? $cookies : $_COOKIE;
		$this->files   = null !== $files ? $files : $_FILES;
		$this->method  = $this->extractMethod( $this->server );
		// phpcs:enable WordPress.Security.NonceVerification.Missing,WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Get the request method.
	 *
	 * @return string The HTTP method (GET, POST, PUT, DELETE, etc.).
	 */
	public function getMethod() {
		return $this->method;
	}

	/**
	 * Check if the request method matches the given method.
	 *
	 * @param string $method The method to check against.
	 *
	 * @return bool True if the request method matches, false otherwise.
	 */
	public function isMethod( $method ) {
		return strtolower( $this->method ) === strtolower( $method );
	}

	/**
	 * Get a GET parameter.
	 *
	 * @param string $key     The parameter key.
	 * @param mixed  $default Default value if the parameter doesn't exist.
	 *
	 * @return mixed The parameter value or default.
	 */
	public function get( $key, $default = null ) {
		return isset( $this->get[ $key ] ) ? $this->get[ $key ] : $default;
	}

	/**
	 * Check if a GET parameter exists.
	 *
	 * @param string $key The parameter key.
	 *
	 * @return bool True if the parameter exists, false otherwise.
	 */
	public function hasGet( $key ) {
		return isset( $this->get[ $key ] );
	}

	/**
	 * Get a POST parameter.
	 *
	 * @param string $key     The parameter key.
	 * @param mixed  $default Default value if the parameter doesn't exist.
	 *
	 * @return mixed The parameter value or default.
	 */
	public function post( $key, $default = null ) {
		return isset( $this->post[ $key ] ) ? $this->post[ $key ] : $default;
	}

	/**
	 * Check if a POST parameter exists.
	 *
	 * @param string $key The parameter key.
	 *
	 * @return bool True if the parameter exists, false otherwise.
	 */
	public function hasPost( $key ) {
		return isset( $this->post[ $key ] );
	}

	/**
	 * Get a cookie.
	 *
	 * @param string $key     The cookie key.
	 * @param mixed  $default Default value if the cookie doesn't exist.
	 *
	 * @return mixed The cookie value or default.
	 */
	public function cookie( $key, $default = null ) {
		return isset( $this->cookies[ $key ] ) ? $this->cookies[ $key ] : $default;
	}

	/**
	 * Check if a cookie exists.
	 *
	 * @param string $key The cookie key.
	 *
	 * @return bool True if the cookie exists, false otherwise.
	 */
	public function hasCookie( $key ) {
		return isset( $this->cookies[ $key ] );
	}

	/**
	 * Get all GET parameters.
	 *
	 * @return array All GET parameters.
	 */
	public function getAllGet() {
		return $this->get;
	}

	/**
	 * Get all POST parameters.
	 *
	 * @return array All POST parameters.
	 */
	public function getAllPost() {
		return $this->post;
	}

	/**
	 * Get all GET and POST parameters merged.
	 *
	 * POST values take precedence over GET when keys overlap.
	 *
	 * @since 3.0.0
	 *
	 * @return array All GET and POST parameters.
	 */
	public function getAllArgs() {
		return array_merge( $this->get, $this->post );
	}

	/**
	 * Get all cookies.
	 *
	 * @return array All cookies.
	 */
	public function getAllCookies() {
		return $this->cookies;
	}

	/**
	 * Get all file uploads.
	 *
	 * @return array All file uploads.
	 */
	public function getAllFiles() {
		return $this->files;
	}

	/**
	 * Get all server variables.
	 *
	 * @return array All server variables.
	 */
	public function getAllServer() {
		return $this->server;
	}

	/**
	 * Get the request URI.
	 *
	 * @return string The request URI.
	 */
	public function getUri() {
		return isset( $this->server['REQUEST_URI'] ) ? $this->server['REQUEST_URI'] : '';
	}

	/**
	 * Check if a file upload exists.
	 *
	 * @param string $key The file key.
	 *
	 * @return bool True if the file exists, false otherwise.
	 */
	public function hasFile( $key ) {
		return isset( $this->files[ $key ] ) && ! empty( $this->files[ $key ]['name'] );
	}

	/**
	 * Get a file upload.
	 *
	 * @param string $key The file key.
	 *
	 * @return array|null The file data or null if not found.
	 */
	public function getFile( $key ) {
		return isset( $this->files[ $key ] ) ? $this->files[ $key ] : null;
	}

	/**
	 * Resolve a nested GET parameter value using bracket-path segments.
	 *
	 * Navigates $this->get[$rootKey][$path[0]][$path[1]]... to the leaf value.
	 *
	 * @since 3.0.0
	 *
	 * @param string $rootKey Root parameter key.
	 * @param array  $path    Array of bracket-path segments.
	 *
	 * @return mixed|null The leaf value, or null if the path doesn't exist.
	 */
	public function resolveNestedGet( $rootKey, array $path ) {
		return self::resolveNestedPath( $this->get, $rootKey, $path );
	}

	/**
	 * Resolve a nested POST parameter value using bracket-path segments.
	 *
	 * Navigates $this->post[$rootKey][$path[0]][$path[1]]... to the leaf value.
	 *
	 * @since 3.0.0
	 *
	 * @param string $rootKey Root parameter key.
	 * @param array  $path    Array of bracket-path segments.
	 *
	 * @return mixed|null The leaf value, or null if the path doesn't exist.
	 */
	public function resolveNestedPost( $rootKey, array $path ) {
		return self::resolveNestedPath( $this->post, $rootKey, $path );
	}

	/**
	 * Navigate a nested array by root key and path segments.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $data    The source array ($_GET or $_POST).
	 * @param string $rootKey Root key in $data.
	 * @param array  $path    Ordered path segments to traverse.
	 *
	 * @return mixed|null The leaf value, or null if any segment is missing.
	 */
	private static function resolveNestedPath( array $data, $rootKey, array $path ) {
		if ( ! isset( $data[ $rootKey ] ) ) {
			return null;
		}

		$current = $data[ $rootKey ];

		foreach ( $path as $segment ) {
			if ( ! is_array( $current ) || ! isset( $current[ $segment ] ) ) {
				return null;
			}
			$current = $current[ $segment ];
		}

		return $current;
	}

	/**
	 * Recursively extract all leaf string values from a (possibly nested) array.
	 *
	 * Used for scan-all ARGS evaluation when parameter values may be PHP arrays
	 * produced by bracket-notation form fields (e.g., param[key]=val).
	 *
	 * @since 3.0.0
	 *
	 * @param array $data     The array to walk.
	 * @param int   $maxDepth Maximum recursion depth (default 5).
	 * @param int   $maxCount Maximum number of leaf values to return (default 100).
	 *
	 * @return string[] Flat array of leaf string values.
	 */
	public static function extractLeafValues( array $data, $maxDepth = 5, $maxCount = 100 ) {
		$leaves = array();
		self::walkLeaves( $data, $maxDepth, $maxCount, $leaves, 0 );
		return $leaves;
	}

	/**
	 * Recursive helper for extractLeafValues().
	 *
	 * @param array $data     Current array level.
	 * @param int   $maxDepth Maximum recursion depth.
	 * @param int   $maxCount Maximum leaf count.
	 * @param array $leaves   Collected leaves (passed by reference).
	 * @param int   $depth    Current depth.
	 */
	private static function walkLeaves( array $data, $maxDepth, $maxCount, array &$leaves, $depth ) {
		if ( $depth >= $maxDepth ) {
			return;
		}

		foreach ( $data as $value ) {
			if ( count( $leaves ) >= $maxCount ) {
				return;
			}

			if ( is_array( $value ) ) {
				self::walkLeaves( $value, $maxDepth, $maxCount, $leaves, $depth + 1 );
			} elseif ( is_string( $value ) ) {
				$leaves[] = $value;
			}
		}
	}

	/**
	 * Get all GET/POST parameter values whose names match a regex pattern.
	 *
	 * Used with ModSecurity-style field name regex (e.g., ARGS:/field_a|field_b/).
	 * POST parameters take precedence over GET when keys overlap.
	 *
	 * @since 3.0.0
	 *
	 * @param string $pattern Regex pattern to match against parameter names (without delimiters/anchors).
	 *
	 * @return array<string, mixed> Associative array of matching parameter name => value pairs.
	 */
	public function getMatchingArgs( $pattern ) {
		$regex = '#^(?:' . str_replace( '#', '\\#', $pattern ) . ')$#';

		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- intentional; invalid regex must be silently skipped.
		if ( false === @preg_match( $regex, '' ) ) {
			return array();
		}

		$results = array();

		foreach ( $this->get as $key => $value ) {
			if ( is_string( $key ) && preg_match( $regex, $key ) ) {
				$results[ $key ] = $value;
			}
		}

		foreach ( $this->post as $key => $value ) {
			if ( is_string( $key ) && preg_match( $regex, $key ) ) {
				$results[ $key ] = $value;
			}
		}

		return $results;
	}

	/**
	 * Get all cookie values whose names match a regex pattern.
	 *
	 * @since 3.0.0
	 *
	 * @param string $pattern Regex pattern to match against cookie names (without delimiters/anchors).
	 *
	 * @return array<string, mixed> Associative array of matching cookie name => value pairs.
	 */
	public function getMatchingCookies( $pattern ) {
		$regex = '#^(?:' . str_replace( '#', '\\#', $pattern ) . ')$#';

		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- intentional; invalid regex must be silently skipped.
		if ( false === @preg_match( $regex, '' ) ) {
			return array();
		}

		$results = array();

		foreach ( $this->cookies as $key => $value ) {
			if ( is_string( $key ) && preg_match( $regex, $key ) ) {
				$results[ $key ] = $value;
			}
		}

		return $results;
	}

	/**
	 * Get a request header.
	 *
	 * @param string $key The header key.
	 *
	 * @return string|null The header value or null if not found.
	 */
	public function getHeader( $key ) {
		$header_key = 'HTTP_' . strtoupper( str_replace( '-', '_', $key ) );
		return isset( $this->server[ $header_key ] ) ? $this->server[ $header_key ] : null;
	}

	/**
	 * Check if a request header exists.
	 *
	 * @since 3.0.0
	 *
	 * @param string $key The header key (e.g. 'User-Agent', 'X-Custom').
	 *
	 * @return bool True if the header exists, false otherwise.
	 */
	public function hasHeader( $key ) {
		return null !== $this->getHeader( $key );
	}

	/**
	 * Get all header values whose names match a regex pattern.
	 *
	 * PHP normalises header names to HTTP_UPPER_CASE in $_SERVER, so this
	 * method converts them back to Title-Case (e.g. HTTP_USER_AGENT → User-Agent)
	 * and applies a case-insensitive regex match because HTTP header names are
	 * case-insensitive per RFC 7230.
	 *
	 * @since 3.0.0
	 *
	 * @param string $pattern Regex pattern to match against header names (without delimiters/anchors).
	 *
	 * @return array<string, string> Associative array of matching header name => value pairs.
	 */
	public function getMatchingHeaders( $pattern ) {
		$regex = '#^(?:' . str_replace( '#', '\\#', $pattern ) . ')$#i';

		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- intentional; invalid regex must be silently skipped.
		if ( false === @preg_match( $regex, '' ) ) {
			return array();
		}

		$results = array();

		foreach ( $this->server as $key => $value ) {
			if ( 0 !== strpos( $key, 'HTTP_' ) || ! is_string( $value ) ) {
				continue;
			}
			$headerName = str_replace( ' ', '-', ucwords( strtolower( str_replace( '_', ' ', substr( $key, 5 ) ) ) ) );
			if ( preg_match( $regex, $headerName ) ) {
				$results[ $headerName ] = $value;
			}
		}

		return $results;
	}

	/**
	 * Get all HTTP request headers as an associative array.
	 *
	 * Extracts headers from $_SERVER entries with HTTP_ prefix and
	 * returns them with normalised Title-Case names.
	 *
	 * @since 3.0.0
	 *
	 * @return array<string, string> Header name => value pairs.
	 */
	public function getAllHeaders() {
		$headers = array();

		foreach ( $this->server as $key => $value ) {
			if ( 0 !== strpos( $key, 'HTTP_' ) || ! is_string( $value ) ) {
				continue;
			}
			$headerName             = str_replace( ' ', '-', ucwords( strtolower( str_replace( '_', ' ', substr( $key, 5 ) ) ) ) );
			$headers[ $headerName ] = $value;
		}

		return $headers;
	}

	/**
	 * Extract the request method from server variables.
	 *
	 * @param array $server Server variables.
	 *
	 * @return string The HTTP method.
	 */
	private function extractMethod( $server ) {
		// Check for X-HTTP-METHOD-OVERRIDE header (used by some frameworks).
		if ( isset( $server['HTTP_X_HTTP_METHOD_OVERRIDE'] ) ) {
			return $server['HTTP_X_HTTP_METHOD_OVERRIDE'];
		}

		// Check for _method parameter (used by some frameworks).
		if ( isset( $this->post['_method'] ) ) {
			return $this->post['_method'];
		}

		// Check for the standard REQUEST_METHOD.
		if ( isset( $server['REQUEST_METHOD'] ) ) {
			return $server['REQUEST_METHOD'];
		}

		// Default to GET if no method is found.
		return 'GET';
	}
}
