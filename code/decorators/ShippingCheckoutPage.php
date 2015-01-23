<?php
/*
*	ShippingCheckoutPage_Controller extends CheckoutPage_Controller
*/
class ShippingCheckoutPage_Controller extends DataExtension {
	public function onAfterInit(){
		Requirements::CSS('swipestripe-addresses/css/layout.css');
	}
	
	private static $allowed_actions = array (
		'setAddressID',
		'getAddressIDs'
	);
  	
	public function setAddressID($request){
		$data = $request->postVars();
		
		if(isset($data['ShippingAddressID'])){
			Session::set('ShippingAddressID', $data['ShippingAddressID']);
		}
		
		if(isset($data['BillingAddressID'])){
			Session::set('BillingAddressID', $data['BillingAddressID']);
		}
		
		return;
	}
	
	public function getAddressIDs($request){
		$addresses = array(
			'ShippingID' => Session::get('ShippingAddressID') ? Session::get('ShippingAddressID') : '',
			'BillingID' => Session::get('BillingAddressID') ? Session::get('BillingAddressID') : ''
		);
		
		return Convert::array2json($addresses);	
	}
}