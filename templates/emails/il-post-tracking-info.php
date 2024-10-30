<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shipment Tracking
 *
 * Shows tracking information in the HTML order email
 *
 * @author  WooThemes
 * @package WooCommerce Shipment Tracking/templates/email
 * @version 1.6.4
 */
$text_align = is_rtl() ? 'right' : 'left';

if ( $tracking_items ) : ?>
	<h2 style="text-align: <?php echo $text_align; ?>;"><?php echo apply_filters( 'woocommerce_shipment_tracking_my_orders_title', __( 'Israel Post Tracking Information', 'israel-post-for-woocommerce' ) ); ?></h2>

	<table class="td" cellspacing="0" cellpadding="6" style="width: 100%;" border="1">

		<thead>
			<tr>
				<th class="tracking-provider" scope="col" class="td" style="text-align: <?php echo $text_align; ?>; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; color: #737373; border: 1px solid #e4e4e4; padding: 12px;"><?php _e( 'Provider', 'israel-post-for-woocommerce' ); ?></th>
				<th class="tracking-number" scope="col" class="td" style="text-align: <?php echo $text_align; ?>; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; color: #737373; border: 1px solid #e4e4e4; padding: 12px;"><?php _e( 'Tracking Number', 'israel-post-for-woocommerce' ); ?></th>
				<th class="date-shipped" scope="col" class="td" style="text-align: <?php echo $text_align; ?>; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; color: #737373; border: 1px solid #e4e4e4; padding: 12px;"><?php _e( 'Date', 'israel-post-for-woocommerce' ); ?></th>
				<th class="order-actions" scope="col" class="td" style="text-align: <?php echo $text_align; ?>; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; color: #737373; border: 1px solid #e4e4e4; padding: 12px;">&nbsp;</th>
			</tr>
		</thead>

		<tbody><?php
		foreach ( $tracking_items as $tracking_item ) {
				if($tracking_item['tracking_provider'] == 'israel-post'){
				?><tr class="tracking">
					<td class="tracking-provider" data-title="<?php _e( 'Provider', 'israel-post-for-woocommerce' ); ?>" style="text-align: <?php echo $text_align; ?>; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; color: #737373; border: 1px solid #e4e4e4; padding: 12px;">
						<?php echo esc_html( $tracking_item['formatted_tracking_provider'] ); ?>
					</td>
					<td class="tracking-number" data-title="<?php _e( 'Tracking Number', 'israel-post-for-woocommerce' ); ?>" style="text-align: <?php echo $text_align; ?>; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; color: #737373; border: 1px solid #e4e4e4; padding: 12px;">
						<?php echo esc_html( $tracking_item['tracking_number'] ); ?>
					</td>
					<td class="date-shipped" data-title="<?php _e( 'Status', 'israel-post-for-woocommerce' ); ?>" style="text-align: <?php echo $text_align; ?>; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; color: #737373; border: 1px solid #e4e4e4; padding: 12px;">
						<time datetime="<?php echo date( 'Y-m-d', $tracking_item['date_shipped'] ); ?>" title="<?php echo date( 'Y-m-d', $tracking_item['date_shipped'] ); ?>"><?php echo date_i18n( get_option( 'date_format' ), $tracking_item['date_shipped'] ); ?></time>
					</td>
					<td class="order-actions" style="text-align: <?php echo $text_align; ?>; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; color: #737373; border: 1px solid #e4e4e4; padding: 12px;">
							<?php $url = str_replace('%number%',$tracking_item['tracking_number'],$tracking_item['formatted_tracking_link']); ?>	
							<a href="<?php echo esc_url( $url ); ?>" target="_blank"><?php _e( 'Track', 'israel-post-for-woocommerce' ); ?></a>
					</td>
				</tr><?php
				}
		}
		?></tbody>
	</table><br /><br />

<?php
endif;