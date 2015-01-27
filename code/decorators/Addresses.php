<?php
/*
*	Addresses_Order extends Order
*/
class Addresses_Order extends DataExtension {

	private static $db = array(
		//Address fields
		'ShippingFirstName' => 'Varchar',
		'ShippingSurname' => 'Varchar',
		'ShippingCompany' => 'Varchar',
		'ShippingAddress' => 'Varchar(255)',
		'ShippingAddressLine2' => 'Varchar(255)',
		'ShippingCity' => 'Varchar(100)',
		'ShippingPostalCode' => 'Varchar(30)',
		'ShippingState' => 'Varchar(100)',
		'ShippingCountryName' => 'Varchar',
		'ShippingCountryCode' => 'Varchar(2)', //ISO 3166 
		'ShippingRegionName' => 'Varchar',
		'ShippingRegionCode' => 'Varchar(2)',

		'BillingFirstName' => 'Varchar',
		'BillingSurname' => 'Varchar',
		'BillingCompany' => 'Varchar',
		'BillingAddress' => 'Varchar(255)',
		'BillingAddressLine2' => 'Varchar(255)',
		'BillingCity' => 'Varchar(100)',
		'BillingPostalCode' => 'Varchar(30)',
		'BillingState' => 'Varchar(100)',
		'BillingCountryName' => 'Varchar',
		'BillingCountryCode' => 'Varchar(2)', //ISO 3166 
		'BillingRegionName' => 'Varchar',
		'BillingRegionCode' => 'Varchar(2)'
	);

	public function onBeforeWrite() {
		//Update address names
		$country = Country_Shipping::get()->where("\"Code\" = '{$this->owner->ShippingCountryCode}'")->first();
		if ($country && $country->exists()){
			$this->owner->ShippingCountryName = $country->Title;
		}

		$region = Region_Shipping::get()->where("\"Code\" = '{$this->owner->ShippingRegionCode}'")->first();
		if ($region && $region->exists()){
			$this->owner->ShippingRegionName = $region->Title;
		}

		$country = Country_Billing::get()->where("\"Code\" = '{$this->owner->BillingCountryCode}'")->first();
		if ($country && $country->exists()){
			$this->owner->BillingCountryName = $country->Title;
		}
	}

	public function onBeforePayment() {
		//Save the addresses to the Customer
		$customer = $this->owner->Member();
		if ($customer && $customer->exists()) {
			$customer->createAddresses($this->owner);
		}
	}
}

/*
*	Addresses_Customer extends Customer
*/
class Addresses_Customer extends DataExtension {
	private static $has_many = array(
		'ShippingAddresses' => 'Address_Shipping',
		'BillingAddresses' => 'Address_Billing'
	);

