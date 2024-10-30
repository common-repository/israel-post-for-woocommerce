jQuery(document).ready(function () {
	jQuery(".woocommerce-help-tip").tipTip();
	jQuery('.color_field input').wpColorPicker();
	var page_type = jQuery("#il_post_tracking_page_type").val();
	if (page_type == 'app') {
		jQuery('.select_tracking_page').show();
	} else {
		jQuery('.select_tracking_page').hide();
	}


	var x = 0;
	jQuery('.add_more_package').click(function (e) {
		e.preventDefault();
		jQuery('.add_more_package_container').append('<div class="package_dimension_div"><input type="hidden" name="package_details[' + x + '][package_dimension_key]" value=' + Date.now() + '><input type="text" name="package_details[' + x + '][package_dimension_title]" class="package_dimension_title validate-package-input" placeholder=' + ilpost_settings.i18n.title + '><ul class="package_dimension_input_ul"><li><input type="number" name="package_details[' + x + '][package_dimension_length]" placeholder=' + ilpost_settings.i18n.length + ' class="package_dimension_length validate-package-input"></li><li><input type="number" name="package_details[' + x + '][package_dimension_width]" placeholder=' + ilpost_settings.i18n.width + ' class="package_dimension_width validate-package-input"></li><li><input type="number" name="package_details[' + x + '][package_dimension_height]" placeholder=' + ilpost_settings.i18n.height + ' class="package_dimension_height validate-package-input"></li><span class="dimension_unit_label">cm</span></ul><a href="#" class="remove_package">' + ilpost_settings.i18n.cancel + '</a></div>');
		jQuery('.save_package').show();
		x++;
	});

	jQuery('.add_more_package_container').on("click", ".remove_package", function (e) { //user click on remove text links
		e.preventDefault();
		jQuery(this).parent('div').remove();
		x--;
		if (x == 0) {
			jQuery('.save_package').hide();
		}
	});

	jQuery('.save_package').click(function (e) {
		var package_input = jQuery('.add_more_package_container').find('.validate-package-input');
		var package_check = true;
		for (var i = 0; i < package_input.length; i++) {
			if (validate(package_input[i]) === false) {
				showerror(package_input[i]);
				package_check = false;
			} else {
				hideerror(jQuery(package_input[i]));
			}
		}

		var package_details = jQuery('input[name^="package_details"]').serialize();
		var ajax_data = {
			action: 'il_post_save_package_dimension',
			package_input: package_details,
		};
		if (package_check == true) {
			jQuery("#il_shipping_options_form").block({
				message: null,
				overlayCSS: {
					background: "#fff",
					opacity: .6
				}
			});
			jQuery.ajax({
				url: ajaxurl,
				data: ajax_data,
				type: 'POST',
				//dataType: "json",
				success: function (response) {
					jQuery('.dimension_div').replaceWith(response);
					jQuery('.package_dimension_div').remove();
					jQuery('.save_package').hide();
					x = 0;
					jQuery("#il_shipping_options_form").unblock();
				},
				error: function (response) {
					console.log(response);
				}
			});
		}
		return false;
	});
});

jQuery(document).on("click", ".set_package_default", function () {

	jQuery("#il_shipping_options_form").block({
		message: null,
		overlayCSS: {
			background: "#fff",
			opacity: .6
		}
	});


	if (jQuery(this).prop("checked") != true) {
		var key = '';
		jQuery(this).removeAttr('checked');
		jQuery('.set_package_default').removeAttr('checked');
	} else {
		var key = jQuery(this).data('key');
		jQuery('.set_package_default').removeAttr('checked');
		jQuery(this).attr('checked', 'checked');
	}

	var ajax_data = {
		action: 'il_post_set_package_default',
		key: key,
	};

	jQuery.ajax({
		url: ajaxurl,
		data: ajax_data,
		type: 'POST',
		success: function (response) {
			jQuery("#il_shipping_options_form").unblock();
		},
		error: function (response) {
			console.log(response);
		}
	});
});

jQuery(document).on("click", ".remove_package_dimesion", function () {
	var key = jQuery(this).data('key');
	var ajax_data = {
		action: 'il_post_remove_package_dimesion',
		key: key,
	};
	jQuery("#il_shipping_options_form").block({
		message: null,
		overlayCSS: {
			background: "#fff",
			opacity: .6
		}
	});
	jQuery(this).attr('checked', 'checked');
	jQuery.ajax({
		url: ajaxurl,
		data: ajax_data,
		type: 'POST',
		success: function (response) {
			jQuery('.dimension_div').replaceWith(response);
			jQuery("#il_shipping_options_form").unblock();
		},
		error: function (response) {
			console.log(response);
		}
	});
});

