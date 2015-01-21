<?php
/*
*	ShippingCheckoutPage extends CheckoutPage_Controller
*/
class ShippingCheckoutPage_Controller extends DataExtension {
	public function onAfterInit(){
		Requirements::CSS('swipestripe-addresses/css/layout.css');
	}
	
	private static $allowed_actions = array (
		'setAddressID'
	);
  	
	public function setAddressID($request){
		$data = $request->postVars();
		if(isset($data['ShippingAddressID'])){
			Session::set('ShippingAddressID', $data['ShippingAddressID']);
		}
		return;
	}
}