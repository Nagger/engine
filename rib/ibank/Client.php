<?php       
namespace rib\ibank;
use rib\db;
// Базовый класс конструируемого документа в ИБ, 
// содержит статический метод получения текущего списка для обработки с параметром, 
// который определяет таблицу источник загрузки документовВщсгьутеШИ

class Client {

	public static function getClient($client_id) {
		$sql_query = "select * from ibank2.pclients where client_id = $client_id";		

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