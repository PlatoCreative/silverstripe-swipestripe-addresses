jQuery(document).ready(function($){
	$.entwine('sws', function($){
		$('input.shipping-same-address').entwine({
			onmatch : function() {
				var self = this,
					form = this.closest('form');

				this.on('change', function(e) {
					self._copyAddress(e);
				});

				$('#address-shipping input[type=text], #address-shipping select', form).on('keyup blur', function(e){
					self._copyAddress(e);
				});

				this._super();
			},
			onunmatch: function() {
				this._super();
			},
			_copyAddress: function(e) {
				var form = $(this).closest('form'),
					formID = form.attr('ID');
				if($(this).is(':checked')) {
					$("#billing").find(".callout").removeClass("callout");
					$('#' + formID + ' #address-shipping input[type=text], #' + formID + ' #address-shipping select', form).each(function(){
						$('#' + $(this).attr('id').replace(/Shipping/i, 'Billing')).val($('#' + $(this).attr('id')).val());
					});
					// set session shipping variable
					$.post('checkout/setAddressID', {'BillingAddressID' : ''});
				}
				// Only clear fields if specifically unticking checkbox
				else if ($(e.currentTarget).attr('id') == $(this).attr('id')) {
					$('#' + formID + ' #address-shipping input[type=text], #' + formID + ' #address-shipping select', form).each(function(){
						$('#' + $(this).attr('id').replace(/Shipping/i, 'Billing')).val('');
							//.parent().parent().show(); // Removed and hidden with css instead
					});
				}
			}
		});
	});

	// order form submission
	$("#OrderForm_OrderForm").submit(function(e) {
		// check if a shipping and billing
		if($("#shipping .selected").length > 0){
			// Check if billing selected or same as shipping checked
			if($("#billing .callout").length > 0 || $('#BillToShippingAddress .checkbox').is(':checked')){
			} else {
				StopFormSending(e);
			}
		} else {
			StopFormSending(e);
		}
	});

	// Stop form from sending
	function StopFormSending(e){
		e.stopPropagation();
		e.preventDefault();
		if($('#alertMessagesText').length > 0){
			var offset = $('#shipping').offset();
			$("html, body").animate({scrollTop : (offset.top - 200)}, '500', 'swing', function(){
				$('#alertMessagesText').html("<h2>Address error:</h2><p>Please enter or select your shipping and billing address before proceeding.</p>");
				$('#alertMessages').foundation('reveal', 'open');
			});
		} else {
			alert("Please select your shipping and billing address.");
		}
		$(".Actions .loading img").css("display", "none");
		Window.setTimeout(function(){
			$("#OrderForm_OrderForm_action_process").val("Proceed to pay");
		}, 500);
	}

	function refreshEventListeners(){
		// manage shipping selections
		$("#shipping .selectable").unbind('click');
		$("#shipping .selectable").click(function(e){
			if($('.CheckoutPage').length > 0){
				// set session shipping variable
				$.post('checkout/setAddressID', {'ShippingAddressID' : $(this).attr('data-id')});

				$("#shipping .callout").removeClass("callout selected");
				$(this).parent().parent().addClass("callout selected");

				var addressID = $(this).data("id"),
					typeOfAddress = "shipping",
					dataquery = {addressID : addressID, type : typeOfAddress};
				modal = $(this);

				$.ajax({
					type: "GET",
					url : "account/getAddress",
					data: dataquery,
					dataType : 'json',
					success: function(data) {
						$('.address #address-shipping #ShippingFirstName input').val(data.FirstName);
						$('.address #address-shipping #ShippingSurname input').val(data.Surname);
						$('.address #address-shipping #ShippingCompany input').val(data.Company);
						$('.address #address-shipping #ShippingAddress input').val(data.Address);
						$('.address #address-shipping #ShippingAddressLine2 input').val(data.AddressLine2);
						$('.address #address-shipping #ShippingCity input').val(data.City);
						$('.address #address-shipping #ShippingPostalCode input').val(data.PostalCode);
						$('.address #address-shipping #ShippingState input').val(data.State);
						$('.address #address-shipping #ShippingRegionCode select').val(data.RegionCode);
						//$('.address #address-shipping #ShippingCity input').val(data.City);
						$('.address #address-shipping #ShippingDefault input').prop('checked', (data.Default > 0 ? true : false));
						//update cart
						$('.order-form').entwine('sws').updateCart();

						// Update the billing address if same checkbox checked
						if($('#OrderForm_OrderForm_BillToShippingAddress').is(':checked')){
							$('#OrderForm_OrderForm_BillToShippingAddress')._copyAddress(e);
						}
					}
				});
			}
		});

		// manage billing selections
		$("#billing .selectable").unbind('click');
		$("#billing .selectable").click(function(){
			if($('.CheckoutPage').length > 0){
				$('#OrderForm_OrderForm_BillToShippingAddress').attr('checked', false);

				// set session shipping variable
				$.post('checkout/setAddressID', {'BillingAddressID' : $(this).attr('data-id')});

				$("#billing .callout").removeClass("callout");
				$(this).parent().parent().addClass("callout");

				var addressID = $(this).data("id"),
					typeOfAddress = "billing",
					dataquery = { addressID: addressID, type: typeOfAddress };
				modal = $(this);

				$.ajax({
					type: "GET",
					url : "account/getAddress",
					data: dataquery,
					dataType : 'json',
					success: function(data) {
						$('.address #address-billing #BillingFirstName input').val(data.FirstName);
						$('.address #address-billing #BillingSurname input').val(data.Surname);
						$('.address #address-billing #BillingCompany input').val(data.Company);
						$('.address #address-billing #BillingAddress input').val(data.Address);
						$('.address #address-billing #BillingAddressLine2 input').val(data.AddressLine2);
						$('.address #address-billing #BillingCity input').val(data.City);
						$('.address #address-billing #BillingPostalCode input').val(data.PostalCode);
						$('.address #address-billing #BillingState input').val(data.State);
						$('.address #address-billing #BillingDefault input').prop('checked', (data.Default > 0 ? true : false));
						//$('.address #address-billing #BillingCity input').val(data.City);
					}
				});
			}
		});

		// delete selected address
		$(".delete-address").unbind('click');
		$(".delete-address").click(function(){
			var cnfm = confirm("Are you sure you want to delete this address?");
			if (cnfm == true) {
				btn = $(this);

				var addressID = $(this).data("id"),
					typeOfAddress = $(this).data("type"),
					dataquery = { addressID: addressID, type: typeOfAddress };

				$.ajax({
					type: "POST",
					url : "account/deleteAddress",
					data : dataquery,
					success : function(data){
						// if success
						if(data == 1){
							btn.addClass('deleting').find("span").html("Deleting");
							setTimeout(function(){
								// after a second or two remove the deleted item from the DOM
								btn.closest( "li" ).remove();

								if($("ul#" + typeOfAddress + ' li').length <= 1){
									var newAddressElement = '<li id="no-' + typeOfAddress + '-address"><p class="panel address no-addresses">You do not currently have any ' + typeOfAddress + ' addresses saved.</p></li>';
									$("ul#" + typeOfAddress).prepend(newAddressElement);
								}
							}, 2000);
						} else {
							// silly animatation to show user something failed
							btn.addClass('deleting').find("span").html("Deleting");
							setTimeout(function(){
								btn.find("span")
									.animate({'left':(+10)+'px'},200)
									.animate({'left':(-10)+'px'},200)
									.animate({'left':(+10)+'px'},200)
									.animate({'left':(-10)+'px'},200)
									.animate({'left':(+0)+'px'},200);
									btn.find("span").html("REMOVE").css("background-color", "");
							}, 2000);

						}
					}
				});
			}
		});

		// get address details before edit
		$(".edit-address").unbind('click');
		$(".edit-address").click(function(){
			var addressesID = $(this).data("id"),
				typeOfAddress = $(this).data("type"),
				dataquery = {'addressID' : addressesID, 'type' : typeOfAddress};
			modal = $(this);

			if(typeOfAddress == "shipping"){
				$('#shippingAddressModal form').animate({opacity : 0}, 0);
			} else {
				$('#billingAddressModal form').animate({opacity : 0}, 0);
			}

			$.ajax({
				type: "GET",
				url : "account/getAddress",
				data: dataquery,
				dataType : 'json',
				success: function(data) {
					if(typeOfAddress == "shipping"){
						$('#shippingAddressModal #ShippingFirstName input').val(data.FirstName);
						$('#shippingAddressModal #ShippingSurname input').val(data.Surname);
						$('#shippingAddressModal #ShippingCompany input').val(data.Company);
						$('#shippingAddressModal #ShippingAddress input').val(data.Address);
						$('#shippingAddressModal #ShippingAddressLine2 input').val(data.AddressLine2);
						$('#shippingAddressModal #ShippingCity input').val(data.City);
						$('#shippingAddressModal #ShippingPostalCode input').val(data.PostalCode);
						$('#shippingAddressModal #ShippingState input').val(data.State);
						$('#shippingAddressModal #ShippingRegionCode select').val(data.RegionCode);
						$('#shippingAddressModal #ShippingDefault input').prop('checked', (data.Default > 0 ? true : false));
						//$('#shippingAddressModal #ShippingCity input').val(data.City);
						$("#shippingAddressModal form").attr("data-id", data.ID);

						$('#shippingAddressModal form').animate({opacity : 1}, 300);
					} else {
						$('#billingAddressModal #BillingFirstName input').val(data.FirstName);
						$('#billingAddressModal #BillingSurname input').val(data.Surname);
						$('#billingAddressModal #BillingCompany input').val(data.Company);
						$('#billingAddressModal #BillingAddress input').val(data.Address);
						$('#billingAddressModal #BillingAddressLine2 input').val(data.AddressLine2);
						$('#billingAddressModal #BillingCity input').val(data.City);
						$('#billingAddressModal #BillingPostalCode input').val(data.PostalCode);
						$('#billingAddressModal #BillingState input').val(data.State);
						$('#billingAddressModal #BillingDefault input').prop('checked', (data.Default > 0 ? true : false));
						//$('#billingAddressModal #BillingCity input').val(data.City);
						$("#billingAddressModal form").attr("data-id", data.ID);

						$('#billingAddressModal form').animate({opacity : 1}, 300);
					}
				}
			});
		});
	}
	refreshEventListeners();

	// edit address
	$("#shippingAddressModal form, #billingAddressModal form").submit(function(e){
		e.preventDefault();

		var dataString = $(this).serializeArray(),
			addressID = $(this).data("id"),
			typeOfAddress = $(this).data("type");

		dataString.push({ name: "type", value: typeOfAddress });
		dataString.push({ name: "addressID", value: addressID });

		$.ajax({
			type: "POST",
			url : "account/editAddressAction",
			data: dataString,
			dataType : 'json',
			success: function(data) {
				// make the new element
				var newAddressContent = data.Address + '<br>' + data.City;

				// add new address to the DOM so no reload is required.
				if(typeOfAddress == "shipping"){
					var elm = $("ul#shipping").find("#address-" + data.ID).first();
					if(elm.hasClass('account-addr')){
						elm.find(".adress-inner").html(newAddressContent);
					} else {
						elm.find(".panel .selectable").html(newAddressContent);
					}
				} else {
					var elm = $("ul#billing").find("#address-" + data.ID).first();
					if(elm.hasClass('account-addr')){
						elm.find(".adress-inner").html(newAddressContent);
					} else {
						elm.find(".panel .selectable").html(newAddressContent);
					}
				}

				refreshEventListeners();
				$('.reveal-modal').foundation('reveal', 'close');

				// Update the form
				if(typeOfAddress == 'shipping'){
					$("#" + typeOfAddress + " .callout .selectable").click();
				}
			}
		});

		return false;
	});

	// add address
	$("#addShippingAddress form, #addBillingAddress form").submit(function (e) {
		e.preventDefault();

		var dataString = $(this).serializeArray(),
			form = $(this),
			typeOfAddress = $(this).data("type");
		dataString.push({ name: "type", value: typeOfAddress });

		$.ajax({
			type: "POST",
			url : "account/addAddress",
			data: dataString,
			dataType : 'json',
			success: function(data) {
				if($('.account-shipping-block').length > 0){
					var newAddressElement = '<li id="address-' + data.ID + '" class="account-addr"><div class="panel address"><p><span class="adress-inner">' + data.Address + '<br>' + data.City + '</span><br><a href="javascript:;" data-id="' + data.ID + '" class="edit-address" data-reveal-id="' + typeOfAddress + 'AddressModal" data-type="' + typeOfAddress + '"><span class="label success">EDIT</span></a> <a href="javascript:;" data-id="' + data.ID + '" data-type="' + typeOfAddress + '" class="delete-address"><span class="label warning">DELETE</span></a></p></div></li>';
				} else {
					var newAddressElement = '<li id="address-' + data.ID + '"><div class="panel address"><p><a href="javascript:;" data-id="' + data.ID + '" class="selectable">' + data.Address + '<br>' + data.City + '</a><br><br><a href="javascript:;" data-id="' + data.ID + '" class="edit-address" data-reveal-id="' + typeOfAddress + 'AddressModal" data-type="' + typeOfAddress + '"><span class="label success">EDIT</span></a> <a href="javascript:;" data-id="' + data.ID + '" data-type="' + typeOfAddress + '" class="delete-address"><span class="label warning">DELETE</span></a></p></div></li>';
				}

				// add new address to the DOM so no reload is required.
				if(typeOfAddress == "shipping"){
					if($('#no-shipping-address').length > 0){
						$('#no-shipping-address').remove();
					}
					$("ul#shipping").prepend(newAddressElement);
				} else {
					if($('#no-billing-address').length > 0){
						$('#no-billing-address').remove();
					}
					$("ul#billing").prepend(newAddressElement);
				}
				refreshEventListeners();
				$('a.selectable[data-id="' + data.ID + '"]').click();

				$('.reveal-modal').foundation('reveal', 'close');

				// Clear the form fields
				form.find('input').each(function(){
					if($(this).hasClass('text')){
						$(this).val('');
					} else if ($(this).hasClass('checkbox')){
						$(this).prop( "checked", false );
					}
				});
			}
		});

		return false;
	});
});
