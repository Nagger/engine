<?php
// Класс Контрагент в CRM
namespace rib\crm;

class Account extends CRM {

	// Получить данные из CRM	
	public $id;
	public $last_name;
	public $first_name;
	public $sur_name;
	public $name_latin;
	public $phone_alternate;
	public $email1;
	public $passport_serial;
	public $passport_number;
	public $passport_issued_by;
	public $passport_issued_date;
	public $passport_issued_code;
	public $birthdate;
	public $birthplace;
	public $keyword;
	public $citizenship;
	public $billing_address_postalcode;
	public $billing_address_region_type_c;
	public $billing_address_state;
	public $billing_address_district_ty_c;
	public $billing_address_district_c;
	public $billing_address_city_type_c;
	public $billing_address_city;
	public $billing_address_village_typ_c;
	public $billing_address_place;
	public $billing_address_street_type_c;
	public $billing_address_street;
	public $billing_address_build;
	public $billing_address_flat;
	public $shipping_address_postalcode;
	public $shipping_address_region_type_c;
	public $shipping_address_state;
	public $shipping_address_district_ty_c;
	public $shipping_address_district_c;
	public $shipping_address_city_type_c;
	public $shipping_address_city;
	public $shipping_address_village_typ_c;
	public $shipping_address_place;
	public $shipping_address_street_type_c;
	public $shipping_address_street;
	public $shipping_address_build;
	public $shipping_address_flat;
	public $keydate;
	public $hintq;
	public $hinta;
	public $code_abs;
	public $code_retail;
	public $worker_c;

	public function __construct($account) {		
		// Получаем свойства текущего класса 	
		if (!is_null($account)) {
			$varAcc = get_class_vars('\rib\crm\Account');        	
			// Присваиваем каждому свойству объекта полученное значение из контрагента
			foreach ($varAcc as $key => $value) {
				// Заполняем нулевые свойства
				if (!$this->$key) {
					//echo $account[$key];
					$this->$key = iconv("windows-1251","utf-8",$account[$key]);
				}
			}    	
			parent::__construct();
		}
	}

	public static function getAccount($ext_client_id) {

	// Нормализация данных в контрагенте
        $url_check = "http://192.168.100.13:4447/v1/reference/kladr/correct_address_idabs.php";
        $ch2 = curl_init();
	$input = '{"id": "'.$ext_client_id.'","typereq":"Accounts"}';
        curl_setopt($ch2, CURLOPT_URL, $url_check);
	curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
	curl_setopt($ch2, CURLOPT_POSTFIELDS, $input);                                                                  
	curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);                                                                      
	curl_setopt($ch2, CURLOPT_HTTPHEADER, array(                                                                          
	    'Content-Type: application/json',                                                                                
	    'Content-Length: ' . strlen($input))                                                                       
	);  
        $output = curl_exec($ch2);        	
        curl_close($ch2);

		$crm = new CRM();
        $query = "accounts.code_abs='$ext_client_id' and accounts.deleted=0";
        $order_by = "date_entered desc";
        
		$res = 0;		
        $result = $crm->soapclient->get_entry_list($crm->sess, 'Accounts', $query, $order_by);        
        
        if ($result->result_count > 0) {
        	// преобразуем массив к нормальному виду ключ->значение        
        	$account_data = $crm->nameValueToArray($result);
        	$account = new self(null);
        	// Получаем список свойств класса
        	$varAcc = get_class_vars('\rib\crm\Account');        	
        	// Присваиваем каждому свойству объекта полученное значение
			foreach ($varAcc as $key => $value) {
			     	$account->$key = $account_data[$key];
			}     
        }
        else {            
        	$account = null;            
        }
        return $account;

	}

	public function saveAccount() {
		$varAccount = get_class_vars('\rib\crm\Account');   		
		$varCRM = get_class_vars('\rib\crm\CRM');  		
		$data = array();
		
		// Лень перечислять все свойства, поэтому беру их из текущего объекта
		foreach ($varAccount as $key => $value) {
			// Кроме тех которые определены в классе CRM
			//if ($this->$key != '') {
				if (!array_key_exists($key, $varCRM))
					$data[] = array('name' => $key, 'value' => $this->$key);			
			//}
		}
		//print_r($data);				
		$response = $this->soapclient->set_entry($this->sess,'Accounts', $data);			
		
		if ($response->error->number != 0) {
			throw new \Exception("При сохранении обращения в CRM произошла ошибка");
		}

		return $response->id;
	}

	public function getId() {
		return $this->id;
	}
	public function getPassport() {
		return "{$this->passport_serial}, {$this->passport_number}, {$this->passport_issued_date}";
	}
	public function getKeyword() {
		return $this->keyword;
	}
	public function getName() {
		return "{$this->last_name} {$this->first_name} {$this->sur_name}";
	}
}
