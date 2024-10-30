<?php
/**
 * @wordpress-plugin
 * Plugin Name: Israel Post for WooCommerce 
 * Plugin URI: https://www.zorem.co.il/product/israel-post-plugin-for-woocommerce/ 
 * Description: The Israel post app allows you to create & print Israel Post international shipping labels, clearance documents directly through your store's orders admin. The plugin integrates the Israel post API into WooCommerce to create and print shipping labels, fulfill orders and track shipments. 
 * Version: 1.6.1
 * Author: Israel Post
 * Author URI: https://www.israelpost.co.il/ 
 * License: GPL-2.0+
 * License URI: 
 * Text Domain: israel-post-for-woocommerce
 * Domain Path: /lang/
 * WC tested up to: 7.1
*/


class zorem_woo_il_post {
	/**
	 * Israel Post for WooCommerce version.
	 *
	 * @var string
	 */
	public $version = '1.6.1';
	
	/**
	 * Initialize the main plugin function
	*/
    public function __construct() {
		
		$this->plugin_file = __FILE__;
		// Add your templates to this array.
		
		if(!defined('IL_POST_PATH')) define( 'IL_POST_PATH', $this->get_plugin_path());							
			
		
		if ( $this->is_wc_active() ) {
			
			// Include required files.
			$this->includes();			
			
			//start adding hooks
			$this->init();		

			//admin class init
			$this->admin->init();

			$this->settings->init();	
		}		
    }
	
	/**
	 * Check if WooCommerce is active
	 *
	 * @access private
	 * @since  1.0.0
	 * @return bool
	*/
	private function is_wc_active() {
		
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}
		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			$is_active = true;
		} else {
			$is_active = false;
		}
		

		// Do the WC active check
		if ( false === $is_active ) {
			add_action( 'admin_notices', array( $this, 'notice_activate_wc' ) );
		}		
		return $is_active;
	}
	
	/**
	 * Display WC active notice
	 *
	 * @access public
	 * @since  1.0.0
	*/
	public function notice_activate_wc() {
		?>
		<div class="error">
			<p><?php printf( __( 'Please install and activate %sWooCommerce%s for Israel Post for WooCommerce!', '' ), '<a href="' . admin_url( 'plugin-install.php?tab=search&s=WooCommerce&plugin-search-input=Search+Plugins' ) . '">', '</a>' ); ?></p>
		</div>
		<?php
	}
	
	/**
	 * Gets the absolute plugin path without a trailing slash, e.g.
	 * /path/to/wp-content/plugins/plugin-directory.
	 *
	 * @return string plugin path
	 */
	public function get_plugin_path() {
		if ( isset( $this->plugin_path ) ) {
			return $this->plugin_path;
		}

		$this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );

		return $this->plugin_path;
	}
	
	/**
	 * Gets the absolute plugin url.
	 */	
	public function plugin_dir_url(){
		return plugin_dir_url( __FILE__ );
	}
	
	/*
	* init when class loaded
	*/
	public function init(){
		add_action( 'plugins_loaded', array( $this, 'il_post_load_textdomain'));
		add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'ilpost_plugin_action_links' ) );		
		register_activation_hook( __FILE__, array( $this, 'il_post_install' ));				
	}
	
	/*** Method load Language file ***/
	function il_post_load_textdomain() {
		load_plugin_textdomain( 'israel-post-for-woocommerce', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
	}
	
	/*
	* include files
	*/
	private function includes(){
		
		require_once $this->get_plugin_path() . '/includes/class-wc-il-post-admin.php';
		$this->admin = WC_IL_Post_Admin::get_instance();

		require_once $this->get_plugin_path() . '/includes/class-wc-il-post-settings.php';
		$this->settings = WC_IL_Post_Settings::get_instance();		
		
		require_once $this->get_plugin_path() . '/includes/class-wc-il-post-front.php';
		$this->front = WC_IL_Post_Front::get_instance();
	}
	
	/**
	 * Define plugin activation function
	 *
	 * Create Table
	 *
	 * Insert data 
	 *
	 * 
	*/
	public function il_post_install(){
		$new_page_title = 'Israel Post Shipment Tracking';
		$new_page_slug = 'il-post-shipment-tracking';		
		$new_page_content = '[il-post-track-order]';       
		//don't change the code below, unless you know what you're doing
		$page_check = get_page_by_title($new_page_title);
		//echo $page_check;exit;
		$new_page = array(
				'post_type' => 'page',
				'post_title' => $new_page_title,
				'post_name' => $new_page_slug,
				'post_content' => $new_page_content,
				'post_status' => 'publish',
				'post_author' => 1,
		);
		if(!isset($page_check->ID)){
			$new_page_id = wp_insert_post($new_page);	
			update_option( 'il_post_trackship_page_id', $new_page_id );	
		}
	}
	
	/**
	* Add plugin action links.
	*
	* Add a link to the settings page on the plugins.php page.
	*
	* @since 2.6.5
	*
	* @param  array  $links List of existing plugin action links.
	* @return array         List of modified plugin action links.
	*/
	function ilpost_plugin_action_links( $links ) {
		$links = array_merge( array(
			'<a href="' . esc_url( admin_url( '/admin.php?page=israel-post-for-woocommerce' ) ) . '">' . __( 'Settings' ) . '</a>'
		), $links );
		return $links;
	}
}

/**
 * Returns an instance of zorem_woo_il_post.
 *
 * @since 1.0
 * @version 1.0
 *
 * @return zorem_woo_il_post
*/
function wc_il_post() {
	static $instance;

	if ( ! isset( $instance ) ) {		
		$instance = new zorem_woo_il_post();
	}

	return $instance;
}

/**
 * Register this class globally.
 *
 * Backward compatibility.
*/
$GLOBALS['WC_il_post'] = wc_il_post();