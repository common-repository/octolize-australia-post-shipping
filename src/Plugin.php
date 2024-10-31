<?php
/**
 * Plugin main class.
 *
 * @package Octolize\Shipping\AustraliaPost;
 */

namespace Octolize\Shipping\AustraliaPost;

use Octolize\Shipping\AustraliaPost\Beacon\Beacon;
use Octolize\Shipping\AustraliaPost\Beacon\BeaconDisplayStrategy;
use OctolizeShippingAustraliaPostVendor\Octolize\Onboarding\PluginUpgrade\MessageFactory\LiveRatesFsRulesTable;
use OctolizeShippingAustraliaPostVendor\Octolize\Onboarding\PluginUpgrade\PluginUpgradeOnboardingFactory;
use OctolizeShippingAustraliaPostVendor\Octolize\ShippingExtensions\ShippingExtensions;
use OctolizeShippingAustraliaPostVendor\Octolize\Tracker\TrackerInitializer;
use OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\Settings\SettingsValuesAsArray;
use OctolizeShippingAustraliaPostVendor\WPDesk\AustraliaPostShippingService\AustraliaPostSettingsDefinition;
use OctolizeShippingAustraliaPostVendor\WPDesk\AustraliaPostShippingService\AustraliaPostShippingService;
use OctolizeShippingAustraliaPostVendor\WPDesk\Logger\SimpleLoggerFactory;
use OctolizeShippingAustraliaPostVendor\WPDesk\Notice\AjaxHandler;
use OctolizeShippingAustraliaPostVendor\WPDesk\PluginBuilder\Plugin\AbstractPlugin;
use OctolizeShippingAustraliaPostVendor\WPDesk\PluginBuilder\Plugin\HookableCollection;
use OctolizeShippingAustraliaPostVendor\WPDesk\PluginBuilder\Plugin\HookableParent;
use OctolizeShippingAustraliaPostVendor\WPDesk\RepositoryRating\DisplayStrategy\ShippingMethodDisplayDecision;
use OctolizeShippingAustraliaPostVendor\WPDesk\RepositoryRating\RatingPetitionNotice;
use OctolizeShippingAustraliaPostVendor\WPDesk\RepositoryRating\RepositoryRatingPetitionText;
use OctolizeShippingAustraliaPostVendor\WPDesk\RepositoryRating\TextPetitionDisplayer;
use OctolizeShippingAustraliaPostVendor\WPDesk\RepositoryRating\TimeWatcher\ShippingMethodGlobalSettingsWatcher;
use OctolizeShippingAustraliaPostVendor\WPDesk\WooCommerceShipping\AddMethodReminder\AddMethodReminder;
use OctolizeShippingAustraliaPostVendor\WPDesk\WooCommerceShipping\AustraliaPost\AustraliaPostAdminOrderMetaDataDisplay;
use OctolizeShippingAustraliaPostVendor\WPDesk\WooCommerceShipping\AustraliaPost\AustraliaPostShippingMethod;
use OctolizeShippingAustraliaPostVendor\WPDesk\WooCommerceShipping\CustomFields\ApiStatus\FieldApiStatusAjax;
use OctolizeShippingAustraliaPostVendor\WPDesk\WooCommerceShipping\OrderMetaData\AdminOrderMetaDataDisplay;
use OctolizeShippingAustraliaPostVendor\WPDesk\WooCommerceShipping\OrderMetaData\FrontOrderMetaDataDisplay;
use OctolizeShippingAustraliaPostVendor\WPDesk\WooCommerceShipping\OrderMetaData\SingleAdminOrderMetaDataInterpreterImplementation;
use OctolizeShippingAustraliaPostVendor\WPDesk\WooCommerceShipping\PluginShippingDecisions;
use OctolizeShippingAustraliaPostVendor\WPDesk\WooCommerceShipping\ShippingBuilder\WooCommerceShippingMetaDataBuilder;
use OctolizeShippingAustraliaPostVendor\WPDesk\WooCommerceShipping\ShopSettings;
use OctolizeShippingAustraliaPostVendor\WPDesk\WooCommerceShipping\Ups\MetaDataInterpreters\FallbackAdminMetaDataInterpreter;
use OctolizeShippingAustraliaPostVendor\WPDesk\WooCommerceShipping\Ups\MetaDataInterpreters\PackedPackagesAdminMetaDataInterpreter;
use OctolizeShippingAustraliaPostVendor\WPDesk_Plugin_Info;
use OctolizeShippingAustraliaPostVendor\Psr\Log\LoggerAwareInterface;
use OctolizeShippingAustraliaPostVendor\Psr\Log\LoggerAwareTrait;
use OctolizeShippingAustraliaPostVendor\Psr\Log\NullLogger;

/**
 * Main plugin class. The most important flow decisions are made here.
 *
 * @package Octolize\OctolizeShippingAustraliaPost
 */
class Plugin extends AbstractPlugin implements LoggerAwareInterface, HookableCollection {

	use LoggerAwareTrait;
	use HookableParent;

	/**
	 * Scripts version.
	 *
	 * @var string
	 */
	private $scripts_version = '1';

