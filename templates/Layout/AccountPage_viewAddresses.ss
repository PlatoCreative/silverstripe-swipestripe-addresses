<div class="account-page sws row">
	<div class="small-12 columns">
		<h1>Your Addresses</h1>
		
		<% with CurrentMember %>
			
			<div class="account-shipping-block">
				<h3>Your Shipping Addresses</h3>
				
				<ul id="shipping" class="small-block-grid-1 medium-block-grid-2 large-block-grid-3">
					<% if ShippingAddresses %>
						<% loop ShippingAddresses %>
							<li><div class="panel address"><p>
								<a href="javascript:;" data-id="{$ID}" class="selectable">
								$Address<br />
								$City</a><br /><br />
								<a href="javascript:;" class="edit-address" data-id="{$ID}" data-type="shipping" data-reveal-id="shippingAddressModal"><span class="label success">EDIT</span></a>
								<a href="javascript:;" data-id="{$ID}" data-type="shipping" class="delete-address"><span class="label warning">DELETE</span></a>
							</p></div></li>
						<% end_loop %>
					<% else %>
						<li>
							<p class="panel address no-addresses">
								You do not currently have any shipping addresses saved.
							</p>
						</li>
					<% end_if %>
					<li><a class="panel address add-new" data-reveal-id="addShippingAddress"><span>+</span></a></li>
				</ul>
			</div>
			
			<div class="account-shipping-block">
				<h3>Your Billing Addresses</h3>
				<% with CurrentMember %>
					<ul id="billing" class="small-block-grid-1 medium-block-grid-2 large-block-grid-3">
						<% if BillingAddresses %>
							<% loop BillingAddresses %>
								<li><div class="panel address"><p>
									<a href="javascript:;" data-id="{$ID}" class="selectable">
									$Address<br />
									$City</a><br /><br />
									<a href="javascript:;" class="edit-address" data-id="{$ID}" data-type="billing" data-reveal-id="billingAddressModal"><span class="label success">EDIT</span></a>
									<a href="javascript:;" class="delete-address" data-id="{$ID}" data-type="billing"><span class="label warning">DELETE</span></a>
								</p></div></li>
							<% end_loop %>
						<% else %>
							<li>
								<p class="panel address no-addresses">
									You do not currently have any billing addresses saved.
								</p>
							</li>
						<% end_if %>
						<li><a class="panel address add-new" data-reveal-id="addBillingAddress"><span>+</span></a></li>
					</ul>
				<% end_with %>
			</div>
			
			
		
		<% end_with %>
		
		<% with OrderForm %>
			<% include Modals %>
		<% end_with %>
		
	</div>
</div>