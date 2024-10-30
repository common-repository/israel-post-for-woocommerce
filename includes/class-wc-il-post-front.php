<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_IL_Post_Front {
	
	/**
	 * Instance of this class.
	 *
	 * @var object Class Instance
	 */
	private static $instance;
	
	/**
	 * Initialize the main plugin function
	*/
    public function __construct() {						
		$this->init();	
    }
	
	/**
	 * Get the class instance
	 *
	 * @return WC_Advanced_Shipment_Tracking_Actions
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
	
		add_shortcode( 'il-post-track-order', array( $this, 'il_post_track_order_function') );
		
		add_action( 'wp_enqueue_scripts', array( $this, 'front_styles' ));	
		
		add_action( 'template_redirect', array( $this, 'preview_il_post_tracking_page_fun') );
		//add_action( 'wp_ajax_nopriv_preview_il_post_tracking_page', array( $this, 'preview_il_post_tracking_page_fun') );
	}
	
	/*
	* Load Front JS and CSS
	*/
	public function front_styles(){		
		wp_register_script( 'il-post-front-js', wc_il_post()->plugin_dir_url().'assets/js/front.js', array( 'jquery' ), wc_il_post()->version );
		wp_localize_script( 'il-post-front-js', 'il_post_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		wp_register_style( 'il-post-front-css',  wc_il_post()->plugin_dir_url() . 'assets/css/front.css', array(), wc_il_post()->version );
	}
	
	/*
	* Main Shortcode function - [il-post-track-order]
	*/
	public function il_post_track_order_function(){
		wp_enqueue_style( 'il-post-front-css');		
		wp_enqueue_script( 'il-post-front-js' ); 
		$tracking_number = wc_clean($_GET['tracking_number']);
		
		$order_id = wc_clean($_GET['order_id']);
		$in_transit_array = array('B','C','D','E','F','G','H','J','K');
		if($tracking_number){
			$clientname = get_option('ilpost_client_name');
			$clientidentity = get_option('ilpost_client_identity');
			$auth_user = get_option('ilpost_username');
			$auth_password = get_option('ilpost_password');
			$ilpost_enable_logging = get_option('ilpost_enable_logging',1);	
			$ilpost_sandbox_mode = get_option('ilpost_sandbox_mode',0);	
			$il_post_access_token_timestamp = get_option('il_post_access_token_timestamp');	
			
			// Formulate the Difference between two dates 
			$diff = round(abs(time() - $il_post_access_token_timestamp) / 60, 2);		
			
			if($diff > 50){
				if($ilpost_sandbox_mode == 1){				
					$url = 'https://is.israelpost.co.il:8090/core/connect/token';
				}
				else{
					$url = 'https://is.israelpost.co.il/core/connect/token';
				}
				
				
				$args['body'] = 'grant_type=password&username='.$auth_user.'&password='.$auth_password.'&scope=read+write';
			
				$args['headers'] = array(
					'Authorization' => 'Basic ' . base64_encode( $clientname . ':' . $clientidentity ),				
				);	
				
				try {
					$response = wp_remote_post( $url, $args );
					
					if($response['response']['code'] == 200){
						$body = json_decode($response['body']);
						$access_token = $body->access_token;			
						update_option('il_post_access_token', $access_token);
						update_option('il_post_access_token_timestamp', time());						
					} else{
						if($ilpost_enable_logging == 1){	
							$body = json_decode($response['body']);
							$code = $response['response']['code']; 
							$logger = wc_get_logger();
							$context = array( 'source' => 'Il_Post_apicall_error' );
							$logger->error( "\nError code: ".$code."\nError: ".$body->error."\nDescription: " .$body->error_description, $context );
						}									
					}
				}  catch (Exception $e) {
					if($ilpost_enable_logging == 1){						
						$logger = wc_get_logger();
						$context = array( 'source' => 'Il_Post_apicall_error' );
						$logger->error( "\nError: ".$body->error, $context );
					}					
				}			
			}
			
			$access_token = get_option('il_post_access_token');
			
			if($ilpost_sandbox_mode == 1){				
				$ILPOST_endpoint = 'https://testngw.israelpost.co.il:9444';
			}
			else{
				$ILPOST_endpoint = 'https://ngw.israelpost.co.il:1444';
			}
			
			$url = $ILPOST_endpoint."/Tevel.External.Shipments.WebApi/api/Tracking?TrackingNumber=".$tracking_number;
			
			$args['headers'] = array(
				'Authorization' => 'Bearer '.$access_token,				
			);	
			
			try {
				$response = wp_remote_get( $url, $args );
				
				if ( is_wp_error( $response ) ) {
					if($ilpost_enable_logging == 1){						
						$logger = wc_get_logger();
						$context = array( 'source' => 'Il_Post_apicall_error' );
						$logger->error( "\nError: ".$response->get_error_message(), $context );
					}
					return;
				} else{
					if(isset($response['body']) && is_array($response)){		
						$body = json_decode($response['body']);
					} else{
						return;	
					}		
				}							
			} catch (Exception $e) {
				if($ilpost_enable_logging == 1){						
					$logger = wc_get_logger();
					$context = array( 'source' => 'Il_Post_apicall_error' );
					$logger->error( "\nError: ".$e->getMessage(), $context );
				}					
			}

			//echo '<pre>';print_r($response);echo '</pre>';exit;

			$event_array = $body->Result;
			$lastevent_array = $event_array[0];
			
			
			$status = false;
			if( $lastevent_array->EventCharCode == 'I' ){
				$status = 'Delivered';
			} else if( $lastevent_array->EventCharCode == 'F' ){
				$status = 'Out For Delivery';
			} else if( $lastevent_array->EventCharCode == 'A' ){
				$status = 'Label Printed';
			} else if( in_array( $lastevent_array->EventCharCode, $in_transit_array) ){
				$status = 'In Transit';
			}	

			$theme_color = get_option('tracking_page_theme_color');
			if($theme_color){ ?>
				<style>
					.tracking-section .content2-header1 p{
						color: <?php echo $theme_color; ?>;
					}
					.tracking-section .pr_header{
						color: <?php echo $theme_color; ?>;
					}
				</style>
			<?php }
		}		
		?>
		<div class="tracking-section" style="min-height: auto;">
			<div class="">
				<div class="">
					<div class="">
						<div class="">							
							<span class="contact100-form-title logo-header">
								<img src="<?php echo wc_il_post()->plugin_dir_url()?>assets/images/ilpost-logo-transparent.png"><br>
							</span>							
						</div>
						<div class="">							
							<div class="content2">
								<div class="content2-header1">
									<p><?php _e( 'Order', 'woocommerce'); ?> #<?php echo $order_id; ?> is <span><?php _e( $status, 'woocommerce'); ?></span></p>
								</div>
								<div class="content2-header1">
									<p><?php _e( 'Tracking Number', 'israel-post-for-woocommerce'); ?> : <span><?php echo $tracking_number; ?></span></p>
								</div>
																
								<div class="clear"></div>
							</div>
							<div class="content3">
								<div class="shipment">									
									<div class="quality <?php if($status == 'Delivered' || $status == 'Label Printed' || $status == 'Out For Delivery' || $status == 'In Transit'){ echo 'status_done'; }?>">
										<div class="imgcircle">
											<img src="<?php echo wc_il_post()->plugin_dir_url()?>assets/images/quality.png" alt="quality check">
										</div>
										<span class="line <?php if($status == 'Delivered' || $status == 'Out For Delivery' || $status == 'In Transit'){ echo 'line_done'; }?>"></span>
										<p><?php _e( 'Label Printed', 'woocommerce'); ?></p>
									</div>
									<div class="in_transit <?php if($status == 'Delivered' || $status == 'Out For Delivery' || $status == 'In Transit'){ echo 'status_done'; }?>">
										<div class="imgcircle">
											<img src="<?php echo wc_il_post()->plugin_dir_url()?>assets/images/process.png" alt="dispatch product">
										</div>
										<span class="line <?php if($status == 'Delivered' || $status == 'Out For Delivery'){ echo 'line_done'; }?>"></span>
										<p><?php _e( 'In Transit', 'woocommerce'); ?></p>
									</div>	
									<div class="dispatch <?php if($status == 'Delivered' || $status == 'Out For Delivery'){ echo 'status_done'; }?>">
										<div class="imgcircle">
											<img src="<?php echo wc_il_post()->plugin_dir_url()?>assets/images/dispatch.png" alt="dispatch product">
										</div>
										<span class="line <?php if($status == 'Delivered'){ echo 'line_done'; }?>"></span>
										<p><?php _e( 'Out For Delivery', 'woocommerce'); ?></p>
									</div>
									<div class="delivery <?php if($status == 'Delivered'){ echo 'status_done'; }?>">
										<div class="imgcircle">
											<img src="<?php echo wc_il_post()->plugin_dir_url()?>assets/images/delivery.png" alt="delivery">
										</div>
										<p><?php _e( 'Delivered', 'woocommerce'); ?></p>
									</div>
									<div class="clear"></div>
								</div>
							</div>
							<h2 class="pr_header"><?php _e( 'Shipment Progress', 'israel-post-for-woocommerce'); ?></h2>
							<table class="">								
								<tbody>
									<?php 
									foreach((array)$event_array as $event){
											$datetime = new DateTime($event->EventDate);
											$date = $datetime->format('Y-m-d');
											$time = $datetime->format('H:i:s');
										?>
										<tr>								
											<td><?php echo $date; ?></td>
											<td><?php echo $time; ?></td>
											<td><?php 
											if($event->EventDescriptionEng == 'posting'){
												echo $event->SubEventDescriptionEng;	
											} else{
												echo $event->EventDescriptionEng;	
											}
											 ?></td>
										</tr>											
									<?php } ?>									
								</tbody>
							</table>
						</div>										
					</div>
				</div>
			</div>
		</div>	
	<?php 
	}
	
	/*
	* Preview Tracking page function
	*/
	public function preview_il_post_tracking_page_fun(){
		if(isset($_REQUEST["action"])){
			$action = wc_clean($_REQUEST["action"]);
		} else{
			$action = '';
		}					
		if($action != 'preview_il_post_tracking_page')return;
		get_header();
		wp_enqueue_style( 'il-post-front-css');	
		$theme_color = get_option('tracking_page_theme_color');
		if($theme_color){ ?>
			<style>
				.tracking-section .content2-header1 p{
					color: <?php echo $theme_color; ?>;
				}
				.tracking-section .pr_header{
					color: <?php echo $theme_color; ?>;
				}
			</style>
		<?php }
		?>
		<div class="tracking-section" style="min-height: auto;">
			<div class="">
				<div class="">
					<div class="">
						<div class="">							
							<span class="contact100-form-title logo-header">
								<img src="<?php echo wc_il_post()->plugin_dir_url()?>assets/images/ilpost-logo-transparent.png"><br>
							</span>							
						</div>
						<div class="">
							<div class="content2">
								<div class="content2-header1">
									<p><?php _e( 'Order', 'woocommerce'); ?> #5943 is <span><?php _e( 'Label Printed', 'woocommerce'); ?></span></p>
								</div>
								<div class="content2-header1">
									<p><?php _e( 'Tracking Number', 'israel-post-for-woocommerce'); ?> : <span>LX600026830IL</span></p>
								</div>
																
								<div class="clear"></div>
							</div>
							<div class="content3">
								<div class="shipment">									
									<div class="quality status_done">
										<div class="imgcircle">
											<img src="<?php echo wc_il_post()->plugin_dir_url()?>assets/images/quality.png" alt="quality check">
										</div>
										<span class="line "></span>
										<p><?php _e( 'Label Printed', 'woocommerce'); ?></p>
									</div>
									<div class="in_transit ">
										<div class="imgcircle">
											<img src="<?php echo wc_il_post()->plugin_dir_url()?>assets/images/process.png" alt="dispatch product">
										</div>
										<span class="line "></span>
										<p><?php _e( 'In Transit', 'woocommerce'); ?></p>
									</div>	
									<div class="dispatch ">
										<div class="imgcircle">
											<img src="<?php echo wc_il_post()->plugin_dir_url()?>assets/images/dispatch.png" alt="dispatch product">
										</div>
										<span class="line "></span>
										<p><?php _e( 'Out For Delivery', 'woocommerce'); ?></p>
									</div>
									<div class="delivery ">
										<div class="imgcircle">
											<img src="<?php echo wc_il_post()->plugin_dir_url()?>assets/images/delivery.png" alt="delivery">
										</div>
										<p><?php _e( 'Delivered', 'woocommerce'); ?></p>
									</div>
									<div class="clear"></div>
								</div>
							</div>
							<h2 class="pr_header"><?php _e( 'Shipment Progress', 'israel-post-for-woocommerce'); ?></h2>
							<table class="">								
								<tbody>
									<tr>								
										<td>2019-09-27</td>
										<td>12:16:30</td>
										<td>The sender has issued an address lable for this tracking number</td>
									</tr>
								</tbody>
							</table>
						</div>										
					</div>
				</div>
			</div>
		</div>
		<?php
		get_footer();
		exit;
	}	
}