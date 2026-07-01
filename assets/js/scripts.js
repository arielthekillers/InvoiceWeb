var BASE_URL = 'http://localhost/invoice/';

$(document).ready(function() {

	// Invoice Type
	$('#invoice_type').change(function() {
		var invoiceType = $("#invoice_type option:selected").text();
		$(".invoice_type").text(invoiceType);
	});



	// password strength
	if ($('#password').length && typeof $.fn.pwstrength !== 'undefined') {
		var options = {
	        onLoad: function () {
	            $('#messages').text('Start typing password');
	        },
	        onKeyUp: function (evt) {
	            $(evt.target).pwstrength("outputErrorList");
	        }
	    };
	    $('#password').pwstrength(options);
	}

	// add user
	$("#action_add_user").click(function(e) {
		e.preventDefault();
	    actionAddUser();
	});

	// update customer
	$(document).on('click', "#action_update_user", function(e) {
		e.preventDefault();
		updateUser();
	});

	// delete user
	$(document).on('click', ".delete-user", function(e) {
        e.preventDefault();

        var userId = 'action=delete_user&delete='+ $(this).attr('data-user-id'); //build a post data structure
        var user = $(this);

	    $('#delete_user').modal({ backdrop: 'static', keyboard: false }).one('click', '#delete', function() {
			deleteUser(userId);
			$(user).closest('tr').remove();
        });
   	});

   	// delete customer
	$(document).on('click', ".delete-customer", function(e) {
        e.preventDefault();

        var userId = 'action=delete_customer&delete='+ $(this).attr('data-customer-id'); //build a post data structure
        var user = $(this);

	    $('#delete_customer').modal({ backdrop: 'static', keyboard: false }).one('click', '#delete', function() {
			deleteCustomer(userId);
			$(user).closest('tr').remove();
        });
   	});

	// update customer
	$(document).on('click', "#action_update_customer", function(e) {
		e.preventDefault();
		updateCustomer();
	});

	// login form
	$(document).bind('keypress', function(e) {
		e.preventDefault;
		
        if(e.keyCode==13){
            $('#btn-login').trigger('click');
        }
    });

	$(document).on('click','#btn-login', function(e){
		e.preventDefault;
		actionLogin();
	});



	// email invoice
	$(document).on('click', ".email-invoice", function(e) {
        e.preventDefault();

        var invoiceId = 'action=email_invoice&id='+$(this).attr('data-invoice-id')+'&email='+$(this).attr('data-email')+'&invoice_type='+$(this).attr('data-invoice-type')+'&custom_email='+$(this).attr('data-custom-email'); //build a post data structure
		emailInvoice(invoiceId);
   	});

	// delete invoice
	$(document).on('click', ".delete-invoice", function(e) {
        e.preventDefault();

        var invoiceId = 'action=delete_invoice&delete='+ $(this).attr('data-invoice-id'); //build a post data structure
        var invoice = $(this);

	    $('#delete_invoice').modal({ backdrop: 'static', keyboard: false }).one('click', '#delete', function() {
			deleteInvoice(invoiceId);
			$(invoice).closest('tr').remove();
        });
   	});

	// delete invoice from detail page
	$(document).on('click', ".delete-invoice-detail", function(e) {
        e.preventDefault();
        var invoiceIdStr = $(this).attr('data-invoice-id');
        var invoiceId = 'action=delete_invoice&delete='+ invoiceIdStr; 

	    $('#delete_invoice_detail').modal({ backdrop: 'static', keyboard: false }).one('click', '#delete_detail_confirm', function() {
			deleteInvoiceAndRedirect(invoiceId);
        });
   	});

	// create customer
	$("#action_create_customer").click(function(e) {
		e.preventDefault();
	    actionCreateCustomer();
	});

	$(document).on('click', ".item-select", function(e) {

   		e.preventDefault;

   		var product = $(this);

   		$('#insert').modal({ backdrop: 'static', keyboard: false }).one('click', '#selected', function(e) {

		    var itemText = $('#insert').find("option:selected").text();
		    var itemValue = $('#insert').find("option:selected").val();

		    $(product).closest('tr').find('.invoice_product').val(itemText);
		    $(product).closest('tr').find('.invoice_product_price').val(itemValue);

		    updateTotals('.calculate');
        	calculateTotal();

   		});

   		return false;

   	});

   	$(document).on('click', ".select-customer", function(e) {

   		e.preventDefault;

   		var customer = $(this);

   		$('#insert_customer').modal({ backdrop: 'static', keyboard: false });

   		return false;

   	});

   	$(document).on('click', ".customer-select", function(e) {

		    var customer_name = $(this).attr('data-customer-name');
		    var customer_email = $(this).attr('data-customer-email');
		    var customer_phone = $(this).attr('data-customer-phone');

		    var customer_address_1 = $(this).attr('data-customer-address-1');
		    var customer_address_2 = $(this).attr('data-customer-address-2');
		    var customer_town = $(this).attr('data-customer-town');
		    var customer_county = $(this).attr('data-customer-county');
		    var customer_postcode = $(this).attr('data-customer-postcode');


		    $('#customer_name').val(customer_name);
		    $('#customer_email').val(customer_email);
		    $('#customer_phone').val(customer_phone);

		    $('#customer_address_1').val(customer_address_1);
		    $('#customer_address_2').val(customer_address_2);
		    $('#customer_town').val(customer_town);
		    $('#customer_county').val(customer_county);
		    $('#customer_postcode').val(customer_postcode);



		    $('#insert_customer').modal('hide');

	});

	// create invoice
	$("#action_create_invoice").click(function(e) {
		e.preventDefault();
	    actionCreateInvoice();
	});

	// update invoice
	$(document).on('click', "#action_edit_invoice", function(e) {
		e.preventDefault();
		updateInvoice();
	});

	// ========== Date Picker ===========
	if ($('#invoice_date_raw').length) {
		$('#invoice_date_raw').on('change', function() {
			var rawVal = $(this).val(); // YYYY-MM-DD
			if (rawVal) {
				var parts = rawVal.split('-');
				if (parts.length === 3) {
					var formatted = parts[2] + '/' + parts[1] + '/' + parts[0];
					$('#invoice_date_formatted').val(formatted);
					
					// If on create page, fetch next invoice number
					if ($('#action_create_invoice').length) {
						getNextInvoiceNumber(formatted);
					}
				}
			} else {
				$('#invoice_date_formatted').val('');
			}
		});

		// Trigger change on load if value exists to populate hidden field
		if ($('#invoice_date_raw').val()) {
			$('#invoice_date_raw').trigger('change');
		}
	}

    // remove product row
    $('#invoice_table').on('click', ".delete-row", function(e) {
    	e.preventDefault();
       	$(this).closest('tr').remove();
        calculateTotal();
    });

    // add new product row - creates a fresh empty row with FOC checkbox
    $(".add-row").click(function(e) {
        e.preventDefault();
        var newRow = '<tr>' +
            '<td><div class="d-flex align-items-center mb-2">' +
            '<a href="#" class="btn btn-danger btn-sm delete-row me-2"><i class="bi bi-x-lg"></i></a>' +
            '<textarea class="form-control invoice_product" name="invoice_product[]" placeholder="Masukkan deskripsi pekerjaan/jasa" rows="1" style="resize: vertical; min-height: 38px; height: auto;"></textarea>' +
            '</div>' +
            '<div class="ps-5 sub-items-wrapper">' +
            '<input type="hidden" class="invoice_product_desc_hidden" name="invoice_product_desc[]" value="">' +
            '<div class="sub-items-list"></div>' +
            '<button type="button" class="btn btn-sm btn-outline-secondary add-sub-item mt-1"><i class="bi bi-plus"></i> Tambah Sub Item</button>' +
            '</div></td>' +
            '<td><div class="input-group">' +
            '<span class="input-group-text">Rp</span>' +
            '<input type="number" class="form-control calculate invoice_product_price required text-end" name="invoice_product_price[]" placeholder="0" min="0">' +
            '</div></td>' +
            '<td class="text-center">' +
            '<div class="form-check d-flex justify-content-center align-items-center gap-1">' +
            '<input type="hidden" name="invoice_product_foc[]" value="0" class="foc-hidden">' +
            '<input type="checkbox" class="form-check-input foc-checkbox" title="Gratis / Free of Charge">' +
            '<label class="form-check-label small text-muted">Gratis</label>' +
            '</div></td>' +
            '</tr>';
        $('#invoice_table tbody').append(newRow);
    });
    
    calculateTotal();
    
    // Recalculate when any price input changes
    $('#invoice_table').on('input', '.calculate', function () {
	    calculateTotal();
	});

	// FOC checkbox per item: when checked show strikethrough, when unchecked restore
	$(document).on('change', '.foc-checkbox', function() {
		var $row = $(this).closest('tr');
		var $priceInput = $row.find('.invoice_product_price');
		var $hiddenFoc  = $row.find('.foc-hidden');
		if ($(this).is(':checked')) {
			// Save original price then zero it visually
			$priceInput.attr('data-original-price', $priceInput.val());
			$priceInput.closest('.input-group').css('opacity', '0.4');
			if ($hiddenFoc.length) $hiddenFoc.val('1');
		} else {
			$priceInput.closest('.input-group').css('opacity', '1');
			if ($hiddenFoc.length) $hiddenFoc.val('0');
		}
		calculateTotal();
	});

	// Sub Items Logic
	$(document).on('click', '.add-sub-item', function(e) {
		e.preventDefault();
		var wrapper = $(this).closest('.sub-items-wrapper');
		var list = wrapper.find('.sub-items-list');
		var subItemRow = '<div class="sub-item-row d-flex align-items-center mb-1">' +
						 '<input type="text" class="form-control form-control-sm sub-item-input" placeholder="Sub item">' +
						 '<button type="button" class="btn btn-sm btn-outline-danger ms-1 remove-sub-item"><i class="bi bi-x"></i></button>' +
						 '</div>';
		list.append(subItemRow);
	});

	$(document).on('click', '.remove-sub-item', function(e) {
		e.preventDefault();
		var wrapper = $(this).closest('.sub-items-wrapper');
		$(this).closest('.sub-item-row').remove();
		syncSubItems(wrapper);
	});

	$(document).on('input', '.sub-item-input', function() {
		var wrapper = $(this).closest('.sub-items-wrapper');
		syncSubItems(wrapper);
	});

	function syncSubItems(wrapper) {
		var subItems = [];
		wrapper.find('.sub-item-input').each(function() {
			var val = $(this).val().trim();
			if (val !== "") {
				subItems.push(val);
			}
		});
		wrapper.find('.invoice_product_desc_hidden').val(subItems.join("\n"));
	}
	
	// Sync all sub-items on form submit
	$('#create_invoice, #update_invoice').on('submit', function() {
		$('.sub-items-wrapper').each(function() {
			syncSubItems($(this));
		});
	});

	function updateTotals(elem) {
		// No longer needed as we don't have qty or discount per item
	}

	function calculateTotal() {
	    var grandTotal = 0;

	    $('#invoice_table tbody tr').each(function() {
	    	var $focCheck = $(this).find('.foc-checkbox');
	    	var isFoc = $focCheck.is(':checked');
	    	if (!isFoc) {
	        	var price = $(this).find('[name="invoice_product_price[]"]').val() || 0;
	            grandTotal += parseFloat(price);
	    	}
	    });

	    var finalTotal = parseFloat(grandTotal);

        // Format with thousand separator
        var formatted = Math.round(finalTotal).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        $('.invoice-total').text(formatted);
        $('#invoice_total').val(Math.round(finalTotal));
	}

	// AJAX: get next invoice number based on selected date
	function getNextInvoiceNumber(dateStr) {
		$.ajax({
			url: BASE_URL + 'includes/response.php',
			type: 'POST',
			data: {
				action: 'get_invoice_number',
				date: dateStr
			},
			dataType: 'json',
			success: function(data) {
				if (data.status === 'Success') {
					$('#invoice_id').val(data.invoice_number);
				} else {
					console.error('get_invoice_number failed:', data);
				}
			},
			error: function(xhr, status, error) {
				console.error('AJAX get_invoice_number error:', status, error, xhr.responseText);
			}
		});
	}

	function actionAddUser() {

		var errorCounter = validateForm();

		if (errorCounter > 0) {
		    $("#response").removeClass("alert-success").addClass("alert-warning").fadeIn();
		    $("#response .message").html("<strong>Error</strong>: It appear's you have forgotten to complete something!");
		    $("html, body").animate({ scrollTop: $('#response').offset().top }, 1000);
		} else {

			$(".required").parent().removeClass("has-error");

			var $btn = $("#action_add_user").button("loading");

			$.ajax({

				url: BASE_URL + 'includes/response.php',
				type: 'POST',
				data: $("#add_user").serialize(),
				dataType: 'json',
				success: function(data){
					$("#response .message").html("<strong>" + data.status + "</strong>: " + data.message);
					$("#response").removeClass("alert-warning").addClass("alert-success").fadeIn();
					$("html, body").animate({ scrollTop: $('#response').offset().top }, 1000);
					$btn.button("reset");
				},
				error: function(data){
					$("#response .message").html("<strong>" + data.status + "</strong>: " + data.message);
					$("#response").removeClass("alert-success").addClass("alert-warning").fadeIn();
					$("html, body").animate({ scrollTop: $('#response').offset().top }, 1000);
					$btn.button("reset");
				}

			});
		}

	}

	function actionCreateCustomer(){

		var errorCounter = validateForm();

		if (errorCounter > 0) {
		    $("#response").removeClass("alert-success").addClass("alert-warning").fadeIn();
		    $("#response .message").html("<strong>Error</strong>: It appear's you have forgotten to complete something!");
		    $("html, body").animate({ scrollTop: $('#response').offset().top }, 1000);
		} else {

			var $btn = $("#action_create_customer").button("loading");

			$(".required").parent().removeClass("has-error");

			$.ajax({

				url: BASE_URL + 'includes/response.php',
				type: 'POST',
				data: $("#create_customer").serialize(),
				dataType: 'json',
				success: function(data){
					$("#response .message").html("<strong>" + data.status + "</strong>: " + data.message);
					$("#response").removeClass("alert-warning").addClass("alert-success").fadeIn();
					$("html, body").animate({ scrollTop: $('#response').offset().top }, 1000);
					$("#create_customer").before().html("<a href='" + BASE_URL + "pages/customer-add.php' class='btn btn-primary'>Add New Customer</a>");
					$("#create_cuatomer").remove();
					$btn.button("reset");
				},
				error: function(data){
					$("#response .message").html("<strong>" + data.status + "</strong>: " + data.message);
					$("#response").removeClass("alert-success").addClass("alert-warning").fadeIn();
					$("html, body").animate({ scrollTop: $('#response').offset().top }, 1000);
					$btn.button("reset");
				} 

			});
		}

	}

	function actionCreateInvoice(){

		var errorCounter = validateForm();

		if (errorCounter > 0) {
		    $("#response").removeClass("alert-success").addClass("alert-warning").fadeIn();
		    $("#response .message").html("<strong>Error</strong>: It appear's you have forgotten to complete something!");
		    $("html, body").animate({ scrollTop: $('#response').offset().top }, 1000);
		} else {

			var $btn = $("#action_create_invoice").button("loading");

			$(".required").parent().removeClass("has-error");
			$("#create_invoice").find(':input:disabled').removeAttr('disabled');

			$.ajax({

				url: BASE_URL + 'includes/response.php',
				type: 'POST',
				data: $("#create_invoice").serialize(),
				dataType: 'json',
				success: function(data){
					$("#response .message").html("<strong>" + data.status + "</strong>: " + data.message);
					$("#response").removeClass("alert-warning").addClass("alert-success").fadeIn();
					$("html, body").animate({ scrollTop: $('#response').offset().top }, 1000);
					$("#create_invoice").before().html("<a href='" + BASE_URL + "pages/invoice-create.php' class='btn btn-primary'>Create new invoice</a>");
					$("#create_invoice").remove();
					$btn.button("reset");
				},
				error: function(data){
					$("#response .message").html("<strong>" + data.status + "</strong>: " + data.message);
					$("#response").removeClass("alert-success").addClass("alert-warning").fadeIn();
					$("html, body").animate({ scrollTop: $('#response').offset().top }, 1000);
					$btn.button("reset");
				} 

			});
		}

	}

   	function deleteUser(userId) {

        jQuery.ajax({

        	url: BASE_URL + 'includes/response.php',
            type: 'POST', 
            data: userId,
            dataType: 'json', 
            success: function(data){
				$("#response .message").html("<strong>" + data.status + "</strong>: " + data.message);
				$("#response").removeClass("alert-warning").addClass("alert-success").fadeIn();
				$("html, body").animate({ scrollTop: $('#response').offset().top }, 1000);
				$btn.button("reset");
			},
			error: function(data){
				$("#response .message").html("<strong>" + data.status + "</strong>: " + data.message);
				$("#response").removeClass("alert-success").addClass("alert-warning").fadeIn();
				$("html, body").animate({ scrollTop: $('#response').offset().top }, 1000);
				$btn.button("reset");
			} 
    	});

   	}

	function deleteCustomer(userId) {

        jQuery.ajax({

        	url: BASE_URL + 'includes/response.php',
            type: 'POST', 
            data: userId,
            dataType: 'json', 
            success: function(data){
				$("#response .message").html("<strong>" + data.status + "</strong>: " + data.message);
				$("#response").removeClass("alert-warning").addClass("alert-success").fadeIn();
				$("html, body").animate({ scrollTop: $('#response').offset().top }, 1000);
			},
			error: function(data){
				$("#response .message").html("<strong>" + data.status + "</strong>: " + data.message);
				$("#response").removeClass("alert-success").addClass("alert-warning").fadeIn();
				$("html, body").animate({ scrollTop: $('#response').offset().top }, 1000);
			} 
    	});

   	}

   	function emailInvoice(invoiceId) {

        jQuery.ajax({

        	url: BASE_URL + 'includes/response.php',
            type: 'POST', 
            data: invoiceId,
            dataType: 'json', 
            success: function(data){
				$("#response .message").html("<strong>" + data.status + "</strong>: " + data.message);
				$("#response").removeClass("alert-warning").addClass("alert-success").fadeIn();
				$("html, body").animate({ scrollTop: $('#response').offset().top }, 1000);
			},
			error: function(data){
				$("#response .message").html("<strong>" + data.status + "</strong>: " + data.message);
				$("#response").removeClass("alert-success").addClass("alert-warning").fadeIn();
				$("html, body").animate({ scrollTop: $('#response').offset().top }, 1000);
			} 
    	});

   	}

   	function deleteInvoice(invoiceId) {

        jQuery.ajax({

        	url: BASE_URL + 'includes/response.php',
            type: 'POST', 
            data: invoiceId,
            dataType: 'json', 
            success: function(data){
				$("#response .message").html("<strong>" + data.status + "</strong>: " + data.message);
				$("#response").removeClass("alert-warning").addClass("alert-success").fadeIn();
				$("html, body").animate({ scrollTop: $('#response').offset().top }, 1000);
				$btn.button("reset");
			},
			error: function(data){
				$("#response .message").html("<strong>" + data.status + "</strong>: " + data.message);
				$("#response").removeClass("alert-success").addClass("alert-warning").fadeIn();
				$("html, body").animate({ scrollTop: $('#response').offset().top }, 1000);
				$btn.button("reset");
			} 
    	});

   	}

    function deleteInvoiceAndRedirect(invoiceId) {
        jQuery.ajax({
        	url: BASE_URL + 'includes/response.php',
            type: 'POST', 
            data: invoiceId,
            dataType: 'json', 
            success: function(data){
				window.location.href = 'invoice-list.php';
			},
			error: function(data){
				alert("Error deleting invoice");
			} 
    	});
   	}

   	function updateUser() {

   		var $btn = $("#action_update_user").button("loading");

        jQuery.ajax({

        	url: BASE_URL + 'includes/response.php',
            type: 'POST', 
            data: $("#update_user").serialize(),
            dataType: 'json', 
            success: function(data){
				$("#response .message").html("<strong>" + data.status + "</strong>: " + data.message);
				$("#response").removeClass("alert-warning").addClass("alert-success").fadeIn();
				$("html, body").animate({ scrollTop: $('#response').offset().top }, 1000);
				$btn.button("reset");
			},
			error: function(data){
				$("#response .message").html("<strong>" + data.status + "</strong>: " + data.message);
				$("#response").removeClass("alert-success").addClass("alert-warning").fadeIn();
				$("html, body").animate({ scrollTop: $('#response').offset().top }, 1000);
				$btn.button("reset");
			} 
    	});

   	}

   	function updateCustomer() {

   		var $btn = $("#action_update_customer").button("loading");

        jQuery.ajax({

        	url: BASE_URL + 'includes/response.php',
            type: 'POST', 
            data: $("#update_customer").serialize(),
            dataType: 'json', 
            success: function(data){
				$("#response .message").html("<strong>" + data.status + "</strong>: " + data.message);
				$("#response").removeClass("alert-warning").addClass("alert-success").fadeIn();
				$("html, body").animate({ scrollTop: $('#response').offset().top }, 1000);
				$btn.button("reset");
			},
			error: function(data){
				$("#response .message").html("<strong>" + data.status + "</strong>: " + data.message);
				$("#response").removeClass("alert-success").addClass("alert-warning").fadeIn();
				$("html, body").animate({ scrollTop: $('#response').offset().top }, 1000);
				$btn.button("reset");
			} 
    	});

   	}

    	function updateInvoice() {

   		var $btn = $("#action_update_invoice").button("loading");
   		$("#update_invoice").find(':input:disabled').removeAttr('disabled');

        jQuery.ajax({

        	url: BASE_URL + 'includes/response.php',
            type: 'POST', 
            data: $("#update_invoice").serialize(),
            dataType: 'json', 
            success: function(data){
				var invoiceId = $("input[name='invoice_id']").val();
				window.location.href = 'invoice-detail.php?id=' + invoiceId;
			},
			error: function(data){
				$("#response .message").html("<strong>" + data.status + "</strong>: " + data.message);
				$("#response").removeClass("alert-success").addClass("alert-warning").fadeIn();
				$("html, body").animate({ scrollTop: $('#response').offset().top }, 1000);
				$btn.button("reset");
			} 
    	});

   	}



   	// login function
	function actionLogin() {

		var errorCounter = validateForm();

		if (errorCounter > 0) {

		    $("#response").removeClass("alert-success").addClass("alert-warning").fadeIn();
		    $("#response .message").html("<strong>Error</strong>: Missing something are we? check and try again!");
		    $("html, body").animate({ scrollTop: $('#response').offset().top }, 1000);

		} else {

			var $btn = $("#btn-login").button("loading");

			jQuery.ajax({
				url: BASE_URL + 'includes/response.php',
				type: "POST",
				data: $("#login_form").serialize(), // serializes the form's elements.
				dataType: 'json',
				success: function(data){
					$("#response .message").html("<strong>" + data.status + "</strong>: " + data.message);
					$("#response").removeClass("alert-warning").addClass("alert-success").fadeIn();
					$("html, body").animate({ scrollTop: $('#response').offset().top }, 1000);
					$btn.button("reset");

					window.location = BASE_URL + "pages/dashboard.php";
				},
				error: function(data){
					$("#response .message").html("<strong>" + data.status + "</strong>: " + data.message);
					$("#response").removeClass("alert-success").addClass("alert-warning").fadeIn();
					$("html, body").animate({ scrollTop: $('#response').offset().top }, 1000);
					$btn.button("reset");
				}

			});

		}
		
	}

   	function validateForm() {
	    // error handling
	    var errorCounter = 0;

	    $(".required").each(function(i, obj) {

	        if($(this).val() === ''){
	            $(this).parent().addClass("has-error");
	            errorCounter++;
	        } else{ 
	            $(this).parent().removeClass("has-error"); 
	        }


	    });

	    return errorCounter;
	}

});