	public function createAddresses($order) {
		// Find identical addresses
		// If none exist then create a new address and set it as default
		// Default is not used when comparing
		/* REMOVED AS ADDRESSES ARE MANAGED ON THE CHECKOUTPAGE AS OBJECTS
		$data = $order->toMap();
		
		// Set Firstname/Surname Fields on Member table
		if(!$this->owner->FirstName) {
			$this->owner->FirstName = $data['ShippingFirstName'];
			$this->owner->write();
		}
		if(!$this->owner->Surname) {
			$this->owner->Surname = $data['ShippingSurname'];
			$this->owner->write();
		}
		
		$shippingFields = array(
			'MemberID' => $this->owner->ID,
			'FirstName' => isset($data['ShippingFirstName']) ? $data['ShippingFirstName'] : null,
			'Surname' => isset($data['ShippingSurname']) ? $data['ShippingSurname'] : null,
			'Company' => isset($data['ShippingCompany']) ? $data['ShippingCompany'] : null,
			'Address' => isset($data['ShippingAddress']) ? $data['ShippingAddress'] : null,
			'AddressLine2' => isset($data['ShippingAddressLine2']) ? $data['ShippingAddressLine2'] : null,
			'City' => isset($data['ShippingCity']) ? $data['ShippingCity'] : null,
			'PostalCode' => isset($data['ShippingPostalCode']) ? $data['ShippingPostalCode'] : null,
			'State' => isset($data['ShippingState']) ? $data['ShippingState'] : null,
			'CountryName' => isset($data['ShippingCountryName']) ? $data['ShippingCountryName'] : null,
			'CountryCode' => isset($data['ShippingCountryCode']) ? $data['ShippingCountryCode'] : null,
			'RegionName' => isset($data['ShippingRegionName']) ? $data['ShippingRegionName'] : null,
			'RegionCode' => isset($data['ShippingRegionCode']) ? $data['ShippingRegionCode'] : null,
		);

		$billingFields = array(
			'MemberID' => $this->owner->ID,
			'FirstName' => isset($data['BillingFirstName']) ? $data['BillingFirstName'] : null,
			'Surname' => isset($data['BillingSurname']) ? $data['BillingSurname'] : null,
			'Company' => isset($data['BillingCompany']) ? $data['BillingCompany'] : null,
			'Address' => isset($data['BillingAddress']) ? $data['BillingAddress'] : null,
			'AddressLine2' => isset($data['BillingAddressLine2']) ? $data['BillingAddressLine2'] : null,
			'City' => isset($data['BillingCity']) ? $data['BillingCity'] : null,
			'PostalCode' => isset($data['BillingPostalCode']) ? $data['BillingPostalCode'] : null,
			'State' => isset($data['BillingState']) ? $data['BillingState'] : null,
			'CountryName' => isset($data['BillingCountryName']) ? $data['BillingCountryName'] : null,
			'CountryCode' => isset($data['BillingCountryCode']) ? $data['BillingCountryCode'] : null,
			'RegionName' => isset($data['BillingRegionName']) ? $data['ShippingRegionName'] : null,
			'RegionCode' => isset($data['BillingRegionCode']) ? $data['ShippingRegionCode'] : null,
		);
		
		$shippingID = Session::get('ShippingAddressID');
		$billingID = Session::get('BillingAddressID');
		
		//Look for existing addresses or create a new one if needed
		$shippingAddress = Address_Shipping::get()->filter(array('ID' => $shippingID))->first();
		$shippingAddress = $shippingAddress ? $shippingAddress : Address_Shipping::create($shippingFields);
		$shippingAddress->write();
		
		$billingAddress = Address_Billing::get()->filter(array('ID' => $billingID))->first();
		$billingAddress = $billingAddress ? $billingAddress : Address_Billing::create($billingFields);
		$billingAddress->write();
		*/
		Session::clear('ShippingAddressID');
		Session::clear('BillingAddressID');
		/*
		//TODO when a match is made then make that matched address the default now
		$match = false;
		foreach ($this->owner->ShippingAddresses() as $address) {

			$existing = $address->toMap();
			$new = $shippingAddress->toMap();
			$result = array_intersect_assoc($existing, $new);

			//If no difference, then match is found
			$diff = array_diff_assoc($new, $result);
			$match = empty($diff);
		}

		if (!$match) {
			$shippingAddress->Default = 0;
			$shippingAddress->write();
		}

		$match = false;
		foreach ($this->owner->BillingAddresses() as $address) {

			$existing = $address->toMap();
			$new = $billingAddress->toMap();
			$result = array_intersect_assoc($existing, $new);

			$diff = array_diff_assoc($new, $result);
			$match = empty($diff);
		}

		if (!$match) {
			$billingAddress->Default = 0;
			$billingAddress->write();
		}
		*/
	}

	/**
	 * Retrieve the last used billing address for this Member from their previous saved addresses.
	 * TODO make this more efficient
	 * 
	 * @return Address The last billing address
	 */
	public function BillingAddress($addressID=null) {
		$addrs = $this->owner->BillingAddresses();
		if ($addrs && $addrs->exists()) {
			
			if ($addressID > 0){
			
				return $addrs
				->where("ID",$addressID)
				->first();
				
			}else{
				return $addrs
				->where("\"Default\" = 1")
				->first();
			}
			
		}
		return null;
	}
	
