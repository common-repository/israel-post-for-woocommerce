jQuery(document).on("change", "#package_dimension_details", function () {
	var package_value = jQuery(this).val();
	if (package_value != '') {
		var length = jQuery(this).find(':selected').data('length');
		var width = jQuery(this).find(':selected').data('width');
		var height = jQuery(this).find(':selected').data('height');
		jQuery('#package_length').val(length);
		jQuery('#package_width').val(width);
		jQuery('#package_height').val(height);
	} else {
		jQuery('#package_length').val('');
		jQuery('#package_width').val('');
		jQuery('#package_height').val('');
	}

});

jQuery(document).on("submit", "#generate-label-form", function () {

	var input = jQuery('.validate-input .input100');
	var length_input = jQuery('.validate-length-input .input100');
	var phone_input = jQuery('.validate-phone .input100');
	var form = jQuery('#generate-label-form');
	var error;
	var order_total_weight = jQuery("#generate-label-form input[name=total_weight_input]");
	var total_weight = jQuery("#generate-label-form input[name=total_weight]");
	var maxweight = jQuery('#shipping_type').find(':selected').data('maxweight');
	var files = jQuery("#generate-label-form :file");
	var check = true;
	jQuery(".error_msg").remove();

	if (total_weight.val() > maxweight) {
		alert("Total Weight can't be more than " + maxweight);
		return false;
	}

	var fileExtension = ['pdf',	'jpg', 'jpeg', 'png', 'tiff', 'tif', 'doc', 'docx', 'txt'];
	for (var i = 0; i < files.length; i++) {
		if (files[i].files.length > 0) {
			if (files[i].files[0].size > 5242880) {
				alert("The file - " + files[i].files[0].name + " is too large. File must be less than 5 megabytes.");
				return false;
			}
			if (jQuery.inArray(files[i].files[0].name.split('.').pop().toLowerCase(), fileExtension) == -1) {
				alert("The file "+files[i].files[0].name+" is an unauthorized");
				return false;
			}
		}
	}

	for (var i = 0; i < input.length; i++) {
		if (validate(input[i]) == false) {
			//showValidate(input[i]);
			addValidateMessage(input[i]);
			jQuery('.error_msg').show();
			jQuery('.apploader').removeClass("show");
			jQuery(".contact100-form-btn").removeAttr("disabled");
			check = false;
		} else {
			var thisAlert = jQuery(input[i]).parent();
			jQuery(thisAlert).removeClass('alert-validate');
		}
	}
	for (var i = 0; i < length_input.length; i++) {
		if (length_validate(length_input[i]) == false) {
			addlengthValidateMessage(length_input[i]);
			jQuery('.error_msg').show();
			jQuery('.apploader').removeClass("show");
			jQuery(".contact100-form-btn").removeAttr("disabled");
			check = false;
		}
	}

	if (check == true) {
		jQuery('.generate_label_popup').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});

		var formData = new FormData(form[0]);
		var req = new XMLHttpRequest();
		req.open("POST", ajaxurl, true);
		req.onload = function (oEvent) {
			if (req.status == 200) {
				let response = JSON.parse(req.response);
				if (response.StatusCode == 200) {
					let attachMessage=response.message?response.message:'';
					alert('Label generated successfully.\n'+attachMessage);
					if (response.Message != null)
						alert(responses.Msessage)
					jQuery('.generate_label_popup').hide();
					location.reload(true);
				} else if (response.StatusCode == 206) {
					alert(response.message);
				} else {
					alert(response.message);
				}
				jQuery('.generate_label_popup').unblock();
			} else {
				alert('Error');
				jQuery('.generate_label_popup').unblock();
			}
		};

		req.send(formData);

	}
	return false;
});

