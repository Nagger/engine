<?php
namespace rib\crm;
// Класс заявки в CRM
 
class OpportunityBaseNew extends CRM {
	protected $last_name;
	protected $first_name;
	protected $sur_name;
	protected $name_latin;
	protected $phone_alternate;
	protected $passport_serial;
	protected $passport_number;
	protected $passport_issued_by;
	protected $passport_issued_date;
	protected $passport_issued_code;
	protected $birthdate;
	protected $birthplace;
	protected $passport_citizenship;
	protected $billing_address_postalcode;
	protected $billing_address_region_type_c;
	protected $billing_address_state;
	protected $billing_address_district_ty_c;
	protected $billing_address_district_c;
	protected $billing_address_city_type_c;
	protected $billing_address_city;
	protected $billing_address_village_typ_c;
	protected $billing_address_place;
	protected $billing_address_street_type_c;
	protected $billing_address_street;
	protected $billing_address_build;
	protected $billing_address_flat;
	protected $shipping_address_postalcode;
	protected $shipping_address_region_type_c;
	protected $shipping_address_state;
	protected $shipping_address_district_ty_c;
	protected $shipping_address_district_c;
	protected $shipping_address_city_type_c;
	protected $shipping_address_city;
	protected $shipping_address_village_typ_c;
	protected $shipping_address_place;
	protected $shipping_address_street_type_c;
	protected $shipping_address_street;
	protected $shipping_address_build;
	protected $shipping_address_flat;
	protected $email1;
	protected $keyword;
	protected $keydate;
	protected $hintq;
	protected $hinta;
	protected $code_abs;
	protected $code_retail;
	protected $sales_stage = "New";
	protected $ui_id = '';
	protected $landing = 'IB';

	public function __construct($ext_client_id)
	{

			$contragent = Account::getAccount($ext_client_id);
			if (!$contragent) {
				throw new \Exception ("Контрагент с $ext_client_id не найден");
			}     
			// Получаем свойства текущего класса 	
		        $varAcc = get_class_vars('\rib\crm\OpportunityBaseNew');        	
		    	// Присваиваем каждому свойству объекта полученное значение из контрагента
			foreach ($varAcc as $key => $value) {
				// Заполняем нулевые свойства
				if (!$this->$key) {
			     	$this->$key = $contragent->$key;
			     }
			}    	
		parent::__construct();
	}



}