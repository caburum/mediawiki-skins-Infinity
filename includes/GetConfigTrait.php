<?php

namespace MediaWiki\Skin\Infinity;

use ConfigException;
use OutputPage;

trait GetConfigTrait {
	/**
	 * getConfig() wrapper to catch exceptions.
	 * Returns null on exception
	 *
	 * @param string $key
	 * @param OutputPage|null $out
	 * @return mixed|null
	 * @see SkinTemplate::getConfig()
	 */
	protected function getConfigValue( $key, $out = null ) {
		if ( isset( $this->out ) ) {
			$out = $this->out;
		}

		if ( is_callable( [ $this, 'getOutput' ] ) ) {
			$out = $this->getOutput();
		}

		try {
			$value = $out->getConfig()->get( $key );
		} catch ( ConfigException $e ) {
			$value = null;
		}

		return $value;
	}
}