	/**
	 * Plugin constructor.
	 *
	 * @param WPDesk_Plugin_Info $plugin_info Plugin info.
	 */
	public function __construct( WPDesk_Plugin_Info $plugin_info ) {
		if ( defined( 'OCTOLIZE_AUSTRALIA_POST_SHIPPING_VERSION' ) ) {
			$this->scripts_version = OCTOLIZE_AUSTRALIA_POST_SHIPPING_VERSION . '.' . $this->scripts_version;
		}
		parent::__construct( $plugin_info );
		$this->plugin_url       = $this->plugin_info->get_plugin_url();
		$this->plugin_namespace = $this->plugin_info->get_text_domain();
	}

	/**
	 * Returns true when debug mode is on.
	 *
	 * @return bool
	 */
	private function is_debug_mode() {
		$global_australia_post_settings = $this->get_global_australia_post_settings();

		return isset( $global_australia_post_settings['debug_mode'] ) && 'yes' === $global_australia_post_settings['debug_mode'];
	}


	/**
	 * Get global Australia Post settings.
	 *
	 * @return string[]
	 */
	private function get_global_australia_post_settings() {
		/** @phpstan-ignore-next-line */
		return get_option( 'woocommerce_' . AustraliaPostShippingService::UNIQUE_ID . '_settings', [] );
	}

	/**
	 * Init plugin
	 *
	 * @return void
	 */
	public function init() {
		$global_australia_post_settings = new SettingsValuesAsArray( $this->get_global_australia_post_settings() );

		$origin_country = $this->get_origin_country_code( $global_australia_post_settings );

		$this->setLogger( $this->is_debug_mode() ? ( new SimpleLoggerFactory( 'australia-post' ) )->getLogger() : new NullLogger() );

		/** @var AustraliaPostShippingService $australia_post_service */
		// @phpstan-ignore-next-line.
		$australia_post_service = apply_filters( 'octolize_shipping_australia_post_shipping_service', new AustraliaPostShippingService( $this->logger, new ShopSettings( AustraliaPostShippingService::UNIQUE_ID ), $origin_country ) );

		$this->add_hookable(
			new \OctolizeShippingAustraliaPostVendor\WPDesk\WooCommerceShipping\Assets( $this->get_plugin_url() . 'vendor_prefixed/wpdesk/wp-woocommerce-shipping/assets', 'australia-post' )
		);
		$this->init_repository_rating();

		$admin_meta_data_interpreter = new AdminOrderMetaDataDisplay( AustraliaPostShippingService::UNIQUE_ID );
		$admin_meta_data_interpreter->add_interpreter(
			new SingleAdminOrderMetaDataInterpreterImplementation(
				WooCommerceShippingMetaDataBuilder::SERVICE_TYPE,
				__( 'Service Code', 'octolize-australia-post-shipping' )
			)
		);
		$admin_meta_data_interpreter->add_interpreter( new FallbackAdminMetaDataInterpreter() );
		$admin_meta_data_interpreter->add_hidden_order_item_meta_key( WooCommerceShippingMetaDataBuilder::COLLECTION_POINT );
		$admin_meta_data_interpreter->add_interpreter( new PackedPackagesAdminMetaDataInterpreter() );
		$this->add_hookable( $admin_meta_data_interpreter );

		$meta_data_interpreter = new FrontOrderMetaDataDisplay( AustraliaPostShippingService::UNIQUE_ID );
		$this->add_hookable( $meta_data_interpreter );

		/**
		 * Handles API Status AJAX requests.
		 *
		 * @var FieldApiStatusAjax $api_ajax_status_handler .
		 */
		// @phpstan-ignore-next-line.
		$api_ajax_status_handler = new FieldApiStatusAjax( $australia_post_service, $global_australia_post_settings, $this->logger );
		$this->add_hookable( $api_ajax_status_handler );

		// @phpstan-ignore-next-line.
		$plugin_shipping_decisions = new PluginShippingDecisions( $australia_post_service, $this->logger );
		$plugin_shipping_decisions->set_field_api_status_ajax( $api_ajax_status_handler );

		AustraliaPostShippingMethod::set_plugin_shipping_decisions( $plugin_shipping_decisions );

		$this->add_hookable( new AustraliaPostAdminOrderMetaDataDisplay( AustraliaPostShippingService::UNIQUE_ID ) );

		$this->add_hookable(
			new AddMethodReminder(
				$australia_post_service->get_name(),
				$australia_post_service::UNIQUE_ID,
				$australia_post_service::UNIQUE_ID,
				AustraliaPostSettingsDefinition::API_KEY
			)
		);

		$this->add_hookable( new ShippingExtensions( $this->plugin_info ) );

		$this->init_tracker();

		$this->init_upgrade_onboarding();

		parent::init();
	}

	/**
	 * @return void
	 */
	private function init_tracker() {
		$this->add_hookable( TrackerInitializer::create_from_plugin_info_for_shipping_method( $this->plugin_info, AustraliaPostShippingService::UNIQUE_ID ) );
	}

