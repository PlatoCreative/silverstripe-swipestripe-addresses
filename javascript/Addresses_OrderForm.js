;(function($) { 
	$.entwine('sws', function($){

		$('input.shipping-same-address').entwine({

			onmatch : function() {
				var self = this;
				var form = this.closest('form');

				this.on('change', function(e) {
					self._copyAddress(e);
				});
				
				$('#address-shipping input[type=text], #address-shipping select', form).on('keyup blur', function(e){
					self._copyAddress(e);
				});

				this._copyAddress();

				this._super();
			},

			onunmatch: function() {
				this._super();
			},
			
			_copyAddress: function(e) {
				var form = this.closest('form');

				if (this.is(':checked')) {
					$('#address-shipping input[type=text], #address-shipping select', form).each(function(){
						$('#' + $(this).attr('id').replace(/Shipping/i, 'Billing'))
							.val($('#' + $(this).attr('id')).val())
							.parent().parent().hide();
					});
				}
				//Only clear fields if specifically unticking checkbox
				else if ($(e.currentTarget).attr('id') == this.attr('id')) {
					$('#address-shipping input[type=text], #address-shipping select', form).each(function(){
						$('#' + $(this).attr('id').replace(/Shipping/i, 'Billing'))
							.val('')
							.parent().parent().show();
					});
				}
			}
		});

	});
	
})(jQuery);
	
;(function($) { 
	
	// order form submission
	$("#OrderForm_OrderForm").submit(function() {
		// check if a shipping and billing
		if($("ul#shipping").find("li a.selectable").parent().parent().hasClass("selected")){
			return true;
		}else{
			alert("Please select your shipping and billing address.")
			$(".Actions .loading img").css("display", "none");
			$(".Actions input#OrderForm_OrderForm_action_process").val("Proceed to pay");
			return false;
		}
	});
	
	refreshEventListeners();
	function refreshEventListeners(){
		
		// hide normal form but keep it on the page
		$(".customer-addresses").hide();
		
		// manage shipping selections
		$("#shipping .selectable").unbind('click');
		$("#shipping .selectable").click(function(){
			$("#shipping").find( ".callout" ).removeClass("callout selected");
			$(this).parent().parent().addClass("callout selected");
			
			var addressID = $(this).data("id");
			var typeOfAddress = "shipping";
			var dataquery = { addressID: addressID, type: typeOfAddress };
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
					//$('.address #address-shipping #ShippingCity input').val(data.City);	
									
				}
			});	
			
			
		});
		
		// manage billing selections
		$("#billing .selectable").unbind('click');
		$("#billing .selectable").click(function(){
			$("#billing").find( ".callout" ).removeClass("callout");
			$(this).parent().parent().addClass("callout");
			
			var addressID = $(this).data("id");
			var typeOfAddress = "shipping";
			var dataquery = { addressID: addressID, type: typeOfAddress };
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
					//$('.address #address-billing #BillingCity input').val(data.City);	
					
				}
			});	
			
		});	
		
		// delete selected address
		$(".delete-address").unbind('click');
		$(".delete-address").click(function(){
			var cnfm = confirm("Are you sure you want to delete this address?");
			if (cnfm == true) {
				btn = $(this);
				
				var addressID = $(this).data("id");
				var typeOfAddress = $(this).data("type");
				var dataquery = { addressID: addressID, type: typeOfAddress };
				
				$.ajax({
					type: "POST",
					url : "account/deleteAddress",
					data : dataquery,
					success : function(data){
						// if success
						if(data == 1){
							
							btn.find("span").html("DELETING").css("background-color", "red");						
							setTimeout(function(){
								// after a second or two remove the deleted item from the DOM
								btn.closest( "li" ).remove();
							}, 2000);
							
						}else{
							// silly animatation to show user something failed
							btn.find("span").html("DELETING").css("background-color", "red");					
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
			var addressID = $(this).data("id");
			var typeOfAddress = $(this).data("type");
			var dataquery = { addressID: addressID, type: typeOfAddress };
			modal = $(this);
			
			$.ajax({
				type: "GET",
				url : "account/getAddress",
				data: dataquery,
				dataType : 'json',
				success: function(data) {
					console.log(data);   
					if(typeOfAddress == "shipping"){
						$('#shippingAddressModal #ShippingFirstName input').val(data.FirstName);
						$('#shippingAddressModal #ShippingSurname input').val(data.Surname);
						$('#shippingAddressModal #ShippingCompany input').val(data.Company);
						$('#shippingAddressModal #ShippingAddress input').val(data.Address);
						$('#shippingAddressModal #ShippingAddressLine2 input').val(data.AddressLine2);
						$('#shippingAddressModal #ShippingCity input').val(data.City);
						$('#shippingAddressModal #ShippingPostalCode input').val(data.PostalCode);
						$('#shippingAddressModal #ShippingState input').val(data.State);
						//$('#shippingAddressModal #ShippingCity input').val(data.City);
						
						$("#shippingAddressModal form").data("id", addressID);
						
					}else{
						$('#billingAddressModal #BillingFirstName input').val(data.FirstName);
						$('#billingAddressModal #BillingSurname input').val(data.Surname);
						$('#billingAddressModal #BillingCompany input').val(data.Company);
						$('#billingAddressModal #BillingAddress input').val(data.Address);
						$('#billingAddressModal #BillingAddressLine2 input').val(data.AddressLine2);
						$('#billingAddressModal #BillingCity input').val(data.City);
						$('#billingAddressModal #BillingPostalCode input').val(data.PostalCode);
						$('#billingAddressModal #BillingState input').val(data.State);
						//$('#billingAddressModal #BillingCity input').val(data.City);
						
						$(this).data( $("#billingAddressModal form"), "id", addressID );
					}
										
				}
			});	
			
		});	
			
	}
	
	// edit address
	$("#shippingAddressModal form, #billingAddressModal form").submit(function (e) {
		e.preventDefault();
		
		var dataString = $(this).serializeArray();
		var addressID = $(this).data("id");
		var typeOfAddress = $(this).data("type");
		
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
					$("ul#shipping").find("li .panel .selectable").html(newAddressContent);
				}else{
					$("ul#billing").find("li .panel .selectable").html(newAddressContent);
				}
				
				refreshEventListeners();
				
				
				$('.reveal-modal').foundation('reveal', 'close');
				
				
			}
		});
		
		return false;
		
	});	
	

	// add address
	$("#addShippingAddress form").submit(function (e) {
		e.preventDefault();
		
		var dataString = $(this).serializeArray();
		
		var typeOfAddress = $(this).data("type");
		dataString.push({ name: "type", value: typeOfAddress });
		
		$.ajax({
			type: "POST",
			url : "account/addAddress",
			data: dataString,
			dataType : 'json',
			success: function(data) {

				var newAddressElement = '<li><div class="panel address"><p><a href="javascript:;" data-id="' + data.ID + '" class="selectable">' + data.Address + '<br>' + data.City + '</a><br><br><a href="javascript:;" data-id="' + data.ID + '" data-reveal-id="shippingAddressModal"><span class="label success">EDIT</span></a> <a href="javascript:;" data-id="' + data.ID + '" data-type="shipping" class="delete-address"><span class="label warning">DELETE</span></a></p></div></li>';
				
				// add new address to the DOM so no reload is required.
				if(typeOfAddress == "shipping"){
					$("ul#shipping").prepend(newAddressElement);
				}else{
					$("ul#billing").prepend(newAddressElement);
				}
				refreshEventListeners();
				
				$('.reveal-modal').foundation('reveal', 'close');
				
				
			}
		});
		
		return false;
		
	});	

})(jQuery);
	
	