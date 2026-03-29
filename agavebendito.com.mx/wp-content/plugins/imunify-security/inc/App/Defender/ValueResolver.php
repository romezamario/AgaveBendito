<?php
/**
 * Copyright (с) Cloud Linux GmbH & Cloud Linux Software, Inc 2010-2025 All Rights Reserved
 *
 * Licensed under CLOUD LINUX LICENSE AGREEMENT
 * https://www.cloudlinux.com/legal/
 */

namespace CloudLinux\Imunify\App\Defender;

use CloudLinux\Imunify\App\Defender\Model\Condition;
use CloudLinux\Imunify\App\Defender\Model\ConditionSource;

/**
 * Resolves candidate values from a Request based on a parsed condition name.
 *
 * Handles the three resolution modes (field regex, scan-all, single field),
 * bracket-path navigation, URI decoding, and source dispatching.
 *
 * @since 3.0.0
 */
class ValueResolver {

	/**
	 * Resolve candidate values for a condition from the request.
	 *
	 * Returns a mixed[] of values (strings or arrays) that should each be
	 * tested by the caller's matcher. The caller is responsible for type
	 * checking and leaf extraction on array values.
	 *
	 * @param Condition $condition The condition to resolve values for.
	 * @param Request   $request   Request object.
	 *
	 * @return array Candidate values to test.
	 */
	public function resolveValues( Condition $condition, Request $request ) {
		if ( ! $condition->hasRequiredFields() ) {
			return array();
		}

		$parsed = $condition->parseName();
		$source = $parsed['source'];

		if ( null !== $parsed['field_regex'] ) {
			$regexValues = $this->getFieldValuesByRegex( $request, $source, $parsed['field_regex'] );

			if ( null !== $parsed['bracket_path'] ) {
				return $this->navigateBracketPathIntoValues( array_values( $regexValues ), $parsed['bracket_path'] );
			}

			return array_values( $regexValues );
		}

		if ( null === $parsed['field'] && $this->isCollectionSource( $source ) ) {
			return array_values( $this->getAllSourceValues( $request, $source ) );
		}

		if ( ConditionSource::REQUEST_URI === $source ) {
			return array( $this->getDecodedUri( $request ) );
		}

		if ( null !== $parsed['bracket_path'] && self::bracketPathHasRegex( $parsed['bracket_path'] ) ) {
			return $this->resolveFieldWithRegexBrackets( $request, $parsed );
		}

		$value = $this->getFieldValue( $request, $parsed );

		if ( null === $value ) {
			return array();
		}

		return array( $value );
	}

	/**
	 * Get field value from request based on parsed condition name.
	 *
	 * Supports bracket-notation for nested PHP arrays (e.g., ARGS:param[key]).
	 * Resolution order: nested array traversal first, literal key fallback.
	 *
	 * @param Request $request Request object.
	 * @param array   $parsed  Parsed condition name from Condition::parseNameString().
	 *
	 * @return string|array<string, mixed>|null Field value or null if not found.
	 */
	public function getFieldValue( $request, $parsed ) {
		$source      = $parsed['source'];
		$field       = $parsed['field'];
		$bracketPath = isset( $parsed['bracket_path'] ) ? $parsed['bracket_path'] : null;
		$rawField    = isset( $parsed['raw_field'] ) ? $parsed['raw_field'] : null;

		switch ( $source ) {
			case ConditionSource::ARGS:
				if ( null === $field ) {
					return null;
				}
				if ( null !== $bracketPath ) {
					$value = $request->resolveNestedGet( $field, $bracketPath );
					if ( null === $value ) {
						$value = $request->resolveNestedPost( $field, $bracketPath );
					}
					if ( null !== $value ) {
						return $value;
					}
					$value = $request->get( $rawField );
					if ( null === $value ) {
						$value = $request->post( $rawField );
					}
					return $value;
				}
				$fieldValue = $request->get( $field );
				if ( null === $fieldValue ) {
					$fieldValue = $request->post( $field );
				}
				return $fieldValue;
			case ConditionSource::REQUEST_URI:
				return $this->getDecodedUri( $request );
			case ConditionSource::FILES:
				if ( null === $field ) {
					return null;
				}
				return $request->getFile( $field );
			case ConditionSource::REQUEST_COOKIES:
				if ( null === $field ) {
					return null;
				}
				return $request->cookie( $field );
			case ConditionSource::REQUEST_HEADERS:
				if ( null === $field ) {
					return null;
				}
				return $request->getHeader( $field );
			default:
				return null;
		}
	}

	/**
	 * Get all field values from request whose names match a regex pattern.
	 *
	 * @param Request $request    Request object.
	 * @param string  $source     Field source (e.g., ARGS, REQUEST_COOKIES).
	 * @param string  $fieldRegex Regex pattern for field names (without delimiters).
	 *
	 * @return array<string, mixed> Associative array of matching field name => value pairs.
	 */
	private function getFieldValuesByRegex( $request, $source, $fieldRegex ) {
		switch ( $source ) {
			case ConditionSource::ARGS:
				return $request->getMatchingArgs( $fieldRegex );
			case ConditionSource::REQUEST_COOKIES:
				return $request->getMatchingCookies( $fieldRegex );
			case ConditionSource::REQUEST_HEADERS:
				return $request->getMatchingHeaders( $fieldRegex );
			default:
				return array();
		}
	}

