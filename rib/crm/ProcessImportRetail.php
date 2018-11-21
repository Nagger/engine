<?php
namespace rib\crm;

use rib\utils;
use rib\ibank;

// Класс обработки заявки на импорт из Retail
class ProcessImportRetail extends Process {
    protected $successCount = 0;
    
        // Обрабатываем в цикле, запускаем фабрику и создаём обращение в CRM
        public function processConstrDoc()
        {
            if (mssql_num_rows($this->constrDocuments)) {
                // Есть кандидаты
                $this->logg->log("Найдено заявок: " . mssql_num_rows($this->constrDocuments));
                while ($row = mssql_fetch_array($this->constrDocuments, MSSQL_ASSOC)) {
                    $status = 'success';                    
                    // Проверяем есть Контрагент или нет
                    $account_exist = Account::getAccount($row['code_abs']);
                    //$row['worker_c'] = '1';                    
                    
                    if (!is_null($account_exist)) {
                        // Если контрагент уже существует сохраняем его ID, тогда будет update при сохранении
                        // Если нет то передаём $row, как есть, т.е без id в этом случае будет создан новый контрагент
                        // Обновляем данными из $row для существующего клиента с $account_exist->id
                        $row['id'] = $account_exist->id; 
                        $row['account_id'] = $account_exist->id;               
                    }   

                    try {                      

                        $account = new Account($row);                            
                        $account_id = $account->saveAccount();
                        // Создаем заявку на вклад привязанную к этому контрагенту    
                        // возможно передали только контрагента, тогда заявку не создаем

                        if (trim($row['IID']) != '') {
				        // Заявка из ритейла сразу выполнена
					$row['sales_stage'] = 'Done';
                                        $row['account_id'] = $account_id;
                                        $case = new OpportunityDepo($row);
                                        $response = $case->saveOpportunityDepo();
                        }
                    } catch (\Exception $e)  {
                        $this->logg->log($e->__toString()); 
                        $status = 'error';
                    }                                     
                    if ($status != "error") {
                        $this->logg->log("Обращение успешно размещено с ID: " . $response->id);
                        $this->successCount ++;
                        //$mailer = new utils\Mailer('AAnikushin@russipoteka.ru', 'Создана заявка на перевыпуск карты', 'Создна заявка на перевыпуск карты id:' . $response->id);
                        //$mailer = new utils\Mailer('pin@russipoteka.ru', 'Создана заявка на перевыпуск карты', 'Создана <a href="http://192.168.100.88/index.php?module=Cases&action=DetailView&record='.$response->id.'">заявка на перевыпуск карты</a>');
                        //$mailer->sendEmail();
                    }
    
                    // меняем статус документа в шлюзовой таблице
		    // возможно передали только контрагента, тогда заявку не создаем
			if (trim($row['IID']) != '') {
	                        ibank\ConstrDocument::updateStatus($this->type, $row['id_gate'], $status, $response->id);
			} else {
				ibank\ConstrDocument::updateStatus($this->type, $row['id_gate'], $status, $account_id);
			}
                }
                $this->logg->log("Успешно обработано заявок: " . $this->successCount);
            }
        }
}