jQuery(document).on("change", "#il_post_tracking_page_type", function () {
	var page_type = jQuery(this).val();
	if (page_type == 'app') {
		jQuery('.select_tracking_page').show();
	} else {
		jQuery('.select_tracking_page').hide();
	}
});

jQuery(document).on("click", ".il-post-save-settings", function () {

	var form = jQuery(this).closest("form");

	var input = jQuery(form).find('.validate-input');
	var input_length = jQuery(form).find('.validate-length-input');
	var check = true;

	for (var i = 0; i < input.length; i++) {
		if (validate(input[i]) === false) {
			showerror(input[i]);
			check = false;
		} else {
			hideerror(jQuery(input[i]));
		}
	}

	for (var i = 0; i < input_length.length; i++) {
		if (length_validate(input_length[i]) === false) {
			showerror(input_length[i],true);
			check = false;
		} else {
			hideerror(jQuery(input_length[i]),true);
		}
	}

	if (check == true) {
		jQuery(form).find('.spinner').addClass("active");
		jQuery(form).find('.success_msg').hide();
		jQuery.ajax({
			url: ajaxurl,
			data: form.serialize(),
			type: 'POST',
			dataType: "json",
			success: function (response) {
				jQuery(form).find('.spinner').removeClass("active");
				jQuery(form).find('.success_msg').show();
			},
			error: function (response) {
				console.log(response);
			}
		});
	}
	return false;
});

function validate(input) {

	if (jQuery(input).attr('type') == 'email' || jQuery(input).attr('name') == 'email') {
		if (jQuery(input).val().trim().match(/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{1,5}|[0-9]{1,3})(\]?)$/) == null) {
			return false;
		}
	}
	else {
		if (jQuery(input).val().trim() == '' || jQuery(input).val().trim() == 0) {
			return false;
		}
	}
}

function length_validate(input) {
	var max_length = jQuery(input).data('max');
	var input_length = jQuery(input).val().length;
	if (input_length > max_length) {
		return false;
	}
	return true;
}

jQuery(document).on("click", ".test_connection_button button", function () {
	jQuery("#il_api_settings_form").block({
		message: null,
		overlayCSS: {
			background: "#fff",
			opacity: .6
		}
	});
	var ilpost_username = jQuery('#il_settings_form #ilpost_username').val();
	var ilpost_password = jQuery('#il_settings_form #ilpost_password').val();
	var ilpost_client_name = jQuery('#il_api_settings_form #ilpost_client_name').val();
	var ilpost_client_identity = jQuery('#il_api_settings_form #ilpost_client_identity').val();
	var ilpost_sandbox_mode = jQuery('#il_api_settings_form #ilpost_sandbox_mode').prop('checked');
	var ilpost_enable_logging = jQuery('#il_api_settings_form #ilpost_enable_logging').prop('checked');

	var ajax_data = {
		action: 'il_post_test_connection',
		ilpost_username: ilpost_username,
		ilpost_password: ilpost_password,
		ilpost_client_name: ilpost_client_name,
		ilpost_client_identity: ilpost_client_identity,
		ilpost_sandbox_mode: ilpost_sandbox_mode,
		ilpost_enable_logging: ilpost_enable_logging,
	};

	jQuery.ajax({
		url: ajaxurl,
		data: ajax_data,
		type: 'POST',
		dataType: "json",
		success: function (response) {
			jQuery("#il_api_settings_form").unblock();
			if (response.test == 'success') {
				jQuery('.connection_success').show();
				jQuery('.connection_success label').text(response.message);
				jQuery('.connection_fail').hide();
			} else {
				jQuery('.connection_fail').show();
				jQuery('.connection_fail label').text(response.message);
				jQuery('.connection_success').hide();
			}
		},
		error: function (response) {
			console.log(response);
		}
	});
	return false;
});
jQuery(document).on("click", ".il_tab_input", function () {
	var tab = jQuery(this).data('tab');
	var url = window.location.protocol + "//" + window.location.host + window.location.pathname + "?page=israel-post-for-woocommerce&tab=" + tab;
	window.history.pushState({ path: url }, '', url);
});

function showerror(element, max) {
	jQuery(element).css("border", "1px solid red");
	// if (max) {
	// 	var max_length = jQuery(element).data('max');
	// 	jQuery(element).after("<div style='color:red'>Can't be greater than " + max_length + " characters </div>");
	// }
}
function hideerror(element,max) {
	jQuery(element).css("border", "1px solid #ddd");
	// if(max){
	// 	jQuery(element).next('div').remove();
	// }
}