	/**
	 * Retrieve the last used shipping address for this Member from their previous saved addresses.
	 * TODO make this more efficient
	 * 
	 * @return Address The last shipping address
	 */
	public function ShippingAddress($addressID=null) {

		$addrs = $this->owner->ShippingAddresses();
		if ($addrs && $addrs->exists()) {
			if ($addressID > 0){
				
				return $addrs
				->where("ID",$addressID)
				->first();
				
			}else{
				return $addrs
				->where("\"Default\" = 1")
				->first();
			}
		}
		return null;
	}
}

/*
*	Addresses_OrderForm extends OrderForm
*/
class Addresses_OrderForm extends Extension {

	public function updateFields($fields) {
		Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.js');
		Requirements::javascript(THIRDPARTY_DIR . '/jquery-entwine/dist/jquery.entwine-dist.js');
		Requirements::javascript(THIRDPARTY_DIR . '/jquery-validate/jquery.validate.min.js');
		Requirements::javascript('swipestripe-addresses/javascript/Addresses_OrderForm.js');

		$shippingAddressFields = CompositeField::create(
			HeaderField::create(_t('CheckoutPage.SHIPPING_ADDRESS',"Shipping Address"), 3),
			TextField::create('ShippingFirstName', _t('CheckoutPage.FIRSTNAME',"First Name"), '')
				->addExtraClass('shipping-firstname')
				->setCustomValidationMessage(_t('CheckoutPage.PLEASE_ENTER_FIRSTNAME',"Please enter a first name.")),
			TextField::create('ShippingSurname', _t('CheckoutPage.SURNAME',"Surname"), '')
				->setCustomValidationMessage(_t('CheckoutPage.PLEASE_ENTER_SURNAME',"Please enter a surname.")),
			TextField::create('ShippingCompany', _t('CheckoutPage.COMPANY',"Company"), ''),
			TextField::create('ShippingAddress', _t('CheckoutPage.ADDRESS',"Address"), '')
				->setCustomValidationMessage(_t('CheckoutPage.PLEASE_ENTER_ADDRESS',"Please enter an address."))
				->addExtraClass('address-break'),
			TextField::create('ShippingAddressLine2', '&nbsp;', ''),
			TextField::create('ShippingCity', _t('CheckoutPage.CITY',"City"), '')
				->setCustomValidationMessage(_t('CheckoutPage.PLEASE_ENTER_CITY',"Please enter a city.")),
			TextField::create('ShippingPostalCode', _t('CheckoutPage.POSTAL_CODE',"Zip / Postal Code"), ''),
			TextField::create('ShippingState', _t('CheckouanypotPage.STATE',"State / Province"), '')
				->addExtraClass('address-break'),
            DropdownField::create('ShippingRegionCode',
                "Region", Region_Shipping::get()->map('Code', 'Title')->toArray()
            ),
			DropdownField::create('ShippingCountryCode', 
					_t('CheckoutPage.COUNTRY',"Country"), 
					Country_Shipping::get()->map('Code', 'Title')->toArray()
				)
				->setCustomValidationMessage(_t('CheckoutPage.PLEASE_ENTER_COUNTRY',"Please enter a country."))
				->addExtraClass('country-code')
		)->setID('ShippingAddress')->setName('ShippingAddress');

		$billingAddressFields = CompositeField::create(
			HeaderField::create(_t('CheckoutPage.BILLINGADDRESS',"Billing Address"), 3),
			TextField::create('BillingFirstName', _t('CheckoutPage.FIRSTNAME',"First Name"), '')
				->setCustomValidationMessage(_t('CheckoutPage.PLEASEENTERYOURFIRSTNAME',"Please enter your first name."))
				->addExtraClass('address-break'),
			TextField::create('BillingSurname', _t('CheckoutPage.SURNAME',"Surname"), '')
				->setCustomValidationMessage(_t('CheckoutPage.PLEASEENTERYOURSURNAME',"Please enter your surname.")),
			TextField::create('BillingCompany', _t('CheckoutPage.COMPANY',"Company"), ''),
			TextField::create('BillingAddress', _t('CheckoutPage.ADDRESS',"Address"), '')
				->setCustomValidationMessage(_t('CheckoutPage.PLEASEENTERYOURADDRESS',"Please enter your address."))
				->addExtraClass('address-break'),
			TextField::create('BillingAddressLine2', '&nbsp;', ''),
			TextField::create('BillingCity', _t('CheckoutPage.CITY',"City"), '')
				->setCustomValidationMessage(_t('CheckoutPage.PLEASEENTERYOURCITY',"Please enter your city")),
			TextField::create('BillingPostalCode', _t('CheckoutPage.POSTALCODE',"Zip / Postal Code")), '',
			TextField::create('BillingState', _t('CheckoutPage.STATE',"State / Province"), '')
				->addExtraClass('address-break'),
		   //DropdownField::create('BillingRegionCode',
                //"Region", Region_Billing::get()->map('Code', 'Title')->toArray()
            //),
			DropdownField::create('BillingCountryCode', 
					_t('CheckoutPage.COUNTRY',"Country"), 
					Country_Billing::get()->map('Code', 'Title')->toArray()
				)->setCustomValidationMessage(_t('CheckoutPage.PLEASEENTERYOURCOUNTRY', "Please enter your country."))
		)->setID('BillingAddress')->setName('BillingAddress');
		
		$sameAsBilling = CompositeField::create(
			CheckboxField::create('BillToShippingAddress', _t('CheckoutPage.SAME_ADDRESS', "Same as shipping address?"))
				->addExtraClass('shipping-same-address')->setValue(0)->addExtraClass('left')
		)->setID('SameBillingAddress')->setName('SameBillingAddress');
		
		$fields->push($shippingAddressFields);
		$fields->push($billingAddressFields);
		$fields->push($sameAsBilling);
	}

