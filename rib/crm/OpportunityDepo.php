<?php
namespace rib\crm;
// Класс заявки на карту
class OpportunityDepo extends OpportunityBaseNew {

	private $amount = '';
	private $account_id = '';	
	private $currency_id;
	private $description = '';
	private $type_of_opportunity_c = 'forDeposit';	
	private $wf_type = 'default';
	private $date_closed = '';
	private $deposit_term_days = '';	
	private $deposit_term_months = '';	
	private $contract_card_number = '';
	private $sca_number = '';
	private $mask_card_number = '';
	private $opportunity_type = '';
	private $contract_contrib_number = '';
	private $account_contrib_number = '';
	private $capitalization = '';	
	private $region = '';
	private $regioncode_c = '';	
	private $interest_account = '';	
	private $citizenship = '';	
	
	private $bonus = '';
	private $rate = '';
	private $bonus_type = '';	

	private $bar_code_c;
	//protected $contragent;

	public function __construct($doc)
	{
		$this->amount = $doc["amount"];
		$this->account_id = $doc["account_id"];
		$this->currency_id = $this->getCurrency($doc["currency"]);
		$this->date_closed = $doc["date_closed"];
		$this->deposit_term_days = $doc["deposit_term_days"];
		$this->deposit_term_months = (int)($doc["deposit_term_days"] / 30);
		$this->contract_card_number = $this->toUTF8($doc["contract_card_number"]);
		$this->sca_number = $this->toUTF8($doc["sca_number"]);
		$this->mask_card_number = $doc["mask_card_number"];
		$this->opportunity_type = "region";
		$this->description = $this->toUTF8($doc["contract_prod_type"]);
		$this->contract_contrib_number = $this->toUTF8($doc["contract_contrib_number"]);
		$this->account_contrib_number = $this->toUTF8($doc["account_contrib_number"]);
		$this->capitalization = $this->getCapitalization($doc["capitalization_back"]);
		$this->citizenship = $this->getResident($doc["citizenship"]);
		
		$this->bar_code_c = $this->getBarCode();
		$this->interest_account = $doc["interest_account"];
		$this->sales_stage = $doc["sales_stage"];

		$this->setRegionInfo($doc['Region']);
		parent::__construct($doc['code_abs']);
	}

	public function saveOpportunityDepo() {
		$varCard = get_class_vars('\rib\crm\OpportunityDepo');   		
		$varCRM = get_class_vars('\rib\crm\CRM');  		
		$data = array();
		
		// Лень перечислять все свойства, поэтому беру их из текущего объекта
		foreach ($varCard as $key => $value) {
			// Кроме тех которые определены в классе CRM
			if (!array_key_exists($key, $varCRM))
			$data[] = array('name' => $key, 'value' => $this->$key);			
		}
		//print_r($data);
		$response = $this->soapclient->set_entry($this->sess,'Opportunities', $data);
		$id_opp = $response->id;
		$oppRelation = array('module1' => 'Opportunities', 'module1_id' => $id_opp, 'module2' => 'Accounts', 'module2_id' => $this->account_id);    
		try {
		$this->soapclient->set_relationship($this->sess, $oppRelation);
			} catch (SoapFault $e) {
				// Ошибка при сохранении
				throw new \Exception("Ошибка при установлении связи контрагента и заявки");
			}
		if ($response->error->number != 0) {
			throw new \Exception("При сохранении обращения в CRM произошла ошибка");
		}

		return $response;
	}

	private function getCurrency($currency) {
        if ($currency == "RUB" or $currency == "RUR")
            $currency = "-99";
        if ($currency == "USD")
            $currency = "292bf9a6-ac0d-70a5-a9f6-50d025de1534";
        if ($currency == "EUR")
            $currency = "76361650-e7f7-04c1-d5dd-50d02655e780";  
        return $currency;
	}

	private function getCapitalization($cap) {
		$ret = '1';
		if ($cap == 'YES')
			$ret = '2';
		return $ret;
	}

	private function getResident($resident) {
		$ret = 'Resident';
		if ($resident != $ret)
			$ret = 'Non resident';
		return $ret;
	}
	private function getBarCode() {
		$bar_code = mt_rand(1,9);
        for ($i = 0; $i<11; $i++) 
        {
            $bar_code .= mt_rand(0,9);
        }
        return $bar_code;
	}

	private function setRegionInfo($code) {      	    		
      	$json = file_get_contents("https://www.russipoteka.ru/regions_list.php?mode=city_code&region=$code");        
		$obj = json_decode($json);                
		
        $region_name = $obj[0];
        $this->region = $region_name->reg_name;
        $this->regioncode_c = $code;
	}
	private function toUTF8($str) {
		return iconv("windows-1251","utf-8", $str); 
	}

}