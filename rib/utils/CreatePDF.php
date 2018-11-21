<?php
namespace rib\utils;

require_once('/usr/local/lib/smarty-3.1.30/libs/Smarty.class.php');

class CreatePDF {

    private $type;

    // Массив с данными отчета
    private $data;    

    // Имя файла для записи
    private $filename;
    private $report_dir;    
    
    public $n_file_html;
    public $n_file_pdf;

    public function __construct($type) {

        $this->logg = Logger::getLogger($type);

        $this->smarty = new \Smarty();
        $this->smarty->setTemplateDir('/var/smarty/templates/');
        $this->smarty->setCompileDir('/var/smarty/templates_c/');
        $this->smarty->setConfigDir('/var/smarty/configs/');
        $this->smarty->setCacheDir('/var/smarty/cache/');

        $this->type = $type;
        $this->data = $data;
        $this->filename = $filename;
    }

    // Сеттер для установки каталога куда будем писать файлы
    public function setReportDir($report_dir) {
        $this->report_dir = $report_dir;
    }

    // Сеттер для данных
    public function setData($data) {
        $this->data = $data;
    }

    // Сеттер для имени файлы
    public function setFileName($filename) {
        $this->filename = $filename;
    }

    // С использование шаблона пишу HTML файл
    public function createHTML() {

        $this->smarty->assignByRef('data', $this->data);
        //$smarty->debugging = true;

        $output = $this->smarty->fetch($this->type.".tpl");
        // echo $output;
        // die();
        // Записываем в файл
        $this->n_file_html = $this->report_dir.'/html/'.$this->filename.'.htm';         
        $this->n_file_pdf = $this->report_dir.'/pdf/'.$this->filename.'.pdf';         
        $file = fopen($this->n_file_html,"w+");

        fwrite($file,$output); 
        fclose($file);

        return $this->n_file_html;
    }

    
    public function convertToPDF() {
        // Конвертация в PDF
        $command = "wkhtmltopdf --page-size A4 $this->n_file_html $this->n_file_pdf";
        //echo $command;
        exec($command);
        $res = false;
        if (file_exists($this->n_file_pdf)) {
            $res = true;            
            // header('Content-type: application/pdf');
            // header('Content-Disposition: attachment; filename="catalog.pdf"');
            // readfile($this->n_file_pdf);
            unlink($this->n_file_html);
        } else {
            $log->info('PDF не получен');  
        }
        return $res;
    }
}