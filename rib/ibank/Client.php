<?php       
namespace rib\ibank;
use rib\db;
// ������� ����� ��������������� ��������� � ��, 
// �������� ����������� ����� ��������� �������� ������ ��� ��������� � ����������, 
// ������� ���������� ������� �������� �������� ��������������������

class Client {

	public static function getClient($client_id) {
		$sql_query = "select * from ibank2.pclients where client_id = $client_id";		

		$result = self::execQuery($sql_query);	
	   	return $result;
	}
// ��������� ������

	private static function execQuery($sql_query) {
		$db = db\DBMssql::getInstance();
		$mssql = $db->getConnection();		
		$result = mssql_query($sql_query);     
	   	return $result;			
	}
}