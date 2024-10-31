<?php

namespace Octolize\Shipping\AustraliaPost;

use OctolizeShippingAustraliaPostVendor\WPDesk\PluginBuilder\Plugin\Hookable;

/**
 * Can display settings sidebar.
 */
class SettingsSidebar implements Hookable {

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'octolize_australia_post_shipping_settings_sidebar', [ $this, 'display_settings_sidebar_when_no_pro_version' ] );
	}

	/**
	 * Maybe display settings sidebar.
	 *
	 * @return void
	 */
	public function display_settings_sidebar_when_no_pro_version() {
		if ( ! defined( 'OCTOLIZE_AUSTRALIA_POST_SHIPPING_PRO_VERSION' ) ) {
			$pro_url  = 'https://octol.io/ap-up-box';
			include __DIR__ . '/views/settings-sidebar-html.php';
		}
	}

}