	public function updateValidator($validator) {
		/*
		$validator->appendRequiredFields(RequiredFields::create(
			'ShippingFirstName',
			'ShippingSurname',
			'ShippingAddress',
			'ShippingCity',
			'ShippingCountryCode',
			'BillingFirstName',
			'BillingSurname',
			'BillingAddress',
			'BillingCity',
			'BillingCountryCode'
		));
		*/
	}

	public function updatePopulateFields(&$data) {

		$member = Customer::currentUser() ? Customer::currentUser() : singleton('Customer');

		$shippingAddress = $member->ShippingAddress();
		$shippingAddressData = ($shippingAddress && $shippingAddress->exists()) 
			? $shippingAddress->getCheckoutFormData()
			: array();
		unset($shippingAddressData['ShippingRegionCode']); //Not available billing address option

		$billingAddress = $member->BillingAddress();
		$billingAddressData = ($billingAddress && $billingAddress->exists()) 
			? $billingAddress->getCheckoutFormData()
			: array();

		//If billing address is a subset of shipping address, consider them equal
		$intersect = array_intersect(array_values($shippingAddressData), array_values($billingAddressData));
		if (array_values($intersect) == array_values($billingAddressData)) $billingAddressData['BillToShippingAddress'] = true;

		$data = array_merge(
			$data, 
			$shippingAddressData,
			$billingAddressData
		);
	}

	public function getShippingAddressFields() {
		return $this->owner->Fields()->fieldByName('ShippingAddress');
	}

	public function getBillingAddressFields() {
		return $this->owner->Fields()->fieldByName('BillingAddress');
	}
	
	public function getSameBillingAddressFields() {
		return $this->owner->Fields()->fieldByName('SameBillingAddress');
	}
}

/*
*	Addresses_Extension extends ShopConfig
*/
class Addresses_Extension extends DataExtension {

	private static $has_many = array(
		'ShippingCountries' => 'Country_Shipping',
		'BillingCountries' => 'Country_Billing',
		'ShippingRegions' => 'Region_Shipping',
		'BillingRegions' => 'Region_Billing'
	);
}

class Addresses_CountriesAdmin extends ShopAdmin {

