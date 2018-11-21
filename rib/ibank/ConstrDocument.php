<?php       
namespace rib\ibank;
use rib\db;
// Базовый класс конструируемого документа в ИБ, 
// содержит статический метод получения текущего списка для обработки с параметром, 
// который определяет таблицу источник загрузки документовВщсгьутеШИ

class ConstrDocument {
	private $id_gate = '';
	private $date_doc = '';
	private $ext_client_id = '';
	private $owner_id = '';
	private $num_doc = '';
	private $status = '';
	private $query_id = '';
	private $last_sign_date = '';
	private $doc_id = '';
	private $sign_key_id = '';

// Получить список документов на выгрузку
	public static function getCandidats($type) {
		$sql_query = "select * from gate_crm_$type where status='load'";		
		$result = self::execQuery($sql_query);	
	   	return $result;
	}
// Изменить статус документа с помещением id документа в CRM
	public static function updateStatus($type,$id_gate,$status,$id_crm = null) {		
		if (!is_null($id_crm))
			$sql_query = "update gate_crm_$type set status='$status', id_crm='$id_crm' where id_gate = $id_gate";
		else
			$sql_query = "update gate_crm_$type set status='$status' where id_gate = $id_gate";
		$result = self::execQuery($sql_query);		
	   	return $result;		
	}
// Выполнить запрос
	private static function execQuery($sql_query) {
		$db = db\DBMssql::getInstance();
		$mssql = $db->getConnection();		
		$result = mssql_query($sql_query);     
	   	return $result;			
	}

}