<?php
namespace rib\crm;
// Класс тип обращение на перевыпуск карты

class CaseReissue extends CaseBase {
	protected $type = "card_reissuance"; 	// тип обращения
	private $type_rus = "Перевыпуск карты";
	protected $pan_c; 						// номер карты для перевыпуска
	protected $description;					// Описание обращения
	protected $assigned_user_id = 1;		// На кого назначена
	protected $reason_restore_c;			// причина перевыпуска
	protected $logg;	
	protected $type_reason = array('Украдена' => 1, 'Потеряна'=> 2, 'Порча'=> 3, 'Изменение фамилии'=> 4, 'Завершение срока действия'=> 5, 'Прочее'=> 8);

	public function __construct($doc) {		
		$this->setPAN($doc["pan"]);
	    // Передаем причину перевыпуска
	    $this->setReason($doc["reason"]);
	    // Передаем описание обращения
	    $this->setDescription("Заявка из ИБ. Перевыпуск карты. " . iconv("windows-1251","utf-8",$doc["city"]));
	    // Назначаем заявку
	    // fdb4092b-801a-ca76-9b6e-533d36536d47 - тестовый	        	
	    // a454275f-4d13-b38b-e8d7-5110cc662d4b - Аникушин
	    $this->setAssign("a454275f-4d13-b38b-e8d7-5110cc662d4b");

		parent::__construct($doc["ext_client_id"], $this->type_rus);		
	}
	// Преобразуем причину перевыпуска в код
	public function setReason($reason) {
		$reason = iconv("windows-1251", "utf-8", $reason);		
		$reason = $this->type_reason[$reason];		
		$this->reason_restore_c = $reason;
	}
	public function setPAN($pan) {
		$pan = substr_replace($pan, str_repeat("*", 8), 4, 8);
		$this->pan_c = $pan;
	}
	public function setDescription($description) {
		$this->description = $description;
	}
	public function setAssign($user_id) {
		$this->assigned_user_id = $user_id;
	}
	// Сохранение обращения
	public function saveCaseReissue() {		
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
			array("name" => 'pan_c',"value" => $this->pan_c),
			array("name" => 'reason_restore_c',"value" => $this->reason_restore_c),
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