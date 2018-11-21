<?php
namespace rib\crm;
// Класс тип обращение

class CaseBase extends CRM {	
	// Оставляем ненулевыми свойства которые не должны заполнятья из контрагента	
	protected $keyword;
	protected $passport_series_c;
	protected $passport_number_c;
	protected $passport_issued_c;
	protected $issued_date_c;
	protected $passport_subdivision_code;
	protected $mobile_phone_c;
	protected $account_id; 				// Контрагент		
	protected $passport_c; 				// Паспорт
	protected $control_word_c; 			// Кодовое слово
	protected $priority = "P2"; 		// Приоритет
	protected $status = "New"; 	// Статус	
	protected $name;					// тема обращения
	private $contragent;				// Контрагент

	public function __construct($ext_client_id, $type) {
		$this->contragent = Account::getAccount($ext_client_id);
		if (!$this->contragent) {
			throw new \Exception ("Контрагент с $ext_client_id не найден");
		} 
		$this->passport_series_c = $this->contragent->passport_serial;
		$this->passport_number_c = $this->contragent->passport_number;
		$this->passport_issued_c = $this->contragent->passport_issued_by;
		$this->issued_date_c = $this->contragent->passport_issued_date;
		$this->passport_subdivision_code = $this->contragent->passport_issued_code;
		$this->mobile_phone_c = $this->contragent->phone_alternate;
		$this->account_id = $this->contragent->id;
		$this->control_word_c = $this->contragent->keyword;
		$this->name = $type . ' ' . $this->contragent->getName();
		$this->passport_c = $this->contragent->getPassport();  			
		parent::__construct();
	}	
}