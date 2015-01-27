<div id="shippingAddressModal" class="reveal-modal" data-reveal>
  <form mehtod="post" action="/account/edtAddressAction" data-id data-type="shipping">
    <div class="row">
      <div class="large-12 columns">
        <% loop ShippingAddressFields %>
        	$FieldHolder
        <% end_loop %>
      </div>
    </div>
    <div class="row">
      <div class="large-12 text-right columns">
        <input type="submit" class="button tiny radius" value="Save" />
      </div>
    </div>
  </form>
  <a class="close-reveal-modal">&#215;</a>
</div>

<div id="billingAddressModal" class="reveal-modal" data-reveal>
  <form mehtod="post" action="/account/edtAddressAction" data-id data-type="billing">
    <div class="row">
      <div class="large-12 columns">
        <% loop BillingAddressFields %>
        	$FieldHolder
        <% end_loop %>
      </div>
    </div>
    <div class="row">
      <div class="large-12 text-right columns">
        <input type="submit" class="button tiny radius" value="Save" />
      </div>
    </div>
  </form>
  <a class="close-reveal-modal">&#215;</a>
</div>


<div id="addShippingAddress" class="reveal-modal" data-reveal>
  <form mehtod="post" action="/account/addAddress" data-type="shipping">
    <div class="row">
      <div class="large-12 columns">
        <% loop ShippingAddressFields %>
        	$FieldHolder
        <% end_loop %>
      </div>
    </div>
    <div class="row">
      <div class="large-12 text-right columns">
        <input type="submit" class="submit button tiny radius" value="Save" />
      </div>
    </div>
  </form>
  <a class="close-reveal-modal">&#215;</a>
</div>

<div id="addBillingAddress" class="reveal-modal" data-reveal>
  <form mehtod="post" action="/account/addAddress" data-type="billing">
    <div class="row">
      <div class="large-12 columns">
        <% loop BillingAddressFields %>
        	$FieldHolder
        <% end_loop %>
      </div>
    </div>
    <div class="row">
      <div class="large-12 text-right columns">
        <input type="submit" class="button tiny radius" value="Save" />
      </div>
    </div>
  </form>
  <a class="close-reveal-modal">&#215;</a>
</div>

<div id="alertMessages" class="reveal-modal" data-reveal>
    <div class="row">
      <div class="large-12 columns" id="alertMessagesText">
        <h2></h2>
		<p></p>
      </div>
    </div>
  <a class="close-reveal-modal">&#215;</a>
</div>