<?php
/**
 * HTML view file for Default Shipping Options Tab in settings page
 */
?>
<section id="il_content2" class="il_tab_section">
	<div class="il_tab_inner_container">				
		<form method="post" id="il_shipping_options_form" action="" enctype="multipart/form-data">
			<table class="form-table heading-table">
				<tbody>
					<tr valign="top">
						<td>
							<h3 style=""><?php _e( 'Shipping Options Settings', 'israel-post-for-woocommerce' ); ?></h3>
						</td>
					</tr>
				</tbody>
			</table>
			<?php $this->get_html( $this->get_shipping_options_data() );?>	
			<table class="form-table">
				<tbody>
					<tr valign="top">						
						<td class="button-column">
							<div class="submit">								
								<button name="save" class="button-primary btn_il_post il-post-save-settings" type="submit" value="Save changes"><?php _e( 'Save Changes', 'israel-post-for-woocommerce' ); ?></button>
								<div class="spinner"></div>	
								<div class="success_msg" style="display:none;"><?php _e( 'Data saved successfully.', 'israel-post-for-woocommerce' ); ?></div>								
								<?php wp_nonce_field( 'il_shipping_options_form', 'il_shipping_options_form_nonce' );?>
								<input type="hidden" name="action" value="il_shipping_options_form_update">
							</div>	
						</td>
					</tr>
				</tbody>
			</table>
		</form>		
	</div>	
	<?php //include 'il_post_admin_sidebar.php';?>
</section>