	/**
	 * Check whether a source represents a collection of named values.
	 *
	 * Collection sources (ARGS, REQUEST_COOKIES, REQUEST_HEADERS) contain
	 * multiple named fields and support scan-all semantics when no specific
	 * field is given.
	 *
	 * @param string $source Condition source constant.
	 *
	 * @return bool True if the source is a collection.
	 */
	private function isCollectionSource( $source ) {
		return in_array(
			$source,
			array( ConditionSource::ARGS, ConditionSource::REQUEST_COOKIES, ConditionSource::REQUEST_HEADERS ),
			true
		);
	}

	/**
	 * Get all values for a source (scan-all mode).
	 *
	 * @param Request $request Request object.
	 * @param string  $source  Condition source constant.
	 *
	 * @return array Associative array of field name => value pairs.
	 */
	private function getAllSourceValues( $request, $source ) {
		switch ( $source ) {
			case ConditionSource::ARGS:
				return $request->getAllArgs();
			case ConditionSource::REQUEST_COOKIES:
				return $request->getAllCookies();
			case ConditionSource::REQUEST_HEADERS:
				return $request->getAllHeaders();
			default:
				return array();
		}
	}

	/**
	 * Check whether a bracket path contains any regex segments.
	 *
	 * @since 3.0.0
	 *
	 * @param array $bracketPath Array of bracket-path segments.
	 *
	 * @return bool True if at least one segment is a /regex/ pattern.
	 */
	private static function bracketPathHasRegex( array $bracketPath ) {
		foreach ( $bracketPath as $segment ) {
			if ( preg_match( '#^/(.+)/$#', $segment ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Resolve a literal field value then navigate regex-aware bracket path.
	 *
	 * Used when the field name is literal but bracket segments contain /regex/.
	 *
	 * @since 3.0.0
	 *
	 * @param Request $request Request object.
	 * @param array   $parsed  Parsed condition name from Condition::parseNameString().
	 *
	 * @return array Resolved leaf values.
	 */
	private function resolveFieldWithRegexBrackets( Request $request, array $parsed ) {
		$source = $parsed['source'];
		$field  = $parsed['field'];

		if ( null === $field ) {
			return array();
		}

		$rootValue = null;
		switch ( $source ) {
			case ConditionSource::ARGS:
				$rootValue = $request->get( $field );
				if ( null === $rootValue ) {
					$rootValue = $request->post( $field );
				}
				break;
			case ConditionSource::REQUEST_COOKIES:
				$rootValue = $request->cookie( $field );
				break;
			case ConditionSource::REQUEST_HEADERS:
				$rootValue = $request->getHeader( $field );
				break;
			default:
				return array();
		}

		if ( null === $rootValue ) {
			return array();
		}

		return self::navigateBracketPath( $rootValue, $parsed['bracket_path'] );
	}

	/**
	 * Navigate a bracket path into each value from a regex-matched set.
	 *
	 * Each regex-matched value is expected to be an array. The bracket path
	 * segments are traversed into each value. Segments wrapped in /regex/
	 * are treated as regex patterns that match multiple keys at that level.
	 *
	 * @since 3.0.0
	 *
	 * @param array $values      Flat array of matched values.
	 * @param array $bracketPath Array of bracket-path segments.
	 *
	 * @return array Resolved leaf values after bracket navigation.
	 */
	private function navigateBracketPathIntoValues( array $values, array $bracketPath ) {
		$results = array();

		foreach ( $values as $value ) {
			$navigated = self::navigateBracketPath( $value, $bracketPath );
			foreach ( $navigated as $leaf ) {
				$results[] = $leaf;
			}
		}

		return $results;
	}

	/**
	 * Navigate a single value through bracket-path segments.
	 *
	 * Literal segments perform a direct array key lookup. Segments matching
	 * the /regex/ convention iterate over keys at that level.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $value       The value to navigate into.
	 * @param array $bracketPath Array of bracket-path segments.
	 *
	 * @return array Resolved leaf values (may contain strings or arrays).
	 */
	private static function navigateBracketPath( $value, array $bracketPath ) {
		$current = array( $value );

		foreach ( $bracketPath as $segment ) {
			$next = array();

			if ( preg_match( '#^/(.+)/$#', $segment, $m ) ) {
				$regex = '#^(?:' . str_replace( '#', '\\#', $m[1] ) . ')$#';
				// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- invalid regex silently skipped.
				if ( false === @preg_match( $regex, '' ) ) {
					return array();
				}

				foreach ( $current as $item ) {
					if ( ! is_array( $item ) ) {
						continue;
					}
					foreach ( $item as $key => $val ) {
						if ( preg_match( $regex, (string) $key ) ) {
							$next[] = $val;
						}
					}
				}
			} else {
				foreach ( $current as $item ) {
					if ( is_array( $item ) && isset( $item[ $segment ] ) ) {
						$next[] = $item[ $segment ];
					}
				}
			}

			if ( empty( $next ) ) {
				return array();
			}

			$current = $next;
		}

		return $current;
	}

	/**
	 * Get the decoded request URI for condition evaluation.
	 *
	 * Applies urldecode() twice to defend against both single-encoded
	 * and double-encoded URI bypass attempts (e.g., %2F and %252F).
	 *
	 * @param Request $request Request object.
	 *
	 * @return string The decoded URI.
	 */
	private function getDecodedUri( $request ) {
		return urldecode( urldecode( $request->getUri() ) );
	}
}