	private function init_upgrade_onboarding(): void {
		$upgrade_onboarding = new PluginUpgradeOnboardingFactory(
			$this->plugin_info->get_plugin_name(),
			$this->plugin_info->get_version(),
			$this->plugin_info->get_plugin_file_name()
		);
		$upgrade_onboarding->add_upgrade_message( ( new LiveRatesFsRulesTable() )->create_message( '2.0.0', $this->plugin_info->get_plugin_url() ) );
		$upgrade_onboarding->create_onboarding();
	}


	/**
	 * Show repository rating notice when time comes.
	 *
	 * @return void
	 */
	private function init_repository_rating() {
		$this->add_hookable( new AjaxHandler( trailingslashit( $this->get_plugin_url() ) . 'vendor_prefixed/wpdesk/wp-notice/assets' ) );

		$time_tracker = new ShippingMethodGlobalSettingsWatcher( AustraliaPostShippingService::UNIQUE_ID );
		$this->add_hookable( $time_tracker );
		$this->add_hookable(
			new RatingPetitionNotice(
				$time_tracker,
				AustraliaPostShippingService::UNIQUE_ID,
				__( 'Australia Post Live Rates for WooCommerce', 'octolize-australia-post-shipping' ),
				'https://octol.io/rate-ap'
			)
		);

		$this->add_hookable(
			new TextPetitionDisplayer(
				'woocommerce_after_settings_shipping',
				new ShippingMethodDisplayDecision( new \WC_Shipping_Zones(), AustraliaPostShippingService::UNIQUE_ID ),
				new RepositoryRatingPetitionText(
					'Octolize',
					__( 'Australia Post Live Rates for WooCommerce', 'octolize-australia-post-shipping' ),
					'https://octol.io/rate-ap',
					'center'
				)
			)
		);
	}

	/**
	 * Init hooks.
	 *
	 * @return void
	 */
	public function hooks() {
		parent::hooks();

		add_filter( 'woocommerce_shipping_methods', [ $this, 'woocommerce_shipping_methods_filter' ], 20, 1 );

		add_filter(
			'pre_option_woocommerce_settings_shipping_recommendations_hidden',
			function () {
				return 'yes';
			}
		);

		$this->add_hookable( new SettingsSidebar() );

		$beacon = new Beacon(
			new BeaconDisplayStrategy(),
			trailingslashit( $this->get_plugin_url() ) . 'vendor_prefixed/wpdesk/wp-helpscout-beacon/assets/'
		);
		$beacon->hooks();

		$this->hooks_on_hookable_objects();
	}

	/**
	 * Adds shipping method to Woocommerce.
	 *
	 * @param string[] $methods Methods.
	 *
	 * @return string[]
	 */
	public function woocommerce_shipping_methods_filter( $methods ) {
		$methods[ AustraliaPostShippingService::UNIQUE_ID ] = AustraliaPostShippingMethod::class;

		return $methods;
	}

	/**
	 * Quick links on plugins page.
	 *
	 * @param string[] $links .
	 *
	 * @return string[]
	 */
	public function links_filter( $links ) {
		$docs_link    = 'https://octol.io/ap-docs';
		$support_link = 'https://octol.io/ap-support';
		$settings_url = \admin_url( 'admin.php?page=wc-settings&tab=shipping&section=' . AustraliaPostShippingService::UNIQUE_ID );

		$external_attributes = ' target="_blank" ';

		$plugin_links = [
			'<a href="' . esc_url( $settings_url ) . '">' . __( 'Settings', 'octolize-australia-post-shipping' ) . '</a>',
			'<a href="' . esc_url( $docs_link ) . '"' . $external_attributes . '>' . __( 'Docs', 'octolize-australia-post-shipping' ) . '</a>',
			'<a href="' . esc_url( $support_link ) . '"' . $external_attributes . '>' . __( 'Support', 'octolize-australia-post-shipping' ) . '</a>',
		];

		if ( ! defined( 'OCTOLIZE_AUSTRALIA_POST_SHIPPING_PRO_VERSION' ) ) {
			$upgrade_link   = 'https://octol.io/ap-upgrade';
			$plugin_links[] = '<a target="_blank" href="' . $upgrade_link . '" style="color:#d64e07;font-weight:bold;">' . __( 'Upgrade', 'octolize-australia-post-shipping' ) . '</a>';
		}

		return array_merge( $plugin_links, $links );
	}

	/**
	 * Get origin country code.
	 *
	 * @param SettingsValuesAsArray $global_australia_post_settings .
	 *
	 * @return string
	 */
	private function get_origin_country_code( $global_australia_post_settings ) {
		if ( 'yes' === $global_australia_post_settings->get_value( AustraliaPostSettingsDefinition::CUSTOM_ORIGIN, 'no' ) ) {
			$origin_country_code_with_state = $global_australia_post_settings->get_value( AustraliaPostSettingsDefinition::ORIGIN_COUNTRY, '' );
		} else {
			$origin_country_code_with_state = get_option( 'woocommerce_default_country', '' );
		}

		/** @phpstan-ignore-next-line */
		[ $origin_country ] = explode( ':', $origin_country_code_with_state );

		return $origin_country;
	}

}