	private static $tree_class = 'ShopConfig';
	
	private static $allowed_actions = array(
		'Countries',
		'CountriesForm'
	);

	private static $url_rule = 'ShopConfig/Countries';
	protected static $url_priority = 70;
	private static $menu_title = 'Shop Countries';

	private static $url_handlers = array(
		'ShopConfig/Countries/CountriesForm' => 'CountriesForm',
		'ShopConfig/Countries' => 'Countries'
	);

	public function init() {
		parent::init();
		if (!in_array(get_class($this), self::$hidden_sections)) {
			$this->modelClass = 'ShopConfig';
		}
	}

	public function Breadcrumbs($unlinked = false) {

		$request = $this->getRequest();
		$items = parent::Breadcrumbs($unlinked);

		if ($items->count() > 1) $items->remove($items->pop());

		$items->push(new ArrayData(array(
			'Title' => 'Countries',
			'Link' => $this->Link(Controller::join_links($this->sanitiseClassName($this->modelClass), 'Countries'))
		)));

		return $items;
	}

	public function SettingsForm($request = null) {
		return $this->CountriesForm();
	}

	public function Countries($request) {

		if ($request->isAjax()) {
			$controller = $this;
			$responseNegotiator = new PjaxResponseNegotiator(
				array(
					'CurrentForm' => function() use(&$controller) {
						return $controller->CountriesForm()->forTemplate();
					},
					'Content' => function() use(&$controller) {
						return $controller->renderWith('ShopAdminSettings_Content');
					},
					'Breadcrumbs' => function() use (&$controller) {
						return $controller->renderWith('CMSBreadcrumbs');
					},
					'default' => function() use(&$controller) {
						return $controller->renderWith($controller->getViewer('show'));
					}
				),
				$this->response
			); 
			return $responseNegotiator->respond($this->getRequest());
		}

		return $this->renderWith('ShopAdminSettings');
	}

	public function CountriesForm() {

		$shopConfig = ShopConfig::get()->First();

		$fields = new FieldList(
			$rootTab = new TabSet("Root",
				$tabMain = new Tab('Shipping',
					new HiddenField('ShopConfigSection', null, 'Countries'),
					new GridField(
						'ShippingCountries',
						'Shipping Countries',
						$shopConfig->ShippingCountries(),
						GridFieldConfig_RecordEditor::create()
							->removeComponentsByType('GridFieldFilterHeader')
							->removeComponentsByType('GridFieldAddExistingAutocompleter')
					)
				),
				new Tab('Billing',
					new GridField(
						'BillingCountries',
						'Billing Countries',
						$shopConfig->BillingCountries(),
						GridFieldConfig_RecordEditor::create()
							->removeComponentsByType('GridFieldFilterHeader')
							->removeComponentsByType('GridFieldAddExistingAutocompleter')
					)
				)
			)
		);

		$actions = new FieldList();

		$form = new Form(
			$this,
			'EditForm',
			$fields,
			$actions
		);

		$form->setTemplate('ShopAdminSettings_EditForm');
		$form->setAttribute('data-pjax-fragment', 'CurrentForm');
		$form->addExtraClass('cms-content cms-edit-form center ss-tabset');
		if($form->Fields()->hasTabset()) $form->Fields()->findOrMakeTab('Root')->setTemplate('CMSTabSet');
		$form->setFormAction(Controller::join_links($this->Link($this->sanitiseClassName($this->modelClass)), 'Countries/CountriesForm'));

		$form->loadDataFrom($shopConfig);

		return $form;
	}

	public function getSnippet() {

		if (!$member = Member::currentUser()) return false;
		if (!Permission::check('CMS_ACCESS_' . get_class($this), 'any', $member)) return false;

		return $this->customise(array(
			'Title' => 'Countries and Regions',
			'Help' => 'Shipping and billing countries and regions.',
			'Link' => Controller::join_links($this->Link('ShopConfig'), 'Countries'),
			'LinkTitle' => 'Edit Countries and Regions'
		))->renderWith('ShopAdmin_Snippet');
	}

}
