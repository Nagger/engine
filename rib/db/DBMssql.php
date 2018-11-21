<?php
namespace rib\db;

class DBMssql {
	private $_host = '192.168.100.136';
	private $_dbname = 'ibank2';
	private $_user = 'ibank2';
	private $_password = '***';	

	private $_connection;
	private static $_instance;

	public function __construct() {
		$this->_connection = mssql_connect($this->_host, $this->_user, $this->_password);
		if (!$this->_connection) {
			// Генерируем сообщение
			trigger_error('Невозможно соединиться с сервером MSSQL '. mssql_get_last_message(), E_USER_ERROR);
		} else {
			 mssql_select_db($this->_dbname);
		}
	}

	public static function getInstance() {
		if (!self::$_instance) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function getConnection() {
		return $this->_connection;
	}

}