<?php
if (!defined('ABSPATH')) {
	exit;
}

class WC_IL_Post_Admin
{
	public static $customsFlag = TRUE;
	/**
	 * Initialize the main plugin function
	 */
	public function __construct()
	{
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
	public static function get_instance()
	{

		if (null === self::$instance) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/*
	* init from parent mail class
	*/
	public function init()
	{

		//Hook for add meta box in orders page
		add_action('add_meta_boxes', array($this, 'add_meta_box'));

		// Ajax hook for update access token
		add_action('wp_ajax_il_post_update_access_token', array($this, 'il_post_update_access_token_fun'));

		//Hook for delete tracking item
		add_action('wp_ajax_wc_ilpost_tracking_delete_item', array($this, 'meta_box_delete_tracking'));

		// Ajax hook for generate label
		add_action('wp_ajax_generae_il_post_order_label', array($this, 'generae_il_post_order_label'));

		// Ajax hook for open bulk action shipping label popup
		add_action('wp_ajax_il_post_create_generate_label_popup', array($this, 'il_post_create_generate_label_popup_fun'));
		// Hook for add action button in orders list
		add_filter('woocommerce_admin_order_actions', array($this, 'add_orders_list_actions_button'), 100, 2);

		if (!is_plugin_active('woo-advanced-shipment-tracking/woocommerce-advanced-shipment-tracking.php')) {
			//Hook for add tracking details in completed email 
			add_action('woocommerce_email_before_order_table', array($this, 'email_display'), 0, 4);

			//Hook for show tracking details in My Account Order 
			add_action('woocommerce_view_order', array($this, 'show_tracking_info_order'));
		}
	}

	/**
	 * Add the meta box for shipment info on the order page
	 */
	public function add_meta_box()
	{
		add_meta_box('il-post-generate-label', __('IL Post Shipping Label', 'israel-post-for-woocommerce'), array($this, 'meta_box_generate_label'), 'shop_order', 'side', 'high');
	}

	/* update Il post access token
	 *
	 * @since 1.0
	 * @version 1.0
	 *	 
	 */
	public static function il_post_update_access_token_fun()
	{
		$clientname = get_option('ilpost_client_name');
		$clientidentity = get_option('ilpost_client_identity');
		$auth_user = get_option('ilpost_username');
		$auth_password = get_option('ilpost_password');
		$ilpost_enable_logging = get_option('ilpost_enable_logging', 1);
		$ilpost_sandbox_mode = get_option('ilpost_sandbox_mode', 0);
		$il_post_access_token_timestamp = get_option('il_post_access_token_timestamp');
		$il_post_access_token = get_option('il_post_access_token');
		// Formulate the Difference between two dates 
		$diff = round(abs(time() - $il_post_access_token_timestamp) / 60, 2);

		if ($diff > 50) {
			if ($ilpost_sandbox_mode == 1) {
				$url = 'https://is.israelpost.co.il:8090/core/connect/token';
			} else {
				$url = 'https://is.israelpost.co.il/core/connect/token';
			}

			$args['body'] = 'grant_type=password&username=' . $auth_user . '&password=' . $auth_password . '&scope=read+write';

			$args['headers'] = array(
				'Authorization' => 'Basic ' . base64_encode($clientname . ':' . $clientidentity),
			);

			$args['timeout'] = 120;

			try {
				$response = wp_remote_post($url, $args);
				if (is_wp_error($response)) {

					update_option('il_post_access_token', '');
					update_option('il_post_access_token_timestamp', time());

					if ($ilpost_enable_logging == 1) {
						$logger = wc_get_logger();
						$context = array('source' => 'Il_Post_apicall_error');
						$logger->error("\nError: " . $response->get_error_message() . "\nURL: " . $url, $context);
					}
					echo json_encode(array('status' => 'fail'));
					exit;
				} else {
					if ($response['response']['code'] == 200) {
						$body = json_decode($response['body']);
						$access_token = $body->access_token;
						update_option('il_post_access_token', $access_token);
						update_option('il_post_access_token_timestamp', time());
						echo json_encode(array('status' => 'success'));
						exit;
					} else {

						update_option('il_post_access_token', '');
						update_option('il_post_access_token_timestamp', time());

						if ($ilpost_enable_logging == 1) {
							$body = json_decode($response['body']);
							$code = $response['response']['code'];
							$logger = wc_get_logger();
							$context = array('source' => 'Il_Post_apicall_error');
							$logger->error("\nError code: " . $code . "\nURL: " . $url . "\nError: " . $body->error . "\nDescription: " . $body->error_description, $context);
						}
						echo json_encode(array('status' => 'fail'));
						exit;
					}
				}
			} catch (Exception $e) {

				update_option('il_post_access_token', '');
				update_option('il_post_access_token_timestamp', time());

				if ($ilpost_enable_logging == 1) {
					$logger = wc_get_logger();
					$context = array('source' => 'Il_Post_apicall_error');
					$logger->error("\nError: " . $e->getMessage() . "\nURL: " . $url, $context);
				}
				echo json_encode(array('status' => 'fail'));
				exit;
			}
		} else {
			echo json_encode(array('status' => 'success'));
			exit;
		}
	}

	/**
	 * Show the meta box for IL Post Shipping Label on the order page
	 */
	public function meta_box_generate_label()
	{
		global $post;
		$tracking_items = $this->get_tracking_items($post->ID);

		echo '<div id="tracking-items">';
		if (count($tracking_items) > 0) {
			foreach ($tracking_items as $tracking_item) {

				if (isset($tracking_item['label_file'])) {
					$this->display_html_tracking_item_for_meta_box($post->ID, $tracking_item);
				}
			}
		}
		echo '</div>';

		$order = wc_get_order($post->ID);
		$items = $order->get_items();
		$products_id = array();

		foreach ($items as $item) {
			$products_id[$item->get_product_id()] = $item->get_quantity();
		}

		$product_list = array();

		foreach ($tracking_items as $tracking_item) {

			if (isset($tracking_item['products_list'])) {
				$product_list[] = $tracking_item['products_list'];
			}
		}

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

		$array_check = ($all_list == $products_id);

		if ($array_check != 1) {
			echo '<button class="button button-primary generate-order-label print_bulk_shipping_label" href="#' . $post->ID . '" type="button">' . __('Generate Label', 'israel-post-for-woocommerce') . '</button>';
		}

		wp_enqueue_style('il-post-select2-css');
		wp_enqueue_style('il-post-wc-css');
		wp_enqueue_script('il-post-select2-js');
		wp_enqueue_script('il-post-wc-js');
	}

	/**
	 * Ajax function for generate bulk action generate label popup
	 */
	public static function il_post_create_generate_label_popup_fun()
	{
		$order_id =  wc_clean($_POST['order_id']);
		$order = new WC_Order($order_id);
		$ClientShipmentIdentifier =  substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 1, 8);
		$ilpost_enable_logging = get_option('ilpost_enable_logging', 1);

		$countries_obj   = new WC_Countries();
		$countries   = $countries_obj->__get('countries');

		if ($order->get_shipping_country()) {
			$receiver_country = WC()->countries->countries[$order->get_shipping_country()];
		} else {
			$receiver_country = '';
		}

		$access_token = get_option('il_post_access_token');
		$ilpost_sandbox_mode = get_option('ilpost_sandbox_mode', 0);

		if ($ilpost_sandbox_mode == 1) {
			$ILPOST_endpoint = 'https://testngw.israelpost.co.il:9444';
		} else {
			$ILPOST_endpoint = 'https://ngw.israelpost.co.il:1444';
		}

		$url = $ILPOST_endpoint . "/Tevel.External.Shipments.WebApi/api/countryservices?CountryCode=" . $order->get_shipping_country() . "";

		$args['headers'] = array(
			'Authorization' => 'Bearer ' . $access_token,
		);

		$args['timeout'] = 120;
		$response = wp_remote_get($url, $args);

		$shipmenttypae_data = array();

		if (is_array($response) && !is_wp_error($response)) {
			//echo '<pre>';print_r($response);echo '</pre>';exit;
			if ($response['response']['code'] == 200) {
				$shipmenttypae_data = json_decode($response['body']);
				$shipdata = true;
			} else {
				$shipdata = false;
			}
		} else {
			if ($ilpost_enable_logging == 1) {
				$logger = wc_get_logger();
				$context = array('source' => 'Il_Post_apicall_error');
				$logger->error("\nError: " . $response->get_error_message() . "\nURL: " . $url, $context);
			}
			$shipdata = false;
		}

		$shippingTypeData = array(
			'1' => 'Parcel CP',
			'2' => 'Registered Mail RY',
			'4' => 'EMS',
			'6' => 'Eco post',
			'7' => 'EMS platinum Europe',
			'8' => 'EMS platinum USA',
			'9' => 'EMS platinum USA economy',
			'365' => 'EMS documents',
			'370' => 'Small parcels',
			'389' => 'UA – Regular air mail'
		);

		ob_start();
		require_once('views/generate-label-popup.php');

		wp_enqueue_style('il-post-select2-css');
		wp_enqueue_script('il-post-select2-js');

		$html = ob_get_clean();
		echo $html;
		exit;
	}

	/**
	 * function for generate il post label
	 */
	public function generae_il_post_order_label()
	{
		/**invoice */
		$has_invoice = false;
		$invoice = null;
		$filename = $_FILES['invoice']['name'];
		if ($filename != '') {
			$has_invoice = true;
			$file_type = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
			// function that reads text of a file
			$invoice_content = file_get_contents($_FILES['invoice']['tmp_name']);
			$base64_content = base64_encode($invoice_content);

			$invoice['has_invoice'] = $has_invoice;
			$invoice['base64_content'] = $base64_content;
			$invoice['file_type'] = $file_type;

			$post_invoice_file =
			array(
				'FileTypeExt' => $invoice['file_type'],
				'FileByteString' => $invoice['base64_content'],
			);

		}
		/**end invoice */

		$ilpost_sandbox_mode = get_option('ilpost_sandbox_mode', 0);
		$ilpost_enable_logging = get_option('ilpost_enable_logging', 1);

		if ($ilpost_sandbox_mode == 1) {
			$ILPOST_endpoint = 'https://testngw.israelpost.co.il:9444';
			$ILPOST_port = '9444';
		} else {
			$ILPOST_endpoint = 'https://ngw.israelpost.co.il:1444';
			$ILPOST_port = '1444';
		}

		$products_array = array();
		$ilpost_token = get_option('il_post_access_token');

		foreach ($_POST['PostItem'] as $PostItem) {

			if (isset($PostItem['product'])) {
				$product_data =  (object) array(
					'product' => wc_clean($PostItem['product']),
					'qty' => wc_clean($PostItem['quantity']),
				);
				array_push($products_array, $product_data);
				$data[] = array(
					'HS_CODE' => wc_clean($PostItem['hs_code']),
					'Description' => substr(sanitize_text_field($PostItem['title']), 0, 50),
					'Quantity' => (int)wc_clean($PostItem['quantity']),
					'DocumentType' => NULL,
					'CurrencyCode' => wc_clean($_POST['CurrencyCode']),
					'OriginCountryCode' => wc_clean($_POST['OriginCountryCode']),
					'ContentValue' => (float)(wc_clean($PostItem['price'])),
					'Weight' => (float)(wc_clean($PostItem['grams'])),
				);
			}
		}

		if ($_POST['destination_address2']) {
			$address = wc_clean($_POST['destination_address']) . ', ' . wc_clean($_POST['destination_address2']);
		} else {
			$address = wc_clean($_POST['destination_address']);
		}

		if (isset($_POST['ShippingTypeID'])) {
			$ShippingTypeID = wc_clean($_POST['ShippingTypeID']);
		} else {
			$ShippingTypeID = '';
		}

		$Length = NULL;
		$Width = NULL;
		$Height = NULL;

		$CustomsProcedureTypeID = wc_clean($_POST['CustomsProcedureTypeID']);

		$package_dimension_details = wc_clean($_POST['package_dimension_details']);

		if ($_POST['package_length'] != '') {
			$Length = (float)wc_clean($_POST['package_length']);
		}
		if ($_POST['package_width'] != '') {
			$Width = (float)wc_clean($_POST['package_width']);
		}
		if ($_POST['package_height'] != '') {
			$Height = (float)wc_clean($_POST['package_height']);
		}

		if (self::$customsFlag) {
		$post_data_new = array(
			'Shipments' =>
			array(
				array(
					'ClientShipmentIdentifier' => wc_clean($_POST['ClientShipmentIdentifier']),
					'Shipping' =>  array(
						'RecipientName' => wc_clean($_POST['destination_name']),
						'RecipientCompanyName' => wc_clean($_POST['destination_company']),
						'RecipientCity' => wc_clean($_POST['destination_city']),
						'RecipientZipCode' => wc_clean($_POST['destination_code']),
						'RecipientCountry' => wc_clean($_POST['destination_country']),
						'RecipientState' => wc_clean($_POST['destination_state']),
						'RecipientAddress' => wc_clean($address),
						'RecipientHouseNumber' => "",
						//'RecipientHouseNumber' => substr(wc_clean($_POST['destination_address2']), 0, 10),
						'RecipientEmail' => wc_clean($_POST['destination_email']),
						'RecipientPhoneNumber' => wc_clean($_POST['destination_phone']),
						'RecipientCellPhone' => wc_clean($_POST['destination_cellphone']),
						'RecipientFax' => wc_clean($_POST['destination_fax']),
						'SenderName' => wc_clean($_POST['origin_name']),
						'SenderCompanyName' => wc_clean($_POST['origin_company']),
						'SenderAddress' => wc_clean($_POST['origin_address']),
						'SenderCity' => wc_clean($_POST['origin_city']),
						'SenderZipCode' => wc_clean($_POST['postal_code']),
						'SenderCountry' => wc_clean($_POST['origin_country']),
						'SenderEmail' => wc_clean($_POST['origin_email']),
						'SenderPhoneNumber' => wc_clean($_POST['origin_phone']),
						'SenderCellPhone' => wc_clean($_POST['origin_cellphone']),
						'SenderFax' => wc_clean($_POST['origin_fax']),
						'IsCreateExportEntry' => $_POST['IsCreateExportEntry'] === 'true' ? true : false,
						'ShippingTypeID' => (int)$ShippingTypeID,
						'CustomsProcedureTypeID' => (int)$CustomsProcedureTypeID,
						'Ioss' => wc_clean($_POST['IossNumber']),
					),
					'PostItem' => array(
						'Weight' => (float)wc_clean($_POST['total_weight']),
						'Length' => $Length,
						'Height' => $Height,
						'Width' => 	$Width,
						'InsuranceValue' => (int)wc_clean($_POST['InsuranceValue']),
						'InsuranceTypeID' => (int)wc_clean($_POST['InsuranceTypeID']),
						'Description' => wc_clean($_POST['description']),
						'CustomsDeclarationID' => (int) wc_clean($_POST['CustomsDeclarationID']),
						'Contents' => $data,
					),
					'CustomsDetails' =>  array (
						'IsNotDangerousGoodsDeclaration'  => wc_clean($_POST['IsNotDangerousGoodsDeclaration']) ?  isset($_POST['IsNotDangerousGoodsDeclaration']) : true ,
						'IsTaxRefundRequested' => $_POST['IsTaxRefundRequested'] === 'true'? true: false,
						'IsExportLegalityRequired' => $_POST['IsExportLegalityRequired'] === 'true'? true: false,
						'SenderAutonomyRegionID' => (int)wc_clean($_POST['SenderAutonomyRegionID']),
						'IsPreferenceDocumentInd' => $_POST['IsCreateExportInvoiceEur1'] === 'true'? true: false,
						'PreferenceDocumentTypeID'  => (int)wc_clean($_POST['Eur1AgreementCode']),
						'PreferenceDocumentNumber' => wc_clean($_POST['Eur1declarationnumber']),
					),
					'Invoice' => array(
						'InvoiceNumber' => wc_clean($_POST['InvoiceNumber'])=="" && self::$customsFlag == false ? wc_clean($_POST['order_id']) : wc_clean($_POST['InvoiceNumber']) ,
						'InvoiceDate' => wc_clean($_POST['InvoiceDate']) ? wc_clean($_POST['InvoiceDate']) . 'T00:00:00' : wc_clean($_POST['order_date']),
						'OriginCountryCode' => wc_clean($_POST['OriginCountryCode']),
						'InvoiceValue' => (float)wc_clean($_POST['declared_value']),
						'CurrencyCode' => wc_clean($_POST['CurrencyCode']),
					),
					'InvoiceFile' => $post_invoice_file,
				),
			),
			'IsMergeLabels' => true,
			'MergedLabelsFileType' => (int)wc_clean($_POST['MergedLabelsFileType']),
		);

} else
 {
	$post_data_new = array(
		'Shipments' =>
		array(
			array(
				'ClientShipmentIdentifier' => wc_clean($_POST['ClientShipmentIdentifier']),
				'Shipping' =>  array(
					'RecipientName' => wc_clean($_POST['destination_name']),
					'RecipientCompanyName' => wc_clean($_POST['destination_company']),
					'RecipientCity' => wc_clean($_POST['destination_city']),
					'RecipientZipCode' => wc_clean($_POST['destination_code']),
					'RecipientCountry' => wc_clean($_POST['destination_country']),
					'RecipientState' => wc_clean($_POST['destination_state']),
					'RecipientAddress' => wc_clean($address),
					'RecipientHouseNumber' => "",
					//'RecipientHouseNumber' => substr(wc_clean($_POST['destination_address2']), 0, 10),
					'RecipientEmail' => wc_clean($_POST['destination_email']),
					'RecipientPhoneNumber' => wc_clean($_POST['destination_phone']),
					'RecipientCellPhone' => wc_clean($_POST['destination_cellphone']),
					'RecipientFax' => wc_clean($_POST['destination_fax']),
					'SenderName' => wc_clean($_POST['origin_name']),
					'SenderCompanyName' => wc_clean($_POST['origin_company']),
					'SenderAddress' => wc_clean($_POST['origin_address']),
					'SenderCity' => wc_clean($_POST['origin_city']),
					'SenderZipCode' => wc_clean($_POST['postal_code']),
					'SenderCountry' => wc_clean($_POST['origin_country']),
					'SenderEmail' => wc_clean($_POST['origin_email']),
					'SenderPhoneNumber' => wc_clean($_POST['origin_phone']),
					'SenderCellPhone' => wc_clean($_POST['origin_cellphone']),
					'SenderFax' => wc_clean($_POST['origin_fax']),
					'IsCreateExportEntry' => $_POST['IsCreateExportEntry'] === 'true' ? true : false,
					'ShippingTypeID' => (int)$ShippingTypeID,
					'CustomsProcedureTypeID' => (int)$CustomsProcedureTypeID,
					'Ioss' => wc_clean($_POST['IossNumber']),
				),
				'PostItem' => array(
					'Weight' => (float)wc_clean($_POST['total_weight']),
					'Length' => $Length,
					'Height' => $Height,
					'Width' => 	$Width,
					'InsuranceValue' => (int)wc_clean($_POST['InsuranceValue']),
					'InsuranceTypeID' => (int)wc_clean($_POST['InsuranceTypeID']),
					'Description' => wc_clean($_POST['description']),
					'CustomsDeclarationID' => (int) wc_clean($_POST['CustomsDeclarationID']),
					'Contents' => $data,
				),

				'Invoice' => array(
					'InvoiceNumber' => wc_clean($_POST['InvoiceNumber'])=="" && self::$customsFlag == false ? wc_clean($_POST['order_id']) : wc_clean($_POST['InvoiceNumber']) ,
					'InvoiceDate' => wc_clean($_POST['InvoiceDate']) ? wc_clean($_POST['InvoiceDate']) . 'T00:00:00' : wc_clean($_POST['order_date']),
					'OriginCountryCode' => wc_clean($_POST['OriginCountryCode']),
					'InvoiceValue' => (float)wc_clean($_POST['declared_value']),
					'CurrencyCode' => wc_clean($_POST['CurrencyCode']),
				),
					'InvoiceFile' => array(
						'FileTypeExt' => $has_invoice ? $invoice['file_type'] : '',
						'FileByteString' => $has_invoice ? $invoice['base64_content'] : '',
					),
				),
			),
			'IsMergeLabels' => true,
			'MergedLabelsFileType' => (int)wc_clean($_POST['MergedLabelsFileType']),
		);

}
		$json_data = json_encode($post_data_new);

		$response = wp_remote_post($ILPOST_endpoint . "/Tevel.External.Shipments.WebApi/api/Shipments", array(
			'method' => 'POST',
			'redirection' => 10,
			'httpversion' => '1.0',
			'body'    => $json_data,
			'timeout'     => 120,
			'headers' => array(
				'Authorization' => 'Bearer ' . $ilpost_token,
				'Content-Type' => 'application/json',
			),
		));

		if (is_wp_error($response)) {
			$error_message = $response->get_error_message();
			if ($ilpost_enable_logging == 1) {
				$logger = wc_get_logger();
				$context = array('source' => 'Il_Post_apicall_error');
				$logger->error("\nError: " . $error_message . "\nPost_Data: " . print_r($post_data_new, true), $context);
			}
			echo json_encode(array('success' => 'false', 'StatusCode' => 500, 'message' => $error_message));
			die();
		} else {
			$response_data = json_decode($response['body']);
		}

		if (isset($response_data->StatusCode) && $response_data->StatusCode == 200) {
			$tracking_number = $response_data->Result->LabelsList[0]->TrackingNumber;
			$shipment_num = $response_data->Result->LabelsList[0]->ShipmentNum;

			/**attaments */
			for ($i = 1; $i <= 3; $i++) {
				$filename = $_FILES['attach' . $i]['name'];
				if ($filename != '') {
					
					$file_type = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
					// function that reads text of a file
					$attach_content = file_get_contents($_FILES['attach' . $i]['tmp_name']);
					$base64_content = base64_encode($attach_content);
					$post_data_attachments = array(
						"ShipmentNum" => $shipment_num,
						"FileTypeExt" => $file_type,
						"TrackingNumber" => $tracking_number,
						"Description" => wc_clean($_POST['descriptionAttach' . $i]),
						"FileByteString" => $base64_content,
						"DocumentType" =>(int)$_POST['Document' . $i]
					);
					$json_data_attachments = json_encode($post_data_attachments);
				
					$response_attachment = wp_remote_post($ILPOST_endpoint . "/Tevel.External.Shipments.WebApi/api/FileUpload", array(
						'method' => 'POST',
						'redirection' => 10,
						'httpversion' => '1.0',
						'body'    => $json_data_attachments,
						'timeout'     => 120,
						'headers' => array(
							'Authorization' => 'Bearer ' . $ilpost_token,
							'Content-Type' => 'application/json',
						),
					));
					
					if (is_wp_error($response_attachment)) {
						$message = "There was an error uploading a file.\n"  . $response_attachment->get_error_message();
						if ($ilpost_enable_logging == 1) {
							$logger = wc_get_logger();
							$context = array('source' => 'Il_Post_apicall_error');
							$logger->error("\nError: " . $message ,$context);
						}
						$out_message="An error occurred while uploading the document/s. \n".
						"Please send the files to Exporter's Service (Sherut La Yetzuan) at yezuan1@postil.com, In your email please state your member's account number and barcode number.\n "; 
					}else{
						$response_attachment_data = json_decode($response_attachment['body']);
						if(!isset($response_attachment_data->StatusCode) || $response_attachment_data->StatusCode != 200){
							$message = "There was an error uploading a file.\n"  . $response_attachment_data->StatusCode;
							if ($ilpost_enable_logging == 1) {
								$logger = wc_get_logger();
								$context = array('source' => 'Il_Post_apicall_error');
								$logger->error("\nError: " . $message ,$context);
							}
							$out_message="An error occurred while uploading the document/s. \n".
							"Please send the files to Exporter's Service (Sherut La Yetzuan) at yezuan1@postil.com, In your email please state your member's account number and barcode number.\n "; 
						}
					}
				}
			}
			/**end attament */

			$order_id = wc_clean($_POST['order_id']);
			$order = wc_get_order($order_id);

			if ($_POST['MergedLabelsFileType'] == 1) {
				$label_file = $tracking_number . '.pdf';
			} else if ($_POST['MergedLabelsFileType'] == 5) {
				$label_file = $tracking_number . '.png';
			} else if ($_POST['MergedLabelsFileType'] == 4) {
				$label_file = $tracking_number . '.zip';
			} else if ($_POST['MergedLabelsFileType'] == 6) {
				$label_file = $tracking_number . '.zip';
			} else {
				$label_file = $tracking_number . '.pdf';
			}

			$pdf_string = $response_data->Result->MergedLabelsFile->FileByteString;
			$pdf_data =  base64_decode($pdf_string);

			$uploads_dir = trailingslashit(wp_upload_dir()['basedir']) . 'il-post-labels';
			wp_mkdir_p($uploads_dir);

			$pdf = fopen($uploads_dir . '/' . $label_file, 'w');
			fwrite($pdf, $pdf_data);
			//close output file
			fclose($pdf);

			$date = str_replace("/", "-", date_i18n(__('Y-m-d', 'israel-post-for-woocommerce'), current_time('timestamp')));
			$date = date_create($date);
			$date = date_format($date, "d-m-Y");

			$ilpost_use_usps = get_option('ilpost_use_usps', 'false');

			if ($ilpost_use_usps == 'true' && $_POST['destination_country'] == 'US') {
				$tracking_item['tracking_provider'] = 'usps';
			} else {
				$tracking_item['tracking_provider'] = 'israel-post';
			}
			$tracking_item['tracking_number'] = $tracking_number;
			$tracking_item['label_file'] = $label_file;
			$tracking_item['tracking_id'] = md5("{$tracking_item['tracking_provider']}-{$tracking_item['tracking_number']}" . microtime());
			$tracking_item['date_shipped'] = wc_clean(strtotime($date));
			$tracking_item['products_list'] = $products_array;

			$tracking_items = $this->get_tracking_items($order_id);
			$tracking_items[] = $tracking_item;

			$this->save_tracking_items($order_id, $tracking_items);

			// The text for the note
			$note = sprintf(__("Israel Post shipping label generated successfully.", 'israel-post-for-woocommerce'));

			// Add the note
			$order->add_order_note($note);
			echo json_encode(array('success' => 'true', 'StatusCode' => 200, 'message'=> $out_message));
			die();
		} else {
			if (isset($response_data->StatusCode) && $response_data->StatusCode == 206) {

				if ($ilpost_enable_logging == 1) {
					$logger = wc_get_logger();
					$context = array('source' => 'Il_Post_apicall_error');
					$logger->error("\nError code: " . $response_data->StatusCode . "\nPost_Data: " . print_r($post_data_new, true) . "\nError: " . $response_data->Result->LabelsList[0]->ShipmentRequestStatus->Message, $context);
				}

				echo json_encode(array('success' => 'false', 'StatusCode' => 206, 'message' => $response_data->Result->LabelsList[0]->ShipmentRequestStatus->Message));
				die();
			} else if (isset($response_data->StatusCode) && $response_data->StatusCode == 500) {
				if ($ilpost_enable_logging == 1) {
					$logger = wc_get_logger();
					$context = array('source' => 'Il_Post_apicall_error');
					$logger->error("\nError code: " . $response_data->StatusCode . "\nPost_Data: " . print_r($post_data_new, true) . "\nError: " . $response_data->Message, $context);
				}
				echo json_encode(array('success' => 'false', 'StatusCode' => 500, 'message' => $response_data->Message));
				die();
			} else {

				if ($ilpost_enable_logging == 1) {
					$logger = wc_get_logger();
					$context = array('source' => 'Il_Post_apicall_error');
					$logger->error("\nError: " . $response_data->Message . "\nPost_Data: " . print_r($post_data_new, true), $context);
				}

				echo json_encode(array('success' => 'false', 'StatusCode' => 400, 'message' => $response_data->Message));
				die();
			}
		}
	}

	/*
	 * Gets all tracking itesm fron the post meta array for an order
	 *
	 * @param int  $order_id  Order ID
	 * @param bool $formatted Wether or not to reslove the final tracking link
	 *                        and provider in the returned tracking item.
	 *                        Default to false.
	 *
	 * @return array List of tracking items
	 */
	public function get_tracking_items($order_id, $formatted = false)
	{

		global $wpdb;
		$order = wc_get_order($order_id);

		if ($order) {
			if (version_compare(WC_VERSION, '3.0', '<')) {
				$tracking_items = get_post_meta($order_id, '_wc_shipment_tracking_items', true);
			} else {
				$order          = new WC_Order($order_id);
				$tracking_items = $order->get_meta('_wc_shipment_tracking_items', true);
			}

			if (is_array($tracking_items)) {
				if ($formatted) {
					foreach ($tracking_items as &$item) {
						$formatted_item = $this->get_formatted_tracking_item($order_id, $item);
						$item           = array_merge($item, $formatted_item);
					}
				}
				return $tracking_items;
			} else {
				return array();
			}
		} else {
			return array();
		}
	}

	/**
	 * Saves the tracking items array to post_meta.
	 *
	 * @param int   $order_id       Order ID
	 * @param array $tracking_items List of tracking item
	 */
	public function save_tracking_items($order_id, $tracking_items)
	{

		if (version_compare(WC_VERSION, '3.0', '<')) {
			update_post_meta($order_id, '_wc_shipment_tracking_items', $tracking_items);
		} else {
			$order = new WC_Order($order_id);
			$order->update_meta_data('_wc_shipment_tracking_items', $tracking_items);
			$order->save_meta_data();
		}
	}

	/**
	 * Returns a HTML node for a tracking item for the admin meta box
	 */
	public function display_html_tracking_item_for_meta_box($order_id, $item)
	{
		$formatted = $this->get_formatted_tracking_item($order_id, $item);
		$formatted['formatted_label_url'];
		$ext = pathinfo($formatted['formatted_label_url'], PATHINFO_EXTENSION);
		if ($ext == 'png' || $ext == 'pdf') {
			$download_attribute = 'target="_blank"';
		} else {
			$download_attribute = 'download';
		}
?>
		<div class="tracking-item" id="tracking-item-<?php echo esc_attr($item['tracking_id']); ?>">
			<p class="tracking-content">
				<strong><?php echo esc_html($formatted['formatted_tracking_provider']); ?></strong>

				<?php if (strlen($formatted['formatted_tracking_link']) > 0) : ?>
					- <?php
						echo sprintf('<a href="%s" target="_blank" title="' . esc_attr(__('Click here to track your shipment', 'israel-post-for-woocommerce')) . '">' . __('Track', 'israel-post-for-woocommerce') . '</a>', esc_url($formatted['formatted_tracking_link'])); ?>
				<?php endif; ?>
				<br />
				<em><?php echo esc_html($item['tracking_number']); ?></em>
				<a href="<?php echo esc_url($formatted['formatted_label_url']); ?>" <?php echo $download_attribute; ?>><?php _e('Download Label', 'israel-post-for-woocommerce'); ?></a>
			</p>
			<p class="meta">
				<?php echo esc_html(sprintf(__('Shipped on %s', 'israel-post-for-woocommerce'), date_i18n('Y-m-d', $item['date_shipped']))); ?>
				<a href="#" class="delete-il-post-tracking" rel="<?php echo esc_attr($item['tracking_id']); ?>" data-order="<?php echo esc_attr($order_id); ?>"><?php _e('Delete', 'woocommerce'); ?></a>
			</p>
		</div>
<?php
	}

	/*
	 * Works out the final tracking provider and tracking link and appends then to the returned tracking item
	 *
	*/
	public function get_formatted_tracking_item($order_id, $tracking_item)
	{
		$formatted = array();

		$upload_dir = wp_get_upload_dir();
		if (isset($tracking_item['label_file'])) {
			$label_url = $upload_dir['baseurl'] . '/il-post-labels/' . $tracking_item['label_file'];
		}
		$tracking_number = str_replace(' ', '', $tracking_item['tracking_number']);

		if ($tracking_item['tracking_provider'] == 'usps') {
			$formatted['formatted_tracking_provider'] = 'USPS';
		} else {
			$formatted['formatted_tracking_provider'] = 'Israel post';
		}

		$il_post_tracking_page_type = get_option('il_post_tracking_page_type');

		if ($il_post_tracking_page_type == 'app') {
			$tracking_page = get_option('il_post_tracking_page');
			$formatted['formatted_tracking_link'] = get_permalink($tracking_page) . '?order_id=' . $order_id . '&tracking_number=' . $tracking_number;
		} else if ($tracking_item['tracking_provider'] == 'usps') {
			$formatted['formatted_tracking_link'] = "https://tools.usps.com/go/TrackConfirmAction_input?qtc_tLabels1=" . $tracking_number;
		} else {
			$formatted['formatted_tracking_link'] = "https://israelpost.co.il/itemtrace?itemcode=" . $tracking_number;
		}

		if (isset($label_url)) {
			$formatted['formatted_label_url']  = $label_url;
		}

		return $formatted;
	}

	/**
	 * Order Tracking Delete
	 *
	 * Function to delete a tracking item
	 */
	public function meta_box_delete_tracking()
	{
		$order_id    = wc_clean($_POST['order_id']);
		$tracking_id = wc_clean($_POST['tracking_id']);
		$this->delete_tracking_item($order_id, $tracking_id);
	}

	/**
	 * Deletes a tracking item from post_meta array
	 *
	 * @param int    $order_id    Order ID
	 * @param string $tracking_id Tracking ID
	 *
	 * @return bool True if tracking item is deleted successfully
	 */
	public function delete_tracking_item($order_id, $tracking_id)
	{
		$tracking_items = $this->get_tracking_items($order_id);

		$is_deleted = false;

		if (count($tracking_items) > 0) {
			foreach ($tracking_items as $key => $item) {
				if ($item['tracking_id'] == $tracking_id) {
					$tracking_number = $item['tracking_number'];
					$label_file = $item['label_file'];
					$uploads_dir = trailingslashit(wp_upload_dir()['basedir']) . 'il-post-labels';
					$path = $uploads_dir . '/' . $label_file;
					unlink($path);
					unset($tracking_items[$key]);
					$is_deleted = true;
					break;
				}
			}

			$this->save_tracking_items($order_id, $tracking_items);
		}

		return $is_deleted;
	}

	/*
	* Add action button in order list to change print shipping label
	*/
	public function add_orders_list_actions_button($actions, $order)
	{
		wp_enqueue_style('il-post-wc-css');
		wp_enqueue_script('il-post-wc-js');

		if ($order->has_status(array('processing')) || $order->has_status(array('completed'))) {

			$order_id = method_exists($order, 'get_id') ? $order->get_id() : $order->id;

			$order = wc_get_order($order_id);
			$items = $order->get_items();
			$products_id = array();

			foreach ($items as $item) {
				$products_id[$item->get_product_id()] = $item->get_quantity();
			}

			$product_list = array();
			$tracking_items = $this->get_tracking_items($order_id);

			foreach ($tracking_items as $tracking_item) {
				if (isset($tracking_item['products_list'])) {
					$product_list[] = $tracking_item['products_list'];
				}
			}

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
			//echo '<pre>';print_r($all_list);echo '</pre>';exit;	
			$array_check = ($all_list == $products_id);

			if ($array_check != 1) {
				// Set the action button
				$actions['print_il_post_label'] = array(
					'url'       => "#" . $order->get_id(),
					'name'      => __('Print IL Post shipping label', 'israel-post-for-woocommerce'),
					'action'    => "print_bulk_shipping_label", // keep "view" class for a clean button CSS
				);
			}

			foreach ($tracking_items as $key => $tracking_item) {
				if ($tracking_item['tracking_provider'] == 'israel-post' && isset($tracking_item['label_file'])) {
					$formatted = $this->get_formatted_tracking_item($order_id, $tracking_item);
					$actions['download_il_post_label_' . $key] = array(
						'url'       => $formatted['formatted_label_url'],
						'name'      => __('Download IL Post shipping label - ' . $tracking_item['tracking_number'], 'israel-post-for-woocommerce'),
						'action'    => "download_il_post_label", // keep "view" class for a clean button CSS
					);
				}
			}
		}
		return $actions;
	}

	/**
	 * Display shipment info in customer emails.
	 *
	 * @version 1.6.8
	 *
	 * @param WC_Order $order         Order object.
	 * @param bool     $sent_to_admin Whether the email is being sent to admin or not.
	 * @param bool     $plain_text    Whether email is in plain text or not.
	 * @param WC_Email $email         Email object.
	 */
	public function email_display($order, $sent_to_admin, $plain_text = null, $email = null)
	{
		$order_id = is_callable(array($order, 'get_id')) ? $order->get_id() : $order->id;
		$order = wc_get_order($order_id);
		if (!$order) return;
		$order_status = $order->get_status();
		if ($order_status != 'completed') return;
		wc_get_template('emails/il-post-tracking-info.php', array('tracking_items' => $this->get_tracking_items($order_id, true)), 'woo-israel-post/', wc_il_post()->get_plugin_path() . '/templates/');
	}

	/**
	 * Display Shipment info in the frontend (order view/tracking page).
	 */
	public function show_tracking_info_order($order_id)
	{
		wc_get_template('myaccount/il-post-tracking-info.php', array('tracking_items' => $this->get_tracking_items($order_id, true)), 'woo-israel-post/', wc_il_post()->get_plugin_path() . '/templates/');
	}
}
/**
 * Returns an instance of zorem_woocommerce_advanced_shipment_tracking.
 *
 * @since 1.6.5
 * @version 1.6.5
 *
 * @return zorem_woocommerce_advanced_shipment_tracking
 */
function wc_il_post_admin()
{
	static $instance;

	if (!isset($instance)) {
		$instance = new WC_IL_Post_Admin();
	}

	return $instance;
}
/**
 * Register this class globally.
 *
 * Backward compatibility.
 */
$GLOBALS['WC_IL_Post_Admin'] = wc_il_post_admin();
