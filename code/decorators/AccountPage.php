<?php
/*
*	AccountPageExtension_Controller extends AccountPage_Controller
*/
class AccountPageExtension_Controller extends DataExtension {
  private static $allowed_actions = array (
    'deleteAddress',
    'addAddress',
    'getAddress',
    'editAddressAction',
	'viewAddresses',
	'OrderForm'
  );
  
  function OrderForm() {
	$member = Customer::currentUser() ? Customer::currentUser() : singleton('Customer');

	$form = OrderForm::create(
		$this, 
		'OrderForm'
	)->disableSecurityToken();

	//Populate fields the first time form is loaded
	$form->populateFields();

	return $form;
  }
  
  public function viewAddresses(){
	$this->owner->index();
	Requirements::CSS('swipestripe-addresses/css/layout.css');
	Requirements::javascript('swipestripe-addresses/javascript/Addresses_OrderForm.js');
	
	return $this->owner->render();
  }

  /**
  *  Delete the selected customers address from the database
  *  This function can only be called via ajax on the checkout page
  * @return Boolean Either returns True or False
  */
  public function deleteAddress(){

    if($_POST){
      // is there an ID to process or type?
      if(!isset($_POST['addressID']) || !isset($_POST['type'])) return $this->owner->httpError(403, 'No Address found or you are not allowed to delete that address.');

      // is there user logged in
      if(Member::currentUserID()){

        $addressShippingID = $_POST['addressID'];
        $type = $_POST['type'];

        // get the address that mataches the ID and current member
        if($type == "shipping"){
          $address = Address_Shipping::get()->filter(array("ID" => $addressShippingID, "MemberID" => Member::currentUserID()))->first();
        }elseif($type == "billing"){
          $address = Address_Billing::get()->filter(array("ID" => $addressShippingID, "MemberID" => Member::currentUserID()))->first();
        }

		if(Session::get('ShippingAddressID') == $address->ID){
			Session::clear('ShippingAddressID');
		}

        // delete it! Should really have soft delete here but Sivlerstripe doesn't provide that :(
        $address->delete();

        return true;

      } else {
        return false;
      }

    } else {
      return $this->owner->httpError(404, 'You must select an address to delete.');
    }

  }


  /**
  *
  *
  * @return
  */
  public function addAddress($request){

    if($_POST){

      // is there an ID to process or type?
      if(!isset($_POST['type'])) return $this->owner->httpError(403, 'No Address type passed you are not allowed to add an address.');

      // is there user logged in
      if(Member::currentUserID()){

        $type = $_POST['type'];
        $data = $_POST;

        // add the address based on the type
        if($type == "shipping"){

          $newAddress = Address_Shipping::create(array(
			  'MemberID' => Member::currentUserID(),
			  'FirstName' => $data['ShippingFirstName'],
			  'Surname' => $data['ShippingSurname'],
			  'Company' => $data['ShippingCompany'],
			  'Address' => $data['ShippingAddress'],
			  'AddressLine2' => $data['ShippingAddressLine2'],
			  'City' => $data['ShippingCity'],
			  'PostalCode' => $data['ShippingPostalCode'],
			  'State' => $data['ShippingState'],
			  //'CountryName' => $data['ShippingCountryName'],
			  'CountryCode' => $data['ShippingCountryCode'],
			  'RegionName' => (isset($data['ShippingRegionName'])) ? $data['ShippingRegionName'] : null,
			  'RegionCode' => (isset($data['ShippingRegionCode'])) ? $data['ShippingRegionCode'] : null,
          ));

          $newAddress->write(); // automatic escaping
		}elseif($type == "billing"){
          $newAddress = Address_Billing::create(array(
			  'MemberID' => Member::currentUserID(),
			  'FirstName' => $data['BillingFirstName'],
			  'Surname' => $data['BillingSurname'],
			  'Company' => $data['BillingCompany'],
			  'Address' => $data['BillingAddress'],
			  'AddressLine2' => $data['BillingAddressLine2'],
			  'City' => $data['BillingCity'],
			  'PostalCode' => $data['BillingPostalCode'],
			  'State' => $data['BillingState'],
			  //'CountryName' => $data['BillingCountryName'],
			  'CountryCode' => $data['BillingCountryCode'],
			  //'RegionName' => (isset($data['BillingRegionName'])) ? $data['ShippingRegionName'] : null,
			  'RegionCode' => (isset($data['BillingRegionCode'])) ? $data['ShippingRegionCode'] : null,
          ));

          $newAddress->write(); // automatic escaping

        }

        $returnAddress = array();
        $returnAddress['ID'] = $newAddress->ID;
        $returnAddress['FirstName'] = $newAddress->FirstName;
        $returnAddress['Surname'] = $newAddress->Surname;
        $returnAddress['Company'] = $newAddress->Company;
        $returnAddress['Address'] = $newAddress->Address;
        $returnAddress['AddressLine2'] = $newAddress->AddressLine2;
        $returnAddress['City'] = $newAddress->City;
        $returnAddress['PostalCode'] = $newAddress->PostalCode;
        $returnAddress['State'] = $newAddress->State;
        $returnAddress['CountryCode'] = $newAddress->CountryCode;
        $returnAddress['CountryName'] = $newAddress->CountryName;

        return Convert::array2json($returnAddress);
        
      } else {
        return false;
      }	
      
    } else {
      return $this->httpError(404, 'An address type must be passed.');
    }

  }

