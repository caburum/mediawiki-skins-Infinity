<?php
/**
 * Skin subclass for Infinity
 * @ingroup Skins
 */
class SkinInfinity extends SkinMustache {
	/**
	 * Overrides template, styles and scripts module
	 *
	 * @inheritDoc
	 */
	public function __construct( $options = [] ) {
		$options['templateDirectory'] = dirname( __DIR__, 1 ) . '/templates';
		parent::__construct( $options );
	}
}