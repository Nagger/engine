<?php
namespace rib\crm;
// Класс заявки на карту
class OpportunityCard extends OpportunityBase {
	
	private $wf_type = 'forAccumulativeCard';
	private $opportunity_type = '';	
	private $type_of_opportunity_c = 'forAccumulativeCard';
	private $currency_id;
	private $type_of_card_c;
	private $region;
	private $regioncode_c;
	private $bar_code_c;
	private $sign_id;
	private $itn;
	//protected $contragent;

	public function __construct($doc)
	{
		$this->currency_id = $this->getCurrency($doc["currency"]);
		$this->type_of_card_c = $this->getTypeCard($doc["type_card"]);
		$this->setRegionInfo($doc['city']);
		$this->bar_code_c = $this->getBarCode();
		$this->sign_id = $doc['sign_key_id'];
		$this->itn = $doc['sign_key_id'] == '' ? 'PRE' : 'IB';
		parent::__construct($doc);
	}

	public function saveOpportunityCard() {
		$varCard = get_class_vars('\rib\crm\OpportunityCard');   		
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
		if ($response->error->number != 0) {
			throw new \Exception("При сохранении обращения в CRM произошла ошибка");
		}
		return $response;
	}

	private function getTypeCard($typeCard) {
		if ($typeCard == 'MasterCard Gold')
			$typeCard = 'MasterCardGold';
		if ($typeCard == 'Mastercard Platinum')
			$typeCard = 'MasterCardPlatinum';
		if ($typeCard == 'Visa Platinum')
			$typeCard = 'VisaPlatinum';
		return $typeCard;
	}

	private function getCurrency($currency) {
        if ($currency == "RUB")
            $currency = "-99";
        if ($currency == "USD")
            $currency = "292bf9a6-ac0d-70a5-a9f6-50d025de1534";
        if ($currency == "EUR")
            $currency = "76361650-e7f7-04c1-d5dd-50d02655e780";  
        return $currency;
	}

	private function getBarCode() {
		$bar_code = mt_rand(1,9);
        for ($i = 0; $i<11; $i++) 
        {
            $bar_code .= mt_rand(0,9);
        }
        return $bar_code;
	}

	private function setRegionInfo($city) {
		$city_decode = iconv("windows-1251","utf-8", $city);        	    
        $region_name = '7700000000000';
      	$json = file_get_contents("https://www.russipoteka.ru/regions_list.php?mode=city_name&region=$code");        
        $obj = json_decode($json);                
        $region_name = $obj[0];
        $this->region = $city_decode;
        $this->regioncode_c = $region_name->reg_code;
    }

}