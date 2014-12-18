<?php
/*
*	ShippingCheckoutPage extends CheckoutPage_Controller
*/
class ShippingCheckoutPage_Controller extends DataExtension {
	private static $allowed_actions = array (
		'setAddressID'
	);
  	
	public function setAddressID($request){
		$data = $request->postVars();
		if(isset($data['ShippingAddressID'])){
			Session::set('ShippingAddress', $data['ShippingAddressID']);
		}
		return;
	}
}