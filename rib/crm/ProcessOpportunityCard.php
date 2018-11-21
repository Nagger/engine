<?php
namespace rib\crm;
use rib\utils;
use rib\ibank;

// Класс обработки заявки на перевыпуск карты
class ProcessOpportunityCard extends Process {

	protected $successCount = 0;

	// Обрабатываем в цикле, запускаем фабрику и создаём заявку в CRM
	function processConstrDoc() {
		if (mssql_num_rows($this->constrDocuments)) {
			// Есть кандидаты
			$this->logg->log("Найдено заявок: " . mssql_num_rows($this->constrDocuments));
		    while ($row = mssql_fetch_array($this->constrDocuments, MSSQL_ASSOC)) {
		        $status = 'success';

		        // Создаем объект заявки 		        
		        try {		        	
		        	$case = new OpportunityCard($row);
		        	$response = $case->saveOpportunityCard();
		    	} catch (\Exception $e) {
		    		$this->logg->log($e->__toString());    	
		    		$status = 'error';
		    	}   

			    if ($status != "error") {
			       	$this->logg->log("Заявка на карту успешно размещена с ID: " . $response->id);	        
			       	$this->successCount ++;
			    }

			    // меняем статус документа в шлюзовой таблице
			    ibank\ConstrDocument::updateStatus($this->type, $row['id_gate'], $status);
		    }	
	    	$this->logg->log("Успешно обработано заявок: " . $this->successCount);
		}    
	}
}