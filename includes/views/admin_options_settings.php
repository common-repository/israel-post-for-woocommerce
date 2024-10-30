<?php
/**
 * HTML view file for Account settings Tab in settings page
 */
?>
<section id="il_content1" class="il_tab_section">
	<div class="il_tab_inner_container">					
		<form method="post" id="il_settings_form" action="" enctype="multipart/form-data">				
			<table class="form-table heading-table">
				<tbody>
					<tr valign="top">
						<td>
							<h3 style=""><?php _e( 'Israel Post Account', 'israel-post-for-woocommerce' ); ?></h3>
						</td>
					</tr>
				</tbody>
			</table>
			<?php $this->get_html( $this->get_accounts_data() );?>	
			<table class="form-table">
				<tbody>
					<tr valign="top">						
						<td class="button-column">
							<div class="submit">								
								<button name="save" class="button-primary btn_il_post il-post-save-settings" type="submit" value="Save changes"><?php _e( 'Save Changes', 'israel-post-for-woocommerce' ); ?></button>
								<div class="spinner"></div>
								<div class="success_msg" style="display:none;"><?php _e( 'Data saved successfully.', 'israel-post-for-woocommerce' ); ?></div>
								<?php wp_nonce_field( 'il_settings_form', 'il_settings_form_nonce' );?>
								<input type="hidden" name="action" class="form_action" value="il_settings_form_update">
							</div>	
						</td>
					</tr>
				</tbody>
			</table>
		</form>
		<form method="post" id="il_api_settings_form" action="" enctype="multipart/form-data">	
			<table class="form-table heading-table">
				<tbody>
					<tr valign="top">
						<td>
							<h3 style=""><?php _e( 'API Settings', 'israel-post-for-woocommerce' ); ?></h3>
						</td>
					</tr>
				</tbody>
			</table>
			<?php $this->get_html( $this->get_api_settings_data() );?>	
			<table class="form-table">
				<tbody>
					<tr valign="top">						
						<td class="button-column">
							<div class="submit">								
								<button name="save" class="button-primary btn_il_post il-post-save-settings" type="submit" value="Save changes"><?php _e( 'Save Changes', 'israel-post-for-woocommerce' ); ?></button>
								<div class="spinner"></div>		
								<div class="success_msg" style="display:none;"><?php _e( 'Data saved successfully.', 'israel-post-for-woocommerce' ); ?></div>								
								<?php wp_nonce_field( 'il_api_settings_form', 'il_api_settings_form_nonce' );?>
								<input type="hidden" name="action" value="il_api_settings_form_update">
							</div>	
						</td>
					</tr>
				</tbody>
			</table>									
		</form>
	</div>	
	<?php //include 'il_post_admin_sidebar.php';?>
</section>