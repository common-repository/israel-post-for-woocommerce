<?php

/**
 * HTML view file for Generate label 
 */
$europe_country_list = array('AT', 'BE', 'BG', 'HR', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GB', 'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL', 'NO', 'PL', 'PT', 'RO', 'RS', 'SI', 'SK', 'ES', 'SE', 'CH', 'TR');
?>
<div id="" class="il_post_popupwrapper generate_label_popup" style="display:none;">
	<div class="il_post_popuprow">
		<?php $use_sender_phone_as_recepient_phone = get_option('use_sender_phone_as_recepient_phone');
		$recipient_phone = '';
		if ($order->get_billing_phone()) {
			$recipient_phone = $order->get_billing_phone();
		} else if (isset($use_sender_phone_as_recepient_phone) && $use_sender_phone_as_recepient_phone) {
			$recipient_phone = get_option('sender_phone');
		}
		?>
		<h3 class="popup_title"><?php _e('Create Israel Post Shipping Label', 'israel-post-for-woocommerce'); ?> - <?php _e('Order', 'woocommerce'); ?> #<?php echo $order->get_id(); ?></h2>
			<div class="container-contact100">
				<?php
				if (is_array($response) && !is_wp_error($response)) {
					if ($response['response']['code'] == 401) { ?>
						<div class="alert alert-danger"><?php _e('No response from Israel Post API, please verify with the Israel Post that your server IP is whitelisted.', 'israel-post-for-woocommerce'); ?></div>
					<?php } else if ($response['response']['code'] == 400) { ?>
						<div class="alert alert-danger"><?php _e('Shipping country not found. Please check in order that the shipping country exists.', 'israel-post-for-woocommerce'); ?></div>
					<?php } else if ($response['response']['code'] == 204) { ?>
						<div class="alert alert-danger"><?php _e('No available shipping options for the destination country.', 'israel-post-for-woocommerce'); ?></div>
					<?php }
				} else if (!$shipdata) { ?>
					<div class="alert alert-danger"><?php _e('Connection refused, your server blocks the calls to Israel Post API on ports 1444 or 8090', 'israel-post-for-woocommerce'); ?></div>
				<?php }

				if (get_option('sender_name') && get_option('sender_name') && get_option('sender_email') && get_option('sender_phone') && get_option('sender_address', 'woocommerce_store_address') && get_option('sender_city', 'woocommerce_store_city') && get_option('sender_postal_code', 'woocommerce_store_postcode')) {
				} else { ?>
					<div class="alert alert-danger">
						<?php echo sprintf(__('Before you can create your first label. please complete all required fields in the <a href="%s" target="blank"> sender address settings.</a>', 'israel-post-for-woocommerce'), admin_url('/admin.php?page=israel-post-for-woocommerce&tab=sender-address')); ?>
					</div>
				<?php } ?>
				<div class="wrap-contact100">
					<form class="contact100-form validate-form" id="generate-label-form" method="post" enctype="multipart/form-data">
						<?php
						if ($order->get_shipping_address_1() == '' || $order->get_shipping_city() == '' || $order->get_shipping_state() == '' || $order->get_shipping_country() == '' || $order->get_shipping_postcode() == '') { ?>
							<span class="header_error_msg" style="display:block"><?php _e('Please note that the order shipping address is incomplete', 'israel-post-for-woocommerce'); ?></span>
						<?php } ?>
						<div class="row mt-1 mb-1 no-gutter " style="">
							<div class="recipient_section">
								<div class="recipient_address_table">
									<h3 class="addres_table_header"><?php _e('To', 'israel-post-for-woocommerce'); ?></h3>
									<p style="margin: 0;">
										<?php if ($order->get_shipping_first_name() || $order->get_shipping_last_name()) { ?>
											<?php echo $order->get_shipping_first_name(); ?> <?php echo $order->get_shipping_last_name(); ?>
										<?php }
										if ($order->get_shipping_company()) { ?>
											<?php echo ', ' . $order->get_shipping_company(); ?>
										<?php }
										if ($order->get_billing_email()) { ?>
											</br>
											<?php echo $order->get_billing_email(); ?>
											</br>
										<?php }
										if ($order->get_billing_phone()) { ?>
											<?php echo $order->get_billing_phone(); ?>
											</br>
										<?php } else if (isset($use_sender_phone_as_recepient_phone) && $use_sender_phone_as_recepient_phone) { ?>
											<?php echo get_option('sender_phone'); ?>
											</br>
										<?php }
										if ($order->get_shipping_address_1()) { ?>
											<?php echo $order->get_shipping_address_1(); ?>
										<?php }
										if ($order->get_shipping_address_2()) { ?>
											<?php echo ' - ' . $order->get_shipping_address_2(); ?>
											</br>
										<?php }
										if ($order->get_shipping_city() || $receiver_country || $order->get_shipping_postcode()) { ?>
											<?php echo $order->get_shipping_city(); ?>, <?php echo $receiver_country; ?> <?php echo $order->get_shipping_postcode(); ?>
											</br>
										<?php } ?>
									</p>
									<!--a href="JavaScript:void(0);" class="edit_recipient_info"><?php _e('Edit', 'woocommerce'); ?></a-->
								</div>
								<div class="recipient_info_div">
									<a href="JavaScript:void(0);" class="cancel_recipient_info"><?php _e('Cancel', 'woocommerce'); ?></a>

									<h3 class="addres_table_header" style="width:100%;"><?php _e('To', 'israel-post-for-woocommerce'); ?></h3>

									<div class="wrap-input100 validate-length-input bg1 rs1-wrap-input100" data-length="<?php _e("Recipient company can't be greater than 200 character.", 'israel-post-for-woocommerce'); ?>">
										<span class="label-input100"><?php _e('Company', 'woocommerce'); ?></span>
										<input class="input100" type="text" name="destination_company" value="<?php echo $order->get_shipping_company(); ?>" placeholder="<?php _e('Please enter recipient company', 'israel-post-for-woocommerce'); ?>" data-max="200">
									</div>
									<div class="wrap-input100 validate-input validate-length-input bg1 rs1-wrap-input100" data-validate="<?php _e('Please enter recipient name', 'israel-post-for-woocommerce'); ?>" data-length="<?php _e("Recipient name can't be greater than 200 character.", 'israel-post-for-woocommerce'); ?>">
										<span class="label-input100"><?php _e('Name', 'woocommerce'); ?> <span class="required_star">*</span></span>
										<input class="input100" type="text" name="destination_name" value="<?php echo $order->get_shipping_first_name(); ?> <?php echo $order->get_shipping_last_name(); ?>" placeholder="<?php _e('Please enter recipient name', 'israel-post-for-woocommerce'); ?>" data-max="200">
									</div>

									<div class="wrap-input100 bg1 validate-input" data-validate="<?php _e('Please enter recipient email', 'israel-post-for-woocommerce'); ?>">
										<span class="label-input100"><?php _e('Email', 'woocommerce'); ?> <span class="required_star">*</span></span>
										<input class="input100" type="text" name="destination_email" value="<?php echo $order->get_billing_email(); ?>" placeholder="<?php _e('Please enter recipient email', 'israel-post-for-woocommerce'); ?>">
									</div>

									<div class="wrap-input100 validate-input validate-length-input validate-phone bg1" data-validate="<?php _e('Please enter recipient phone', 'israel-post-for-woocommerce'); ?>" data-length="<?php _e("Recipient phone can't be greater than 20 numbers.", 'israel-post-for-woocommerce'); ?>" data-phone="<?php _e("Please enter valid recipient phone number.", 'israel-post-for-woocommerce'); ?>">
										<span class="label-input100"><?php _e('Phone', 'woocommerce'); ?> <span class="required_star">*</span></span>
										<input class="input100" type="text" name="destination_phone" value="<?php echo $recipient_phone; ?>" placeholder="<?php _e('Please enter recipient phone', 'israel-post-for-woocommerce'); ?>" data-max="20">
									</div>

									<div class="wrap-input100 bg1">
										<span class="label-input100"><?php _e('Cell Phone', 'israel-post-for-woocommerce'); ?></span>
										<input class="input100" type="text" name="destination_cellphone" value="" placeholder="<?php _e('Please enter recipient cell phone', 'israel-post-for-woocommerce'); ?>">
									</div>

									<div class="wrap-input100 bg1">
										<span class="label-input100"><?php _e('Fax', 'israel-post-for-woocommerce'); ?></span>
										<input class="input100" type="text" name="destination_fax" value="" placeholder="<?php _e('Please enter recipient fax', 'israel-post-for-woocommerce'); ?>">
									</div>


									<div class="wrap-input100 validate-input validate-length-input bg1" data-validate="<?php _e('Please enter recipient address line 1', 'israel-post-for-woocommerce'); ?>" data-length="<?php _e("Recipient address can't be greater than 200 character.", 'israel-post-for-woocommerce'); ?>">
										<span class="label-input100"><?php _e('Address line 1', 'woocommerce'); ?> <span class="required_star">*</span></span>
										<input class="input100" type="text" name="destination_address" value="<?php echo $order->get_shipping_address_1(); ?>" placeholder="<?php _e('Please enter recipient address line 1', 'israel-post-for-woocommerce'); ?>" data-max="200">
									</div>

									<div class="wrap-input100 bg1">
										<span class="label-input100"><?php _e('Address line 2', 'woocommerce'); ?></span>
										<input class="input100" type="text" name="destination_address2" value="<?php echo $order->get_shipping_address_2(); ?>" placeholder="<?php _e('Please enter recipient address line 2', 'israel-post-for-woocommerce'); ?>">
									</div>

									<div class="wrap-input100 validate-input validate-length-input bg1" data-validate="<?php _e('Please enter recipient city', 'israel-post-for-woocommerce'); ?>" data-length="<?php _e("Recipient city can't be greater than 200 character.", 'israel-post-for-woocommerce'); ?>">
										<span class="label-input100"><?php _e('City', 'woocommerce'); ?> <span class="required_star">*</span></span>
										<input class="input100" type="text" name="destination_city" value="<?php echo $order->get_shipping_city(); ?>" placeholder="<?php _e('Please enter recipient city', 'israel-post-for-woocommerce'); ?>" data-max="200">
									</div>

									<div class="wrap-input100 bg1">
										<span class="label-input100"><?php _e('State', 'woocommerce'); ?></span>
										<input class="input100" type="text" name="destination_state" value="<?php if ($order->get_shipping_state()) {
																												echo WC()->countries->get_states($order->get_shipping_country())[$order->get_shipping_state()];
																											} ?>" placeholder="<?php _e('Please enter recipient State', 'israel-post-for-woocommerce'); ?>">
									</div>

									<div class="wrap-input100 bg1">
										<span class="label-input100"><?php _e('Country', 'woocommerce'); ?> <span class="required_star">*</span></span>

										<div>
											<select  name="destination_country">
												<option value=""><?php _e('Select', 'woocommerce'); ?></option>
												<?php
												foreach ($countries as $c_code => $c_name) { ?>
													<option value="<?php echo $c_code; ?>" <?php if ($c_code == $order->get_shipping_country()) {
																								echo 'selected';
																							} ?>><?php echo $c_name; ?></option>
												<?php }
												?>
											</select>
											<div class="dropDownSelect2"></div>
										</div>
									</div>

									<div class="wrap-input100 validate-input validate-length-input bg1" data-validate="<?php _e('Please enter recipient postal code', 'israel-post-for-woocommerce'); ?>" data-length="<?php _e("Recipient postal code can't be greater than 50 character.", 'israel-post-for-woocommerce'); ?>">
										<span class="label-input100"><?php _e('Postcode / ZIP', 'woocommerce'); ?> <span class="required_star">*</span></span>
										<input class="input100" type="text" name="destination_code" value="<?php echo $order->get_shipping_postcode(); ?>" placeholder="<?php _e('Please enter recipient postal code', 'israel-post-for-woocommerce'); ?>" data-max="50">
									</div>
								</div>
							</div>

							<div class="sender_section">
								<div class="sender_address_table">
									<h3 class="addres_table_header"><?php _e('From', 'israel-post-for-woocommerce'); ?></h3>
									<p style="margin: 0;">
										<?php if (get_option('sender_name')) { ?>
											<?php echo get_option('sender_name'); ?>
										<?php }
										if (get_option('sender_company')) { ?>
											<?php echo ', ' . get_option('sender_company'); ?>
											</br>
										<?php }
										if (get_option('sender_email')) { ?>
											<?php echo get_option('sender_email'); ?>
											</br>
										<?php }
										if (get_option('sender_phone')) { ?>
											<?php echo get_option('sender_phone'); ?>
											</br>
										<?php }
										if (get_option('sender_address') || get_option('woocommerce_store_address')) { ?>
											<?php echo get_option('sender_address', get_option('woocommerce_store_address')); ?>
										<?php }
										if (get_option('sender_address2') || get_option('woocommerce_store_address_2')) { ?>
											<?php echo ' - ' . get_option('sender_address2', get_option('woocommerce_store_address_2')); ?>
											</br>
										<?php }
										if (get_option('sender_city') || get_option('sender_country') || get_option('sender_postal_code') || get_option('woocommerce_store_city') || get_option('woocommerce_store_postcode')) { ?>
											<?php echo get_option('sender_city', get_option('woocommerce_store_city')); ?>, <?php echo WC()->countries->countries[get_option('sender_country', 'IL')]; ?> <?php echo get_option('sender_postal_code', get_option('woocommerce_store_postcode')); ?>
											</br>
										<?php } ?>
									</p>
									<a href="<?php echo admin_url('/admin.php?page=israel-post-for-woocommerce&tab=sender-address'); ?>" class="edit_sender_info"><?php _e('Edit', 'woocommerce'); ?></a>
								</div>
								<div class="sender_info_div">
									<a href="JavaScript:void(0);" class="cancel_sender_info" style="display: none;"><?php _e('Cancel', 'woocommerce'); ?></a>

									<h3 class="addres_table_header" style="width:100%;"><?php _e('From', 'israel-post-for-woocommerce'); ?></h3>

									<div class="wrap-input100 validate-length-input bg1 rs1-wrap-input100" data-length="<?php _e("Sender company name can't be greater than 200 character.", 'israel-post-for-woocommerce'); ?>">
										<span class="label-input100"><?php _e('Company', 'woocommerce'); ?></span>
										<input class="input100" type="text" name="origin_company" value="<?php echo get_option('sender_company'); ?>" placeholder="<?php _e('Please enter sender company', 'israel-post-for-woocommerce'); ?>" data-max="200">
									</div>

									<div class="wrap-input100 validate-input validate-length-input bg1 rs1-wrap-input100" data-validate="<?php _e('Please enter sender name', 'israel-post-for-woocommerce'); ?>" data-length="<?php _e("Sender name can't be greater than 200 character.", 'israel-post-for-woocommerce'); ?>">
										<span class="label-input100"><?php _e('Name', 'woocommerce'); ?> <span class="required_star">*</span></span>
										<input class="input100" type="text" name="origin_name" value="<?php echo get_option('sender_name'); ?>" placeholder="<?php _e('Please enter sender name', 'israel-post-for-woocommerce'); ?>" data-max="200">
									</div>

									<div class="wrap-input100 bg1">
										<span class="label-input100"><?php _e('Email', 'woocommerce'); ?></span>
										<input class="input100" type="text" name="origin_email" value="<?php echo get_option('sender_email'); ?>" placeholder="<?php _e('Please enter sender email', 'israel-post-for-woocommerce'); ?>">
									</div>

									<div class="wrap-input100 validate-input validate-length-input validate-phone bg1" data-validate="<?php _e('Please enter sender phone', 'israel-post-for-woocommerce'); ?>" data-length="<?php _e("Sender phone can't be greater than 20 numbers.", 'israel-post-for-woocommerce'); ?>" data-phone="<?php _e("Please enter valid sender phone number.", 'israel-post-for-woocommerce'); ?>">
										<span class="label-input100"><?php _e('Phone', 'woocommerce'); ?> <span class="required_star">*</span></span>
										<input class="input100" type="text" name="origin_phone" value="<?php echo get_option('sender_phone'); ?>" data-max="20" placeholder="<?php _e('Please enter sender phone', 'israel-post-for-woocommerce'); ?>">
									</div>

									<div class="wrap-input100 bg1">
										<span class="label-input100"><?php _e('Cell Phone', 'israel-post-for-woocommerce'); ?></span>
										<input class="input100" type="text" name="origin_cellphone" value="" placeholder="<?php _e('Please enter sender cell phone', 'israel-post-for-woocommerce'); ?>">
									</div>

									<div class="wrap-input100 bg1">
										<span class="label-input100"><?php _e('Fax', 'israel-post-for-woocommerce'); ?></span>
										<input class="input100" type="text" name="origin_fax" value="" placeholder="<?php _e('Please enter sender fax', 'israel-post-for-woocommerce'); ?>">
									</div>

									<div class="wrap-input100 validate-input validate-length-input bg1" data-validate="<?php _e('Please enter sender address line 1', 'israel-post-for-woocommerce'); ?>" data-length="<?php _e("Sender address can't be greater than 200 character.", 'israel-post-for-woocommerce'); ?>">
										<span class="label-input100"><?php _e('Address line 1', 'woocommerce'); ?> <span class="required_star">*</span></span>
										<input class="input100" type="text" name="origin_address" value="<?php echo get_option('sender_address', get_option('woocommerce_store_address')); ?>" placeholder="<?php _e('Please enter sender address line 1', 'israel-post-for-woocommerce'); ?>" data-max="200">
									</div>

									<div class="wrap-input100 bg1">
										<span class="label-input100"><?php _e('Address line 2', 'woocommerce'); ?></span>
										<input class="input100" type="text" name="origin_address2" value="<?php echo get_option('sender_address2', get_option('woocommerce_store_address_2')); ?>" placeholder="<?php _e('Please enter sender address line 2', 'israel-post-for-woocommerce'); ?>">
									</div>

									<div class="wrap-input100 validate-input validate-length-input bg1" data-validate="<?php _e('Please enter sender city', 'israel-post-for-woocommerce'); ?>" data-length="<?php _e("Sender city can't be greater than 200 character.", 'israel-post-for-woocommerce'); ?>">
										<span class="label-input100"><?php _e('City', 'woocommerce'); ?> <span class="required_star">*</span></span>
										<input class="input100" type="text" name="origin_city" value="<?php echo get_option('sender_city', get_option('woocommerce_store_city')); ?>" placeholder="<?php _e('Please enter sender city', 'israel-post-for-woocommerce'); ?>" data-max="200">
									</div>

									<div class="wrap-input100 bg1">
										<span class="label-input100"><?php _e('Country', 'woocommerce'); ?> <span class="required_star">*</span></span>
										<div>
											<select  name="origin_country">
												<option value="IL" selected=""><?php _e('Israel', ''); ?></option>
											</select>
											<div class="dropDownSelect2"></div>
										</div>
									</div>

									<div class="wrap-input100 validate-input validate-length-input bg1" data-validate="<?php _e('Please enter sender postal code', 'israel-post-for-woocommerce'); ?>" data-length="<?php _e("Sender Postal Code can't be greater than 50 character.", 'israel-post-for-woocommerce'); ?>">
										<span class="label-input100"><?php _e('Postcode / ZIP', 'woocommerce'); ?> <span class="required_star">*</span></span>
										<input class="input100" type="text" name="postal_code" value="<?php echo get_option('sender_postal_code', get_option('woocommerce_store_postcode')); ?>" placeholder="<?php _e('Please enter sender postal code', 'israel-post-for-woocommerce'); ?>" data-max="50">
									</div>
								</div>
							</div>
						</div>

						<div class="section_header">
							<h3 class="sub-header1"><?php _e('Product & Package Details', 'israel-post-for-woocommerce'); ?></h3>
						</div>
						<table class="wp-list-table widefat fixed posts il-post-table">
							<thead>
								<tr>
									<th scope="col" style="width: 1px;"></th>
									<th scope="col" class="postitem_title_th" style="width: 70%;"><?php _e('Item', 'israel-post-for-woocommerce'); ?></th>
									<th scope="col"><?php _e('Qty', 'israel-post-for-woocommerce'); ?></th>
									<th scope="col"><?php _e('Weight (ea)', 'israel-post-for-woocommerce'); ?></th>
									<th scope="col"><?php _e('Value (ea)', 'israel-post-for-woocommerce'); ?></th>
									<th scope="col"> <?php if (WC_IL_Post_Admin::$customsFlag) {echo '<span class="required_star">*</span>';} ?><?php _e('HS Code', 'israel-post-for-woocommerce'); ?></th>
								</tr>
							</thead>
							<?php $items = $order->get_items(); ?>
							<tbody>
								<?php
								$tracking_items = wc_il_post_admin()->get_tracking_items($order_id);
								$product_list = array();
								foreach ($tracking_items as $tracking_item) {
									if (isset($tracking_item['products_list'])) {
										$product_list[] = $tracking_item['products_list'];
									}
								}

								$package_weight = get_option('ilpost_package_material_weight');
								$total_weight = $package_weight;
								$total_value = 0;

								$n = 0;
								$total_product = count($items);

								foreach ($items as $item) {
									$line_weight = 0;
									$checked = 0;
									$line_value = '';
									$quantity = $item->get_quantity(); // get quantity
									$product = $item->get_product(); // get the WC_Product object
									$product_weight = wc_get_weight($product->get_weight(), 'kg'); // get the product weight

									if ($product_weight != 0) {
										$line_weight += floatval($product_weight * $quantity);
									}

									$hs_code = $product->get_attribute('hs_code');

									$all_list = array();
									foreach ($product_list as $list) {

										foreach ($list as $in_list) {
											if (isset($all_list[$in_list->product])) {
												$all_list[$in_list->product] = (int)$all_list[$in_list->product] + (int)$in_list->qty;
											} else {
												$all_list[$in_list->product] = $in_list->qty;
											}
										}
									}

									if (array_key_exists($item->get_product_id(), $all_list)) {
										if (isset($all_list[$item->get_product_id()])) {
											$qty = (int)$item->get_quantity() - (int)$all_list[$item->get_product_id()];
											if ($all_list[$item->get_product_id()] == $item->get_quantity()) {
												$checked = 1;
											} else {
												$total_weight = $total_weight + $line_weight;
												$total_value = $total_value + $item->get_total();
											}
										}
									} else {
										$total_weight = $total_weight + $line_weight;
										$total_value = $total_value + $item->get_total();
									}
								?>
									<tr class="PostItem_row <?php if ($checked == 0) {
															} else {
																echo 'item_shipped';
															} ?>">
										<td><input type="checkbox" class="PostItem_product_select" name="PostItem[<?php echo $n; ?>][product]" value="<?php echo $item->get_product_id(); ?>" <?php if ($checked == 0) {
																																																	echo 'checked';
																																																} else {
																																																	echo 'readonly ' . 'onclick="return false;"';
																																																} ?> <?php if ($total_product == 1) {
																																																			echo 'readonly ' . 'onclick="return false;"';
																																																		} ?>></td>
										<td>
											<a href="<?php echo get_permalink($item->get_product_id()); ?>" target="_blank"><?php echo preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $item->get_name()); ?></a>
											<?php if ($checked == 1) {
												echo '<span> - shipped</span>';
											} ?>
											<input type="hidden" value="<?php echo preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $item->get_name()); ?>" name="PostItem[<?php echo $n; ?>][title]">
										</td>
										<td><?php echo $item->get_quantity(); ?><input type="hidden" value="<?php echo $item->get_quantity(); ?>" name="PostItem[<?php echo $n; ?>][quantity]">
										</td>
										<td class="validate-input" data-validate="<?php _e('Product weight is required', 'israel-post-for-woocommerce'); ?>">
											<input type="text" value="<?php echo round($line_weight, 2); ?>" class="<?php if ($checked == 0) {
																														echo 'input100';
																													} ?> PostItem_Weight ilpost_table_input" name="PostItem[<?php echo $n; ?>][grams]">
										</td>
										<td class="validate-input" data-validate="<?php _e('Product value is required', 'israel-post-for-woocommerce'); ?>">
											<span class="before_input_currency_symbol"><?php echo get_woocommerce_currency_symbol($order->get_currency()); ?></span>
											<input type="text" class="input100 PostItem_Value ilpost_table_input" value="<?php echo $item->get_total(); ?>" name="PostItem[<?php echo $n; ?>][price]">
										</td>
										<td>
										<input type="text" class="input100 ilpost_table_input" <?php if (WC_IL_Post_Admin::$customsFlag) {echo "required";} ?> value="<?php echo $hs_code ?>" name="PostItem[<?php echo $n; ?>][hs_code]"></td>
									</tr>
								<?php $n++;
								} ?>
							</tbody>
							<tfoot>
								<tr>
									<th scope="col" colspan="6" style="border-top:0;"><?php _e('Contents Description', 'israel-post-for-woocommerce'); ?>
										<input type="text" style="width: calc(100% - 165px);margin-left: 20px;" class="input100 ilpost_table_input" name="description" data-max="500" placeholder="<?php _e('Your description here...', 'israel-post-for-woocommerce'); ?>">
									</th>
								</tr>
							</tfoot>
							<tfoot>
								<tr>
									<th scope="col" colspan="2"><?php _e('Package Weight', 'israel-post-for-woocommerce'); ?></th>
									<th scope="col"></th>
									<th scope="col">
										<input type="text" name="package_weight" class="input100 ilpost_table_input package_weight_input" value="<?php echo get_option('ilpost_package_material_weight'); ?>">
									</th>
									<th scope="col"></th>
									<th scope="col"></th>
								</tr>
							</tfoot>
							<tfoot>
								<tr>
									<th scope="col" colspan="2"><?php _e('Total', 'israel-post-for-woocommerce'); ?></th>
									<th scope="col"></th>
									<th scope="col">
										<span class="total_weight_span"><?php echo round($total_weight, 2); ?></span>
										<input class="total_weight_org" type="hidden" name="total_weight_org" value="<?php echo round($total_weight, 2); ?>">
										<input class="total_weight_input" type="hidden" name="total_weight" value="<?php echo round($total_weight, 2); ?>">
									</th>
									<th scope="col">
										<span><?php echo get_woocommerce_currency_symbol($order->get_currency()); ?><span class="total_value_span"><?php echo $total_value; ?></span></span>
										<input class="total_value_input" type="hidden" name="declared_value" value="<?php echo $total_value; ?>">
									</th>
									<th scope="col"></th>
								</tr>
							</tfoot>
							<tfoot>
								<tr>
									<th scope="col" colspan="6"><?php _e('Package dimension', 'israel-post-for-woocommerce'); ?>
										<select class="" name="package_dimension_details" id="package_dimension_details">
											<option value=""><?php _e('Custom', 'israel-post-for-woocommerce'); ?></option>
											<?php
											$package_dimension_details = get_option('il_post_package_dimension_details');
											$default_key = get_option('set_package_default_key', '');
											$default_length = '';
											$default_width = '';
											$default_height = '';
											if (!empty($package_dimension_details)) {
												foreach ($package_dimension_details as $key => $details) {
													if ($details['package_dimension_key'] == $default_key) {
														$default_length = $details['package_dimension_length'];
														$default_width = $details['package_dimension_width'];
														$default_height = $details['package_dimension_height'];
													}
											?>
													<option value="<?php echo $details['package_dimension_title']; ?>" data-length="<?php echo $details['package_dimension_length']; ?>" data-width="<?php echo $details['package_dimension_width']; ?>" data-height="<?php echo $details['package_dimension_height']; ?>" <?php if ($details['package_dimension_key'] == $default_key) {
																																																																																echo 'selected';
																																																																															} ?>><?php echo $details['package_dimension_title']; ?></option>
											<?php }
											} ?>
										</select>
										<input class="input100 ilpost_table_input allow_decimal" type="text" name="package_length" id="package_length" value="<?php echo $default_length; ?>" placeholder="<?php _e('Length', 'woocommerce'); ?>">
										<input class="input100 ilpost_table_input allow_decimal" type="text" name="package_width" id="package_width" value="<?php echo $default_width; ?>" placeholder="<?php _e('Width', 'woocommerce'); ?>">
										<input class="input100 ilpost_table_input allow_decimal" type="text" name="package_height" id="package_height" value="<?php echo $default_height; ?>" placeholder="<?php _e('Height', 'woocommerce'); ?>">
										<span>(cm)</span>
									</th>
								</tr>
							</tfoot>
						</table>

						<div class="section_header">
							<h3 class="sub-header1"><?php _e('Shipping Options', 'israel-post-for-woocommerce'); ?></h3>
						</div>
						<div class="shipping-option-div">
							<div class="wrap-input100 bg1 rs2-wrap-input100">
								<span class="label-input100"><?php _e('Shipping Types', 'israel-post-for-woocommerce'); ?></span>
								<div>
									<select name="ShippingTypeID" id="shipping_type">
										<?php
										if (isset($shipmenttypae_data->Result)) {
											foreach ($shipmenttypae_data->Result as $sd) { ?>
												<option data-maxweight="<?php echo $sd->MaxWeightInKG; ?>" value="<?php echo $sd->ShippingTypeID; ?>" <?php if ($sd->ShippingTypeID == get_option('ilpost_shipping_service')) {
																																							echo 'selected';
																																						}  ?>><?php echo $shippingTypeData[$sd->ShippingTypeID]; ?></option>
										<?php }
										} ?>
									</select>
									<div class="dropDownSelect2"></div>
								</div>
							</div>

							<div class="wrap-input100 bg1 rs2-wrap-input100">
								<span class="label-input100"><?php _e('Label Format', 'israel-post-for-woocommerce'); ?></span>
								<div>
									<select name="MergedLabelsFileType">
										<option value="5" <?php if (get_option('ilpost_label_format') == 5) {
																echo 'selected';
															}  ?>><?php _e('PNG', ''); ?></option>
										<option value="4" <?php if (get_option('ilpost_label_format') == 4) {
																echo 'selected';
															}  ?>><?php _e('PNG in zip file', ''); ?></option>
										<option value="1" <?php if (get_option('ilpost_label_format') == 1) {
																echo 'selected';
															}  ?>><?php _e('PDF', ''); ?></option>
										<option value="6" <?php if (get_option('ilpost_label_format') == 6) {
																echo 'selected';
															}  ?>><?php _e('Zip of PDF files', ''); ?></option>
										<option value="7" <?php if (get_option('ilpost_label_format') == 7) {
																echo 'selected';
															}  ?>><?php _e('PDF Citizen printer', ''); ?></option>
										<option value="8" <?php if (get_option('ilpost_label_format') == 8) {
																echo 'selected';
															}  ?>><?php _e('PDF Dymo printer', ''); ?></option>
									</select>
									<div class="dropDownSelect2"></div>
								</div>
							</div>

							<div class="wrap-input100 bg1 rs2-wrap-input100">
								<span class="label-input100"><?php _e('Customs Procedure Type', 'israel-post-for-woocommerce'); ?></span>
								<div>
									<select  name="CustomsProcedureTypeID">
										<option value="2" <?php if (get_option('ilpost_precedure_type') == 2) {
																echo 'selected';
															}  ?>><?php _e('Regular', ''); ?></option>
										<?php
										if (!in_array($order->get_shipping_country(), $europe_country_list)) {
										?>
											<option value="1" <?php if (get_option('ilpost_precedure_type') == 1) {
																	echo 'selected';
																}  ?>><?php _e('Fiscal', ''); ?></option>
										<?php } ?>
									</select>
									<div class="dropDownSelect2"></div>
								</div>
							</div>

							<div class="wrap-input100 bg1 rs2-wrap-input100">
								<span class="label-input100"><?php _e('Export Entry Document', 'israel-post-for-woocommerce'); ?></span>
								<div>
									<select  name="IsCreateExportEntry">
										<option value="true" <?php if (get_option('ilpost_export_entry') == 'true') {
																	echo 'selected';
																} ?>><?php _e('Yes', 'woocommerce'); ?></option>
										<option value="false" <?php if (get_option('ilpost_export_entry') != 'true') {
																	echo 'selected';
																} ?>><?php _e('No', 'woocommerce'); ?></option>
									</select>
									<div class="dropDownSelect2"></div>
								</div>
							</div>

							<div class="wrap-input100 bg1 rs2-wrap-input100">
								<span class="label-input100"><?php _e('Customs Declaration ID', 'israel-post-for-woocommerce'); ?></span>
								<div>
									<select  name="CustomsDeclarationID">
										<option value=""><?php _e('Select', 'woocommerce'); ?></option>
										<option <?php if (get_option('ilpost_custom_declaration_id') == 'Merchandise') {
													echo 'selected';
												} ?> value="1">Merchandise</option>
										<option <?php if (get_option('ilpost_custom_declaration_id') == 'Document') {
													echo 'selected';
												} ?> value="2">Document</option>
										<option <?php if (get_option('ilpost_custom_declaration_id') == 'Gift') {
													echo 'selected';
												} ?> value="3">Gift</option>
										<option <?php if (get_option('ilpost_custom_declaration_id') == 'Returned Goods') {
													echo 'selected';
												} ?> value="4">Returned Goods</option>
										<option <?php if (get_option('ilpost_custom_declaration_id') == 'Sample') {
													echo 'selected';
												} ?> value="5">Sample</option>
									</select>
									<div class="dropDownSelect2"></div>
								</div>
							</div>

							<div class="wrap-input100 bg1 rs2-wrap-input100">
								<span class="label-input100"><?php _e('Insurance Type ID', 'israel-post-for-woocommerce'); ?></span>
								<div>
									<select  name="InsuranceTypeID">
										<option value=""><?php _e('Select', 'woocommerce'); ?></option>
										<option <?php if (get_option('ilpost_insurance_type_id') == 0) {
													echo 'selected';
												}  ?> value="0"><?php _e('Nothing', ''); ?></option>
										<option <?php if (get_option('ilpost_insurance_type_id') == 1) {
													echo 'selected';
												}  ?> value="1"><?php _e('Not Covering Jewelry', ''); ?></option>
										<option <?php if (get_option('ilpost_insurance_type_id') == 2) {
													echo 'selected';
												}  ?> value="2"><?php _e('Covering Jewelry', ''); ?></option>
									</select>
									<div class="dropDownSelect2"></div>
								</div>
							</div>

							<div class="wrap-input100 bg1 rs2-wrap-input100">
								<span class="label-input100"><?php _e('Insurance Value', 'israel-post-for-woocommerce'); ?></span>
								<input class="input100" type="number" name="InsuranceValue" value="" placeholder="<?php _e('Enter Insurance Value', 'israel-post-for-woocommerce'); ?>">
							</div>

							<div class="wrap-input100 validate-length-input bg1 rs2-wrap-input100" data-length="<?php _e("IOSS can't be greater than 12 Characters.", 'israel-post-for-woocommerce'); ?>">
								<span class="label-input100"><?php _e('Ioss Number', 'israel-post-for-woocommerce'); ?></span>
								<input class="input100" type="text" name="IossNumber" data-max="12" value="<?php echo get_option('ilpost_ioss_number'); ?>" placeholder="<?php _e('Enter IOSS', 'israel-post-for-woocommerce'); ?>">
							</div>
						</div>

						<div class="section_header" style="<?php if (WC_IL_Post_Admin::$customsFlag == false) echo 'display:none' ?>">
							<h3 class="sub-header1"><?php _e('Customs', 'israel-post-for-woocommerce'); ?></h3>
						</div>

						<div class="customs-div" style="<?php if (WC_IL_Post_Admin::$customsFlag == false) echo 'display:none' ?>">
							<div class="wrap-input100 bg1">

								<input class="input100" type="checkbox" name="IsNotDangerousGoodsDeclaration" 
								<?php if (WC_IL_Post_Admin::$customsFlag) {echo "required";} ?> 
								style="width:3%;display:inline-block;outline: 1px solid #1e5180">
								<span class="required_star">*</span><label for="dangerous">I declare that the shipment does not contain any dangerous substance </label>
								<div class="popup">
									<span class="dashicons dashicons-editor-help" onmouseover="myFunction()" onclick="myFunction()"></span>
									<span class="popuptext" id="myPopup"> 
									  Please notice,
