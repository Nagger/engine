<?php
namespace rib\crm;

use rib\utils;
use rib\ibank;

// Класс обработки заявки на закрытие вклада
class ProcessCaseCloseDepo extends Process
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
                    $case = new CaseCloseDepo($row);
                    $response = $case->saveCaseCloseDepo();
                } catch (\Exception $e) {
                    $this->logg->log($e->__toString());
                    $status = 'error';
                }

                if ($status != "error") {
                    $this->logg->log("Обращение успешно размещено с ID: " . $response->id);
                    $this->successCount ++;
                    $mailer = new utils\Mailer('EMinaeva@russipoteka.ru', 'Создана заявка на закрытие вклада', 'Создна заявка на закрытие вклада id:' . $response->id);
                    $mailer->sendEmail();
                }

                // меняем статус документа в шлюзовой таблице
                ibank\ConstrDocument::updateStatus($this->type, $row['id_gate'], $status);
            }
            $this->logg->log("Успешно обработано заявок: " . $this->successCount);
        }
    }
}