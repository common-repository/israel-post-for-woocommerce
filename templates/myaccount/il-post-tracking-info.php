<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * View Order: Tracking information
 *
 * Shows tracking numbers view order page
 *
 * @author  WooThemes
 * @package WooCommerce Shipment Tracking/templates/myaccount
 * @version 1.6.4
 */

if ( $tracking_items ) : ?>

	<h2><?php _e( 'Israel Post Tracking Information', 'israel-post-for-woocommerce' ); ?></h2>

	<table class="shop_table shop_table_responsive my_account_tracking">
		<thead>
			<tr>
				<th class="tracking-provider"><span class="nobr"><?php _e( 'Provider', 'israel-post-for-woocommerce' ); ?></span></th>
				<th class="tracking-number"><span class="nobr"><?php _e( 'Tracking Number', 'israel-post-for-woocommerce' ); ?></span></th>
				<th class="date-shipped"><span class="nobr"><?php _e( 'Date', 'israel-post-for-woocommerce' ); ?></span></th>
				<th class="order-actions">&nbsp;</th>
			</tr>
		</thead>
		<tbody><?php
		foreach ( $tracking_items as $tracking_item ) {
				if($tracking_item['tracking_provider'] == 'israel-post'){
				?><tr class="tracking">
					<td class="tracking-provider" data-title="<?php _e( 'Provider', 'israel-post-for-woocommerce' ); ?>">
						<?php echo esc_html( $tracking_item['formatted_tracking_provider'] ); ?>
					</td>
					<td class="tracking-number" data-title="<?php _e( 'Tracking Number', 'israel-post-for-woocommerce' ); ?>">
						<?php echo esc_html( $tracking_item['tracking_number'] ); ?>
					</td>
					<td class="date-shipped" data-title="<?php _e( 'Date', 'israel-post-for-woocommerce' ); ?>" style="text-align:left; white-space:nowrap;">
						<time datetime="<?php echo date( 'Y-m-d', $tracking_item['date_shipped'] ); ?>" title="<?php echo date( 'Y-m-d', $tracking_item['date_shipped'] ); ?>"><?php echo date_i18n( get_option( 'date_format' ), $tracking_item['date_shipped'] ); ?></time>
					</td>
					<td class="order-actions" style="text-align: center;">
							<?php if ( '' !== $tracking_item['formatted_tracking_link'] ) { 
							$url = str_replace('%number%',$tracking_item['tracking_number'],$tracking_item['formatted_tracking_link']);
							?>
							<a href="<?php echo esc_url( $url ); ?>" target="_blank" class="button"><?php _e( 'Track', 'israel-post-for-woocommerce' ); ?></a>
							<?php } ?>
					</td>
				</tr><?php
				}
		}
		?></tbody>
	</table>

<?php
endif;
