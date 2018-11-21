<?php
namespace rib\crm;
// Класс CRM содержит методы соединения с API CRM

class CRM {
	private $_options = array("location" => 'http://192.168.100.100/soap.php', "uri" => 'http://192.168.100.100', "trace" => 1);	
//private $_options = array("location" => 'http://192.168.100.88/soap.php', "uri" => 'http://192.168.100.88', "trace" => 1);	
	private $_client;
	public $sess;
	public $soapclient;
	

	public function __construct() {
		$this->soapclient = new \SoapClient(NULL, $this->_options);	
		$this->login();
	}

	private function login() {                
		$user_auth = array("user_name" => 'ib', "password" => md5('***'), "version" => '0.1');				
        $result = $this->soapclient->login($user_auth, 'CRM');

        $this->sess = $result->error->number == 0 ? $result->id : null;
        $this->_client = $this->soapclient->get_user_id($this->sess);        
        return $this->sess;
    }

    // Разбирает большой массив, полученный из CRM, делает из него ключ->значение
    public function nameValueToArray($items) {    	
        $out_arr = array();        
        foreach ($items->entry_list[0]->name_value_list as $item) {        	
            $out_arr[$item->name] = $item->value;
        }
        return $out_arr;
    }

	
}