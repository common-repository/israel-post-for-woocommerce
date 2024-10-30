<?php
/**
 * HTML view file for Tracking Page Tab in settings page
 */
?>
<section id="il_content3" class="il_tab_section">
	<div class="il_tab_inner_container">
		<form method="post" id="il_tracking_page_form" action="" enctype="multipart/form-data">
			<?php #nonce?>					
			<table class="form-table heading-table">
				<tbody>
					<tr valign="top">
						<td>
							<h3 style=""><?php _e( 'Tracking Page Settings', 'israel-post-for-woocommerce' ); ?></h3>
						</td>
					</tr>
				</tbody>
			</table>
			<?php $this->get_html( $this->tracking_page_data() );?>	
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<td>
							<a href="<?php echo get_home_url(); ?>?action=preview_il_post_tracking_page" class="" target="_blank" style="line-height: 30px;"><?php _e( 'Click to preview the tracking page', 'israel-post-for-woocommerce' ); ?></a>
							<p class=""><?php _e( 'PLEASE NOTE - make sure to save your settings before preview.', 'israel-post-for-woocommerce' ); ?></p>
						</td>
					</tr>
					<tr valign="top">						
						<td class="button-column">
							<div class="submit">								
								<button name="save" class="button-primary btn_il_post il-post-save-settings" type="submit" value="Save changes"><?php _e( 'Save Changes', 'israel-post-for-woocommerce' ); ?></button>
								<div class="spinner"></div>	
								<div class="success_msg" style="display:none;"><?php _e( 'Data saved successfully.', 'israel-post-for-woocommerce' ); ?></div>
								<?php wp_nonce_field( 'il_tracking_page_form', 'il_tracking_page_form_nonce' );?>
								<input type="hidden" name="action" value="il_tracking_page_form_update">
							</div>	
						</td>
					</tr>
				</tbody>
			</table>				
		</form>
	</div>	
	<?php //include 'il_post_admin_sidebar.php';?>
</section>