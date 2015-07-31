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
		$country = Country_Shipping::get()->filter(array('Code' => $this->owner->ShippingCountryCode))->first();
		if ($country && $country->exists()){
			$this->owner->ShippingCountryName = $country->Title;
		}

		$region = Region_Shipping::get()->filter(array('Code' => $this->owner->ShippingRegionCode))->first();
		if ($region && $region->exists()){
			$this->owner->ShippingRegionName = $region->Title;
		}

		$country = Country_Billing::get()->filter(array('Code' => $this->owner->BillingCountryCode))->first();
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
		Session::clear('ShippingAddressID');
		Session::clear('BillingAddressID');
	}

	/**
	 * Retrieve the last used billing address for this Member from their previous saved addresses.
	 * TODO make this more efficient
	 *
	 * @return Address The last billing address
	 */
	public function BillingAddress($addressID = null) {
		$addrs = $this->owner->BillingAddresses();
		if($addrs && $addrs->exists()) {
			if($addressID > 0){
				return $addrs->filter(array('ID' => $addressID))->first();
			} else {
				return $addrs->filter(array('Default' => 1))->first();
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
	public function ShippingAddress($addressID = null) {
		$addrs = $this->owner->ShippingAddresses();
		if ($addrs && $addrs->exists()) {
			if ($addressID > 0){
				return $addrs->filter(array('ID' => $addressID))->first();
			} else {
				return $addrs->filter(array('Default' => 1))->first();
			}
		}
		return null;
	}

	public function DefaultShippingAddress(){
		return $this->owner->ShippingAddresses()->filter(array('Default' => 1))->first();
	}

	public function DefaultBillingAddress(){
		return $this->owner->BillingAddresses()->filter(array('Default' => 1))->first();
	}
}

/*
*	Addresses_OrderForm extends OrderForm
*/
class Addresses_OrderForm extends Extension {

 	public function ShippingAddressFields(){
		$shippingAddSession = self::SessionAddress('shipping');
		$currentUser = Member::currentUser();

		$shippingAddressFields = CompositeField::create(
			HeaderField::create(_t('CheckoutPage.SHIPPING_ADDRESS',"Shipping Address"), 3),
			TextField::create('ShippingFirstName', _t('CheckoutPage.FIRSTNAME',"First Name"), $shippingAddSession ? $shippingAddSession->FirstName : '')
				->addExtraClass('shipping-firstname')
				->setCustomValidationMessage(_t('CheckoutPage.PLEASE_ENTER_FIRSTNAME',"Please enter a first name.")),
			TextField::create('ShippingSurname', _t('CheckoutPage.SURNAME',"Surname"), $shippingAddSession ? $shippingAddSession->Surname : '')
				->setCustomValidationMessage(_t('CheckoutPage.PLEASE_ENTER_SURNAME',"Please enter a surname.")),
			TextField::create('ShippingCompany', _t('CheckoutPage.COMPANY',"Company"), $shippingAddSession ? $shippingAddSession->Company : ''),
			TextField::create('ShippingAddress', _t('CheckoutPage.ADDRESS',"Address"), $shippingAddSession ? $shippingAddSession->Address : '')
				->setCustomValidationMessage(_t('CheckoutPage.PLEASE_ENTER_ADDRESS',"Please enter an address."))
				->addExtraClass('address-break'),
			TextField::create('ShippingAddressLine2', '&nbsp;', $shippingAddSession ? $shippingAddSession->AddressLine2 : ''),
			TextField::create('ShippingCity', _t('CheckoutPage.CITY',"City"), $shippingAddSession ? $shippingAddSession->City : '')
				->setCustomValidationMessage(_t('CheckoutPage.PLEASE_ENTER_CITY',"Please enter a city.")),
			TextField::create('ShippingPostalCode', _t('CheckoutPage.POSTAL_CODE',"Zip / Postal Code"), $shippingAddSession ? $shippingAddSession->PostalCode : ''),
			TextField::create('ShippingState', _t('CheckouanypotPage.STATE',"State / Province"), $shippingAddSession ? $shippingAddSession->State : '')
				->addExtraClass('address-break'),
            DropdownField::create('ShippingRegionCode',
                "Region", Region_Shipping::get()->map('Code', 'Title')->toArray()
            )->setValue($shippingAddSession ? $shippingAddSession->RegionCode : ''),
			DropdownField::create('ShippingCountryCode',
					_t('CheckoutPage.COUNTRY',"Country"),
					Country_Shipping::get()->map('Code', 'Title')->toArray()
				)
				->setCustomValidationMessage(_t('CheckoutPage.PLEASE_ENTER_COUNTRY',"Please enter a country."))
				->addExtraClass('country-code')
				->setValue($shippingAddSession ? $shippingAddSession->CountryCode : ''),
			CheckboxField::create('ShippingDefault', 'Default shipping address?')
		)->setID('ShippingAddress')->setName('ShippingAddress');

		return $shippingAddressFields;
	}

	public function BillingAddressFields(){
		$shippingAddSession = self::SessionAddress('shipping');

		if(!self::SessionAddress('billing')){
			$billingAddSession = $shippingAddSession;
		} else {
			$billingAddSession = self::SessionAddress('billing');
		}

		$billingAddressFields = CompositeField::create(
			HeaderField::create(_t('CheckoutPage.BILLINGADDRESS',"Billing Address"), 3),
			TextField::create('BillingFirstName', _t('CheckoutPage.FIRSTNAME',"First Name"), $billingAddSession ? $billingAddSession->FirstName : '')
				->setCustomValidationMessage(_t('CheckoutPage.PLEASEENTERYOURFIRSTNAME',"Please enter your first name."))
				->addExtraClass('address-break'),
			TextField::create('BillingSurname', _t('CheckoutPage.SURNAME',"Surname"), $billingAddSession ? $billingAddSession->Surname : '')
				->setCustomValidationMessage(_t('CheckoutPage.PLEASEENTERYOURSURNAME',"Please enter your surname.")),
			TextField::create('BillingCompany', _t('CheckoutPage.COMPANY',"Company"), $billingAddSession ? $billingAddSession->Company : ''),
			TextField::create('BillingAddress', _t('CheckoutPage.ADDRESS',"Address"), $billingAddSession ? $billingAddSession->Address : '')
				->setCustomValidationMessage(_t('CheckoutPage.PLEASEENTERYOURADDRESS',"Please enter your address."))
				->addExtraClass('address-break'),
			TextField::create('BillingAddressLine2', '&nbsp;', $billingAddSession ? $billingAddSession->AddressLine2 : ''),
			TextField::create('BillingCity', _t('CheckoutPage.CITY',"City"), $billingAddSession ? $billingAddSession->City : '')
				->setCustomValidationMessage(_t('CheckoutPage.PLEASEENTERYOURCITY',"Please enter your city")),
			TextField::create('BillingPostalCode', _t('CheckoutPage.POSTALCODE',"Zip / Postal Code"), $billingAddSession ? $billingAddSession->PostalCode : ''),
			TextField::create('BillingState', _t('CheckoutPage.STATE',"State / Province"), $billingAddSession ? $billingAddSession->State : '')
				->addExtraClass('address-break'),
		   //DropdownField::create('BillingRegionCode',
                //"Region", Region_Billing::get()->map('Code', 'Title')->toArray()
            //),
			DropdownField::create('BillingCountryCode',
					_t('CheckoutPage.COUNTRY',"Country"),
					Country_Billing::get()->map('Code', 'Title')->toArray()
				)->setCustomValidationMessage(_t('CheckoutPage.PLEASEENTERYOURCOUNTRY', "Please enter your country."))
				->setValue($billingAddSession ? $billingAddSession->CountryCode : ''),
			CheckboxField::create('BillingDefault', 'Default billing address?')
		)->setID('BillingAddress')->setName('BillingAddress');

		return $billingAddressFields;
	}

	public function updateFields($fields) {
		Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.js');
		Requirements::javascript(THIRDPARTY_DIR . '/jquery-entwine/dist/jquery.entwine-dist.js');
		Requirements::javascript(THIRDPARTY_DIR . '/jquery-validate/jquery.validate.min.js');
		Requirements::javascript('swipestripe-addresses/javascript/Addresses_OrderForm.js');

		$shippingAddSession = self::SessionAddress('shipping');

		$currentUser = Member::currentUser();
		if($currentUser){
			if(!self::SessionAddress('billing')){
				$billingAddSession = $shippingAddSession;
			} else {
				$billingAddSession = self::SessionAddress('billing');
			}

			// Moved CompositeField generation to separate functions
			$shippingAddressFields = $this->owner->ShippingAddressFields();
			$billingAddressFields = $this->owner->BillingAddressFields();

			$defaultBilling = $currentUser->BillingAddresses()->filter(array('Default' => 1));
			if($billingAddSession && $shippingAddSession && $billingAddSession->ID != $shippingAddSession->ID || $defaultBilling->Count() > 0){
				$sameChecked = false;
			} else {
				$sameChecked = true;
			}

			$sameAsBilling = CompositeField::create(
				CheckboxField::create('BillToShippingAddress', _t('CheckoutPage.SAME_ADDRESS', "Same as shipping address?"))
					->addExtraClass('shipping-same-address')
					// Check made here instead of updatePopulateField()
					->setValue($sameChecked)
					->addExtraClass('left')
			)->setID('SameBillingAddress')->setName('SameBillingAddress');

			$fields->push($shippingAddressFields);
			$fields->push($billingAddressFields);
			$fields->push($sameAsBilling);
		}
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
		$shippingAddressData = ($shippingAddress && $shippingAddress->exists()) ? $shippingAddress->getCheckoutFormData() : array();
		unset($shippingAddressData['ShippingRegionCode']); // Not available billing address option

		$billingAddress = $member->BillingAddress();
		$billingAddressData = ($billingAddress && $billingAddress->exists()) ? $billingAddress->getCheckoutFormData() : array();

		$data = array_merge(
			$data,
			$shippingAddressData,
			$billingAddressData
		);
	}

	public function getShippingAddressFields() {
		$fields = $this->owner->Fields()->fieldByName('ShippingAddress');
		return $fields;
	}

	// Return the form fields empty
	public function EmptyShippingAddressFields() {
		$fields = $this->owner->ShippingAddressFields();
		foreach($fields->FieldList()->dataFields() as $field){
			$field->setValue('');
		}
		return $fields;
	}

	public function getBillingAddressFields() {
		$fields = $this->owner->Fields()->fieldByName('BillingAddress');
		return $fields;
	}

	// Return the form fields empty
	public function EmptyBillingAddressFields() {
		$fields = $this->owner->BillingAddressFields();
		foreach($fields->FieldList()->dataFields() as $field){
			$field->setValue('');
		}
		return $fields;
	}

	public function getSameBillingAddressFields() {
		return $this->owner->Fields()->fieldByName('SameBillingAddress');
	}

	public function SessionAddress($type = 'shipping'){
		$customer = Member::currentUser();
		$shippingAddress = $customer ? $customer->ShippingAddresses()->filter(array('Default' => 1))->first() : null;
		$billingAddress = $customer ? $customer->BillingAddresses()->filter(array('Default' => 1))->first() : null;

		$ShippingID = Session::get('ShippingAddressID') ? Session::get('ShippingAddressID') : ($shippingAddress ? $shippingAddress->ID : false);
		$BillingID = Session::get('BillingAddressID') ? Session::get('BillingAddressID') : ($billingAddress ? $billingAddress->ID : false);
		$address = false;

		if($type == 'shipping' && $ShippingID){
			$address = Address_Shipping::get()->filter(array('ID' => $ShippingID))->first();
		} elseif($type == 'billing' && $BillingID){
			$address = Address_Billing::get()->filter(array('ID' => $BillingID))->first();
		}

		return $address ? $address : false;
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

		if ($items->count() > 1){
			$items->remove($items->pop());
		}

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
		if($form->Fields()->hasTabset()){
			$form->Fields()->findOrMakeTab('Root')->setTemplate('CMSTabSet');
		}
		$form->setFormAction(Controller::join_links($this->Link($this->sanitiseClassName($this->modelClass)), 'Countries/CountriesForm'));

		$form->loadDataFrom($shopConfig);

		return $form;
	}

	public function getSnippet() {
		if (!$member = Member::currentUser()){
			return false;
		}
		if (!Permission::check('CMS_ACCESS_' . get_class($this), 'any', $member)){
			return false;
		}

		return $this->customise(array(
			'Title' => 'Countries and Regions',
			'Help' => 'Shipping and billing countries and regions.',
			'Link' => Controller::join_links($this->Link('ShopConfig'), 'Countries'),
			'LinkTitle' => 'Edit Countries and Regions'
		))->renderWith('ShopAdmin_Snippet');
	}
}
