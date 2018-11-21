<?php
namespace rib\ibentity;
use rib\utils;

class IBEntity {

    protected $conn;
    public $smarty;

    public function __construct() {        

        // Делаем коннект
        $mssql = \rib\ibentity\IBEntityMssql::getInstance();        
        $this->conn = $mssql->getConnection();

        // Смарти объект
        $this->smarty = new \rib\utils\CreatePDF("AKRCharge");
        $this->smarty->setReportDir("/var/report-data-akr");
    }

    public function execQuery($sql_query) {         
        $result = mssql_query($sql_query,$this->conn);
        return $result;
    }
}