  function getAddress(){

    if(isset($_GET['addressID']) && isset($_GET['type'])){

      if(Member::currentUserID()){

        $addressID = $_GET['addressID'];
        $type = $_GET['type'];

        if($type == "shipping"){
          $address = Address_Shipping::get()->filter(array("ID" => $addressID, "MemberID" => Member::currentUserID()))->first()->toMap();
        }elseif($type == "billing"){
          $address = Address_Billing::get()->filter(array("ID" => $addressID, "MemberID" => Member::currentUserID()))->first()->toMap();
        }

        return Convert::array2json($address);

      }else{
        return $this->owner->httpError(500, 'An error has occured.');
      }

    }else{
      return $this->owner->httpError(404, 'An address type must be passed.');
    }

  }


  /**
  *
  *
  * @return
  */
  public function editAddressAction(){

    if($_POST){

      // is there an ID to process or type?
      if(!isset($_POST['type'])) return $this->owner->httpError(403, 'No Address type passed you are not allowed to edit an address.');

      // is there user logged in
      if(Member::currentUserID()){

        $type = $_POST['type'];
        $addressID = $_POST['addressID'];
        $data = $_POST;
		
        if($type == "shipping"){
          $address = Address_Shipping::get()->filter(array("ID" => $addressID, "MemberID" => Member::currentUserID()))->first();
        } elseif($type == "billing"){
          $address = Address_Billing::get()->filter(array("ID" => $addressID, "MemberID" => Member::currentUserID()))->first();
        }

        // add the address based on the type
        if($type == "shipping"){

          $address->FirstName = isset($data['ShippingFirstName']) ? $data['ShippingFirstName'] : null;
          $address->Surname = isset($data['ShippingSurname']) ? $data['ShippingSurname'] : null;
          $address->Company = isset($data['ShippingCompany']) ? $data['ShippingCompany'] : null;
          $address->Address = isset($data['ShippingAddress']) ? $data['ShippingAddress'] : null;
          $address->AddressLine2 = isset($data['ShippingAddressLine2']) ? $data['ShippingAddressLine2'] : null;
          $address->City = isset($data['ShippingCity']) ? $data['ShippingCity'] : null;
          $address->PostalCode = isset($data['ShippingPostalCode']) ? $data['ShippingPostalCode'] : null;
          $address->State = isset($data['ShippingState']) ? $data['ShippingState'] : null;
		  $address->RegionCode = isset($data['ShippingRegionCode']) ? $data['ShippingRegionCode'] : null;

          $address->write(); // automatic escaping

        } elseif($type == "billing"){

          $address->FirstName = isset($data['BillingFirstName']) ? $data['BillingFirstName'] : null;
          $address->Surname = isset($data['BillingSurname']) ? $data['BillingSurname'] : null;
          $address->Company = isset($data['BillingCompany']) ? $data['BillingCompany'] : null;
          $address->Address = isset($data['BillingAddress']) ? $data['BillingAddress'] : null;
          $address->AddressLine2 = isset($data['BillingAddressLine2']) ? $data['BillingAddressLine2'] : null;
          $address->City = isset($data['BillingCity']) ? $data['BillingCity'] : null;
          $address->PostalCode = isset($data['BillingPostalCode']) ? $data['BillingPostalCode'] : null;
          $address->State = isset($data['BillingState']) ? $data['BillingState'] : null;
		  $address->RegionCode = isset($data['BillingRegionCode']) ? $data['BillingRegionCode'] : null;

          $address->write(); // automatic escaping
        }

        $returnAddress = array(
        	'ID' => $address->ID,
        	'FirstName' => $address->FirstName,
        	'Surname' => $address->Surname,
        	'Company' => $address->Company,
        	'Address' => $address->Address,
        	'AddressLine2' => $address->AddressLine2,
        	'City' => $address->City,
        	'PostalCode' => $address->PostalCode,
        	'State' => $address->State,
        	'CountryCode' => $address->CountryCode,
        	'CountryName' => $address->CountryName,
			'RegionCode' => $address->RegionCode
		);

        return Convert::array2json($returnAddress);

      }else{
        return false;
      }

    }else{
      return $this->owner->httpError(404, 'An address type must be passed.');
    }

  }
}