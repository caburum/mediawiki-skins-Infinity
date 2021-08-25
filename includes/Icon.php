<?php

namespace MediaWiki\Skin\Infinity;

class Icon {
	private static $svgDir =  __DIR__ . '/../resources/skins.infinity.icons';
	private static $svgCache = [ ];

	/**
	 * @desc Returns SVG content
	 *
	 * @param string $name
	 * @param string $classNames
	 * @param string $alt
	 *
	 * @return string
	 */
	public static function renderSvg( $name, $classNames = '', $alt = '' ) {
		$xml = static::getCachedSvg( $name );

		if ( $xml instanceof \SimpleXMLElement ) {
			if ( !empty( $classNames ) ) {
				$xml->addAttribute( 'class', $classNames );
			}

			if ( !empty( $alt ) ) {
				$xml->addAttribute( 'alt', $alt );
			}

			$xml->addAttribute( 'id', $name );

			return $xml->asXML();
		} else {
			wfWarn( __METHOD__ . ' failed to load SVG "' . $name . '"');
			return null;
		}
	}

	/**
	 * @desc Loads SVG file as a SimpleXMLElement object or gets it from cache
	 *
	 * @param string $name
	 *
	 * @return SimpleXMLElement
	 */
	private static function getCachedSvg( $name ) {
		if ( isset( static::$svgCache[$name] ) ) {
			$xml = static::$svgCache[$name];
		} else {
			$xml = simplexml_load_file( static::$svgDir . '/' . $name . '.svg', 'SimpleXMLElement' );
			static::$svgCache[$name] = $xml;
		}

		return ( $xml instanceof \SimpleXMLElement ) ? $xml : null;
	}
}