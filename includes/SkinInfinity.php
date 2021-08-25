<?php

namespace MediaWiki\Skin\Infinity;

use Exception;
use SkinMustache;
use Linker;
use Html;
use MediaWiki\MediaWikiServices;
use MediaWiki\Skin\Infinity\GetConfigTrait;

class SkinInfinity extends SkinMustache {
	use GetConfigTrait;

	/**
	 * Overrides template, styles and scripts module
	 *
	 * @inheritDoc
	 */
	public function __construct( $options = [] ) {
		$options['templateDirectory'] = dirname( __DIR__, 1 ) . '/templates';
		parent::__construct( $options );
	}

	/**
	 * Extends the getTemplateData function to add additional template keys
	 * which can be rendered in skin.mustache
	 */
	public function getTemplateData() {
		return parent::getTemplateData() + [
			'data-pagetools' => $this->buildPageTools(),
		];
	}

	/**
	 * Render page-related tools
	 *
	 * @return array html
	 */
	public function buildPageTools(): array {
		$contentNavigation = parent::buildContentNavigationUrls();
		$portals = parent::buildSidebar();

		$viewshtml = $this->getMenuData( 'views', $contentNavigation[ 'views' ], ['makeButtons' => true] );
		$actionshtml = $this->getMenuData( 'actions', $contentNavigation[ 'actions' ] );
		$namespaceshtml = $this->getMenuData( 'namespaces', $contentNavigation[ 'namespaces' ] );
		$variantshtml = $this->getMenuData( 'variants', $contentNavigation[ 'variants' ] );
		$toolboxhtml = $this->getMenuData( 'tb',  $portals['TOOLBOX'] );

		return [
			'data-page-views' => $viewshtml,
			'data-page-actions' => $actionshtml,
			'data-namespaces' => $namespaceshtml,
			'data-variants' => $variantshtml,
			'data-page-toolbox' => $toolboxhtml,
		];
	}

	/**
	 * @param string $label to be used to derive the id and human readable label of the menu
	 * @param array $urls to convert to list items stored as string in html-items key
	 * @param array $options (optional) to be passed to makeListItem, set makeButtons to make buttons instead of links
	 * @return array
	 */
	public function getMenuData(
		string $label,
		array $urls = [],
		array $options = []
	): array {
		$skin = $this->getSkin();

		$messages = [
			'tb' => 'toolbox',
			'personal' => 'personaltools',
			'lang' => 'otherlanguages',
		];
		$msgObj = $skin->msg( $messages[ $label ] ?? $label );

		$props = [
			'id' => "p-$label",
			'label-class' => null,
			'label-id' => "p-{$label}-label",
			'label' => $msgObj->exists() ? $msgObj->text() : $label,
			'html-items' => '',
			'html-tooltip' => Linker::tooltip( 'p-' . $label ),
		];

		foreach ( $urls as $key => $item ) {
			if ( isset( $options['makeButtons'] ) ) {
				$props['html-items'] .= $this->makeButton( $key, $item );
			} else {
				$props['html-items'] .= $this->makeListItem( $key, $item, $options );
			}
		}

		$props['html-after-portal'] = $this->getAfterPortlet( $label );

		// Mark the portal as empty if it has no content
		$props['class'] = ( empty( $urls ) && !$props['html-after-portal'] ) ? ' mw-portal-empty' : '';
		return $props;
	}

	/**
	 * Make a button, similar to Skin::makeListItem()
	 * @param string $icon
	 * @param array $item 
	 * @return array
	 */
	public function makeButton($iconName, $item) {
		// return json_encode($iconName) . ' ';
		$html = '';

		// Surround button in <a> if it is a link or <div> if it is not
		$isLinked = !empty( $item['href'] );

		$isPrimary = ! ( isset($item['redundant']) ? $item['redundant'] : false ) && isset($item['primary']) ? $item['primary'] : false;

		$html .= Html::openElement(
			$isLinked ? 'a' : 'div',
			[ 'id' => $item['id'], 'class' => join( ' ', array( $item['class'], $isPrimary ? ' primary' : '', 'mw-button' ) ) ] +
			($isLinked ? [ 'href' => $item['href'], 'title' => $item['title'] ?? '' ] : [])
		);

		if ( isset( $iconName ) ) {
			$icon = Icon::renderSvg( $iconName );
			if ( isset( $icon ) ) {
				$html .= $icon;
			}
		}

		// Place the button text in a <span> element
		$html .= Html::element( 'span', [], $item['text'] );

		// Close elements
		$html .= Html::closeElement( $isLinked ? 'a' : 'div' );

		return $html;
	}
}