It is forbidden to send a package that contains dangerous substances such as: flammable, combustible, explosive, toxic, oxidizing, corrosive, infectious or radioactive substances, compressed or liquid gas, as well as any other substance whose transportation may be dangerous to human health. 
                                    </span>
								</div>
								<script>
									// When the user clicks on div, open the popup "il-logo"
									function myFunction() {
										var popup = document.getElementById("myPopup");
										popup.classList.toggle("show");
									}
								</script>
							</div>

							<div class="shipping-option-div">
								<div class="wrap-input100 bg1 rs3-wrap-input100">
									<span class="label-input100"><?php _e('Sender From Palestinian Authority', 'israel-post-for-woocommerce'); ?></span>
									<div>
										<select  name="SenderAutonomyRegionID">
											<option value=""><?php _e('Select', 'woocommerce'); ?></option>
											<option <?php if (get_option('ilpost_Sender_From_Palestinian_Authority') == 0) {
														echo 'selected';
													}  ?> value="0"><?php _e('None', ''); ?></option>
											<option <?php if (get_option('ilpost_Sender_From_Palestinian_Authority') == 70) {
														echo 'selected';
													}  ?> value="70"><?php _e('Palestinian Authority - West bank', ''); ?></option>
											<option <?php if (get_option('ilpost_Sender_From_Palestinian_Authority') == 80) {
														echo 'selected';
													}  ?> value="80"><?php _e('Palestinian Authority - Gaza', ''); ?></option>
										</select>
										<div class="dropDownSelect2"></div>
									</div>
								</div>

								<div class="wrap-input100 bg1 rs3-wrap-input100">
									<span class="label-input100"><?php _e('Import Tax Refund Required', 'israel-post-for-woocommerce'); ?></span>
									<div>
										<select  name="IsTaxRefundRequested">
											<option value="true" <?php if (get_option('ilpost_Import_Tax_Refund_Required') == 'true') {
																		echo 'selected';
																	} ?>><?php _e('Yes', 'woocommerce'); ?></option>
											<option value="false" <?php if (get_option('ilpost_Import_Tax_Refund_Required') != 'true') {
																		echo 'selected';
																	} ?>><?php _e('No', 'woocommerce'); ?></option>


										</select>
										<div class="dropDownSelect2"></div>
									</div>
								</div>

								<div class="wrap-input100 bg1 rs3-wrap-input100">
									<span class="label-input100"><?php _e('Export Legality Required', 'israel-post-for-woocommerce'); ?></span>
									<div class="popup">
									<span class="dashicons dashicons-editor-help" onmouseover="myFunction2()" onclick="myFunction2()"></span>
									<span class="popuptext" id="myPopup2"> 
									Export Legality is required when a package contains:<br/>
                                    1. Goods that require a special export permit issued by one of the authorized authorities such as: the Ministry of Health (nutritional supplements, medical cannabis and its products), Antiquities Authority (antiquities over 250 years old) etc.<br/>
                                    2.  Weapons and means of warfare
 									</span>
								</div>
									<div>
										<select  name="IsExportLegalityRequired">
											<option value="false" <?php if (get_option('ilpost_Export_Legality_Required') != 'true') {
																		echo 'selected';
																	} ?>><?php _e('No', 'woocommerce'); ?></option>
											<option value="true" <?php if (get_option('ilpost_Export_Legality_Required') == 'true') {
																		echo 'selected';
																	} ?>><?php _e('Yes', 'woocommerce'); ?></option>

										</select>
										<div class="dropDownSelect2"></div>
									</div>
									<script>
									function myFunction2() {
										var popup = document.getElementById("myPopup2");
										popup.classList.toggle("show");
									}
								    </script>
								</div>

								<div class="wrap-input100 bg1 rs3-wrap-input100">
									<span class="label-input100"><?php _e('EXPORT INVOICE CONTAINS EUR1 DECLARATION', 'israel-post-for-woocommerce'); ?></span>
									<div>
										<select  name="IsCreateExportInvoiceEur1">
											<option value="false" <?php if (get_option('ilpost_Export_Invoice_Eur1_Declaraton') != 'true') {
																		echo 'selected';
																	} ?>><?php _e('No', 'woocommerce'); ?></option>
											<option value="true" <?php if (get_option('ilpost_Export_Invoice_Eur1_Declaraton') == 'true') {
																		echo 'selected';
																	} ?>><?php _e('Yes', 'woocommerce'); ?></option>

										</select>
										<div class="dropDownSelect2"></div>
									</div>
								</div>
								<div class="wrap-input100 bg1 rs3-wrap-input100">
									<span class="label-input100"><?php _e('EUR1 AGREEMENT CODE', 'israel-post-for-woocommerce'); ?></span>
									<div>
										<select  name="Eur1AgreementCode">
											<option value=""><?php _e('Select', 'woocommerce'); ?></option>
											<option <?php if (get_option('ilpost_Eur1_Agreement_Code') == 'USA') {
														echo 'selected';
													} ?> value="110">USA</option>
											<option <?php if (get_option('ilpost_Eur1_Agreement_Code') == 'EFTA') {
														echo 'selected';
													} ?> value="112">EFTA</option>
											<option <?php if (get_option('ilpost_Eur1_Agreement_Code') == 'EU') {
														echo 'selected';
													} ?> value="113">EU</option>
											<option <?php if (get_option('ilpost_Eur1_Agreement_Code') == 'TRK') {
														echo 'selected';
													} ?> value="114">TRK</option>
											<option <?php if (get_option('ilpost_Eur1_Agreement_Code') == 'JOR') {
														echo 'selected';
													} ?> value="115">JOR</option>
											<option <?php if (get_option('ilpost_Eur1_Agreement_Code') == 'CAN') {
														echo 'selected';
													} ?> value="116">CAN</option>
											<option <?php if (get_option('ilpost_Eur1_Agreement_Code') == 'MEX') {
														echo 'selected';
													} ?> value="117">MEX</option>
											<option <?php if (get_option('ilpost_Eur1_Agreement_Code') == 'MERC') {
														echo 'selected';
													} ?> value="118">MERC</option>
											<option <?php if (get_option('ilpost_Eur1_Agreement_Code') == 'QIZ-J') {
														echo 'selected';
													} ?> value="119">QIZ-J</option>
											<option <?php if (get_option('ilpost_Eur1_Agreement_Code') == 'QIZ-E') {
														echo 'selected';
													} ?> value="120">QIZ-E</option>
											<option <?php if (get_option('ilpost_Eur1_Agreement_Code') == 'PAN') {
														echo 'selected';
													} ?> value="121">PAN</option>
											<option <?php if (get_option('ilpost_Eur1_Agreement_Code') == 'COL') {
														echo 'selected';
													} ?> value="122">COL</option>
											<option <?php if (get_option('ilpost_Eur1_Agreement_Code') == 'UKR') {
														echo 'selected';
													} ?> value="123">UKR</option>
											<option <?php if (get_option('ilpost_Eur1_Agreement_Code') == 'GB') {
														echo 'selected';
													} ?> value="125">GB</option>
										</select>
										<div class="dropDownSelect2"></div>
									</div>
								</div>

								<div class="wrap-input100 validate-length-input bg1 rs3-wrap-input100" data-length="<?php _e("EUR1 DECLARATION NUMBER can't be greater than 35 Characters.", 'israel-post-for-woocommerce'); ?>">
									<span class="label-input100"><?php _e('EUR1 DECLARATION NUMBER', 'israel-post-for-woocommerce'); ?></span>
									<input class="input100" type="text" name="Eur1declarationnumber" data-max="35" value="" placeholder="<?php _e('Enter EUR1 DECLARATION NUMBER', 'israel-post-for-woocommerce'); ?>">
								</div>
							</div>
						</div>

						<div class="section_header">
							<h3 class="sub-header1"><?php _e('Invoice', 'israel-post-for-woocommerce'); ?></h3>
						</div>
						<div class="shipping-option-div">
							<div class="wrap-input100 bg1 rs3-wrap-input100">
								<span class="label-input100"><?php if (WC_IL_Post_Admin::$customsFlag) {echo '<span class="required_star">*</span>';} ?><?php _e('Upload Invoice', 'israel-post-for-woocommerce'); ?></span>
								<input type="file" class="contact100-btn" id="invoice" name="invoice" 

								<?php if (WC_IL_Post_Admin::$customsFlag) {echo "required";} ?> 	
						/>
								<!-- <span class="dashicons dashicons-trash" style="float: right;"></span> -->
							</div>
							<div class="wrap-input100 bg1 rs3-wrap-input100">
								<span class="label-input100"><?php if (WC_IL_Post_Admin::$customsFlag) {echo '<span class="required_star">*</span>';} ?><?php _e('Invoice Number', 'israel-post-for-woocommerce'); ?></span>
								<input class="input100 " type="text" name="InvoiceNumber" <?php if (WC_IL_Post_Admin::$customsFlag) {echo "required";} ?> 
								value="" placeholder="<?php _e('Invoice Number', 'israel-post-for-woocommerce'); ?>" >
							
							</div>
							<div class="wrap-input100 bg1 rs3-wrap-input100">
								<span class="label-input100"><?php if (WC_IL_Post_Admin::$customsFlag) {echo '<span class="required_star">*</span>';} ?><?php _e('Invoice Date', 'israel-post-for-woocommerce'); ?></span>
								<input class="input100" type="date" name="InvoiceDate"
								<?php if (WC_IL_Post_Admin::$customsFlag) {echo "required";} ?>  
								 value="" max="<?= date('Y-m-d'); ?>">

								<!--value='<?php echo $order->get_date_created()->format('Y-m-d'); ?>'-->
							</div>
						</div>

						<div class="section_header">
							<h3 class="sub-header1"><?php _e('Attachments', 'israel-post-for-woocommerce'); ?></h3>
						</div>
						<div class="shipping-option-div ">
							<!-- upload attachment file1 -->
							<div class="wrap-input100 bg1 rs3-wrap-input100">
								<input type="file" class="contact100-btn" name="attach1" accept=".pdf" />
								<input type="text" class="input100" name="descriptionAttach1" placeholder="<?php _e('Enter File Description ', 'israel-post-for-woocommerce'); ?>">
			
						<div class="wrap-input100 bg1">
						<span class="label-input100">Document Type<span class="required_star">*</span></span>
						<div>
						<select  name="Document1">
							<option value=""><?php _e('Select', 'woocommerce'); ?></option>
							<option <?php if (get_option['ilpost_Document1_Type'] == 503) {
													echo 'selected';
												} ?>value="503"><?php _e('Permits/Licenses', ''); ?></option>
							<option <?php if (get_option['ilpost_AttachDoc1_Type'] == 514) {
													echo 'selected';
												} ?>value="514"><?php _e('EUR1', ''); ?></option>
							<option <?php if (get_option['ilpost_AttachDoc1_Type'] == 600) {
													echo 'selected';
												} ?>value="600"><?php _e('EXPORT DECLARATION', ''); ?></option>
							<option <?php if (get_option['ilpost_Document1 Type'] == 999) {
													echo 'selected';
												} ?>value="999"><?php _e('Other', ''); ?></option>
							
						</select>
						<div class="dropDownSelect2"></div>
						</div>
						</div>
							</div>
						<!-- upload attachment file2 -->
							<div class="wrap-input100 bg1 rs3-wrap-input100">
								<input type="file" class="contact100-btn" name="attach2" accept=".pdf" />
								<input type="text" class="input100" name="descriptionAttach2" placeholder="<?php _e('Enter File Description ', 'israel-post-for-woocommerce'); ?>">
							<div class="wrap-input100 bg1">
						<span class="label-input100">Document Type<span class="required_star">*</span></span>
						<div>
						<select  name="Document2">					
						<option value=""><?php _e('Select', 'woocommerce'); ?></option>
							<option <?php if (get_option['ilpost_Document2_Type'] == 503) {
													echo 'selected';
												} ?>value="503"><?php _e('Permits/Licenses', ''); ?></option>
							<option <?php if (get_option['ilpost_Document2_Type'] == 514) {
													echo 'selected';
												} ?>value="514"><?php _e('EUR1', ''); ?></option>
							<option <?php if (get_option['ilpost_Document2_Type'] == 600) {
													echo 'selected';
												} ?>value="600"><?php _e('EXPORT DECLARATION', ''); ?></option>
							<option <?php if (get_option['ilpost_Document2_Type'] == 999) {
													echo 'selected';
												} ?>value="999"><?php _e('Other', ''); ?></option>					
						</select>
						<div class="dropDownSelect2"></div>
						</div>
						</div>
							</div>
							<div class="wrap-input100 bg1 rs3-wrap-input100">
								<input type="file" class="contact100-btn" name="attach3" accept=".pdf" />
								<input type="text" class="input100" name="descriptionAttach3" placeholder="<?php _e('Enter File Description ', 'israel-post-for-woocommerce'); ?>">

							<div class="wrap-input100 bg1">
						<span class="label-input100">Document Type<span class="required_star">*</span></span>
						<div>
						<select  name="Document3">
							<option value=""><?php _e('Select', 'woocommerce'); ?></option>
							<option <?php if (get_option['ilpost_Document3_Type'] == 503) {
													echo 'selected';
												} ?>value="503"><?php _e('Permits/Licenses', ''); ?></option>
							<option <?php if (get_option['ilpost_Document3_Type'] == 514) {
													echo 'selected';
												} ?>value="514"><?php _e('EUR1', ''); ?></option>
							<option <?php if (get_option['ilpost_Document3_Type'] == 600) {
													echo 'selected';
												} ?>value="600"><?php _e('EXPORT DECLARATION', ''); ?></option>
							<option <?php if (get_option['ilpost_Document3_Type'] == 999) {
													echo 'selected';
												} ?>value="999"><?php _e('Other', ''); ?></option>
						</select>
						<div class="dropDownSelect2"></div>
						</div>
						</div>
							</div>
						</div>

						<input type="hidden" name="CurrencyCode" value="<?php echo $order->get_currency(); ?>">
						<input type="hidden" name="OriginCountryCode" value="<?php echo get_option('sender_country', 'IL') ?>">
						<input type="hidden" name="order_total_weight" value="<?php echo $total_weight; ?>">
						<input type="hidden" name="order_date" value="<?php echo $order->get_date_created(); ?>">
						<input type="hidden" name="shop" value="<?php echo get_home_url(); ?>">
						<input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
						<input type="hidden" name="ClientShipmentIdentifier" value="<?php echo $ClientShipmentIdentifier; ?>">
						<input type="hidden" name="action" value="generae_il_post_order_label">

						<?php if (is_array($response) && !is_wp_error($response)) {
							if ($response['response']['code'] == 401) { ?>
								<div class="alert alert-danger"><?php _e('No response from Israel Post API, please verify with the Israel Post that your server IP is whitelisted.', 'israel-post-for-woocommerce'); ?></div>
							<?php } else if ($response['response']['code'] == 400) { ?>
								<div class="alert alert-danger"><?php _e('Shipping country not found. Please check in order that the shipping country exists.', 'israel-post-for-woocommerce'); ?></div>
							<?php } else if ($response['response']['code'] == 204) { ?>
								<div class="alert alert-danger"><?php _e('No available shipping options for the destination country.', 'israel-post-for-woocommerce'); ?></div>
							<?php }
						} else if (!$shipdata) { ?>
							<div class="alert alert-danger"><?php _e('Connection refused, your server blocks the calls to Israel Post API on ports 1444 or 8090', 'israel-post-for-woocommerce'); ?></div>
						<?php } ?>

						<div class="container-contact100-form-btn">
							<button class="contact100-form-btn button-primary btn_il_post" <?php if (!$shipdata || $response['response']['code'] == 204) {
																								echo 'disabled';
																							} ?>>
								<span>
									<?php _e('Print IL Post Shipping Label', 'israel-post-for-woocommerce'); ?>
									<i class="fa fa-long-arrow-right m-l-7" aria-hidden="true"></i>
								</span>
							</button>
							<span class="error_msg"></span>
						</div>
					</form>
				</div>
			</div>
	</div>
	<div class="il_post_popupclose"></div>
</div>