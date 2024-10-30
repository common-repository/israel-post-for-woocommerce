<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_IL_Post_Settings {
	/**
	 * Initialize the main plugin function
	*/
    public function __construct() {			
			
	}
	
	/**
	 * Instance of this class.
	 *
	 * @var object Class Instance
	 */
	private static $instance;
	
	/**
	 * Get the class instance
	 *
	 * @return WC_Advanced_Shipment_Tracking_Admin
	*/
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
	
	/*
	* init from parent mail class
	*/
	public function init(){						
		
		//Hook on save general settings data
		add_action( 'wp_ajax_il_settings_form_update', array( $this, 'il_settings_form_update_fun' ) );
		
		//Hook on save API settings data
		add_action( 'wp_ajax_il_api_settings_form_update', array( $this, 'il_api_settings_form_update_fun' ) );
		
		//Hook on save From Address data
		add_action( 'wp_ajax_il_from_address_form_update', array( $this, 'il_from_address_form_update_fun' ) );
		
		//Hook on save Shipping Options Settings data
		add_action( 'wp_ajax_il_shipping_options_form_update', array( $this, 'il_shipping_options_form_update_fun' ) );		
		
		//Hook on save Shipping Options Settings data
		add_action( 'wp_ajax_il_post_save_package_dimension', array( $this, 'il_post_save_package_dimension' ) );
		
		add_action( 'wp_ajax_il_post_set_package_default', array( $this, 'il_post_set_package_default' ) );
		add_action( 'wp_ajax_il_post_remove_package_dimesion', array( $this, 'il_post_remove_package_dimesion' ) );
		
		//Hook on save Tracking page form data
		add_action( 'wp_ajax_il_tracking_page_form_update', array( $this, 'il_tracking_page_form_update_fun' ) );				

		// Ajax hook for test connection
		add_action( 'wp_ajax_il_post_test_connection', array( $this, 'il_post_test_connection_fun' ) );				
		
		// Hook for include css and js
		add_action( 'admin_enqueue_scripts', array( $this, 'il_post_enque_script') );				

		//Custom Woocomerce menu
		add_action('admin_menu', array( $this, 'register_woocommerce_menu' ), 99 );		
		
		// Hook for add admin body class in settings page
		add_filter( 'admin_body_class', array( $this, 'il_post_admin_body_class' ) );
	}
	
	/*
	* Admin Menu add function
	* WC sub menu
	*/
	public function register_woocommerce_menu() {
		add_submenu_page( 'woocommerce', __( 'Israel Post', 'israel-post-for-woocommerce' ), __( 'Israel Post', 'israel-post-for-woocommerce' ), 'manage_options', 'israel-post-for-woocommerce', array( $this, 'woo_il_post_page_callback' ) );
	}
	
	/*
	* Add class in admin settings page
	*/
	public function il_post_admin_body_class($classes){
		if( (isset($_GET['page']) && $_GET['page'] == 'israel-post-for-woocommerce') || (isset($_GET['page']) && $_GET['page'] == 'israel-post-for-woocommerce-docs')) {
			$classes .= 'il_post_admin_settings';
		}
        return $classes;
	}
	
	/*
	* callback for Israel Post page
	*/
	public function woo_il_post_page_callback(){ ?>	
		<div class="il-header-bg">
			<?php 
			if ( is_rtl() ) { ?>
				<img class="il-post-logo" src="<?php echo wc_il_post()->plugin_dir_url()?>assets/images/Israel_Post_Logo_HE.png ">
			<?php } else{ ?>
				<img class="il-post-logo" src="<?php echo wc_il_post()->plugin_dir_url()?>assets/images/Israel_Post_Logo.png">
			<?php } ?>
		</div>
        <div class="woocommerce il_post_admin_layout">
            <div class="il_post_admin_content" >					
				<input id="il_tab1" type="radio" name="tabs" class="il_tab_input" data-tab="account-settings" checked>
				<label for="il_tab1" class="il_tab_label first_label"><?php _e('Account settings', 'israel-post-for-woocommerce'); ?></label>
				
				<input id="il_tab5" type="radio" name="tabs" class="il_tab_input" data-tab="sender-address" <?php if(isset($_GET['tab']) && $_GET['tab'] == 'sender-address'){ echo 'checked'; } ?>>
				<label for="il_tab5" class="il_tab_label"><?php _e('Sender Address', 'israel-post-for-woocommerce'); ?></label>
				
				<input id="il_tab2" type="radio" name="tabs" class="il_tab_input" data-tab="shipping-options" <?php if(isset($_GET['tab']) && $_GET['tab'] == 'shipping-options'){ echo 'checked'; } ?>>
				<label for="il_tab2" class="il_tab_label"><?php _e('Shipping Options', 'israel-post-for-woocommerce'); ?></label>
				
				<input id="il_tab3" type="radio" name="tabs" class="il_tab_input" data-tab="tracking-page" <?php if(isset($_GET['tab']) && $_GET['tab'] == 'tracking-page'){ echo 'checked'; } ?>>
				<label for="il_tab3" class="il_tab_label"><?php _e('Tracking Page', 'israel-post-for-woocommerce'); ?></label>
								
				<a class="il_tab_label doc_label" target="blank" href="https://www.zorem.co.il/docs-israel-post-woocommerce-plugin/"><?php _e('Documentation', 'israel-post-for-woocommerce'); ?></a>
								
				<?php require_once( 'views/admin_options_settings.php' );?>	
				<?php require_once( 'views/admin_options_shipping_settings.php' );?>	
				<?php require_once( 'views/admin_options_sender_address.php' );?>	
				<?php require_once( 'views/admin_options_t_page_settings.php' );?>
				<?php require_once( 'views/admin_docs.php' );?>				
            </div>				
        </div> 
	<?php }	
	
	/**
	* Load admin styles.
	*/
	public function il_post_enque_script(){
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_register_style( 'il-post-select2-css',  wc_il_post()->plugin_dir_url() . 'assets/css/select2.min.css', array(), wc_il_post()->version );
		wp_register_style( 'il-post-wc-css',  wc_il_post()->plugin_dir_url() . 'assets/css/admin.css', array(), wc_il_post()->version );wp_register_script( 'il-post-select2-js', wc_il_post()->plugin_dir_url() . 'assets/js/select2.min.js', array( 'jquery', 'wp-util' ), wc_il_post()->version );	
		wp_register_script( 'il-post-wc-js', wc_il_post()->plugin_dir_url() . 'assets/js/admin.js', array( 'jquery', 'wp-util' ), wc_il_post()->version );
		wp_register_script( 'il-post-settings-js', wc_il_post()->plugin_dir_url() . 'assets/js/settings.js', array( 'jquery', 'wp-util' ), wc_il_post()->version );
		wp_register_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );
		wp_register_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.min.js', array( 'jquery' ), WC_VERSION, true );
		wp_register_script( 'jquery-blockui', WC()->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI' . $suffix . '.js', array( 'jquery' ), '2.70', true );
		
		if(!isset($_GET['page'])) {
			return;
		}
		if( $_GET['page'] == 'israel-post-for-woocommerce' || $_GET['page'] == 'israel-post-for-woocommerce-docs' ) {
			wp_enqueue_style( 'il-post-wc-css' );		
			wp_enqueue_style( 'woocommerce_admin_styles' );				
			wp_enqueue_script( 'jquery-tiptip' );		
			wp_enqueue_script( 'il-post-settings-js' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( 'jquery-blockui' );
			
			wp_localize_script( 'il-post-settings-js', 'ilpost_settings', array(
			'i18n' => array(
				'title' => __( 'Title', 'israel-post-for-woocommerce' ),
				'length' => __( 'Length', 'israel-post-for-woocommerce' ),
				'width' => __( 'Width', 'israel-post-for-woocommerce' ),
				'height' => __( 'Height', 'israel-post-for-woocommerce' ),
				'cancel' => __( 'Cancel', 'israel-post-for-woocommerce' ),
			),			
		) );
		}			
	}
	
	/*
	* get html of fields
	*/
	private function get_html( $arrays ){
				
		?>
		<table class="form-table">
			<tbody>
            	<?php foreach( (array)$arrays as $id => $array ){					
					if($array['type'] == 'title'){ ?>
                		<tr valign="top titlerow">
                        	<th colspan="2"><h3><?php echo $array['title']?></h3></th>
                        </tr>    	
                    <?php continue;}
					
					if( isset( $array['type'] ) && $array['type'] == 'dynamic_dropdown' ){ ?>
					<tr valign="top" class="<?php echo $array['class']; ?>">	
                        <th scope="row" class="titledesc"  colspan="2">
							<label for=""><?php echo $array['title'];
								if( isset($array['tooltip']) ){ ?>
									<span class="woocommerce-help-tip tipTip" title="<?php echo $array['tooltip']?>"></span>
								<?php } ?>
							</label>
							<fieldset>
								<?php echo $this->package_dimension_html(); ?>
								<div class="add_more_package_container">	
									<a href="javaScript:void(0);" class="add_more_package"><?php _e( 'Add new', 'israel-post-for-woocommerce'); ?><span class="dashicons dashicons-plus-alt"></span></a>
								</div>
								<button class="button-primary btn_il_post save_package" style="display:none;"><?php _e( 'Add', 'israel-post-for-woocommerce'); ?></button>
								<?php if(isset($array['desc']) && $array['desc'] != ''){ ?>
									<p class="description"><?php echo (isset($array['desc']))? $array['desc']: ''?></p>
								<?php } ?>	
							</fieldset>
						</th>						
					</tr>	
					<?php continue; }
					
					if($array['type'] == 'desc'){ ?>
                		<tr valign="top">
                        	<td colspan="2"><p><?php echo $array['title']?></p></td>
                        </tr>    	
                    <?php continue;}
					if($array['type'] == 'ip_desc'){ ?>
                		<tr valign="top">
                        	<td colspan="2">
							<p style="margin-bottom: 15px;"><?php echo $array['title']?></p>
							<label class="your_ip_label"><?php _e( 'Your IP', 'israel-post-for-woocommerce'); ?> - <?php echo $_SERVER['SERVER_ADDR']; ?></label>
							</td>
                        </tr>    	
                    <?php continue;}
					?>
				<tr valign="top" class="<?php echo $array['class']; ?>">
					<?php if($array['type'] != 'desc'){
						$required = (isset($array['required'])?$array['required']:"");
						?>										
					<th scope="row" class="titledesc"  >
						<label for=""><?php echo $array['title']?><?php if(isset($array['title_link'])){ echo $array['title_link']; } ?>
							<?php 
							if($required){ ?>
								<span class="required_star">*</span>
							<?php }
							if( isset($array['tooltip']) ){ ?>
                            	<span class="woocommerce-help-tip tipTip" title="<?php echo $array['tooltip']?>"></span>
                            <?php } ?>
                        </label>
					</th>
					<?php } ?>
					<td class="forminp"  <?php if($array['type'] == 'desc'){ ?> colspan=2 <?php } ?>>
                    	<?php if( $array['type'] == 'checkbox' ){
							
							if($id == 'ilpost_enable_logging'){
								$defalu_value = 1;
							} else{
								$defalu_value = 0;
							}	 
							
							if(get_option($id,$defalu_value)){
								$checked = 'checked';
							} else{
								$checked = '';
							} 							
							
							if(isset($array['disabled']) && $array['disabled'] == true){
								$disabled = 'disabled';
								$checked = '';
							} else{
								$disabled = '';
							}							
							?>
						<fieldset>	
							<input type="hidden" name="<?php echo $id?>" value="0"/>
							<input type="checkbox" id="<?php echo $id?>" name="<?php echo $id?>" class="mdl-switch__input" <?php echo $checked ?> value="1" <?php echo $disabled; ?>/>	
							<?php if(isset($array['desc']) && $array['desc'] != ''){ ?>
								<p class="description"><?php echo (isset($array['desc']))? $array['desc']: ''?></p>
							<?php } ?>									
						</fieldset>
                        <?php }  elseif( isset( $array['type'] ) && $array['type'] == 'dropdown' ){ ?>
                        	<?php
								if( isset($array['multiple']) ){
									$multiple = 'multiple';
									$field_id = $array['multiple'];
								} else {
									$multiple = '';
									$field_id = $id;
								}
							?>
                        	<fieldset>
								<select class="select select2" id="<?php echo $field_id?>" name="<?php echo $id?>" <?php echo $multiple;?>>    <?php foreach((array)$array['options'] as $key => $val ){?>
                                    	<?php
											$selected = '';
											if( isset($array['multiple']) ){
												if (in_array($key, (array)$this->data->$field_id ))$selected = 'selected';
											} else {
												if( get_option($id) == (string)$key )$selected = 'selected';
											}
                                        
										?>
										<option value="<?php echo $key?>" <?php echo $selected?> ><?php echo $val?></option>
                                    <?php } ?>
								</select>
								<?php if(isset($array['desc']) && $array['desc'] != ''){ ?>
									<p class="description"><?php echo (isset($array['desc']))? $array['desc']: ''?></p>
								<?php } ?>	
							</fieldset>
                        <?php }
						elseif( $array['type'] == 'button' ){ ?>
							<fieldset>
								<button class="button-primary btn_il_post  <?php echo $array['button_class'];?>"><?php echo $array['label'];?></button>
								<div class="connection_success">
									<span class="dashicons dashicons-yes"></span>
									<label><?php _e( 'Connection Successfull', 'israel-post-for-woocommerce'); ?></label>					
								</div>
								<div class="connection_fail">					
									<span class="dashicons dashicons-no"></span>
									<label><?php _e( 'Connection Failed', 'israel-post-for-woocommerce'); ?></label>
								</div>
								<?php if(isset($array['desc']) && $array['desc'] != ''){ ?>
									<p class="description"><?php echo (isset($array['desc']))? $array['desc']: ''?></p>
								<?php } ?>
							</fieldset>							
						<?php }					
						elseif( $array['type'] == 'password' ){ 
						$required = (isset($array['required'])?$array['required']:"");	
						?>
							<fieldset>
                                <input class="input-text regular-input <?php if($required){ echo 'validate-input'; } ?>" type="password" name="<?php echo $id?>" id="<?php echo $id?>" style="" value="<?php echo get_option($id)?>" placeholder="<?php if(!empty($array['placeholder'])){echo $array['placeholder'];} ?>">
								<?php if(isset($array['desc']) && $array['desc'] != ''){ ?>
									<p class="description"><?php echo (isset($array['desc']))? $array['desc']: ''?></p>
								<?php } ?>
                            </fieldset>	
						<?php }
						else { 								
							$default = (isset($array['default'])?$array['default']:"");
							$required = (isset($array['required'])?$array['required']:"");
							$maxlength = (isset($array['maxlength'])?$array['maxlength']:"");
						?>
                            <fieldset>
                                <input class="input-text regular-input <?php if($required){ echo 'validate-input'; } if($maxlength){ echo 'validate-length-input';}?>" type="text" <?php if($maxlength!=""){ echo 'data-max=' . $maxlength;}?> name="<?php echo $id?>" id="<?php echo $id?>" style="" value="<?php echo get_option($id,$default)?>" placeholder="<?php if(!empty($array['placeholder'])){echo $array['placeholder'];} ?>">
								<?php if(isset($array['desc']) && $array['desc'] != ''){ ?>
									<p class="description"><?php echo (isset($array['desc']))? $array['desc']: ''?></p>
								<?php } ?>
                            </fieldset>							
                        <?php } ?>
                        
					</td>
				</tr>						
	<?php  } ?>
			</tbody>
		</table>
	<?php }
	
	/*
	* Accounts Settings->General Settings fields array
	*/
	public function get_accounts_data() {
		$form_data = array(
			
			'ilpost_username' => array(
				'type'		=> 'text',
				'title'		=> __( 'Israel Post username', 'israel-post-for-woocommerce' ),								
				'class'     => '',
				'tooltip'      => __( 'Israel Post Account login Details', 'israel-post-for-woocommerce' ),
				'required'	=> true,
			),
			'ilpost_password' => array(
				'type'		=> 'password',
				'title'		=> __( 'Israel Post password', 'israel-post-for-woocommerce' ),								
				'class'     => '',
				'tooltip'   => __( 'Israel Post Account login Details', 'israel-post-for-woocommerce' ),
				'required'	=> true,
			),					
		);
		return $form_data;
	}
	
	/*
	* Account settings->API Settings fields array
	*/
	public function get_api_settings_data() {	
		$start_a_tag = '<a href="'.admin_url( 'admin.php?page=wc-status&tab=logs' ).'">';
		$end_a_tag = '</a>';
		$form_data = array(
			'ilpost_client_name' => array(
				'type'		=> 'text',
				'title'		=> __( 'Client Name', 'israel-post-for-woocommerce' ),				
				'class'     => 'remove_border',
				'tooltip'   => __( 'The Client Name is required for authentication and creates the tokens needed to ensure secure access to Israel Post API.', 'israel-post-for-woocommerce' ),
				'required'	=> true,
			),
			'ilpost_client_identity' => array(
				'type'		=> 'text',
				'title'		=> __( 'Client Identity', 'israel-post-for-woocommerce' ),								
				'class'     => 'remove_border',	
				'tooltip'   => __( 'The Client Identity is required for authentication and creates the tokens needed to ensure secure access to Israel Post API.', 'israel-post-for-woocommerce' ),				
				'required'	=> true,
			),
			
			'ilpost_desc' => array(
				'type'		=> 'ip_desc',
				'title'		=> sprintf(__('In order to obtain the API keys, you need to send email the Israel Post support (PostilAPISupport@malam.com) and provide your Israel Post account name and server IP address (%s) to whitelist it.', 'israel-post-for-woocommerce'), $_SERVER['SERVER_ADDR']),
				'class'     => '',				
			),
			'ilpost_sandbox_mode' => array(
				'type'		=> 'checkbox',
				'title'		=> __( 'Enable Sandbox Mode', 'israel-post-for-woocommerce' ),	
				'desc'		=> __( 'Please tick here if you want to test the plug-in installation against the Israel Post Sandbox Environment. Labels generated via Sandbox cannot be used for shipping and you need to enter your client ID and client secret for the Sandbox environment instead of the ones for production!', 'israel-post-for-woocommerce' ),					
				'class'     => '',				
			),
			'ilpost_test_connection' => array(
				'type'		=> 'button',
				'title'		=> __( 'Test Connection', 'israel-post-for-woocommerce' ),	
				'label'		=> __( 'Test Connection', 'israel-post-for-woocommerce' ),	
				'desc'		=> __( '<span class="rednotice">Please make sure to save any changed settings before testing the connection.</span><br>Press the test connection button to check the connection against the Israel Post API (depending on the selected environment, Sandbox or Production).', 'israel-post-for-woocommerce' ),					
				'class'     => 'test_connection_button',
				'button_class' => '',	
			),			
			'ilpost_enable_logging' => array(
				'type'		=> 'checkbox',
				'title'		=> __( 'Debug Log', 'israel-post-for-woocommerce' ),	
				'desc'		=> sprintf(__('A log file containing the communication to the Israel Post server will be maintained if this option is checked. This can be used in case of technical issues and can be found %shere%s.', 'israel-post-for-woocommerce'),  $start_a_tag, $end_a_tag),
				__( 'A log file containing the communication to the Israel Post server will be maintained if this option is checked. This can be used in case of technical issues and can be found here', 'israel-post-for-woocommerce' ),					
				'class'     => '',				
			),			
		);
		return $form_data;
	}
	
	/*
	* Default Shipping Options->From Address fields array
	*/
	public function get_from_address_data() {
		$form_data = array(
			'sender_company' => array(
				'type'		=> 'text',
				'title'		=> __( 'Company', 'woocommerce' ),
				'required'	=> true,
				'class'     => '',
			),
			'sender_name' => array(
				'type'		=> 'text',
				'title'		=> __( 'Name', 'woocommerce' ),								
				'required'	=> true,
				'class'     => '',				
			),
			'sender_email' => array(
				'type'		=> 'text',
				'title'		=> __( 'Email address', 'israel-post-for-woocommerce' ),								
				'required'	=> true,
				'class'     => '',				
			),
			'sender_phone' => array(
				'type'		=> 'text',
				'title'		=> __( 'Phone', 'woocommerce' ),				
				'required'	=> true,
				'class'     => '',				
			),
			'use_sender_phone_as_recepient_phone' => array(
				'type'		=> 'checkbox',
				'title'		=> __( 'Use sender phone as customer phone in case customer phone is empty', 'israel-post-for-woocommerce' ),	
				'desc'		=> '',					
				'class'     => '',				
			),
			'sender_address' => array(
				'type'		=> 'text',
				'title'		=> __( 'Address line 1', 'woocommerce' ),								
				'required'	=> true,
				'class'     => '',		
				'default'	=> get_option('woocommerce_store_address'),				
			),
			'sender_address2' => array(
				'type'		=> 'text',
				'title'		=> __( 'Address line 2', 'woocommerce' ),								
				'required'	=> false,
				'class'     => '',				
				'default'	=> get_option('woocommerce_store_address_2'),	
			),
			'sender_city' => array(
				'type'		=> 'text',
				'title'		=> __( 'City', 'woocommerce' ),								
				'required'	=> true,
				'class'     => '',				
				'default'	=> get_option('woocommerce_store_city'),
			),
			'sender_country' => array(
				'type'		=> 'dropdown',
				'title'		=> __( 'Country', 'woocommerce' ),								
				'required'	=> true,
				'class'     => '',				
				'options' => array(					
					'IL'    => __( 'Israel', 'israel-post-for-woocommerce' ),					
				),
			),
			'sender_postal_code' => array(
				'type'		=> 'text',
				'title'		=> __( 'Postcode / ZIP', 'woocommerce' ),								
				'required'	=> true,
				'class'     => '',				
				'default'	=> get_option('woocommerce_store_postcode'),
			),			
		);
		return $form_data;
	}
	
	/*
	* Default Shipping Options->Shipping Options Settings fields array
	*/
	public function get_shipping_options_data() {
		$form_data = array(
			'ilpost_shipping_service' => array(
				'type'		=> 'dropdown',
				'title'		=> __( 'Default shipping service', 'israel-post-for-woocommerce' ),
				'options' => array(	
					''    => __( 'Select', 'woocommerce' ),				
					'1'   => __( 'Parcel CP', '' ),
					'2'   => __( 'Registered Mail RY', '' ),
					'4'   => __( 'EMS', '' ),
					'6'   => __( 'Eco post', '' ),
					'7'   => __( 'EMS platinum Europe', '' ),
					'8'   => __( 'EMS platinum USA', '' ),
					'9'   => __( 'EMS platinum USA economy', '' ),
					'365' => __( 'EMS documents', '' ),
					'370' => __( 'Small parcels', '' ),
					'389' => __('UA – Regular air mail',''),
				),	
				'class'     => '',
			),
			'ilpost_label_format' => array(
				'type'		=> 'dropdown',
				'title'		=> __( 'Label Format', 'israel-post-for-woocommerce' ),
				'options' => array(					
					'5'    => __( 'PNG', '' ),
					'4'    => __( 'PNG in zip file', '' ),
					'1'    => __( 'PDF', '' ),
					'6'    => __( 'Zip of PDF files', '' ),
					'7'    => __( 'PDF Citizen printer', '' ),
					'8'    => __( 'PDF Dymo printer', '' )
				),
				'class'     => '',
			),	
			'ilpost_precedure_type' => array(
				'type'		=> 'dropdown',
				'title'		=> __( 'Customs Procedure Type', 'israel-post-for-woocommerce' ),
				'options' => array(
					''    => __( 'Select', 'woocommerce' ),
					'2'   => __( 'Regular', '' ),
					'1'    => __( 'Fiscal', '' )
					
				),
				'class'     => '',
			),	
			'ilpost_custom_declaration_id' => array(
				'type'		=> 'dropdown',
				'title'		=> __( 'Customs Declaration', 'israel-post-for-woocommerce' ),
				'options' => array(
					''    => __( 'Select', 'woocommerce' ),
					'Merchandise' => __( 'Merchandise', '' ),
					'Document' => __( 'Document', '' ),
					'Gift' => __( 'Gift', '' ),
					'Returned Goods' => __( 'Returned Goods', '' ),
					'Sample' => __( 'Sample', '' ),
				),
				'class'     => '',
			),
			'ilpost_insurance_type_id' => array(
				'type'		=> 'dropdown',
				'title'		=> __( 'Insurance Type', 'israel-post-for-woocommerce' ),
				'options' => array(
					''  => __( 'Select', 'woocommerce' ),
					'0' => __( 'Nothing', '' ),
					'1' => __( 'Not Covering Jewelry', '' ),
					'2' => __( 'Covering Jewelry', '' ),					
				),
				'class'     => '',
			),
			'ilpost_export_entry' => array(
				'type'		=> 'dropdown',
				'title'		=> __( 'Export Entry Document', 'israel-post-for-woocommerce' ),
				'options' => array(
					''    => __( 'Select', 'woocommerce' ),
					'true'    => __( 'Yes', 'woocommerce' ),
					'false'   => __( 'No', 'woocommerce' )
				),
				'class'     => '',
			),
			
			'ilpost_package_material_weight' => array(
				'type'		=> 'text',
				'title'		=> __( 'Default Package Material Weight', 'israel-post-for-woocommerce' ),				
				'class'     => '',
			),			
			
			'ilpost_use_usps' => array(
				'type'		=> 'dropdown',
				'title'		=> __( 'Use USPS if destination country is USA', 'israel-post-for-woocommerce' ),
				'options' => array(
					''    => __( 'Select', 'woocommerce' ),
					'true'    => __( 'Yes', 'woocommerce' ),
					'false'   => __( 'No', 'woocommerce' )
				),
				'class'     => '',
			),
			
			'ilpost_ioss_number' => array(
				'type'		=> 'text',
				'title'		=> __( 'IOSS Number', 'israel-post-for-woocommerce' ),		
				'maxlength'=> '12',	
				'desc'		=> __( "IOSS can't be greater than 12 Characters.", 'israel-post-for-woocommerce' ),					
				'class'     => '',
			),			
			
			'ilpost_Sender_From_Palestinian_Authority' => array(
				'type'		=> 'dropdown',
				'title'		=> __( 'Sender From Palestinian Authority', 'israel-post-for-woocommerce' ),
				'options' => array(
					'0' => __( 'None', '' ),
					'70' => __( 'Palestinian Authority - West bank', '' ),
					'80' => __( 'Palestinian Authority - Gaza', '' ),					
				),
				'class'     => '',
			),
			
			'ilpost_Document1 Type' => array(
				'type'		=> 'dropdown',
				'title'		=> __( 'Document Type', 'israel-post-for-woocommerce' ),
				'options' => array(
					'0' => __( 'None', '' ),
					'503' => __( 'Permits/Licenses', '' ),
					'514' => __( 'EUR1', '' ),
					'600' => __( 'EXPORT DECLARATION', '' ),					
					'999' => __( 'Other', '' ),	
				),
				'class'     => '',
			),

			'ilpost_Document2 Type' => array(
				'type'		=> 'dropdown',
				'title'		=> __( 'Document Type', 'israel-post-for-woocommerce' ),
				'options' => array(
					'0' => __( 'None', '' ),
					'503' => __( 'Permits/Licenses', '' ),
					'514' => __( 'EUR1', '' ),
					'600' => __( 'EXPORT DECLARATION', '' ),					
					'999' => __( 'Other', '' ),	
				),
				'class'     => '',
			),

			'ilpost_Document3 Type' => array(
				'type'		=> 'dropdown',
				'title'		=> __( 'Document Type', 'israel-post-for-woocommerce' ),
				'options' => array(
					'0' => __( 'None', '' ),
					'503' => __( 'Permits/Licenses', '' ),
					'514' => __( 'EUR1', '' ),
					'600' => __( 'EXPORT DECLARATION', '' ),					
					'999' => __( 'Other', '' ),	
				),
				'class'     => '',
			),
			'package_dimension' => array(
				'type'		=> 'dynamic_dropdown',
				'title'		=> __( 'Package dimension template', 'israel-post-for-woocommerce' ),
				'options' => array(
					''    => __( 'Select', 'woocommerce' ),					
				),
				'class'     => '',
			),
		);
		return $form_data;
	}
	
	/*
	* Tracking Page->Tracking Page Settings fields array
	*/
	public function tracking_page_data() {
		$page_list = wp_list_pluck( get_pages(), 'post_title', 'ID' );	
		$form_data = array(			
			'il_post_tracking_page_type' => array(
				'type'		=> 'dropdown',
				'title'		=> __( 'Which tracking page you want to use?', 'israel-post-for-woocommerce' ),
				'options' => array(
					''    => __( 'Select', 'woocommerce' ),
					'carrier'=> __( 'Tracking on Israel Post website', 'israel-post-for-woocommerce' ),
					'app'    => __( 'Tracking page on your store', 'israel-post-for-woocommerce' ),					
				),
				'class'     => '',
			),
			'il_post_tracking_page' => array(
				'type'		=> 'dropdown',
				'title'		=> __( 'Select tracking page', 'israel-post-for-woocommerce' ),
				'options'   => $page_list,			
				'class'     => 'select_tracking_page',
			),
			'il_post_tracking_page_desc' => array(
				'type'		=> 'desc',
				'title'		=> __( 'In case you choose a different page than the default "Shipment Tracking" page, make sure to add the [il-post-track-order] shortcode to the selected page.', 'israel-post-for-woocommerce' ),
				'class'     => '',					
			),
			'tracking_page_theme_color' => array(
				'type'		=> 'text',
				'title'		=> __( 'Tracking Page Theme Color', 'israel-post-for-woocommerce' ),				
				'class'     => 'color_field',
			),			
		);
		return $form_data;
	}
	
	/*
	* Account settings->General Settings save function
	*/
	public function il_settings_form_update_fun(){
		if ( ! empty( $_POST ) && check_admin_referer( 'il_settings_form', 'il_settings_form_nonce' ) ) {
			
			$data = $this->get_accounts_data();					
			
			foreach( $data as $key => $val ){
				if(isset($_POST[ $key ])){	
					update_option( $key, sanitize_text_field( $_POST[ $key ] ) );
				}
			}
			echo json_encode( array('success' => 'true') );die();

		}
	}
	
	/*
	* Account settings->API Settings save function
	*/
	public function il_api_settings_form_update_fun(){
		if ( ! empty( $_POST ) && check_admin_referer( 'il_api_settings_form', 'il_api_settings_form_nonce' ) ) {
			
			$data = $this->get_api_settings_data();					
			
			foreach( $data as $key => $val ){
				if(isset($_POST[ $key ])){
					update_option( $key,$_POST[ $key ]);
				}
			}
			
			$clientname = get_option('ilpost_client_name');
			$clientidentity = get_option('ilpost_client_identity');
			$auth_user = get_option('ilpost_username');
			$auth_password = get_option('ilpost_password');
			$ilpost_enable_logging = get_option('ilpost_enable_logging',1);	
			$ilpost_sandbox_mode = get_option('ilpost_sandbox_mode',0);	
			
			if( $ilpost_sandbox_mode == 1){				
				$url = 'https://is.israelpost.co.il:8090/core/connect/token';
			}
			else{
				$url = 'https://is.israelpost.co.il/core/connect/token';
			}
			
		
			$args['body'] = 'grant_type=password&username='.$auth_user.'&password='.$auth_password.'&scope=read+write';
	
			$args['headers'] = array(
				'Authorization' => 'Basic ' . base64_encode( $clientname . ':' . $clientidentity ),				
			);	
			
			$args['timeout'] = 20;
			
			try {
				$response = wp_remote_post( $url, $args );				
				if ( is_wp_error( $response ) ) {					
					
					update_option('il_post_access_token', '');
					update_option('il_post_access_token_timestamp', time());
						
					if($ilpost_enable_logging == 1){						
						$logger = wc_get_logger();
						$context = array( 'source' => 'Il_Post_apicall_error' );				
						$logger->error( "\nError: ".$response->get_error_message(), $context );
					}						
				} else{	
					if(isset($response['response']['code']) && $response['response']['code'] == 200){
						$body = json_decode($response['body']);
						$access_token = $body->access_token;			
						
						update_option('il_post_access_token', $access_token);
						update_option('il_post_access_token_timestamp', time());
						
					} else{
						
						$body = json_decode($response['body']);
						
						if(isset($body->error_description)){
							$error_description = $body->error_description;
						} else{
							$error_description = $body->error;
						}
						
						update_option('il_post_access_token', '');
						update_option('il_post_access_token_timestamp', time());
						
						if($ilpost_enable_logging == 1){						
							$logger = wc_get_logger();
							$context = array( 'source' => 'Il_Post_apicall_error' );
							$logger->error( "\nError: ".$body->error."\nDescription: " .$error_description, $context );
						}						
					}
				}
			}  catch (Exception $e) {
				
				update_option('il_post_access_token', '');
				update_option('il_post_access_token_timestamp', time());
				
				if($ilpost_enable_logging == 1){						
					$logger = wc_get_logger();
					$context = array( 'source' => 'Il_Post_apicall_error' );
					$logger->error( "\nError: ".$e->getMessage(), $context );
				}					
			}
			
			echo json_encode( array('success' => 'true') );die();

		}
	} 
	
	/*
	* Default Shipping Options->From Address save function
	*/
	public function il_from_address_form_update_fun(){
		if ( ! empty( $_POST ) && check_admin_referer( 'il_from_address_form_', 'il_from_address_form_' ) ) {
			
			$data = $this->get_from_address_data();					
			
			foreach( $data as $key => $val ){
				update_option( $key, sanitize_text_field( $_POST[ $key ] ) );
			}
			echo json_encode( array('success' => 'true') );die();

		}
	}
	
	/*
	* Default Shipping Options->Shipping Options Settings save function
	*/
	public function il_shipping_options_form_update_fun(){
		if ( ! empty( $_POST ) && check_admin_referer( 'il_shipping_options_form', 'il_shipping_options_form_nonce' ) ) {
			
			$data = $this->get_shipping_options_data();					
			
			foreach( $data as $key => $val ){
				if( isset($_POST[ $key ]) ){
					update_option( $key, sanitize_text_field( $_POST[ $key ] ) );
				}
			}
			echo json_encode( array('success' => 'true') );die();

		}
	}
	
	/*
	* Save Package dimension from setings
	*/
	public function il_post_save_package_dimension(){
		//$package_input = unserialize($_POST['package_input']);
		$package_details = array();
		parse_str($_POST['package_input'], $package_details);
		$package_dimension_details = get_option('il_post_package_dimension_details');
		if(!empty($package_dimension_details)){
			$updated_package_details = array_merge($package_dimension_details,$package_details['package_details']);
		} else{
			$updated_package_details = $package_details['package_details'];
		}
		update_option('il_post_package_dimension_details',$updated_package_details);
		
		ob_start();	 
		$this->package_dimension_html();
		$html = ob_get_clean();	
		echo $html;exit;
	}
	
	public function package_dimension_html(){ ?>
		<div class="dimension_div">
		<?php $package_dimension_details = get_option('il_post_package_dimension_details');		
		if(!empty($package_dimension_details)){
		?>
			<table class="wp-list-table widefat dimension_table">
				<thead>
					<tr>
						<th style="width:30px;"><?php _e( 'Default', 'israel-post-for-woocommerce'); ?></th>
						<th><?php _e( 'Title', 'israel-post-for-woocommerce'); ?></th>
						<th><?php _e( 'Dimension', 'israel-post-for-woocommerce'); ?></th>
						<th><?php _e( 'Action', 'israel-post-for-woocommerce'); ?></th>
					</tr>												
				</thead>
				<tbody>
					<?php
					$default_key = get_option('set_package_default_key','');
					foreach((array)$package_dimension_details as $key=>$details){ ?>
					<tr>
						<td><input class="set_package_default" type="checkbox" <?php if($details['package_dimension_key'] == $default_key){ echo 'checked'; } ?> data-key="<?php echo $details['package_dimension_key'];?>"></td>
						<td><?php echo $details['package_dimension_title'];?></td>
						<td><?php echo $details['package_dimension_length'];?> x <?php echo $details['package_dimension_width'];?> x <?php echo $details['package_dimension_height'];?> (cm)</td>
						<td><span class="dashicons dashicons-no-alt remove_package_dimesion" data-key="<?php echo $details['package_dimension_key'];?>"></span></td>
					</tr>
					<?php } ?>
				</tbody>	
			</table>
		<?php } ?>
		</div> <?php
	}
	
	/*
	* Set Package dimension default
	*/
	public function il_post_set_package_default(){
		$key = wc_clean($_POST['key']);
		update_option('set_package_default_key',$key);
		echo 1;exit;
	}
	
	/*
	* Remove Package dimension
	*/
	public function il_post_remove_package_dimesion(){
		$package_dimension_details = get_option('il_post_package_dimension_details');	
		$package_key = wc_clean($_POST['key']);
		foreach($package_dimension_details as $key=>$details){
			if($details['package_dimension_key'] == $package_key){
				unset($package_dimension_details[$key]);
			}
		}
		update_option('il_post_package_dimension_details',$package_dimension_details);
		ob_start();	 
		$this->package_dimension_html();
		$html = ob_get_clean();	
		echo $html;exit;
	}
	
	/*
	* Tracking Page->Tracking Page Settings save function
	*/	
	public function il_tracking_page_form_update_fun(){
		if ( ! empty( $_POST ) && check_admin_referer( 'il_tracking_page_form', 'il_tracking_page_form_nonce' ) ) {
			
			$data = $this->tracking_page_data();					
			
			foreach( $data as $key => $val ){
				update_option( $key, sanitize_text_field( $_POST[ $key ] ) );
			}
			echo json_encode( array('success' => 'true') );die();

		}
	}				
	
	/* test connection functionality
	 *
	 * @since 1.0
	 * @version 1.0
	 *	 
	 */
	public function il_post_test_connection_fun(){
		
		$clientname = wc_clean($_POST['ilpost_client_name']);
		$clientidentity = wc_clean($_POST['ilpost_client_identity']);
		$auth_user = wc_clean($_POST['ilpost_username']);
		$auth_password = wc_clean($_POST['ilpost_password']);
		$ilpost_enable_logging = wc_clean($_POST['ilpost_enable_logging']);
		$ilpost_sandbox_mode = wc_clean($_POST['ilpost_sandbox_mode']);
		
		if($ilpost_sandbox_mode == 'true'){				
			$url = 'https://is.israelpost.co.il:8090/core/connect/token';
		} else{
			$url = 'https://is.israelpost.co.il/core/connect/token';
		}
				
		$args['body'] = 'grant_type=password&username='.$auth_user.'&password='.$auth_password.'&scope=read+write';
	
		$args['headers'] = array(
			'Authorization' => 'Basic ' . base64_encode( $clientname . ':' . $clientidentity ),				
		);	
		
		$args['timeout'] = 20;
		
		try {
			$response = wp_remote_post( $url, $args );
					
			if ( is_wp_error( $response ) ) {
				
				update_option('il_post_access_token', '');
				update_option('il_post_access_token_timestamp', time());				
				
				if($ilpost_enable_logging == 'true'){						
					$logger = wc_get_logger();
					$context = array( 'source' => 'Il_Post_apicall_error' );				
					$logger->error( "\nError: ".$response->get_error_message(), $context );
				}	
				echo json_encode( array('test' => 'fail','message' => __( 'Connection fail, Please try again', 'israel-post-for-woocommerce' )) );exit;
			} else{							
				if($response['response']['code'] == 200){			
					$body = json_decode($response['body']);
					$access_token = $body->access_token;			
					update_option('il_post_access_token', $access_token);
					update_option('il_post_access_token_timestamp', time());
					echo json_encode( array('test' => 'success','message' => __( 'Connection Successfull', 'israel-post-for-woocommerce' )) );exit;	
				} else{
					
					update_option('il_post_access_token', '');
					update_option('il_post_access_token_timestamp', time());
				
					$body = json_decode($response['body']);
					if(isset($body->error_description)){
						$error_description = $body->error_description;
					} else{
						$error_description = $body->error;
					}
					
					if($ilpost_enable_logging == 'true'){						
						$logger = wc_get_logger();
						$context = array( 'source' => 'Il_Post_apicall_error' );
						$logger->error( "\nError: ".$body->error."\nDescription: " .$error_description, $context );
					}						
					echo json_encode( array('test' => 'fail','message' => __( $error_description, 'israel-post-for-woocommerce' )) );exit;	
				}	
			}			
		} catch (Exception $e) {
			
			update_option('il_post_access_token', '');
			update_option('il_post_access_token_timestamp', time());
				
			if($ilpost_enable_logging == 'true'){					
				$logger = wc_get_logger();
				$context = array( 'source' => 'Il_Post_apicall_error' );
				$logger->error( "\nError: ".$e->getMessage(), $context );
			}	
			echo json_encode( array('test' => 'fail','message' => __( $e->getMessage(), 'israel-post-for-woocommerce' )) );exit;
		}				
	}
}