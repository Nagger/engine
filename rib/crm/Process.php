<?php
namespace rib\crm;
use rib\utils;
use rib\ibank;

// Абстрактный класс обработчика конструируемых документов

abstract class Process {
	protected $type;
	protected $constrDocuments;

	function __construct($type) {
		$this->type = $type;	
		$this->logg = utils\Logger::getLogger($this->type);
		// Получаем кандидат-документы на обработку	
		$this->constrDocuments = ibank\ConstrDocument::getCandidats($this->type);	
	}	

	static function getInstance($type) {
		if ($type == 'reissue') {
			return new ProcessCaseReissue($type);
		}
		else if ($type == 'card') {
			return new ProcessOpportunityCard($type);
		}
		else if ($type == 'close_depo') {
			return new ProcessCaseCloseDepo($type);
		}
		else if ($type == 'import_retail') {
			return new ProcessImportRetail($type);
		}
		else {
			throw new \Exception("Не найден тип документа");
		}
	}
	abstract function processConstrDoc();
}