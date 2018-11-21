<?php
namespace rib\ibentity;
use rib\utils;

class Charge {

    public $doc_id;
    public $date_doc;
    public $date_inbank;
    public $date_payment;
    public $num_doc;
    public $payment_type;
    public $payer_inn;
    public $payer_name;
    public $payer_account;
    public $amount;
    public $payer_bank_name;
    public $payer_bank_bic;
    public $payer_bank_acc;
    public $rcpt_inn;
    public $rcpt_account;
    public $rcpt_name;
    public $rcpt_bank_name;
    public $rcpt_bank_bic;
    public $rcpt_bank_acc;
    public $type_oper;
    public $queue;
    public $payment_details;
    public $kpp;
    public $term;
    public $rcpt_kpp;
    public $charge_creator;
    public $charge_kbk;
    public $charge_okato;
    public $charge_basis;
    public $charge_period;
    public $charge_num_doc;
    public $charge_date_doc;
    public $code;
    public $added_info;
    public $format_amount;

    public function __construct($row) {        
        $varAcc = get_class_vars('\rib\ibentity\Charge');        	

    	// Присваиваем каждому свойству объекта полученное значение
		foreach ($varAcc as $key => $value) {
			// Заполняем ненулевые свойства
			if (!$this->$key) {
		     	$this->$key = iconv('windows-1251','utf-8', $row[$key]);
             }             
        }   
        $this->format_amount = utils\Num2Str::format($this->amount);
        $this->amount = $this->formatAmount($this->amount);
        
        return $this; 	
    }
    private function formatAmount($amount) {
        $format = sprintf("%.2f",$amount);
        $arr = explode(".", $format);   
        $out = "$arr[0]-$arr[1]";
        if ($arr[1] === "00")    
            $out = "$arr[0]="; 
        return $out;
    }
}