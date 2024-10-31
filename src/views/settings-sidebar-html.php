<?php
/**
 * Settings sidebar.
 *
 * @package Octolize\OctolizeShippingAustraliaPost
 */

/**
 * Params.
 *
 * @var string $pro_url
 */
?>
<div class="wpdesk-metabox">
	<div class="wpdesk-stuffbox">
		<h3 class="title"><?php esc_html_e( 'Get Australia Post WooCommerce Live Rates PRO!', 'octolize-australia-post-shipping' ); ?></h3>
		<div class="inside">
			<div class="main">
				<ul>
					<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Handling Fees', 'octolize-australia-post-shipping' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Automatic Box Packing', 'octolize-australia-post-shipping' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Premium Support', 'octolize-australia-post-shipping' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Multicurrency Support', 'octolize-australia-post-shipping' ); ?></li>
				</ul>

				<a class="button button-primary" href="<?php echo esc_url( $pro_url ); ?>"
				   target="_blank"><?php esc_html_e( 'Upgrade Now &rarr;', 'octolize-australia-post-shipping' ); ?></a>
			</div>
		</div>
	</div>
</div>