function validate(input) {

	if (jQuery(input).attr('type') == 'email' || jQuery(input).attr('name') == 'email') {
		if (jQuery(input).val().trim().match(/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{1,5}|[0-9]{1,3})(\]?)$/) == null) {
			return false;
		}
	}
	else if (jQuery(input).attr('type') == 'checkbox') {
		if (jQuery(input).prop('checked') == false) {
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


function addValidateMessage(input) {
	var thisAlert = jQuery(input).parent();
	var alert_message = jQuery(thisAlert).data('validate');
	// jQuery(thisAlert).addClass('alert-validate');
	jQuery('.contact100-form-btn').after('<div class="error_msg">' + alert_message + '</div>');
}

function addlengthValidateMessage(input) {
	var thisAlert = jQuery(input).parent();
	var alert_message = jQuery(thisAlert).data('length');
	// jQuery(thisAlert).addClass('alert-validate');
	jQuery('.contact100-form-btn').after('<div class="error_msg">' + alert_message + '</div>');
}

jQuery(document).on("click", ".il_post_popupclose", function () {
	jQuery('.generate_label_popup').hide();
});


jQuery(".js-select2").each(function () {
	jQuery(this).select2({
		minimumResultsForSearch: 20,
		dropdownParent: jQuery(this).next('.dropDownSelect2')
	});


	jQuery(".js-select2").each(function () {
		jQuery(this).on('select2:close', function (e) {
			if (jQuery(this).val() == "Please chooses") {
				jQuery('.js-show-service').slideUp();
			}
			else {
				jQuery('.js-show-service').slideUp();
				jQuery('.js-show-service').slideDown();
			}
		});
	});
});

jQuery(document).on("change", ".package_weight_input", function () {

	var total_value = 0;
	var total_weight = 0;

	jQuery('.PostItem_row').each(function () {
		var PostItem_checkbox = jQuery(this).find('input[type="checkbox"]');
		if (PostItem_checkbox.prop("checked") == true) {
			total_value += Number(jQuery(this).find('.PostItem_Value').val());
			total_weight += Number(jQuery(this).find('.PostItem_Weight').val());
		}
	});

	var package_weight = jQuery(this).val();

	//var total_weight = jQuery('.total_weight_org').val();
	var full_weight = Number(package_weight) + Number(total_weight);

	jQuery('.total_weight_input').val(full_weight);
	jQuery('.total_weight_span').text(full_weight);
});

jQuery(document).on("change", ".PostItem_Weight", function () {

	var total_weight = 0;

	jQuery('.PostItem_row').each(function () {
		var PostItem_checkbox = jQuery(this).find('input[type="checkbox"]');
		if (PostItem_checkbox.prop("checked") == true) {
			total_weight += Number(jQuery(this).find('.PostItem_Weight').val());
		}
	});

	var package_weight = jQuery('.package_weight_input').val();
	//var total_weight = jQuery('.total_weight_org').val();
	var full_weight = Number(package_weight) + Number(total_weight);
	//alert(full_weight.toFixed(2));	
	jQuery('.total_weight_input').val(full_weight.toFixed(2));
	jQuery('.total_weight_span').text(full_weight.toFixed(2));

});

jQuery(document).on("change", ".PostItem_Value", function () {

	var total_value = 0;

	jQuery('.PostItem_row').each(function () {
		var PostItem_checkbox = jQuery(this).find('input[type="checkbox"]');
		if (PostItem_checkbox.prop("checked") == true) {
			total_value += Number(jQuery(this).find('.PostItem_Value').val());
			jQuery(this).find('.PostItem_Weight').addClass('input100');
		} else {
			jQuery(this).find('.PostItem_Weight').removeClass('input100');
		}
	});

	jQuery('.total_value_input').val(total_value);
	jQuery('.total_value_span').text(total_value);

});

jQuery(document).on("click", ".PostItem_product_select", function () {

	if (jQuery(this).prop("readonly") == true) {
		return false;
	}
	var total_value = 0;
	var total_weight = 0;

	jQuery('.PostItem_row').each(function () {
		var PostItem_checkbox = jQuery(this).find('input[type="checkbox"]');
		if (PostItem_checkbox.prop("checked") == true) {
			total_value += Number(jQuery(this).find('.PostItem_Value').val());
			total_weight += Number(jQuery(this).find('.PostItem_Weight').val());
			jQuery(this).find('.PostItem_Weight').addClass('input100');
		} else {
			jQuery(this).find('.PostItem_Weight').removeClass('input100');
		}
	});

	var package_weight = jQuery('.package_weight_input').val();
	//var total_weight = jQuery('.total_weight_org').val();
	var full_weight = Number(package_weight) + Number(total_weight);

	jQuery('.total_value_input').val(total_value);
	jQuery('.total_value_span').text(total_value);
	jQuery('.total_weight_input').val(full_weight);
	jQuery('.total_weight_span').text(full_weight);
});

jQuery(document).ready(function () {
	jQuery(".download_il_post_label").attr("download", '');
	var tracking_page_type = jQuery('#il_post_tracking_page_type').val();
	if (tracking_page_type == 'app') {
		jQuery('.single_select_page').show();
	} else {
		jQuery('.single_select_page').hide();
	}
});

jQuery(document).on("click", ".delete-il-post-tracking", function () {
	var tracking_id = jQuery(this).attr('rel');
	var order_id = jQuery(this).data('order');

	jQuery('#il-post-generate-label #tracking-item-' + tracking_id).block({
		message: null,
		overlayCSS: {
			background: '#fff',
			opacity: 0.6
		}
	});

	var data = {
		action: 'wc_ilpost_tracking_delete_item',
		order_id: order_id,
		tracking_id: tracking_id,
	};

	jQuery.post(ajaxurl, data, function (response) {
		jQuery('#il-post-generate-label #tracking-item-' + tracking_id).unblock();
		if (response != '-1') {
			jQuery('#il-post-generate-label #tracking-item-' + tracking_id).remove();
		}
		location.reload(true);
	});
	return false;
});
jQuery(document).on("click", ".print_bulk_shipping_label", function () {
	var order_id = jQuery(this).attr('href');
	order_id = order_id.substring(1, order_id.length);
	jQuery('.generate_label_popup').remove();
	jQuery(this).closest('.wc_actions').block({
		message: null,
		overlayCSS: {
			background: "#fff",
			opacity: .6
		}
	});
	jQuery("#il-post-generate-label").block({
		message: null,
		overlayCSS: {
			background: "#fff",
			opacity: .6
		}
	});
	var ajax_data = {
		action: 'il_post_update_access_token'
	};

	jQuery.ajax({
		url: ajaxurl,
		data: ajax_data,
		type: 'POST',
		dataType: "json",
		success: function (response) {
			if (response.status == 'success') {

				var ajax_data = {
					action: 'il_post_create_generate_label_popup',
					order_id: order_id,
				};

				jQuery.ajax({
					url: ajaxurl,
					data: ajax_data,
					type: 'POST',
					success: function (response) {
						jQuery("body").append(response);

						jQuery(".js-select2").each(function () {
							jQuery(this).select2({
								minimumResultsForSearch: 20,
								dropdownParent: jQuery(this).next('.dropDownSelect2')
							});

							jQuery(".js-select2").each(function () {
								jQuery(this).on('select2:close', function (e) {
									if (jQuery(this).val() == "Please chooses") {
										jQuery('.js-show-service').slideUp();
									}
									else {
										jQuery('.js-show-service').slideUp();
										jQuery('.js-show-service').slideDown();
									}
								});
							});
						});

						jQuery('.generate_label_popup').show();
						jQuery('.wc_actions').unblock();
						jQuery('#il-post-generate-label').unblock();
					},
					error: function (response) {
						alert('There are some issue with connection, Please check connection from settings.');
						jQuery('.wc_actions').unblock();
						jQuery('#il-post-generate-label').unblock();
					}
				});
			} else {
				alert('There are some issue with connection, Please check connection from settings.');
				jQuery('.wc_actions').unblock();
				jQuery('#il-post-generate-label').unblock();
			}
		},
		error: function (response) {
			alert('There are some issue with connection, Please check connection from settings.');
			jQuery('.wc_actions').unblock();
			jQuery('#il-post-generate-label').unblock();
		}
	});
});
jQuery(document).on("input", ".allow_decimal", function (evt) {
	var self = jQuery(this);
	self.val(self.val().replace(/[^0-9\.]/g, ''));
	if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) {
		evt.preventDefault();
	}
});