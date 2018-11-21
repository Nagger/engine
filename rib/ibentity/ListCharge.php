<?php
namespace rib\ibentity;
use rib\utils;

class ListCharge extends IBEntity {

    protected $client_id;
    protected $from_date;
    protected $logg;

    public function __construct($client_id, $from_date) {
        $this->logg = utils\Logger::getLogger("AKRCharge");                
        
        $this->client_id = $client_id;            
        $this->from_date = $from_date;
        parent::__construct();
    }

    public function process() {

        // Получаем список платёжек из очереди с нужным статусом
        $list = $this->getQueue();

        $countList = mssql_num_rows($list);

        if ($countList) {    
            $this->logg->log("Найдено платежек: $countList");
            $successCount = 0;
            while ($row = mssql_fetch_array($list, MSSQL_ASSOC)) {
                
                try {		        	
                    // Создаем объект платежки 		        
                    $charge = new Charge($row);

                    // Передаём в объект Смарти данные
                    $this->smarty->setData($charge);

                    // Передаем имя файла
                    //$this->smarty->setFileName($row['added_info']);
                    $doc_id = $row['doc_id'];
                    $added_info = $row['added_info'];

                    $this->smarty->setFileName($added_info);

                    // Создаем HTML
                    $file = $this->smarty->createHTML();

                    // Создаём PDF
                    $this->smarty->convertToPDF($file);

                    // Меняем статус в очереди
                    $this->setStatus($doc_id);

                    $successCount++;

                    
                } catch (\Exception $e) {
                    $this->logg->log($e->__toString());    	
                    $status = 'error';
                }   
        
                if ($status != "error") {
                       //$this->logg->log("платежка успешно напечатана ID: " . $row['added_info']);	        
                       $successCount ++;
                }                        
            }	
            $this->logg->log("Успешно обработано платежок: " . $successCount);
        }    

    }

    public function getQueue() {
        $sql = "select * from dbo.queue_payment where status_work = 'load'";        
        $res = $this->execQuery($sql);        
        return $res;
    }

    private function setStatus($doc_id) {
        $sql = "update dbo.queue_payment set status_work = 'print' where doc_id = $doc_id";
        $res = $this->execQuery($sql);        
        return $res;
    }

    public function import() {
        $sql = "INSERT into queue_payment 
SELECT
payment.doc_id,
CONVERT(varchar(20),payment.date_doc, 104) AS date_doc,
CONVERT(varchar(20),Max(hist_inbank.act_time), 104) AS date_inbank,
CONVERT(varchar(20),Max(hist_payment.act_time), 104) AS date_payment,
payment.num_doc,
payment.payment_type,
payment.payer_inn,
payment.payer_name,
payment.payer_account,
payment.amount,
payment.payer_bank_name,
payment.payer_bank_bic,
payment.payer_bank_acc,
payment.rcpt_inn,
payment.rcpt_name,
payment.rcpt_account,
payment.rcpt_bank_name,
payment.rcpt_bank_bic,
payment.rcpt_bank_acc,
payment.type_oper,
payment.queue,
payment.payment_details,
payment.kpp,
CONVERT(varchar(20),payment.term, 104) AS term,
payment.rcpt_kpp,
payment.charge_creator,
payment.charge_kbk,
payment.charge_okato,
payment.charge_basis,
payment.charge_period,
payment.charge_num_doc,
payment.charge_date_doc,
payment.code,
payment.added_info,
'load',
'' 
FROM 
ibank2.payment AS payment 
INNER JOIN ibank2.docs_hist AS hist_inbank ON payment.doc_id = hist_inbank.doc_id 
INNER JOIN ibank2.docs_hist AS hist_payment ON payment.doc_id = hist_payment.doc_id 
WHERE 
hist_inbank.doc_status = 2 AND 
hist_payment.doc_status = 5 AND 
payment.client_id = $this->client_id AND 
payment.date_doc >= '$this->from_date' AND 
payment.status_doc = 5 AND 
payment.doc_id not in(select doc_id from queue_payment) 
GROUP BY 
payment.doc_id,
payment.date_doc,
payment.num_doc,
payment.payment_type,
payment.payer_inn,
payment.payer_name,
payment.payer_account,
payment.amount,
payment.payer_bank_name,
payment.payer_bank_bic,
payment.payer_bank_acc,
payment.rcpt_inn,
payment.rcpt_name,
payment.rcpt_account,
payment.rcpt_bank_name,
payment.rcpt_bank_bic,
payment.rcpt_bank_acc,
payment.type_oper,
payment.queue,
payment.payment_details,
payment.kpp,
payment.term,
payment.rcpt_kpp,
payment.charge_creator,
payment.charge_kbk,
payment.charge_okato,
payment.charge_basis,
payment.charge_period,
payment.charge_num_doc,
payment.charge_date_doc,
payment.code,
payment.added_info
";
    $res = $this->execQuery($sql);        
    return $res;
    }
}