<% if IncludeFormTag %>
<form $FormAttributes>
<% end_if %>

	<% if Message %>
		<p id="{$FormName}_error" class="message $MessageType">$Message</p>
	<% else %>
		<p id="{$FormName}_error" class="message $MessageType" style="display: none"></p>
	<% end_if %>

	<fieldset>

		<% if CurrentMember %>
		<hr />
		<div class="row">

			<style type="text/css">
				.panel, .panel.callout{
					padding: 15px;
					margin-bottom: 0;
				}
				.panel p a{
					color: #000;
				}
				.panel.address p{
				 	font-size: 11px;
					font-weight: bold;
					margin-bottom: 0;
				}
				.add-new{
					text-align: center;
					display: inline-block;
				}
				.add-new span{
					font-size: 20px;
					font-weight: bold;
				}
			</style>


			<div class="large-6 columns">
				<h3>Your Shipping Addresses</h3>
				<br />
				<% with CurrentMember %>
					<ul id="shipping" class="small-block-grid-1 medium-block-grid-2 large-block-grid-3">
					<% if ShippingAddresses %>
						<% loop ShippingAddresses %>
							<li id="address-$ID" class="checkout-addr">
								<div class="panel address <% if Top.getSessionAddress('shipping').ID == $ID %>callout selected<% end_if %>">
									<p>
										<a href="javascript:;" data-id="{$ID}" class="selectable">
											$Address<br />
											$City
										</a><br /><br />
										<a href="javascript:;" class="edit-address" data-id="{$ID}" data-type="shipping" data-reveal-id="shippingAddressModal"><span class="label success">EDIT</span></a>
										<a href="javascript:;" data-id="{$ID}" data-type="shipping" class="delete-address"><span class="label warning">DELETE</span></a>
									</p>
								</div>
							</li>
						<% end_loop %>
					<% else %>
						<li id="no-shipping-address">
							<p class="panel address no-addresses">
								You do not currently have any shipping addresses saved.
							</p>
						</li>
					<% end_if %>
					<li><a class="panel address add-new" data-reveal-id="addShippingAddress"><span>+</span></a></li>
					</ul>
				<% end_with %>
			</div>

			<div class="large-6 columns">
				<h3>Your Billing Addresses</h3>
				<br />
				<% with CurrentMember %>
					<ul id="billing" class="small-block-grid-1 medium-block-grid-2 large-block-grid-3">
					<% if BillingAddresses %>
						<% loop BillingAddresses %>
							<li id="address-$ID" class="checkout-addr">
								<div class="panel address <% if Top.getSessionAddress('billing').ID == $ID %>callout<% end_if %>">
									<p>
										<a href="javascript:;" data-id="{$ID}" class="selectable">
											$Address<br />
											$City
										</a><br /><br />
										<a href="javascript:;" class="edit-address" data-id="{$ID}" data-reveal-id="billingAddressModal"><span class="label success">EDIT</span></a>
										<a href="javascript:;" class="delete-address" data-id="{$ID}" data-type="billing"><span class="label warning">DELETE</span></a>
									</p>
								</div>
							</li>
						<% end_loop %>
					<% else %>
						<li id="no-billing-address">
							<p class="panel address no-addresses">
								You do not currently have any billing addresses saved.
							</p>
						</li>
					<% end_if %>
					<li><a class="panel address add-new" data-reveal-id="addBillingAddress"><span>+</span></a></li>
					</ul>
				<% end_with %>
			</div>

		</div>

		<hr />
		<% end_if %>


		<% if PersonalDetailsFields %>
			<section class="personal-details">
				<% loop PersonalDetailsFields %>
					$FieldHolder
				<% end_loop %>
			</section>

			<hr />
		<% end_if %>

		<div class="row customer-addresses">

			<div class="large-6 columns">
				<section class="address">
					<div id="address-shipping">
						<% loop ShippingAddressFields %>
							$FieldHolder
						<% end_loop %>
					</div>
				</section>
			</div>

			<div class="large-6 columns">
				<section class="address">
					<div id="address-billing">
						<% loop BillingAddressFields %>
							$FieldHolder
						<% end_loop %>
					</div>
				</section>
			</div>

			<hr />

		</div>



		<section class="order-details">
			<h3><% _t('CheckoutForm.YOUR_ORDER', 'Your Order') %></h3>

			<div id="cart-loading-js" class="cart-loading">
				<div>
					<h4>Loading...</h4>
				</div>
			</div>

			<% include OrderFormCart %>
		</section>


		<section class="notes">
			<% loop NotesFields %>
				$FieldHolder
			<% end_loop %>
		</section>

		<hr />

		<section class="payment-details">
			<% loop PaymentFields %>
				$FieldHolder
			<% end_loop %>
		</section>

		<div class="clear" />
	</fieldset>

	<% if Cart.Items %>
		<% if Actions %>
		<div class="Actions">
			<div class="loading">
				<img src="swipestripe/images/loading.gif" />
			</div>
			<% loop Actions %>
				$Field
			<% end_loop %>
		</div>
		<% end_if %>
	<% end_if %>

<% if IncludeFormTag %>
</form>
<% end_if %>


<% include Modals %>
