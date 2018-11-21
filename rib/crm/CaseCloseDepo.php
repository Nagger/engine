<?php
namespace rib\crm;
// Класс тип обращение на перевыпуск карты

class CaseCloseDepo extends CaseBase {
	protected $type = "early_termination"; 	// тип обращения
	private $type_rus = "Досрочное расторжение договора вклада";	
	protected $description;					// Описание обращения
	protected $assigned_user_id = 1;		// На кого назначена
	protected $reason_restore_c;			// причина перевыпуска
	protected $logg;		

	public function __construct($doc) {		

	    // Передаем описание обращения
	    $this->setDescription(iconv("windows-1251","utf-8",$doc["contract"]));
	    // Назначаем заявку
	    // fdb4092b-801a-ca76-9b6e-533d36536d47 - тестовый	        	
	    // a454275f-4d13-b38b-e8d7-5110cc662d4b - Аникушин
	    // df8fb6a0-757b-4874-b6c8-541013bf6689 - Минаева
	    $this->setAssign("df8fb6a0-757b-4874-b6c8-541013bf6689");

		parent::__construct($doc["ext_client_id"], $this->type_rus);		
	}

	public function setDescription($description) {
		$this->description = $description;
	}
	public function setAssign($user_id) {
		$this->assigned_user_id = $user_id;
	}
	// Сохранение обращения
	public function saveCaseCloseDepo() {		
		$data = array(
			array("name" => 'account_id',"value" => $this->account_id),
			array("name" => 'passport_series_c',"value" => $this->passport_series_c),
			array("name" => 'passport_number_c',"value" => $this->passport_number_c),
			array("name" => 'passport_issued_c',"value" => $this->passport_issued_c),
			array("name" => 'issued_date_c',"value" => $this->issued_date_c),
			array("name" => 'passport_subdivision_code',"value" => $this->passport_subdivision_code),
			array("name" => 'mobile_phone_c',"value" => $this->mobile_phone_c),
			array("name" => 'name',"value" => $this->name),
			array("name" => 'passport_c',"value" => $this->passport_c),
			array("name" => 'control_word_c',"value" => $this->control_word_c),			
			array("name" => 'priority',"value" => $this->priority),
			array("name" => 'auth_success_c',"value" => 'yes'),
            array("name" => 'status',"value" => $this->status),
            array("name" => 'type',"value" => $this->type),            
            array("name" => 'description',"value" => $this->description),
            array("name" => 'assigned_user_id',"value" => $this->assigned_user_id)            
        );      
		$response = $this->soapclient->set_entry($this->sess,'Cases', $data);
		if ($response->error->number != 0) {
			throw new \Exception("При сохранении обращения в CRM произошла ошибка");
		}
		return $response;
	}
}