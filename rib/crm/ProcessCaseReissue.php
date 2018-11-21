<?php
namespace rib\crm;

use rib\utils;
use rib\ibank;

// Класс обработки заявки на перевыпуск карты
class ProcessCaseReissue extends Process
{
    protected $successCount = 0;

    // Обрабатываем в цикле, запускаем фабрику и создаём обращение в CRM
    public function processConstrDoc()
    {
        if (mssql_num_rows($this->constrDocuments)) {
            // Есть кандидаты
            $this->logg->log("Найдено заявок: " . mssql_num_rows($this->constrDocuments));
            while ($row = mssql_fetch_array($this->constrDocuments, MSSQL_ASSOC)) {
                $status = 'success';

                // Создаем объект обращения на перевыпуск
                try {
                    $case = new CaseReissue($row);
                    $response = $case->saveCaseReissue();
                } catch (\Exception $e) {
                    $this->logg->log($e->__toString());
                    $status = 'error';
                }

                if ($status != "error") {
                    $this->logg->log("Обращение успешно размещено с ID: " . $response->id);
                    $this->successCount ++;
                    $mailer = new utils\Mailer('AAnikushin@russipoteka.ru', 'Создана заявка на перевыпуск карты', 'Создана <a href="http://192.168.100.100/index.php?module=Cases&action=DetailView&record='.$response->id.'">заявка на перевыпуск карты</a>');
                    $mailer->sendEmail();
                }

                // меняем статус документа в шлюзовой таблице
                ibank\ConstrDocument::updateStatus($this->type, $row['id_gate'], $status, $response->id);
            }
            $this->logg->log("Успешно обработано заявок: " . $this->successCount);
        }
    }
}
